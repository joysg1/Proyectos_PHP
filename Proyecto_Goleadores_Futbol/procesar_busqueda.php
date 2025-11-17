<?php
header('Content-Type: application/json');

// Configurar CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Verificar si el archivo JSON existe
    if (!file_exists('goleadores.json')) {
        throw new Exception('La base de datos no está disponible');
    }

    // Cargar datos desde el archivo JSON
    $data = file_get_contents('goleadores.json');
    $players = json_decode($data, true);

    if ($players === null) {
        throw new Exception('Error al decodificar el archivo JSON');
    }

    // Obtener parámetros de búsqueda
    $playerName = isset($_POST['playerName']) ? trim($_POST['playerName']) : '';
    $country = isset($_POST['country']) ? trim($_POST['country']) : '';
    $minGoals = isset($_POST['minGoals']) ? intval($_POST['minGoals']) : 0;
    $goalType = isset($_POST['goalType']) ? trim($_POST['goalType']) : '';

    // Filtrar jugadores según los criterios de búsqueda
    $filteredPlayers = array_filter($players, function($player) use ($playerName, $country, $minGoals, $goalType) {
        // Filtrar por nombre (si se proporciona)
        if (!empty($playerName) && stripos($player['nombre'], $playerName) === false) {
            return false;
        }
        
        // Filtrar por país (si se proporciona)
        if (!empty($country) && $player['pais'] !== $country) {
            return false;
        }
        
        // Filtrar por goles mínimos (si se proporciona)
        if ($minGoals > 0 && $player['goles_totales'] < $minGoals) {
            return false;
        }
        
        // Filtrar por tipo de gol (si se proporciona)
        if (!empty($goalType) && $player['tipos_goles'][$goalType] == 0) {
            return false;
        }
        
        return true;
    });

    // Reindexar el array
    $filteredPlayers = array_values($filteredPlayers);

    // Ordenar por total de goles (descendente)
    usort($filteredPlayers, function($a, $b) {
        return $b['goles_totales'] - $a['goles_totales'];
    });

    // Calcular estadísticas generales
    $totalPlayers = count($players);
    $totalGoals = array_sum(array_column($players, 'goles_totales'));
    $averageGoals = $totalPlayers > 0 ? $totalGoals / $totalPlayers : 0;

    // Preparar respuesta
    $response = [
        'success' => true,
        'data' => [
            'players' => $filteredPlayers,
            'stats' => [
                'total_players' => $totalPlayers,
                'total_goals' => $totalGoals,
                'average_goals' => round($averageGoals, 2),
                'results_count' => count($filteredPlayers)
            ]
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    // Manejar errores
    $errorResponse = [
        'success' => false,
        'error' => $e->getMessage(),
        'data' => []
    ];
    
    echo json_encode($errorResponse);
}
?>