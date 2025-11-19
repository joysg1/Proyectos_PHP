<?php
/**
 * Manejador de API para comunicación con el backend de Python
 * Este archivo actúa como proxy entre el frontend PHP y el backend Python
 */

class ApiHandler {
    private $apiBaseUrl;
    
    public function __construct($baseUrl = 'http://localhost:5000/api') {
        $this->apiBaseUrl = $baseUrl;
    }
    
    /**
     * Realiza una petición a la API de Python
     */
    private function makeRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->apiBaseUrl . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen(json_encode($data))
                ]);
            }
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response === false) {
            return [
                'success' => false,
                'error' => 'Error de conexión con el servidor de Python'
            ];
        }
        
        $decodedResponse = json_decode($response, true);
        
        return [
            'success' => $httpCode === 200,
            'data' => $decodedResponse,
            'http_code' => $httpCode
        ];
    }
    
    /**
     * Obtiene todos los gráficos
     */
    public function getCharts() {
        return $this->makeRequest('/charts');
    }
    
    /**
     * Obtiene estadísticas
     */
    public function getStatistics() {
        return $this->makeRequest('/stats');
    }
    
    /**
     * Obtiene datos
     */
    public function getData() {
        return $this->makeRequest('/data');
    }
    
    /**
     * Agrega nuevos datos
     */
    public function addData($data) {
        return $this->makeRequest('/data', 'POST', $data);
    }
    
    /**
     * Realiza una predicción
     */
    public function makePrediction($data) {
        return $this->makeRequest('/predict', 'POST', $data);
    }
    
    /**
     * Verifica la salud de la API
     */
    public function healthCheck() {
        return $this->makeRequest('/health');
    }
}

// Ejemplo de uso (para pruebas)
if (isset($_GET['test'])) {
    header('Content-Type: application/json');
    $api = new ApiHandler();
    $result = $api->healthCheck();
    echo json_encode($result);
    exit;
}
?>
