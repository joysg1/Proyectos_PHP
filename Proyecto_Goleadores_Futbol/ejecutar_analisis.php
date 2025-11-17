<?php
header('Content-Type: application/json');

function ejecutar_analisis_python() {
    $timeout = 60; // 60 segundos máximo
    
    // Verificar que el script existe
    if (!file_exists('analisis_goleadores.py')) {
        return [
            'success' => false,
            'error' => 'El script de análisis no existe: analisis_goleadores.py'
        ];
    }
    
    // Verificar que Python está disponible
    $python_check = shell_exec('python --version 2>&1');
    if (strpos($python_check, 'Python') === false) {
        // Intentar con python3
        $python_check = shell_exec('python3 --version 2>&1');
        if (strpos($python_check, 'Python') === false) {
            return [
                'success' => false,
                'error' => 'Python no está instalado o no está en el PATH'
            ];
        } else {
            $python_cmd = 'python3';
        }
    } else {
        $python_cmd = 'python';
    }
    
    // Construir el comando
    $command = "$python_cmd analisis_goleadores.py";
    
    // Ejecutar el comando
    $output = [];
    $return_code = 0;
    exec($command . ' 2>&1', $output, $return_code);
    
    $output_text = implode("\n", $output);
    
    // Verificar si se generaron los archivos esperados
    $archivos_esperados = [
        'grafico_top10.png',
        'grafico_distribucion.png',
        'grafico_paises.png',
        'grafico_eficiencia.png',
        'grafico_detalle_jugadores.png',
        'grafico_correlacion.png',
        'reporte_goleadores.txt'
    ];
    
    $archivos_generados = [];
    foreach ($archivos_esperados as $archivo) {
        if (file_exists($archivo)) {
            $archivos_generados[] = $archivo;
        }
    }
    
    return [
        'success' => $return_code === 0,
        'return_code' => $return_code,
        'output' => $output_text,
        'archivos_generados' => $archivos_generados,
        'python_command' => $python_cmd,
        'timestamp' => date('Y-m-d H:i:s')
    ];
}

// Manejar la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $resultado = ejecutar_analisis_python();
        echo json_encode($resultado);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Error inesperado: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido'
    ]);
}
?>