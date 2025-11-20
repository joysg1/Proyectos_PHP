<?php
class ApiHandler {
    private $python_api_url;
    
    public function __construct() {
        $this->python_api_url = 'http://localhost:5000/api';
    }
    
    private function makeRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->python_api_url . $endpoint;
        
        $options = [
            'http' => [
                'method' => $method,
                'header' => 'Content-type: application/json',
                'timeout' => 30,
                'ignore_errors' => true
            ]
        ];
        
        if ($data && ($method === 'POST' || $method === 'PUT' || $method === 'DELETE')) {
            $options['http']['content'] = json_encode($data);
        }
        
        $context = stream_context_create($options);
        
        try {
            $response = @file_get_contents($url, false, $context);
            
            if ($response === FALSE) {
                $error = error_get_last();
                return ['error' => 'Error de conexión con el backend: ' . ($error['message'] ?? 'Desconocido')];
            }
            
            // Verificar el código de respuesta HTTP
            if (isset($http_response_header[0])) {
                preg_match('/HTTP\/\d\.\d\s+(\d+)/', $http_response_header[0], $matches);
                $http_code = $matches[1] ?? 200;
                
                if ($http_code >= 400) {
                    return ['error' => "Error del servidor backend (HTTP $http_code)"];
                }
            }
            
            $decoded_response = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['error' => 'Error decodificando respuesta JSON: ' . json_last_error_msg()];
            }
            
