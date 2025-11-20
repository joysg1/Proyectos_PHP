<?php
class APIHandler {
    private $api_base_url;
    
    public function __construct($base_url = 'http://localhost:5000/api') {
        $this->api_base_url = $base_url;
    }
    
    private function makeRequest($endpoint) {
        $url = $this->api_base_url . $endpoint;
        
        // DEBUG: Mostrar URL que se está intentando acceder
        error_log("🔗 Intentando conectar a: " . $url);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        // Agregar headers para mejor compatibilidad
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        // DEBUG: Log de la respuesta
        error_log("📡 Respuesta HTTP: " . $http_code);
        error_log("📦 Respuesta: " . substr($response, 0, 200));
        
        if ($response === false) {
            error_log("❌ Error cURL: " . $error);
            return [
                'success' => false,
                'error' => 'Error en la conexión con el servidor Python: ' . $error
            ];
        }
        
        $decoded_response = json_decode($response, true);
        
        if ($http_code !== 200) {
            return [
                'success' => false,
                'error' => 'Error HTTP: ' . $http_code . ' - ' . $response
            ];
        }
        
        if (!$decoded_response) {
            return [
                'success' => false,
                'error' => 'Error decodificando respuesta JSON: ' . $response
            ];
        }
        
        return $decoded_response;
    }
    
    public function getAllCharts() {
        return $this->makeRequest('/charts/all');
    }
    
    public function getMLPredictions() {
        return $this->makeRequest('/ml/predictions');
    }
    
    public function healthCheck() {
        return $this->makeRequest('/health');
    }
}

// Manejo de solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $api = new APIHandler();
    $action = $_POST['action'];
    
    // DEBUG: Log de la acción recibida
    error_log("🎯 Acción recibida: " . $action);
    
    $response = [];
    
    switch ($action) {
        case 'all_charts':
            $response = $api->getAllCharts();
            break;
        case 'ml_predictions':
            $response = $api->getMLPredictions();
            break;
        case 'health_check':
            $response = $api->healthCheck();
            break;
        default:
            $response = ['success' => false, 'error' => 'Acción no válida: ' . $action];
    }
    
    // DEBUG: Log de la respuesta final
    error_log("📤 Enviando respuesta: " . json_encode($response));
    
    echo json_encode($response);
    exit;
}
?>