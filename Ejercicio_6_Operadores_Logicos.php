<?php
// calculadora_logica_interactiva.php

function evaluarExpresionLogica($expresion, $valores) {
    // Extraer variables de la expresión
    preg_match_all('/[a-zA-Z_]\w*/', $expresion, $variables);
    $variables = array_unique($variables[0]);
    
    // Reemplazar variables con sus valores
    foreach ($variables as $variable) {
        if (isset($valores[$variable])) {
            $valor = $valores[$variable] ? 'true' : 'false';
            $expresion = str_replace($variable, $valor, $expresion);
        }
    }
    
    // Reemplazar operadores para evaluación segura
    $expresion = str_replace(['&&', '||', '!'], ['and', 'or', 'not'], $expresion);
    $expresion = str_replace(['true', 'false'], ['true', 'false'], $expresion);
    
    // Evaluar la expresión
    try {
        $resultado = eval("return $expresion;");
        return $resultado ? 'true' : 'false';
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
}

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $valores = [];
    
    // Recoger valores de las variables
    for ($i = 1; $i <= 3; $i++) {
        if (isset($_POST["var{$i}_nombre"]) && $_POST["var{$i}_nombre"] != '') {
            $nombre = $_POST["var{$i}_nombre"];
            $valor = $_POST["var{$i}_valor"] == 'true';
            $valores[$nombre] = $valor;
        }
    }
    
    $expresion = $_POST['expresion'];
    $resultado = evaluarExpresionLogica($expresion, $valores);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Calculadora Lógica PHP</title>
    <style>
        .calculadora {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .variable {
            background-color: #e8f4f8;
            padding: 10px;
            margin: 10px 0;
            border-radius: 3px;
        }
        .resultado {
            background-color: #d4edda;
            padding: 15px;
            margin: 15px 0;
            border-radius: 3px;
            font-weight: bold;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            margin: 15px 0;
            border-radius: 3px;
        }
        .ejemplos {
            background-color: #fff3cd;
            padding: 15px;
            margin: 15px 0;
            border-radius: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        input, select, button {
            padding: 8px;
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="calculadora">
        <h1>Calculadora de Operadores Lógicos</h1>
        
        <div class="ejemplos">
            <h3>Ejemplos de expresiones:</h3>
            <ul>
                <li><code>A && B</code> - AND lógico</li>
                <li><code>A || B</code> - OR lógico</li>
                <li><code>!A</code> - NOT lógico</li>
                <li><code>A && (B || C)</code> - Expresión compleja</li>
                <li><code>!(A && B)</code> - Negación de AND</li>
            </ul>
        </div>
        
        <form method="POST">
            <h3>Definir Variables:</h3>
            
            <?php for ($i = 1; $i <= 3; $i++): ?>
            <div class="variable">
                <strong>Variable <?php echo $i; ?>:</strong>
                <input type="text" name="var<?php echo $i; ?>_nombre" 
                       placeholder="Nombre (ej: A)" 
                       value="<?php echo isset($_POST["var{$i}_nombre"]) ? $_POST["var{$i}_nombre"] : ''; ?>">
                <select name="var<?php echo $i; ?>_valor">
                    <option value="true" <?php echo (isset($_POST["var{$i}_valor"]) && $_POST["var{$i}_valor"] == 'true') ? 'selected' : ''; ?>>true</option>
                    <option value="false" <?php echo (isset($_POST["var{$i}_valor"]) && $_POST["var{$i}_valor"] == 'false') ? 'selected' : ''; ?>>false</option>
                </select>
            </div>
            <?php endfor; ?>
            
            <h3>Expresión Lógica:</h3>
            <input type="text" name="expresion" required 
                   placeholder="Ej: A && B || !C" 
                   style="width: 300px;"
                   value="<?php echo isset($_POST['expresion']) ? $_POST['expresion'] : ''; ?>">
            <br><br>
            
            <button type="submit">Evaluar Expresión</button>
            <button type="button" onclick="limpiarFormulario()">Limpiar</button>
        </form>
        
        <?php if (isset($resultado)): ?>
            <div class="<?php echo strpos($resultado, 'Error') === false ? 'resultado' : 'error'; ?>">
                <h3>Resultado:</h3>
                <strong>Expresión:</strong> <?php echo htmlspecialchars($expresion); ?><br>
                <strong>Variables:</strong> 
                <?php 
                foreach ($valores as $nombre => $valor) {
                    echo "$nombre = " . ($valor ? 'true' : 'false') . " ";
                }
                ?><br>
                <strong>Resultado:</strong> <?php echo $resultado; ?>
            </div>
        <?php endif; ?>
        
        <!-- Tablas de verdad de referencia -->
        <div style="margin-top: 30px;">
            <h3>Tablas de Verdad de Referencia</h3>
            
            <table>
                <caption>AND (&&)</caption>
                <tr><th>A</th><th>B</th><th>A && B</th></tr>
                <tr><td>false</td><td>false</td><td>false</td></tr>
                <tr><td>false</td><td>true</td><td>false</td></tr>
                <tr><td>true</td><td>false</td><td>false</td></tr>
                <tr><td>true</td><td>true</td><td>true</td></tr>
            </table>
            
            <table>
                <caption>OR (||)</caption>
                <tr><th>A</th><th>B</th><th>A || B</th></tr>
                <tr><td>false</td><td>false</td><td>false</td></tr>
                <tr><td>false</td><td>true</td><td>true</td></tr>
                <tr><td>true</td><td>false</td><td>true</td></tr>
                <tr><td>true</td><td>true</td><td>true</td></tr>
            </table>
            
            <table>
                <caption>XOR</caption>
                <tr><th>A</th><th>B</th><th>A XOR B</th></tr>
                <tr><td>false</td><td>false</td><td>false</td></tr>
                <tr><td>false</td><td>true</td><td>true</td></tr>
                <tr><td>true</td><td>false</td><td>true</td></tr>
                <tr><td>true</td><td>true</td><td>false</td></tr>
            </table>
            
            <table>
                <caption>NOT (!)</caption>
                <tr><th>A</th><th>!A</th></tr>
                <tr><td>false</td><td>true</td></tr>
                <tr><td>true</td><td>false</td></tr>
            </table>
        </div>
    </div>

    <script>
        function limpiarFormulario() {
            if (confirm('¿Estás seguro de que quieres limpiar el formulario?')) {
                document.querySelector('form').reset();
            }
        }
        
        // Ejemplos rápidos
        function insertarEjemplo(expresion) {
            document.querySelector('input[name="expresion"]').value = expresion;
        }
    </script>
</body>
</html>