            return $decoded_response;
            
        } catch (Exception $e) {
            return ['error' => 'Error de conexión: ' . $e->getMessage()];
        }
    }
    
    public function getEstadisticas() {
        $result = $this->makeRequest('/estadisticas');
        return $result ?: ['error' => 'No se pudieron obtener estadísticas'];
    }
    
    public function entrenarModelo() {
        return $this->makeRequest('/entrenar', 'POST');
    }
    
    public function predecirPeso($calorias, $edad, $altura, $actividad) {
        // Validar datos de entrada
        $errors = $this->validarDatosPrediccion($calorias, $edad, $altura, $actividad);
        if (!empty($errors)) {
            return ['error' => implode(', ', $errors)];
        }
        
        $data = [
            'calorias' => (int)$calorias,
            'edad' => (int)$edad,
            'altura' => (int)$altura,
            'actividad' => $this->sanitizarActividad($actividad)
        ];
        
        $resultado = $this->makeRequest('/predecir', 'POST', $data);
        
        // Log para debugging
        error_log("Predicción - Datos enviados: " . print_r($data, true));
        error_log("Predicción - Resultado: " . print_r($resultado, true));
        
        return $resultado;
    }
    
    public function getRegistros() {
        $result = $this->makeRequest('/registros');
        
        if (isset($result['error'])) {
            return ['registros' => [], 'error' => $result['error']];
        }
        
        // Asegurar que siempre retorne un array de registros
        if (isset($result['registros'])) {
            return ['registros' => $result['registros']];
        } elseif (is_array($result) && !isset($result['error'])) {
            return ['registros' => $result];
        } else {
            return ['registros' => []];
        }
    }
    
    public function agregarRegistro($registro) {
        // Validar registro
        $errors = $this->validarRegistro($registro);
        if (!empty($errors)) {
            return ['error' => implode(', ', $errors)];
        }
        
        // Sanitizar datos
        $registro_sanitizado = [
            'fecha' => $this->sanitizarFecha($registro['fecha']),
            'calorias' => (int)$registro['calorias'],
            'peso' => (float)$registro['peso'],
            'edad' => (int)$registro['edad'],
            'altura' => (int)$registro['altura'],
            'actividad' => $this->sanitizarActividad($registro['actividad'])
        ];
        
        $resultado = $this->makeRequest('/registros', 'POST', $registro_sanitizado);
        
        // Log para debugging
        error_log("Agregar registro - Datos enviados: " . print_r($registro_sanitizado, true));
        error_log("Agregar registro - Resultado: " . print_r($resultado, true));
        
        return $resultado;
    }
    
    public function actualizarRegistro($registro) {
        if (!isset($registro['id']) || empty($registro['id'])) {
            return ['error' => 'ID de registro requerido'];
        }
        
        // Validar registro para actualización
        $errors = $this->validarRegistroActualizacion($registro);
        if (!empty($errors)) {
            return ['error' => implode(', ', $errors)];
        }
        
        $registro_sanitizado = [
            'id' => (int)$registro['id'],
            'fecha' => $this->sanitizarFecha($registro['fecha']),
            'calorias' => (int)$registro['calorias_registro'],
            'peso' => (float)$registro['peso'],
            'edad' => (int)$registro['edad_registro'],
            'altura' => (int)$registro['altura_registro'],
            'actividad' => $this->sanitizarActividad($registro['actividad_registro'])
        ];
        
        $resultado = $this->makeRequest('/registros', 'PUT', $registro_sanitizado);
        
        // Log para debugging
        error_log("Actualizar registro - Datos enviados: " . print_r($registro_sanitizado, true));
        error_log("Actualizar registro - Resultado: " . print_r($resultado, true));
        
        return $resultado;
    }
    
    public function eliminarRegistro($id) {
        if (empty($id)) {
            return ['error' => 'ID de registro requerido'];
        }
        
        $resultado = $this->makeRequest('/registros', 'DELETE', ['id' => (int)$id]);
        
        // Log para debugging
        error_log("Eliminar registro - ID: " . $id);
        error_log("Eliminar registro - Resultado: " . print_r($resultado, true));
        
        return $resultado;
    }
    
    public function getGraficoUrl($tipo) {
        $tipos_permitidos = ['area', 'radar', 'barras', 'pastel', 'lineas'];
        $tipo_sanitizado = in_array($tipo, $tipos_permitidos) ? $tipo : 'area';
        
        return $this->python_api_url . '/grafico/' . $tipo_sanitizado;
    }
    
    public function checkConnection() {
        $resultado = $this->makeRequest('/health');
        
        // Log para debugging
        error_log("Health check - Resultado: " . print_r($resultado, true));
        
        return $resultado;
    }
    
    // Métodos de validación
    private function validarDatosPrediccion($calorias, $edad, $altura, $actividad) {
        $errors = [];
        
        if (!is_numeric($calorias) || $calorias < 500 || $calorias > 10000) {
            $errors[] = 'Las calorías deben estar entre 500 y 10000';
        }
        
        if (!is_numeric($edad) || $edad < 10 || $edad > 120) {
            $errors[] = 'La edad debe estar entre 10 y 120 años';
        }
        
        if (!is_numeric($altura) || $altura < 100 || $altura > 250) {
            $errors[] = 'La altura debe estar entre 100 y 250 cm';
        }
        
        $actividades_permitidas = ['baja', 'moderada', 'alta'];
        if (!in_array($actividad, $actividades_permitidas)) {
            $errors[] = 'La actividad debe ser: baja, moderada o alta';
        }
        
        return $errors;
    }
    
    private function validarRegistro($registro) {
        $errors = [];
        $campos_requeridos = ['fecha', 'calorias', 'peso', 'edad', 'altura', 'actividad'];
        
        foreach ($campos_requeridos as $campo) {
            if (!isset($registro[$campo]) || empty($registro[$campo])) {
                $errors[] = "El campo {$campo} es requerido";
            }
        }
        
        // Validaciones específicas
        if (isset($registro['calorias']) && (!is_numeric($registro['calorias']) || $registro['calorias'] < 0)) {
            $errors[] = 'Las calorías deben ser un número positivo';
        }
        
        if (isset($registro['peso']) && (!is_numeric($registro['peso']) || $registro['peso'] < 0)) {
            $errors[] = 'El peso debe ser un número positivo';
        }
        
        if (isset($registro['edad']) && (!is_numeric($registro['edad']) || $registro['edad'] < 1 || $registro['edad'] > 120)) {
            $errors[] = 'La edad debe estar entre 1 y 120 años';
        }
        
        if (isset($registro['altura']) && (!is_numeric($registro['altura']) || $registro['altura'] < 50 || $registro['altura'] > 250)) {
            $errors[] = 'La altura debe estar entre 50 y 250 cm';
        }
        
        $actividades_permitidas = ['baja', 'moderada', 'alta'];
        if (isset($registro['actividad']) && !in_array($registro['actividad'], $actividades_permitidas)) {
            $errors[] = 'La actividad debe ser: baja, moderada o alta';
        }
        
        return $errors;
    }
    
    private function validarRegistroActualizacion($registro) {
        $errors = [];
        $campos_requeridos = ['fecha', 'calorias_registro', 'peso', 'edad_registro', 'altura_registro', 'actividad_registro'];
        
        foreach ($campos_requeridos as $campo) {
            if (!isset($registro[$campo]) || empty($registro[$campo])) {
                $errors[] = "El campo {$campo} es requerido";
            }
        }
        
        // Validaciones específicas
        if (isset($registro['calorias_registro']) && (!is_numeric($registro['calorias_registro']) || $registro['calorias_registro'] < 0)) {
            $errors[] = 'Las calorías deben ser un número positivo';
        }
        
        if (isset($registro['peso']) && (!is_numeric($registro['peso']) || $registro['peso'] < 0)) {
            $errors[] = 'El peso debe ser un número positivo';
        }
        
        if (isset($registro['edad_registro']) && (!is_numeric($registro['edad_registro']) || $registro['edad_registro'] < 1 || $registro['edad_registro'] > 120)) {
            $errors[] = 'La edad debe estar entre 1 y 120 años';
        }
        
        if (isset($registro['altura_registro']) && (!is_numeric($registro['altura_registro']) || $registro['altura_registro'] < 50 || $registro['altura_registro'] > 250)) {
            $errors[] = 'La altura debe estar entre 50 y 250 cm';
        }
        
        $actividades_permitidas = ['baja', 'moderada', 'alta'];
        if (isset($registro['actividad_registro']) && !in_array($registro['actividad_registro'], $actividades_permitidas)) {
            $errors[] = 'La actividad debe ser: baja, moderada o alta';
        }
        
        return $errors;
    }
    
    private function sanitizarFecha($fecha) {
        // Validar formato de fecha
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return $fecha;
        }
        
        // Si no es válida, usar fecha actual
        return date('Y-m-d');
    }
    
    private function sanitizarActividad($actividad) {
        $actividades_permitidas = ['baja', 'moderada', 'alta'];
        return in_array(strtolower($actividad), $actividades_permitidas) ? strtolower($actividad) : 'moderada';
    }
}

