<?php
// ==================== ARRAY DE CONTINENTES Y DATOS ====================
$continentes = [
    'Africa' => [
        'paises' => ['Nigeria', 'Egipto', 'Sudáfrica', 'Kenia', 'Etiopía'],
        'poblacion' => 1340000000,
        'area_km2' => 30370000,
        'paises_count' => 54,
        'idiomas' => ['Árabe', 'Swahili', 'Inglés', 'Francés', 'Portugués'],
        'moneda_comun' => false,
        'curiosidad' => 'África es el continente con más países del mundo'
    ],
    'America' => [
        'paises' => ['Estados Unidos', 'Brasil', 'México', 'Argentina', 'Canadá'],
        'poblacion' => 1012000000,
        'area_km2' => 42330000,
        'paises_count' => 35,
        'idiomas' => ['Español', 'Inglés', 'Portugués', 'Francés'],
        'moneda_comun' => false,
        'curiosidad' => 'América tiene el río más largo del mundo: el Amazonas'
    ],
    'Asia' => [
        'paises' => ['China', 'India', 'Japón', 'Rusia', 'Indonesia'],
        'poblacion' => 4640000000,
        'area_km2' => 44579000,
        'paises_count' => 48,
        'idiomas' => ['Chino', 'Hindi', 'Árabe', 'Ruso', 'Japonés'],
        'moneda_comun' => false,
        'curiosidad' => 'Asia es el continente más grande y poblado del mundo'
    ],
    'Europa' => [
        'paises' => ['Alemania', 'Francia', 'Reino Unido', 'Italia', 'España'],
        'poblacion' => 747000000,
        'area_km2' => 10180000,
        'paises_count' => 44,
        'idiomas' => ['Inglés', 'Alemán', 'Francés', 'Italiano', 'Español'],
        'moneda_comun' => true,
        'curiosidad' => 'Europa tiene la moneda común más exitosa: el Euro'
    ],
    'Oceania' => [
        'paises' => ['Australia', 'Nueva Zelanda', 'Papúa Nueva Guinea', 'Fiyi', 'Samoa'],
        'poblacion' => 43000000,
        'area_km2' => 8526000,
        'paises_count' => 14,
        'idiomas' => ['Inglés', 'Francés', 'Tok Pisin', 'Fiyiano'],
        'moneda_comun' => false,
        'curiosidad' => 'Oceanía es el continente más pequeño en superficie terrestre'
    ],
    'Antartida' => [
        'paises' => ['Territorios de varios países', 'Sin población permanente'],
        'poblacion' => 1000, // población temporal de investigadores
        'area_km2' => 14000000,
        'paises_count' => 0,
        'idiomas' => ['Varios idiomas de investigadores'],
        'moneda_comun' => false,
        'curiosidad' => 'La Antártida es el continente más frío, seco y ventoso'
    ]
];

