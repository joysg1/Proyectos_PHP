<?php
// ==================== OPERACIONES CON STRINGS ====================
function procesarString($texto, $operacion) {
    $resultado = '';
    
    switch($operacion) {
        case 'longitud':
            $resultado = "Longitud: " . strlen($texto) . " caracteres";
            break;
            
        case 'mayusculas':
            $resultado = strtoupper($texto);
            break;
            
        case 'minusculas':
            $resultado = strtolower($texto);
            break;
            
        case 'primera_mayuscula':
            $resultado = ucfirst($texto);
            break;
            
        case 'palabras_mayusculas':
            $resultado = ucwords($texto);
            break;
            
        case 'invertir':
            $resultado = strrev($texto);
            break;
            
        case 'eliminar_espacios':
            $resultado = trim($texto);
            break;
            
        case 'reemplazar':
            $resultado = str_replace(' ', '_', $texto);
            break;
            
        case 'palabras_array':
            $palabras = explode(' ', $texto);
            $resultado = "Array: " . implode(' | ', $palabras);
            break;
            
        case 'substring':
            $resultado = substr($texto, 0, 20) . (strlen($texto) > 20 ? '...' : '');
            break;
            
        case 'contar_palabras':
            $resultado = "Palabras: " . str_word_count($texto);
            break;
            
        case 'repetir':
            $resultado = str_repeat($texto . " ", 3);
            break;
            
        case 'shuffle':
            $resultado = str_shuffle($texto);
            break;
            
        case 'posicion':
            $buscar = 'a';
            $pos = strpos($texto, $buscar);
            $resultado = $pos !== false ? "Posición de '$buscar': $pos" : "'$buscar' no encontrada";
            break;
            
        case 'comparar':
            $comparar = "Hola Mundo";
            $resultado = strcmp($texto, $comparar) == 0 ? "Igual a '$comparar'" : "Diferente de '$comparar'";
            break;
            
        case 'htmlspecialchars':
            $resultado = htmlspecialchars($texto);
            break;
            
        case 'md5':
            $resultado = md5($texto);
            break;
            
        case 'sha1':
            $resultado = sha1($texto);
            break;
            
        case 'base64':
            $resultado = base64_encode($texto);
            break;
            
        case 'url_encode':
            $resultado = urlencode($texto);
            break;
            
        case 'sprintf':
            $resultado = sprintf("Texto formateado: '%s' tiene %d caracteres", $texto, strlen($texto));
            break;
            
        case 'nl2br':
            $resultado = nl2br($texto);
            break;
            
        case 'wordwrap':
            $resultado = wordwrap($texto, 20, "<br>\n", true);
            break;
            
        default:
            $resultado = "Operación no reconocida";
    }
    
    return $resultado;
}

// ==================== TIPOS DE STRINGS ====================
function mostrarTiposStrings() {
    $tipos = [
        'String simple' => "Hola Mundo",
        'String con comillas simples' => 'Texto con comillas simples',
        'String con comillas dobles' => "Texto con \"comillas\" dobles",
        'String multilínea' => "Línea 1\nLínea 2\nLínea 3",
        'String con tabulaciones' => "Col1\tCol2\tCol3",
        'String con variables' => "\$variable = 'valor'",
        'String numérico' => "12345",
        'String vacío' => "",
        'String con espacios' => "   texto con espacios   ",
        'String con caracteres especiales' => "ñáéíóú@#\$%&",
        'String HTML' => "<div class='test'>Texto HTML</div>",
        'String JSON' => '{"nombre": "Juan", "edad": 30}',
        'String SQL' => "SELECT * FROM usuarios WHERE id = 1",
        'String URL' => "https://www.ejemplo.com/ruta?param=valor",
        'String con emojis' => "😀 🎉 ❤️ ✨",
        'String hexadecimal' => "\x48\x6f\x6c\x61", // Hola en hex
        'String unicode' => "\u{1F600}", // 😀
        'Heredoc' => <<<EOT
Esto es un string HEREDOC
Puede contener múltiples líneas
y variables: \$variable
EOT,
        'Nowdoc' => <<<'EOT'
Esto es un string NOWDOC
No interpreta variables: \$variable
Mantiene el formato exacto
EOT
    ];
    
    return $tipos;
}

// ==================== PROCESAMIENTO DEL FORMULARIO ====================
$resultado = '';
$texto_original = '';
$operacion_seleccionada = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $texto_original = $_POST['texto'] ?? '';
    $operacion_seleccionada = $_POST['operacion'] ?? '';
    
    if (!empty($texto_original) && !empty($operacion_seleccionada)) {
        $resultado = procesarString($texto_original, $operacion_seleccionada);
    } else {
        $resultado = "❌ Por favor, ingresa un texto y selecciona una operación";
    }
}