// Manejo de requests directos al API Handler
if (isset($_GET['action'])) {
    $api = new ApiHandler();
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'get_registros':
            echo json_encode($api->getRegistros());
            break;
            
        case 'get_estadisticas':
            echo json_encode($api->getEstadisticas());
            break;
            
        case 'check_connection':
            echo json_encode($api->checkConnection());
            break;
            
        default:
            echo json_encode(['error' => 'Acción no válida']);
    }
    exit;
}

// Manejo de solicitudes AJAX para actualización de registros
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar_registro') {
    $api = new ApiHandler();
    
    // Verificar si es una solicitud AJAX
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    
    $registro_actualizado = [
        'id' => $_POST['id'] ?? 0,
        'fecha' => $_POST['fecha'] ?? date('Y-m-d'),
        'calorias_registro' => $_POST['calorias_registro'] ?? 0,
        'peso' => $_POST['peso'] ?? 0,
        'edad_registro' => $_POST['edad_registro'] ?? 0,
        'altura_registro' => $_POST['altura_registro'] ?? 0,
        'actividad_registro' => $_POST['actividad_registro'] ?? 'moderada'
    ];
    
    $resultado = $api->actualizarRegistro($registro_actualizado);
    
    // Si es AJAX, retornar JSON
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode($resultado);
        exit;
    }
}

// Manejo de formularios POST normales (no AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $api = new ApiHandler();
    
    // Verificar si es una solicitud AJAX para excluir del procesamiento normal
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    
    // Solo procesar si NO es AJAX (para evitar duplicación)
    if (!$isAjax) {
        switch ($_POST['accion']) {
            case 'predecir':
                $resultado = $api->predecirPeso(
                    $_POST['calorias'] ?? 0,
                    $_POST['edad'] ?? 0,
                    $_POST['altura'] ?? 0,
                    $_POST['actividad'] ?? 'moderada'
                );
                break;
                
            case 'agregar_registro':
                $nuevo_registro = [
                    'fecha' => $_POST['fecha'] ?? date('Y-m-d'),
                    'calorias' => $_POST['calorias_registro'] ?? 0,
                    'peso' => $_POST['peso'] ?? 0,
                    'edad' => $_POST['edad_registro'] ?? 0,
                    'altura' => $_POST['altura_registro'] ?? 0,
                    'actividad' => $_POST['actividad_registro'] ?? 'moderada'
                ];
                $resultado = $api->agregarRegistro($nuevo_registro);
                break;
                
            case 'eliminar_registro':
                $resultado = $api->eliminarRegistro($_POST['id'] ?? 0);
                break;
        }
        
        // Si hay resultado, podrías guardarlo en sesión para mostrarlo en la página
        if (isset($resultado)) {
            // Podrías almacenar en sesión para mostrar en la página
            session_start();
            $_SESSION['ultimo_resultado'] = $resultado;
            $_SESSION['ultima_accion'] = $_POST['accion'];
        }
    }
}
?>