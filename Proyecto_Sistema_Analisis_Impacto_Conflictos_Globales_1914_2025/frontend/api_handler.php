<?php
// Frontend - api_handler.php
class ApiHandler {
    private $apiBaseUrl;
    
    public function __construct($baseUrl = 'http://localhost:5000/api') {
        $this->apiBaseUrl = $baseUrl;
    }
    
    public function makeRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->apiBaseUrl . $endpoint;
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        if ($method === 'POST' && $data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($data))
            ]);
        }
        
        // Para desarrollo - en producción quitar estas opciones
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_error($ch)) {
            error_log('cURL Error: ' . curl_error($ch));
            return ['success' => false, 'error' => 'Error de conexión'];
        }
        
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);
        
        if ($httpCode !== 200 || !$decodedResponse) {
            return [
                'success' => false, 
                'error' => 'Error en la respuesta del servidor',
                'http_code' => $httpCode
            ];
        }
        
        return $decodedResponse;
    }
    
    public function getStats() {
        return $this->makeRequest('/stats');
    }
    
    public function getConflicts() {
        return $this->makeRequest('/conflicts');
    }
    
    public function getChart($chartType) {
        return $this->makeRequest('/chart/' . $chartType);
    }
    
    public function getClusters() {
        return $this->makeRequest('/ml/clusters');
    }
    
    public function getTrends() {
        return $this->makeRequest('/ml/trends');
    }
    
    public function predictImpact($features, $target) {
        return $this->makeRequest('/ml/predict', 'POST', [
            'features' => $features,
            'target' => $target
        ]);
    }
}

// Función helper para usar en otras páginas PHP
function getApiHandler() {
    return new ApiHandler();
}
?>
