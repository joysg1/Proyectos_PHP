<?php
// Manejo de APIs - Puente entre PHP y Python
include 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$endpoint = str_replace('/api_handler.php', '', $path);

// Proxying requests to Python backend
if ($method === 'GET') {
    $response = api_request($endpoint);
} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $response = api_request($endpoint, 'POST', $input);
} elseif ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $response = api_request($endpoint, 'PUT', $input);
} elseif ($method === 'DELETE') {
    $response = api_request($endpoint, 'DELETE');
}

http_response_code($response['code']);
echo json_encode($response['data']);
?>
