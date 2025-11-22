<?php
// Configuración del sistema
define('API_BASE_URL', 'http://localhost:5000/api');
define('SITE_TITLE', 'Sistema de Análisis de Producción Celular');
define('DATA_REFRESH_INTERVAL', 30000); // 30 segundos

// Función para hacer requests a la API de Python
function api_request($endpoint, $method = 'GET', $data = null) {
    $url = API_BASE_URL . $endpoint;
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data && in_array($method, ['POST', 'PUT'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data))
        ]);
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'success' => $http_code >= 200 && $http_code < 300,
        'data' => json_decode($response, true),
        'code' => $http_code
    ];
}
?>
