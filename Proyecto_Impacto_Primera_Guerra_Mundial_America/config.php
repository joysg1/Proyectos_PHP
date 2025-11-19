<?php
// Configuración del proyecto
define('PROJECT_TITLE', 'Impacto de la Primera Guerra Mundial en América');
define('DEBUG_MODE', true);

// Configuración de la API Python
define('PYTHON_API_URL', 'http://localhost:5000/api');

// Función para hacer requests a la API de Python
function callPythonAPI($endpoint) {
    $url = PYTHON_API_URL . '/' . $endpoint;
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ]
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['error' => "cURL Error: $error"];
    }
    
    if ($httpCode !== 200) {
        return ['error' => "HTTP Error: $httpCode"];
    }
    
    return json_decode($response, true) ?: ['error' => 'Invalid JSON response'];
}

// Función para obtener datos con fallback
function getPythonData($endpoint) {
    $data = callPythonAPI($endpoint);
    
    if (isset($data['error']) && DEBUG_MODE) {
        error_log("API Error ($endpoint): " . $data['error']);
    }
    
    return $data;
}

// Verificar que Python esté disponible
function checkPythonAPI() {
    $health = callPythonAPI('health');
    return isset($health['status']) && $health['status'] === 'healthy';
}
?>