// ==================== OPERACIONES CON ARRAYS ====================
function ejecutarOperacionArray($operacion, $continente = null) {
    global $continentes;
    $resultado = '';
    
    switch($operacion) {
        case 'mostrar_todos':
            $resultado = "<h4>🌍 Todos los continentes:</h4>";
            foreach($continentes as $nombre => $datos) {
                $resultado .= "<div class='continent-item'>";
                $resultado .= "<strong>$nombre</strong> - {$datos['paises_count']} países - " . 
                             number_format($datos['poblacion']) . " habitantes";
                $resultado .= "</div>";
            }
            break;
            
        case 'contar_elementos':
            $resultado = "Número total de continentes: " . count($continentes);
            break;
            
        case 'mostrar_claves':
            $claves = array_keys($continentes);
            $resultado = "Claves del array: " . implode(', ', $claves);
            break;
            
        case 'mostrar_valores':
            $poblaciones = array_column($continentes, 'poblacion');
            $resultado = "Poblaciones: " . implode(', ', array_map('number_format', $poblaciones));
            break;
            
        case 'buscar_continente':
            if ($continente && isset($continentes[$continente])) {
                $datos = $continentes[$continente];
                $resultado = "<h4>🔍 Información de $continente:</h4>";
                $resultado .= "<ul>";
                $resultado .= "<li>🌎 Población: " . number_format($datos['poblacion']) . " habitantes</li>";
                $resultado .= "<li>🗺️ Área: " . number_format($datos['area_km2']) . " km²</li>";
                $resultado .= "<li>🏛️ Países: " . $datos['paises_count'] . "</li>";
                $resultado .= "<li>🗣️ Idiomas principales: " . implode(', ', $datos['idiomas']) . "</li>";
                $resultado .= "<li>💰 Moneda común: " . ($datos['moneda_comun'] ? 'Sí' : 'No') . "</li>";
                $resultado .= "<li>💡 Curiosidad: " . $datos['curiosidad'] . "</li>";
                $resultado .= "</ul>";
            } else {
                $resultado = "❌ Continente no encontrado";
            }
            break;
            
        case 'paises_continente':
            if ($continente && isset($continentes[$continente])) {
                $paises = $continentes[$continente]['paises'];
                $resultado = "<h4>🇺🇳 Países de $continente:</h4>";
                $resultado .= "<div class='countries-grid'>";
                foreach($paises as $pais) {
                    $resultado .= "<span class='country-tag'>$pais</span>";
                }
                $resultado .= "</div>";
            }
            break;
            
        case 'continente_mas_poblado':
            $poblaciones = array_column($continentes, 'poblacion');
            $max_poblacion = max($poblaciones);
            $continente_mas_poblado = array_search($max_poblacion, $poblaciones);
            $resultado = "🏆 Continente más poblado: <strong>$continente_mas_poblado</strong> con " . 
                        number_format($max_poblacion) . " habitantes";
            break;
            
        case 'continente_mas_grande':
            $areas = array_column($continentes, 'area_km2');
            $max_area = max($areas);
            $continente_mas_grande = array_search($max_area, $areas);
            $resultado = "🗺️ Continente más grande: <strong>$continente_mas_grande</strong> con " . 
                        number_format($max_area) . " km²";
            break;
            
        case 'poblacion_total':
            $poblaciones = array_column($continentes, 'poblacion');
            $total = array_sum($poblaciones);
            $resultado = "👥 Población mundial total: " . number_format($total) . " habitantes";
            break;
            
        case 'area_total':
            $areas = array_column($continentes, 'area_km2');
            $total = array_sum($areas);
            $resultado = "🌐 Área terrestre total: " . number_format($total) . " km²";
            break;
            
        case 'filtrar_por_poblacion':
            $filtrados = array_filter($continentes, function($datos) {
                return $datos['poblacion'] > 1000000000; // Más de 1 billón
            });
            $resultado = "<h4>🌍 Continentes con más de 1 billón de habitantes:</h4>";
            foreach($filtrados as $nombre => $datos) {
                $resultado .= "<div class='continent-item'>$nombre: " . number_format($datos['poblacion']) . " hab.</div>";
            }
            break;
            
        case 'ordenar_por_poblacion':
            $poblaciones = array_column($continentes, 'poblacion');
            array_multisort($poblaciones, SORT_DESC, $continentes);
            $resultado = "<h4>📊 Continentes ordenados por población (descendente):</h4>";
            foreach($continentes as $nombre => $datos) {
                $resultado .= "<div class='continent-item'>$nombre: " . number_format($datos['poblacion']) . " hab.</div>";
            }
            break;
            
        case 'ordenar_por_nombre':
            ksort($continentes);
            $resultado = "<h4>🔤 Continentes ordenados alfabéticamente:</h4>";
            foreach($continentes as $nombre => $datos) {
                $resultado .= "<div class='continent-item'>$nombre</div>";
            }
            break;
            
        case 'array_slice':
            $primeros_tres = array_slice($continentes, 0, 3);
            $resultado = "<h4>✂️ Primeros 3 continentes (array_slice):</h4>";
            foreach($primeros_tres as $nombre => $datos) {
                $resultado .= "<div class='continent-item'>$nombre</div>";
            }
            break;
            
        case 'combinar_arrays':
            $nombres = array_keys($continentes);
            $poblaciones = array_column($continentes, 'poblacion');
            $combinado = array_combine($nombres, $poblaciones);
            $resultado = "<h4>🔄 Array combinado (nombre => población):</h4>";
            foreach($combinado as $nombre => $poblacion) {
                $resultado .= "<div class='continent-item'>$nombre: " . number_format($poblacion) . " hab.</div>";
            }
            break;
            
        case 'buscar_pais':
            $pais_buscado = 'Brasil';
            $encontrado = '';
            foreach($continentes as $continente => $datos) {
                if (in_array($pais_buscado, $datos['paises'])) {
                    $encontrado = $continente;
                    break;
                }
            }
            $resultado = $encontrado ? 
                "📍 El país <strong>$pais_buscado</strong> se encuentra en <strong>$encontrado</strong>" :
                "❌ El país $pais_buscado no fue encontrado";
            break;
            
        case 'estadisticas':
            $total_paises = array_sum(array_column($continentes, 'paises_count'));
            $total_poblacion = array_sum(array_column($continentes, 'poblacion'));
            $total_area = array_sum(array_column($continentes, 'area_km2'));
            
            $resultado = "<h4>📈 Estadísticas Mundiales:</h4>";
            $resultado .= "<ul>";
            $resultado .= "<li>🌍 Total de continentes: " . count($continentes) . "</li>";
            $resultado .= "<li>🏛️ Total de países: $total_paises</li>";
            $resultado .= "<li>👥 Población mundial: " . number_format($total_poblacion) . " habitantes</li>";
            $resultado .= "<li>🗺️ Área terrestre total: " . number_format($total_area) . " km²</li>";
            $resultado .= "<li>📊 Densidad promedio: " . number_format($total_poblacion / $total_area, 2) . " hab/km²</li>";
            $resultado .= "</ul>";
            break;
            
        case 'estructura_completa':
            $resultado = "<h4>🏗️ Estructura completa del array:</h4>";
            $resultado .= "<div class='code'>" . highlight_array($continentes) . "</div>";
            break;
            
        default:
            $resultado = "❌ Operación no reconocida";
    }
    
    return $resultado;
}

