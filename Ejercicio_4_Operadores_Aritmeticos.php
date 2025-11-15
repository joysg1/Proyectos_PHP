<?php
// calculadora_interactiva.php

function calcular($num1, $num2, $operacion) {
    switch ($operacion) {
        case 'suma':
            return $num1 + $num2;
        case 'resta':
            return $num1 - $num2;
        case 'multiplicacion':
            return $num1 * $num2;
        case 'division':
            return ($num2 != 0) ? $num1 / $num2 : "Error: División por cero";
        case 'modulo':
            return ($num2 != 0) ? $num1 % $num2 : "Error: Módulo por cero";
        case 'potencia':
            return pow($num1, $num2);
        default:
            return "Operación no válida";
    }
}

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $numero1 = floatval($_POST['numero1']);
    $numero2 = floatval($_POST['numero2']);
    $operacion = $_POST['operacion'];
    
    $resultado = calcular($numero1, $numero2, $operacion);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Calculadora PHP</title>
    <style>
        .calculadora {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .resultado {
            background-color: #f0f0f0;
            padding: 10px;
            margin-top: 10px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="calculadora">
        <h2>Calculadora Interactiva</h2>
        <form method="POST">
            <input type="number" name="numero1" step="any" required 
                   value="<?php echo isset($numero1) ? $numero1 : ''; ?>" 
                   placeholder="Primer número">
            <br><br>
            
            <select name="operacion" required>
                <option value="suma" <?php echo (isset($operacion) && $operacion == 'suma') ? 'selected' : ''; ?>>Suma (+)</option>
                <option value="resta" <?php echo (isset($operacion) && $operacion == 'resta') ? 'selected' : ''; ?>>Resta (-)</option>
                <option value="multiplicacion" <?php echo (isset($operacion) && $operacion == 'multiplicacion') ? 'selected' : ''; ?>>Multiplicación (×)</option>
                <option value="division" <?php echo (isset($operacion) && $operacion == 'division') ? 'selected' : ''; ?>>División (÷)</option>
                <option value="modulo" <?php echo (isset($operacion) && $operacion == 'modulo') ? 'selected' : ''; ?>>Módulo (%)</option>
                <option value="potencia" <?php echo (isset($operacion) && $operacion == 'potencia') ? 'selected' : ''; ?>>Potencia (^)</option>
            </select>
            <br><br>
            
            <input type="number" name="numero2" step="any" required 
                   value="<?php echo isset($numero2) ? $numero2 : ''; ?>" 
                   placeholder="Segundo número">
            <br><br>
            
            <button type="submit">Calcular</button>
        </form>
        
        <?php if (isset($resultado)): ?>
            <div class="resultado">
                <strong>Resultado:</strong> <?php echo $resultado; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>