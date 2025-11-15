<?php
// calculadora_asignacion_interactiva.php

function aplicarOperadorAsignacion(&$variable, $operador, $valor) {
    switch ($operador) {
        case 'asignacion':
            $variable = $valor;
            break;
        case 'suma':
            $variable += $valor;
            break;
        case 'resta':
            $variable -= $valor;
            break;
        case 'multiplicacion':
            $variable *= $valor;
            break;
        case 'division':
            if ($valor != 0) {
                $variable /= $valor;
            } else {
                return "Error: División por cero";
            }
            break;
        case 'modulo':
            if ($valor != 0) {
                $variable %= $valor;
            } else {
                return "Error: Módulo por cero";
            }
            break;
        case 'concatenacion':
            $variable .= $valor;
            break;
        default:
            return "Operador no válido";
    }
    return $variable;
}

// Variables para mantener el estado
$variableActual = isset($_POST['variable_actual']) ? $_POST['variable_actual'] : '0';
$historial = isset($_POST['historial']) ? $_POST['historial'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['operador'])) {
    $operador = $_POST['operador'];
    $valor = $_POST['valor'];
    
    $resultado = aplicarOperadorAsignacion($variableActual, $operador, $valor);
    
    // Agregar al historial
    $operadoresSimbolo = [
        'asignacion' => '=',
        'suma' => '+=',
        'resta' => '-=',
        'multiplicacion' => '*=',
        'division' => '/=',
        'modulo' => '%=',
        'concatenacion' => '.='
    ];
    
    $historial .= "Variable " . $operadoresSimbolo[$operador] . " $valor → Resultado: $resultado<br>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Operadores de Asignación PHP</title>
    <style>
        .calculadora {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .estado {
            background-color: #e0e0e0;
            padding: 10px;
            margin: 10px 0;
            border-radius: 3px;
        }
        .historial {
            background-color: #f0f0f0;
            padding: 10px;
            margin-top: 10px;
            border-radius: 3px;
            max-height: 200px;
            overflow-y: auto;
        }
        button {
            margin: 5px;
            padding: 8px 15px;
        }
        input, select {
            padding: 5px;
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="calculadora">
        <h2>Simulador de Operadores de Asignación PHP</h2>
        
        <div class="estado">
            <strong>Variable actual:</strong> 
            <span id="variableActual"><?php echo $variableActual; ?></span>
        </div>
        
        <form method="POST">
            <input type="hidden" name="variable_actual" value="<?php echo $variableActual; ?>">
            <input type="hidden" name="historial" value="<?php echo $historial; ?>">
            
            <label>Operador:</label>
            <select name="operador" required>
                <option value="asignacion">Asignación (=)</option>
                <option value="suma">Suma (+=)</option>
                <option value="resta">Resta (-=)</option>
                <option value="multiplicacion">Multiplicación (*=)</option>
                <option value="division">División (/=)</option>
                <option value="modulo">Módulo (%=)</option>
                <option value="concatenacion">Concatenación (.=)</option>
            </select>
            <br>
            
            <label>Valor:</label>
            <input type="text" name="valor" required 
                   value="<?php echo isset($_POST['valor']) ? $_POST['valor'] : ''; ?>">
            <br><br>
            
            <button type="submit">Aplicar Operador</button>
            <button type="button" onclick="resetearCalculadora()">Reiniciar</button>
        </form>
        
        <?php if (isset($resultado)): ?>
            <div class="estado">
                <strong>Última operación:</strong> <?php echo $resultado; ?>
            </div>
        <?php endif; ?>
        
        <div class="historial">
            <strong>Historial de operaciones:</strong><br>
            <?php echo $historial ?: 'No hay operaciones registradas'; ?>
        </div>
    </div>

    <script>
        function resetearCalculadora() {
            if (confirm('¿Estás seguro de que quieres reiniciar la calculadora?')) {
                window.location.href = window.location.href.split('?')[0];
            }
        }
    </script>
</body>
</html>
