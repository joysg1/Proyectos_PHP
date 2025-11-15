<?php
// fruteria_interactiva.php

function procesarSeleccionFruta($frutaSeleccionada, $cantidad, $accion) {
    echo "<div style='background-color: #e8f5e8; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>Resultado de tu selección:</h3>";
    
    // if simple para verificar fruta seleccionada
    if ($frutaSeleccionada == "manzana") {
        echo "🍎 Has seleccionado MANZANAS<br>";
    }
    
    if ($frutaSeleccionada == "pera") {
        echo "🍐 Has seleccionado PERAS<br>";
    }
    
    if ($frutaSeleccionada == "ambas") {
        echo "🍎🍐 Has seleccionado AMBAS FRUTAS<br>";
    }
    
    // if simple para verificar cantidad
    if ($cantidad > 0) {
        echo "📦 Cantidad: $cantidad unidades<br>";
    }
    
    if ($cantidad == 0) {
        echo "❌ No seleccionaste cantidad<br>";
    }
    
    if ($cantidad > 10) {
        echo "🎉 ¡Gran compra! Tienes descuento por volumen<br>";
    }
    
    // if simple para verificar acción
    if ($accion == "comprar") {
        echo "🛒 Acción: COMPRAR frutas<br>";
    }
    
    if ($accion == "consultar") {
        echo "ℹ️ Acción: CONSULTAR disponibilidad<br>";
    }
    
    if ($accion == "reservar") {
        echo "📅 Acción: RESERVAR frutas<br>";
    }
    
    // Mensaje final basado en las selecciones
    if ($frutaSeleccionada != "ninguna" && $cantidad > 0 && $accion == "comprar") {
        echo "<br><strong>✅ ¡Pedido procesado correctamente!</strong><br>";
    }
    
    if ($frutaSeleccionada == "ninguna") {
        echo "<br>⚠️ Por favor selecciona una fruta<br>";
    }
    
    echo "</div>";
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fruta = $_POST['fruta'];
    $cantidad = intval($_POST['cantidad']);
    $accion = $_POST['accion'];
    
    procesarSeleccionFruta($fruta, $cantidad, $accion);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fruteria Interactiva - IF Simple</title>
    <style>
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 2px solid #4CAF50;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        .fruteria-header {
            text-align: center;
            color: #2E7D32;
            background-color: #E8F5E9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .form-group {
            margin: 15px 0;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        select, input, button {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        .ejemplo {
            background-color: #FFF3E0;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="fruteria-header">
            <h1>🍎 Frutería "IF Simple" 🍐</h1>
            <p>Selecciona tus frutas y observa cómo funcionan los IF simples</p>
        </div>
        
        <div class="ejemplo">
            <h3>💡 ¿Qué es un IF simple?</h3>
            <p>Un <strong>if simple</strong> ejecuta un bloque de código solo si la condición es verdadera.</p>
            <code>if (condición) { // código a ejecutar }</code>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="fruta">Selecciona una fruta:</label>
                <select name="fruta" id="fruta" required>
                    <option value="ninguna">-- Selecciona --</option>
                    <option value="manzana">🍎 Manzana</option>
                    <option value="pera">🍐 Pera</option>
                    <option value="ambas">🍎🍐 Ambas frutas</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="cantidad">Cantidad:</label>
                <input type="number" name="cantidad" id="cantidad" 
                       min="0" max="50" value="0" required>
            </div>
            
            <div class="form-group">
                <label for="accion">¿Qué quieres hacer?</label>
                <select name="accion" id="accion" required>
                    <option value="consultar">ℹ️ Consultar disponibilidad</option>
                    <option value="reservar">📅 Reservar</option>
                    <option value="comprar">🛒 Comprar</option>
                </select>
            </div>
            
            <button type="submit">Procesar Selección</button>
        </form>
        
        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
        <div style="margin-top: 20px; padding: 15px; background-color: #E3F2FD; border-radius: 5px;">
            <h3>🔍 Análisis de los IF simples usados:</h3>
            <ul>
                <li><code>if ($fruta == "manzana")</code> - Verifica si seleccionaste manzanas</li>
                <li><code>if ($fruta == "pera")</code> - Verifica si seleccionaste peras</li>
                <li><code>if ($cantidad > 0)</code> - Verifica si la cantidad es mayor a cero</li>
                <li><code>if ($cantidad > 10)</code> - Verifica compra grande para descuento</li>
                <li><code>if ($accion == "comprar")</code> - Verifica la acción a realizar</li>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>