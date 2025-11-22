<?php
/**
 * Manejador de APIs para comunicación entre frontend PHP y backend Python
 * Este archivo sirve como puente para las solicitudes AJAX desde el frontend
 */

// Configuración de CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

class APIHandler {
    private $pythonBaseUrl = 'http://localhost:5000';
    
    public function __construct() {
        $this->handleRequest();
    }
    
    private function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $query = $_SERVER['QUERY_STRING'];
        
        // Solo manejar requests a la API
        if (strpos($path, '/api/') === false) {
            return;
        }
        
        // Construir URL para el backend Python
        $pythonUrl = $this->pythonBaseUrl . $path;
        if ($query) {
            $pythonUrl .= '?' . $query;
        }
        
        // Preparar la solicitud cURL
        $ch = curl_init();
        
        // Configurar opciones básicas
        curl_setopt($ch, CURLOPT_URL, $pythonUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        // Configurar según el método HTTP
        switch ($method) {
            case 'GET':
                // No se necesita configuración adicional para GET
                break;
                
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                $postData = file_get_contents('php://input');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                break;
                
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                $putData = file_get_contents('php://input');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $putData);
                break;
                
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }
        
        // Configurar headers
        $headers = [];
        foreach (getallheaders() as $key => $value) {
            if (in_array(strtolower($key), ['content-type', 'authorization'])) {
                $headers[] = $key . ': ' . $value;
            }
        }
        
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        
        // Ejecutar la solicitud
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        
        // Manejar errores
        if (curl_error($ch)) {
            $this->sendError('Error en la conexión con el backend: ' . curl_error($ch), 500);
            curl_close($ch);
            return;
        }
        
        curl_close($ch);
        
        // Establecer el código HTTP y content-type
        http_response_code($httpCode);
        if ($contentType) {
            header('Content-Type: ' . $contentType);
        }
        
        // Devolver la respuesta del backend Python
        echo $response;
    }
    
    private function sendError($message, $code = 500) {
        http_response_code($code);
        echo json_encode([
            'error' => true,
            'message' => $message
        ]);
    }
}

// Inicializar el manejador de API si se accede directamente
if (basename($_SERVER['PHP_SELF']) == 'api_handler.php') {
    new APIHandler();
}
?>