$tipos_strings = mostrarTiposStrings();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operaciones con Strings en PHP</title>
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
            max-width: 1200px;
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
        }
        .form-section {
            padding: 30px;
            background: #f8f9fa;
        }
        .result-section {
            padding: 30px;
            background: white;
        }
        .types-section {
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
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        textarea {
            height: 120px;
            resize: vertical;
        }
        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
        }
        .result-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid #667eea;
            margin-top: 20px;
        }
        .operation-card {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .type-item {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border-left: 4px solid #27ae60;
        }
        .code {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 10px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            margin: 5px 0;
            white-space: pre-wrap;
            word-break: break-all;
        }
        .info-box {
            background: #e8f4fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .operation-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }
        .operation-option {
            background: #3498db;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            transition: background 0.3s;
        }
        .operation-option:hover {
            background: #2980b9;
        }
        .operation-option.selected {
            background: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔤 Operaciones con Strings en PHP</h1>
            <p>Ejemplos prácticos de manipulación de cadenas de texto</p>
        </div>
        
        <div class="content">
            <div class="form-section">
                <h2>🧪 Probar Operaciones</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="texto">Texto de Entrada:</label>
                        <textarea id="texto" name="texto" placeholder="Ingresa el texto que quieres procesar..." required><?php echo htmlspecialchars($texto_original); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="operacion">Operación a Realizar:</label>
                        <select id="operacion" name="operacion" required>
                            <option value="">-- Selecciona una operación --</option>
                            <option value="longitud" <?php echo $operacion_seleccionada == 'longitud' ? 'selected' : ''; ?>>📏 Longitud del string</option>
                            <option value="mayusculas" <?php echo $operacion_seleccionada == 'mayusculas' ? 'selected' : ''; ?>>🔠 Convertir a MAYÚSCULAS</option>
                            <option value="minusculas" <?php echo $operacion_seleccionada == 'minusculas' ? 'selected' : ''; ?>>🔡 Convertir a minúsculas</option>
                            <option value="primera_mayuscula" <?php echo $operacion_seleccionada == 'primera_mayuscula' ? 'selected' : ''; ?>>🏴 Primera letra mayúscula</option>
                            <option value="palabras_mayusculas" <?php echo $operacion_seleccionada == 'palabras_mayusculas' ? 'selected' : ''; ?>>📝 Cada palabra con mayúscula</option>
                            <option value="invertir" <?php echo $operacion_seleccionada == 'invertir' ? 'selected' : ''; ?>>🔄 Invertir string</option>
                            <option value="eliminar_espacios" <?php echo $operacion_seleccionada == 'eliminar_espacios' ? 'selected' : ''; ?>>✂️ Eliminar espacios</option>
                            <option value="reemplazar" <?php echo $operacion_seleccionada == 'reemplazar' ? 'selected' : ''; ?>>🔄 Reemplazar espacios por _</option>
                            <option value="palabras_array" <?php echo $operacion_seleccionada == 'palabras_array' ? 'selected' : ''; ?>>📋 Convertir a array de palabras</option>
                            <option value="substring" <?php echo $operacion_seleccionada == 'substring' ? 'selected' : ''; ?>>✂️ Substring (primeros 20 chars)</option>
                            <option value="contar_palabras" <?php echo $operacion_seleccionada == 'contar_palabras' ? 'selected' : ''; ?>>🔢 Contar palabras</option>
                            <option value="repetir" <?php echo $operacion_seleccionada == 'repetir' ? 'selected' : ''; ?>>🔄 Repetir 3 veces</option>
                            <option value="shuffle" <?php echo $operacion_seleccionada == 'shuffle' ? 'selected' : ''; ?>>🎲 Mezclar caracteres</option>
                            <option value="posicion" <?php echo $operacion_seleccionada == 'posicion' ? 'selected' : ''; ?>>📍 Posición de la letra 'a'</option>
                            <option value="comparar" <?php echo $operacion_seleccionada == 'comparar' ? 'selected' : ''; ?>>⚖️ Comparar con "Hola Mundo"</option>
                            <option value="htmlspecialchars" <?php echo $operacion_seleccionada == 'htmlspecialchars' ? 'selected' : ''; ?>>🛡️ HTML Special Chars</option>
                            <option value="md5" <?php echo $operacion_seleccionada == 'md5' ? 'selected' : ''; ?>>🔐 Hash MD5</option>
                            <option value="sha1" <?php echo $operacion_seleccionada == 'sha1' ? 'selected' : ''; ?>>🔐 Hash SHA1</option>
                            <option value="base64" <?php echo $operacion_seleccionada == 'base64' ? 'selected' : ''; ?>>🔒 Codificación Base64</option>
                            <option value="url_encode" <?php echo $operacion_seleccionada == 'url_encode' ? 'selected' : ''; ?>>🌐 URL Encode</option>
                            <option value="sprintf" <?php echo $operacion_seleccionada == 'sprintf' ? 'selected' : ''; ?>>📝 Formatear con sprintf</option>
                            <option value="nl2br" <?php echo $operacion_seleccionada == 'nl2br' ? 'selected' : ''; ?>>↩️ Convertir saltos de línea</option>
                            <option value="wordwrap" <?php echo $operacion_seleccionada == 'wordwrap' ? 'selected' : ''; ?>>📐 Wordwrap (20 chars)</option>
                        </select>
                    </div>
                    
                    <button type="submit">🚀 Ejecutar Operación</button>
                </form>
                
                <div class="info-box">
                    <h3>💡 Funciones de Strings Incluidas:</h3>
                    <div class="operation-grid">
                        <div class="operation-option">strlen()</div>
                        <div class="operation-option">strtoupper()</div>
                        <div class="operation-option">strtolower()</div>
                        <div class="operation-option">ucfirst()</div>
                        <div class="operation-option">ucwords()</div>
                        <div class="operation-option">strrev()</div>
                        <div class="operation-option">trim()</div>
                        <div class="operation-option">str_replace()</div>
                        <div class="operation-option">explode()</div>
                        <div class="operation-option">substr()</div>
                        <div class="operation-option">str_word_count()</div>
                        <div class="operation-option">str_repeat()</div>
                        <div class="operation-option">str_shuffle()</div>
                        <div class="operation-option">strpos()</div>
                        <div class="operation-option">strcmp()</div>
                        <div class="operation-option">htmlspecialchars()</div>
                        <div class="operation-option">md5()</div>
                        <div class="operation-option">sha1()</div>
                        <div class="operation-option">base64_encode()</div>
                        <div class="operation-option">urlencode()</div>
                        <div class="operation-option">sprintf()</div>
                        <div class="operation-option">nl2br()</div>
                        <div class="operation-option">wordwrap()</div>
                    </div>
                </div>
            </div>
            
            <div class="result-section">
                <h2>📊 Resultado</h2>
                <?php if ($resultado): ?>
                    <div class="result-box">
                        <h3>Operación: <?php echo ucfirst(str_replace('_', ' ', $operacion_seleccionada)); ?></h3>
                        <p><strong>Texto original:</strong></p>
                        <div class="code"><?php echo htmlspecialchars($texto_original); ?></div>
                        
                        <p><strong>Resultado:</strong></p>
                        <div class="code"><?php echo $resultado; ?></div>
                        
                        <p><strong>Información:</strong></p>
                        <div class="code">
Longitud original: <?php echo strlen($texto_original); ?> caracteres
Palabras original: <?php echo str_word_count($texto_original); ?> palabras
Tipo de dato: <?php echo gettype($resultado); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: #7f8c8d;">
                        <p>👆 Ingresa un texto y selecciona una operación para ver los resultados</p>
                        <p><small>Prueba con: "Hola Mundo, este es un texto de ejemplo para probar las operaciones con strings en PHP."</small></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="types-section">
                <h2>📚 Tipos de Strings en PHP</h2>
                <p>Ejemplos de diferentes formas de definir strings en PHP:</p>
                
                <?php foreach ($tipos_strings as $tipo => $ejemplo): ?>
                    <div class="type-item">
                        <h4>🔹 <?php echo $tipo; ?></h4>
                        <div class="code"><?php echo htmlspecialchars($ejemplo); ?></div>
                        <p><small>Longitud: <?php echo strlen($ejemplo); ?> caracteres | Tipo: <?php echo gettype($ejemplo); ?></small></p>
                    </div>
                <?php endforeach; ?>
                
                <div class="info-box">
                    <h3>🎓 Conceptos Importantes:</h3>
                    <p><strong>Comillas simples vs dobles:</strong> Las comillas dobles interpretan variables y caracteres especiales (\n, \t), las simples no.</p>
                    <p><strong>HEREDOC vs NOWDOC:</strong> HEREDOC interpreta variables, NOWDOC las trata como texto literal.</p>
                    <p><strong>Codificación:</strong> PHP soporta UTF-8 para caracteres especiales y emojis.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Agregar interactividad a las opciones de operación
        document.querySelectorAll('.operation-option').forEach(option => {
            option.addEventListener('click', function() {
                const operationName = this.textContent.replace('()', '');
                const select = document.getElementById('operacion');
                
                // Mapear nombres de funciones a valores del select
                const operationMap = {
                    'strlen': 'longitud',
                    'strtoupper': 'mayusculas',
                    'strtolower': 'minusculas',
                    'ucfirst': 'primera_mayuscula',
                    'ucwords': 'palabras_mayusculas',
                    'strrev': 'invertir',
                    'trim': 'eliminar_espacios',
                    'str_replace': 'reemplazar',
                    'explode': 'palabras_array',
                    'substr': 'substring',
                    'str_word_count': 'contar_palabras',
                    'str_repeat': 'repetir',
                    'str_shuffle': 'shuffle',
                    'strpos': 'posicion',
                    'strcmp': 'comparar',
                    'htmlspecialchars': 'htmlspecialchars',
                    'md5': 'md5',
                    'sha1': 'sha1',
                    'base64_encode': 'base64',
                    'urlencode': 'url_encode',
                    'sprintf': 'sprintf',
                    'nl2br': 'nl2br',
                    'wordwrap': 'wordwrap'
                };
                
                if (operationMap[operationName]) {
                    select.value = operationMap[operationName];
                    
                    // Efecto visual de selección
                    document.querySelectorAll('.operation-option').forEach(opt => {
                        opt.classList.remove('selected');
                    });
                    this.classList.add('selected');
                }
            });
        });
    </script>
</body>
</html>