// Función para formatear arrays de manera legible
function highlight_array($array, $nivel = 0) {
    $html = '';
    $indent = str_repeat('  ', $nivel);
    
    foreach($array as $clave => $valor) {
        if (is_array($valor)) {
            $html .= "$indent<strong>$clave</strong> => [<br>";
            $html .= highlight_array($valor, $nivel + 1);
            $html .= "$indent]<br>";
        } else {
            $tipo = gettype($valor);
            $color = $tipo == 'string' ? '#22863a' : 
                    ($tipo == 'integer' ? '#005cc5' : 
                    ($tipo == 'boolean' ? '#d73a49' : '#6f42c1'));
            
            $valor_mostrado = is_bool($valor) ? ($valor ? 'true' : 'false') : $valor;
            $html .= "$indent<strong>$clave</strong> => <span style='color: $color'>$valor_mostrado</span> <small style='color: #666'>($tipo)</small><br>";
        }
    }
    
    return $html;
}

// ==================== PROCESAMIENTO DEL FORMULARIO ====================
$resultado = '';
$operacion_seleccionada = '';
$continente_seleccionado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $operacion_seleccionada = $_POST['operacion'] ?? '';
    $continente_seleccionado = $_POST['continente'] ?? '';
    
    if (!empty($operacion_seleccionada)) {
        $resultado = ejecutarOperacionArray($operacion_seleccionada, $continente_seleccionado);
    } else {
        $resultado = "❌ Por favor, selecciona una operación";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operaciones con Arrays - Continentes</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .header {
            background: #2c3e50;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        .content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            min-height: 600px;
        }
        .form-section {
            padding: 30px;
            background: #f8f9fa;
        }
        .result-section {
            padding: 30px;
            background: white;
            overflow-y: auto;
        }
        .info-section {
            grid-column: 1 / -1;
            padding: 30px;
            background: #f1f2f6;
            border-top: 2px solid #ddd;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        select, button {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }
        select:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .result-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid #667eea;
            margin-top: 20px;
            max-height: 500px;
            overflow-y: auto;
        }
        .continent-item {
            background: white;
            padding: 10px 15px;
            margin: 8px 0;
            border-radius: 6px;
            border-left: 4px solid #3498db;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .country-tag {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 5px 10px;
            margin: 5px;
            border-radius: 15px;
            font-size: 14px;
        }
        .countries-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 10px 0;
        }
        .code {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.4;
            overflow-x: auto;
            white-space: pre-wrap;
        }
        .info-box {
            background: #e8f4fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .operations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .operation-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #27ae60;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s;
        }
        .operation-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .operation-card h4 {
            margin-bottom: 8px;
            color: #2c3e50;
        }
        .operation-card p {
            font-size: 14px;
            color: #666;
            margin: 0;
        }
        .continent-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .stat-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🌍 Operaciones con Arrays - Continentes</h1>
            <p>Ejemplos prácticos de manipulación de arrays asociativos y multidimensionales en PHP</p>
        </div>
        
        <div class="content">
            <div class="form-section">
                <h2>🧪 Operaciones con Arrays</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="operacion">Selecciona una Operación:</label>
                        <select id="operacion" name="operacion" required>
                            <option value="">-- Elige una operación --</option>
                            <option value="mostrar_todos" <?php echo $operacion_seleccionada == 'mostrar_todos' ? 'selected' : ''; ?>>🌍 Mostrar todos los continentes</option>
                            <option value="contar_elementos" <?php echo $operacion_seleccionada == 'contar_elementos' ? 'selected' : ''; ?>>🔢 Contar elementos</option>
                            <option value="mostrar_claves" <?php echo $operacion_seleccionada == 'mostrar_claves' ? 'selected' : ''; ?>>🔑 Mostrar claves</option>
                            <option value="mostrar_valores" <?php echo $operacion_seleccionada == 'mostrar_valores' ? 'selected' : ''; ?>>📊 Mostrar valores</option>
                            <option value="buscar_continente" <?php echo $operacion_seleccionada == 'buscar_continente' ? 'selected' : ''; ?>>🔍 Buscar continente</option>
                            <option value="paises_continente" <?php echo $operacion_seleccionada == 'paises_continente' ? 'selected' : ''; ?>>🇺🇳 Países del continente</option>
                            <option value="continente_mas_poblado" <?php echo $operacion_seleccionada == 'continente_mas_poblado' ? 'selected' : ''; ?>>🏆 Continente más poblado</option>
                            <option value="continente_mas_grande" <?php echo $operacion_seleccionada == 'continente_mas_grande' ? 'selected' : ''; ?>>🗺️ Continente más grande</option>
                            <option value="poblacion_total" <?php echo $operacion_seleccionada == 'poblacion_total' ? 'selected' : ''; ?>>👥 Población total</option>
                            <option value="area_total" <?php echo $operacion_seleccionada == 'area_total' ? 'selected' : ''; ?>>🌐 Área total</option>
                            <option value="filtrar_por_poblacion" <?php echo $operacion_seleccionada == 'filtrar_por_poblacion' ? 'selected' : ''; ?>>📈 Filtrar por población</option>
                            <option value="ordenar_por_poblacion" <?php echo $operacion_seleccionada == 'ordenar_por_poblacion' ? 'selected' : ''; ?>>📊 Ordenar por población</option>
                            <option value="ordenar_por_nombre" <?php echo $operacion_seleccionada == 'ordenar_por_nombre' ? 'selected' : ''; ?>>🔤 Ordenar por nombre</option>
                            <option value="array_slice" <?php echo $operacion_seleccionada == 'array_slice' ? 'selected' : ''; ?>>✂️ Array slice</option>
                            <option value="combinar_arrays" <?php echo $operacion_seleccionada == 'combinar_arrays' ? 'selected' : ''; ?>>🔄 Combinar arrays</option>
                            <option value="buscar_pais" <?php echo $operacion_seleccionada == 'buscar_pais' ? 'selected' : ''; ?>>📍 Buscar país</option>
                            <option value="estadisticas" <?php echo $operacion_seleccionada == 'estadisticas' ? 'selected' : ''; ?>>📈 Estadísticas</option>
                            <option value="estructura_completa" <?php echo $operacion_seleccionada == 'estructura_completa' ? 'selected' : ''; ?>>🏗️ Estructura completa</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="continente-group" style="<?php echo in_array($operacion_seleccionada, ['buscar_continente', 'paises_continente']) ? '' : 'display: none;' ?>">
                        <label for="continente">Selecciona un Continente:</label>
                        <select id="continente" name="continente">
                            <option value="">-- Elige un continente --</option>
                            <?php foreach(array_keys($continentes) as $continente): ?>
                                <option value="<?php echo $continente; ?>" <?php echo $continente_seleccionado == $continente ? 'selected' : ''; ?>>
                                    <?php echo $continente; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit">🚀 Ejecutar Operación</button>
                </form>
                
                <div class="info-box">
                    <h3>📚 Funciones de Arrays Incluidas:</h3>
                    <div class="operations-grid">
                        <div class="operation-card" onclick="selectOperation('mostrar_todos')">
                            <h4>foreach()</h4>
                            <p>Recorrer arrays</p>
                        </div>
                        <div class="operation-card" onclick="selectOperation('contar_elementos')">
                            <h4>count()</h4>
                            <p>Contar elementos</p>
                        </div>
                        <div class="operation-card" onclick="selectOperation('mostrar_claves')">
                            <h4>array_keys()</h4>
                            <p>Obtener claves</p>
                        </div>
                        <div class="operation-card" onclick="selectOperation('mostrar_valores')">
                            <h4>array_column()</h4>
                            <p>Extraer columnas</p>
                        </div>
                        <div class="operation-card" onclick="selectOperation('filtrar_por_poblacion')">
                            <h4>array_filter()</h4>
                            <p>Filtrar elementos</p>
                        </div>
                        <div class="operation-card" onclick="selectOperation('ordenar_por_poblacion')">
                            <h4>array_multisort()</h4>
                            <p>Ordenar arrays</p>
                        </div>
                        <div class="operation-card" onclick="selectOperation('array_slice')">
                            <h4>array_slice()</h4>
                            <p>Extraer porción</p>
                        </div>
                        <div class="operation-card" onclick="selectOperation('combinar_arrays')">
                            <h4>array_combine()</h4>
                            <p>Combinar arrays</p>
                        </div>
                        <div class="operation-card" onclick="selectOperation('buscar_pais')">
                            <h4>in_array()</h4>
                            <p>Buscar en array</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="result-section">
                <h2>📊 Resultado</h2>
                <?php if ($resultado): ?>
                    <div class="result-box">
                        <h3>Operación: <?php echo ucfirst(str_replace('_', ' ', $operacion_seleccionada)); ?></h3>
                        <?php echo $resultado; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: #7f8c8d;">
                        <p>👆 Selecciona una operación para ver los resultados</p>
                        <p><small>Explora las diferentes funciones de arrays con datos reales de continentes</small></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="info-section">
            <h2>🌍 Datos de los Continentes</h2>
            
            <div class="continent-stats">
                <?php 
                $total_poblacion = array_sum(array_column($continentes, 'poblacion'));
                $total_area = array_sum(array_column($continentes, 'area_km2'));
                $total_paises = array_sum(array_column($continentes, 'paises_count'));
                ?>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($continentes); ?></div>
                    <div class="stat-label">Continentes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_paises; ?></div>
                    <div class="stat-label">Países Totales</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($total_poblacion); ?></div>
                    <div class="stat-label">Habitantes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($total_area); ?> km²</div>
                    <div class="stat-label">Área Total</div>
                </div>
            </div>
            
            <div class="info-box">
                <h3>🎓 Tipos de Arrays Demostrados:</h3>
                <p><strong>Array Asociativo:</strong> Claves personalizadas (nombres de continentes)</p>
                <p><strong>Array Multidimensional:</strong> Arrays dentro de arrays con datos estructurados</p>
                <p><strong>Array Indexado:</strong> Listas de países e idiomas</p>
                <p><strong>Datos Mixtos:</strong> Strings, integers, booleans, arrays</p>
            </div>
        </div>
    </div>

    <script>
        // Mostrar/ocultar selector de continente según la operación
        document.getElementById('operacion').addEventListener('change', function() {
            const operacionesConContinente = ['buscar_continente', 'paises_continente'];
            const continenteGroup = document.getElementById('continente-group');
            
            if (operacionesConContinente.includes(this.value)) {
                continenteGroup.style.display = 'block';
            } else {
                continenteGroup.style.display = 'none';
            }
        });
        
        // Seleccionar operación desde las tarjetas
        function selectOperation(operation) {
            document.getElementById('operacion').value = operation;
            document.getElementById('operacion').dispatchEvent(new Event('change'));
        }
        
        // Efectos visuales para las tarjetas de operación
        document.querySelectorAll('.operation-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.operation-card').forEach(c => {
                    c.style.borderLeftColor = '#27ae60';
                });
                this.style.borderLeftColor = '#e74c3c';
            });
        });
    </script>
</body>
</html>