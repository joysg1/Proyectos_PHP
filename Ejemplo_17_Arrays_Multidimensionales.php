<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arrays Multidimensionales - Ejemplo Interactivo</title>
    <style>
        :root {
            --primary: #ec4899;
            --primary-dark: #db2777;
            --secondary: #8b5cf6;
            --accent: #06d6a0;
            --dark-bg: #0f172a;
            --dark-surface: #1e293b;
            --dark-card: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --border-color: #475569;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, var(--dark-bg) 0%, #1e1b4b 100%);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 2rem 0;
        }

        .header h1 {
            font-size: 2.5rem;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        .card {
            background: var(--dark-surface);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            color: var(--primary);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-secondary);
        }

        input, select {
            background: var(--dark-card);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 12px 16px;
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.2);
        }

        .btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 10px;
            padding: 14px 28px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(236, 72, 153, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .result-section {
            margin-top: 2rem;
        }

        .array-visualization {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .array-container {
            background: var(--dark-card);
            border-radius: 12px;
            padding: 1.5rem;
            border: 2px solid var(--primary);
        }

        .array-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .array-table th {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 0.75rem;
            text-align: center;
            font-weight: 600;
        }

        .array-table td {
            padding: 0.75rem;
            text-align: center;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .array-table tr:hover td {
            background-color: rgba(236, 72, 153, 0.1);
            transform: scale(1.02);
        }

        .index-cell {
            background: rgba(139, 92, 246, 0.2) !important;
            font-weight: bold;
            color: var(--secondary);
        }

        .nested-array {
            background: rgba(6, 214, 160, 0.1);
            border: 1px solid var(--accent);
            border-radius: 8px;
            padding: 0.5rem;
            margin: 0.25rem;
        }

        .code-preview {
            background: #1a1f36;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1rem;
            overflow-x: auto;
            border-left: 4px solid var(--primary);
        }

        .code-preview pre {
            color: var(--text-primary);
            font-family: 'Consolas', 'Monaco', monospace;
            line-height: 1.5;
            font-size: 0.9rem;
        }

        .operation-results {
            background: var(--dark-card);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1rem;
        }

        .result-item {
            padding: 1rem;
            margin-bottom: 1rem;
            background: rgba(236, 72, 153, 0.1);
            border-radius: 8px;
            border-left: 4px solid var(--primary);
        }

        .dimension-visual {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin: 1.5rem 0;
            flex-wrap: wrap;
        }

        .dimension-level {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .dimension-box {
            width: 80px;
            height: 80px;
            border: 2px solid var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(236, 72, 153, 0.1);
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .dimension-box:hover {
            transform: scale(1.1);
            background: rgba(236, 72, 153, 0.2);
        }

        .dimension-arrow {
            color: var(--accent);
            font-size: 1.5rem;
            font-weight: bold;
        }

        .array-structure {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            justify-content: center;
            margin: 1rem 0;
        }

        .structure-element {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            animation: elementPop 0.5s ease-out;
        }

        @keyframes elementPop {
            0% { transform: scale(0); opacity: 0; }
            70% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }

        .explanation {
            background: linear-gradient(135deg, var(--dark-surface), var(--dark-card));
            border-radius: 16px;
            padding: 2rem;
            margin-top: 2rem;
        }

        .feature-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 1rem;
            background: rgba(236, 72, 153, 0.1);
            border-radius: 10px;
            border-left: 4px solid var(--primary);
        }

        .feature-icon {
            color: var(--primary);
            font-weight: bold;
        }

        .syntax-example {
            background: var(--dark-card);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
            border: 1px solid var(--border-color);
        }

        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .card {
                padding: 1.5rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .array-visualization {
                grid-template-columns: 1fr;
            }
            
            .dimension-visual {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .header h1 {
                font-size: 1.75rem;
            }
            
            .card {
                padding: 1rem;
            }
            
            .array-table {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>📊 Arrays Multidimensionales</h1>
            <p>Explora y visualiza arrays complejos de múltiples dimensiones de forma interactiva</p>
        </header>

        <div class="card fade-in">
            <h2 class="card-title">
                <i>⚙️</i> Configuración del Array
            </h2>
            <form method="POST" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="tipo_array">🎯 Tipo de Array Multidimensional</label>
                        <select name="tipo_array" id="tipo_array">
                            <option value="matriz_bidimensional" <?php echo (isset($_POST['tipo_array']) && $_POST['tipo_array'] == 'matriz_bidimensional') ? 'selected' : 'selected'; ?>>
                                📈 Matriz Bidimensional (2D)
                            </option>
                            <option value="array_tridimensional" <?php echo (isset($_POST['tipo_array']) && $_POST['tipo_array'] == 'array_tridimensional') ? 'selected' : ''; ?>>
                                🧊 Array Tridimensional (3D)
                            </option>
                            <option value="array_asociativo" <?php echo (isset($_POST['tipo_array']) && $_POST['tipo_array'] == 'array_asociativo') ? 'selected' : ''; ?>>
                                🔑 Array Asociativo Multidimensional
                            </option>
                            <option value="array_irregular" <?php echo (isset($_POST['tipo_array']) && $_POST['tipo_array'] == 'array_irregular') ? 'selected' : ''; ?>>
                                🎭 Array Irregular (Jagged Array)
                            </option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="filas">📏 Filas (2-8):</label>
                        <input type="number" id="filas" name="filas" min="2" max="8" 
                               value="<?php echo isset($_POST['filas']) ? $_POST['filas'] : 4; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="columnas">📐 Columnas (2-8):</label>
                        <input type="number" id="columnas" name="columnas" min="2" max="8" 
                               value="<?php echo isset($_POST['columnas']) ? $_POST['columnas'] : 4; ?>" required>
                    </div>
                </div>
                
                <button type="submit" name="generar" class="btn">
                    <i>🚀</i> Generar Array
                </button>
            </form>
        </div>

        <?php
        function generarMatrizBidimensional($filas, $columnas) {
            $matriz = [];
            $contador = 1;
            
            for ($i = 0; $i < $filas; $i++) {
                for ($j = 0; $j < $columnas; $j++) {
                    $matriz[$i][$j] = [
                        'valor' => $contador++,
                        'fila' => $i,
                        'columna' => $j
                    ];
                }
            }
            
            return $matriz;
        }

        function generarArrayTridimensional($filas, $columnas) {
            $array = [];
            $contador = 1;
            $profundidad = 3; // Fixed depth for 3D
            
            for ($i = 0; $i < $filas; $i++) {
                for ($j = 0; $j < $columnas; $j++) {
                    for ($k = 0; $k < $profundidad; $k++) {
                        $array[$i][$j][$k] = [
                            'valor' => $contador++,
                            'coords' => "($i,$j,$k)"
                        ];
                    }
                }
            }
            
            return $array;
        }

        function generarArrayAsociativo($filas, $columnas) {
            $array = [];
            $departamentos = ['Ventas', 'TI', 'Marketing', 'RH', 'Finanzas', 'Operaciones'];
            $nombres = ['Ana', 'Carlos', 'Maria', 'Luis', 'Elena', 'Pedro', 'Sofia', 'Javier'];
            
            for ($i = 0; $i < $filas; $i++) {
                $depto = $departamentos[$i % count($departamentos)];
                $array[$depto] = [];
                
                for ($j = 0; $j < $columnas; $j++) {
                    $array[$depto][$j] = [
                        'nombre' => $nombres[($i + $j) % count($nombres)],
                        'salario' => rand(30000, 80000),
                        'antiguedad' => rand(1, 15)
                    ];
                }
            }
            
            return $array;
        }

        function generarArrayIrregular($filas, $columnas) {
            $array = [];
            $contador = 1;
            
            for ($i = 0; $i < $filas; $i++) {
                $elementosFila = rand(1, $columnas); // Different number of columns per row
                $array[$i] = [];
                
                for ($j = 0; $j < $elementosFila; $j++) {
                    $array[$i][$j] = [
                        'valor' => $contador++,
                        'longitud_fila' => $elementosFila
                    ];
                }
            }
            
            return $array;
        }

        if (isset($_POST['generar'])) {
            $tipo_array = $_POST['tipo_array'];
            $filas = intval($_POST['filas']);
            $columnas = intval($_POST['columnas']);
            
            echo '<div class="result-section fade-in">';
            
            // Generar array según el tipo seleccionado
            switch ($tipo_array) {
                case 'matriz_bidimensional':
                    $array = generarMatrizBidimensional($filas, $columnas);
                    $titulo = "Matriz Bidimensional {$filas}×{$columnas}";
                    break;
                    
                case 'array_tridimensional':
                    $array = generarArrayTridimensional($filas, $columnas);
                    $titulo = "Array Tridimensional {$filas}×{$columnas}×3";
                    break;
                    
                case 'array_asociativo':
                    $array = generarArrayAsociativo($filas, $columnas);
                    $titulo = "Array Asociativo Multidimensional";
                    break;
                    
                case 'array_irregular':
                    $array = generarArrayIrregular($filas, $columnas);
                    $titulo = "Array Irregular (Jagged Array)";
                    break;
            }
            
            echo '<div class="card">';
            echo '<h2 class="card-title"><i>📊</i> ' . $titulo . '</h2>';
            
            // Visualización del array
            echo '<div class="array-visualization">';
            
            // Tabla principal
            echo '<div class="array-container">';
            echo '<h3 style="color: var(--primary); margin-bottom: 1rem;">🎯 Visualización Tabular</h3>';
            
            if ($tipo_array == 'matriz_bidimensional') {
                echo '<table class="array-table">';
                // Encabezado de columnas
                echo '<tr><th class="index-cell">#</th>';
                for ($j = 0; $j < $columnas; $j++) {
                    echo '<th class="index-cell">Col ' . $j . '</th>';
                }
                echo '</tr>';
                
                // Filas de datos
                foreach ($array as $i => $fila) {
                    echo '<tr>';
                    echo '<td class="index-cell">Fila ' . $i . '</td>';
                    foreach ($fila as $elemento) {
                        echo '<td style="background: rgba(236, 72, 153, 0.2);">' . $elemento['valor'] . '</td>';
                    }
                    echo '</tr>';
                }
                echo '</table>';
                
            } elseif ($tipo_array == 'array_asociativo') {
                echo '<div style="overflow-x: auto;">';
                foreach ($array as $depto => $empleados) {
                    echo '<div style="margin-bottom: 2rem;">';
                    echo '<h4 style="color: var(--secondary); background: rgba(139, 92, 246, 0.2); padding: 0.5rem; border-radius: 8px;">🏢 ' . $depto . '</h4>';
                    echo '<table class="array-table">';
                    echo '<tr><th>Nombre</th><th>Salario</th><th>Antigüedad</th></tr>';
                    foreach ($empleados as $empleado) {
                        echo '<tr>';
                        echo '<td>' . $empleado['nombre'] . '</td>';
                        echo '<td>$' . number_format($empleado['salario']) . '</td>';
                        echo '<td>' . $empleado['antiguedad'] . ' años</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                    echo '</div>';
                }
                echo '</div>';
                
            } elseif ($tipo_array == 'array_irregular') {
                echo '<div class="array-structure">';
                foreach ($array as $i => $fila) {
                    echo '<div style="margin-bottom: 1rem;">';
                    echo '<div style="color: var(--accent); font-weight: bold; margin-bottom: 0.5rem;">Fila ' . $i . ' (' . count($fila) . ' elementos):</div>';
                    echo '<div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">';
                    foreach ($fila as $elemento) {
                        echo '<div class="structure-element">' . $elemento['valor'] . '</div>';
                    }
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
            }
            
            echo '</div>';
            
            // Información del array
            echo '<div class="array-container">';
            echo '<h3 style="color: var(--primary); margin-bottom: 1rem;">ℹ️ Información del Array</h3>';
            
            echo '<div class="operation-results">';
            echo '<div class="result-item">';
            echo '<strong>Dimensión:</strong> ';
            switch ($tipo_array) {
                case 'matriz_bidimensional': echo '2D'; break;
                case 'array_tridimensional': echo '3D'; break;
                case 'array_asociativo': echo 'Asociativo 2D'; break;
                case 'array_irregular': echo 'Irregular 2D'; break;
            }
            echo '</div>';
            
            echo '<div class="result-item">';
            echo '<strong>Total de Elementos:</strong> ' . count($array, COUNT_RECURSIVE) - count($array);
            echo '</div>';
            
            echo '<div class="result-item">';
            echo '<strong>Estructura:</strong> ';
            if ($tipo_array == 'array_irregular') {
                echo 'Filas con longitud variable';
            } else {
                echo $filas . ' × ' . $columnas . ($tipo_array == 'array_tridimensional' ? ' × 3' : '');
            }
            echo '</div>';
            
            // Visualización de dimensiones
            echo '<div class="dimension-visual">';
            if ($tipo_array == 'matriz_bidimensional') {
                echo '<div class="dimension-level">';
                echo '<div class="dimension-box">Array</div>';
                echo '<div>Principal</div>';
                echo '</div>';
                echo '<div class="dimension-arrow">→</div>';
                echo '<div class="dimension-level">';
                echo '<div class="dimension-box">' . $filas . ' Filas</div>';
                echo '<div>Primera Dimensión</div>';
                echo '</div>';
                echo '<div class="dimension-arrow">→</div>';
                echo '<div class="dimension-level">';
                echo '<div class="dimension-box">' . $columnas . ' Columnas</div>';
                echo '<div>Segunda Dimensión</div>';
                echo '</div>';
            } elseif ($tipo_array == 'array_tridimensional') {
                echo '<div class="dimension-level">';
                echo '<div class="dimension-box">Array</div>';
                echo '<div>Principal</div>';
                echo '</div>';
                echo '<div class="dimension-arrow">→</div>';
                echo '<div class="dimension-level">';
                echo '<div class="dimension-box">' . $filas . ' Filas</div>';
                echo '<div>1ra Dimensión</div>';
                echo '</div>';
                echo '<div class="dimension-arrow">→</div>';
                echo '<div class="dimension-level">';
                echo '<div class="dimension-box">' . $columnas . ' Columnas</div>';
                echo '<div>2da Dimensión</div>';
                echo '</div>';
                echo '<div class="dimension-arrow">→</div>';
                echo '<div class="dimension-level">';
                echo '<div class="dimension-box">3 Profundidad</div>';
                echo '<div>3ra Dimensión</div>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>'; // cierre array-visualization
            
            // Código PHP
            echo '<div class="code-preview">';
            echo '<h3 style="color: var(--primary); margin-bottom: 1rem;">📝 Código PHP Generado</h3>';
            echo '<pre>';
            switch ($tipo_array) {
                case 'matriz_bidimensional':
                    echo "// Crear matriz bidimensional\n";
                    echo "\$matriz = [];\n";
                    echo "for (\$i = 0; \$i < $filas; \$i++) {\n";
                    echo "    for (\$j = 0; \$j < $columnas; \$j++) {\n";
                    echo "        \$matriz[\$i][\$j] = ...;\n";
                    echo "    }\n";
                    echo "}\n";
                    echo "// Acceder: \$matriz[fila][columna]";
                    break;
                    
                case 'array_tridimensional':
                    echo "// Crear array tridimensional\n";
                    echo "\$array3D = [];\n";
                    echo "for (\$i = 0; \$i < $filas; \$i++) {\n";
                    echo "    for (\$j = 0; \$j < $columnas; \$j++) {\n";
                    echo "        for (\$k = 0; \$k < 3; \$k++) {\n";
                    echo "            \$array3D[\$i][\$j][\$k] = ...;\n";
                    echo "        }\n";
                    echo "    }\n";
                    echo "}\n";
                    echo "// Acceder: \$array3D[x][y][z]";
                    break;
                    
                case 'array_asociativo':
                    echo "// Array asociativo multidimensional\n";
                    echo "\$empleados = [\n";
                    echo "    'Ventas' => [\n";
                    echo "        ['nombre' => 'Ana', 'salario' => 50000],\n";
                    echo "        ['nombre' => 'Carlos', 'salario' => 55000]\n";
                    echo "    ],\n";
                    echo "    'TI' => [\n";
                    echo "        ['nombre' => 'Maria', 'salario' => 60000]\n";
                    echo "    ]\n";
                    echo "];\n";
                    echo "// Acceder: \$empleados['Ventas'][0]['nombre']";
                    break;
                    
                case 'array_irregular':
                    echo "// Array irregular (jagged array)\n";
                    echo "\$irregular = [];\n";
                    echo "// Cada fila puede tener diferente número de columnas\n";
                    echo "\$irregular[0] = [1, 2, 3];       // 3 elementos\n";
                    echo "\$irregular[1] = [4, 5];          // 2 elementos\n";
                    echo "\$irregular[2] = [6, 7, 8, 9];    // 4 elementos\n";
                    echo "// Acceder: \$irregular[fila][columna]";
                    break;
            }
            echo '</pre>';
            echo '</div>';
            
            echo '</div>'; // cierre del card
            echo '</div>'; // cierre del result-section
        }
        ?>
        
        <div class="explanation fade-in">
            <h3>🎓 ¿Qué son los Arrays Multidimensionales?</h3>
            <div class="feature-list">
                <div class="feature-item">
                    <span class="feature-icon">📚</span>
                    <div>
                        <strong>Definición</strong>
                        <p>Un array que contiene otros arrays como elementos, creando estructuras de datos complejas</p>
                    </div>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">🏗️</span>
                    <div>
                        <strong>Estructura</strong>
                        <p>Pueden ser bidimensionales (tablas), tridimensionales (cubos), o de más dimensiones</p>
                    </div>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">🔑</span>
                    <div>
                        <strong>Arrays Asociativos</strong>
                        <p>Usan strings como índices en lugar de números, creando estructuras más legibles</p>
                    </div>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">🎭</span>
                    <div>
                        <strong>Arrays Irregulares</strong>
                        <p>Cada sub-array puede tener diferente longitud, útil para datos no uniformes</p>
                    </div>
                </div>
            </div>
            
            <div class="syntax-example">
                <h4 style="color: var(--primary); margin-bottom: 1rem;">💡 Sintaxis en PHP:</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                    <div>
                        <div style="font-weight: bold; color: var(--secondary);">Array 2D:</div>
                        <code style="color: var(--text-secondary); font-size: 0.9rem;">
                            $matriz[fila][columna]
                        </code>
                    </div>
                    <div>
                        <div style="font-weight: bold; color: var(--secondary);">Array 3D:</div>
                        <code style="color: var(--text-secondary); font-size: 0.9rem;">
                            $cubo[x][y][z]
                        </code>
                    </div>
                    <div>
                        <div style="font-weight: bold; color: var(--secondary);">Asociativo:</div>
                        <code style="color: var(--text-secondary); font-size: 0.9rem;">
                            $datos['clave']['subclave']
                        </code>
                    </div>
                </div>
            </div>
            
            <div style="background: var(--dark-card); padding: 1.5rem; border-radius: 10px; margin-top: 1.5rem;">
                <h4 style="color: var(--primary); margin-bottom: 1rem;">🎯 Aplicaciones Prácticas:</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div style="text-align: center;">
                        <div style="font-size: 1.2rem; font-weight: bold; color: var(--secondary);">📈</div>
                        <div style="font-size: 0.9rem; color: var(--text-secondary);">Matrices matemáticas</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 1.2rem; font-weight: bold; color: var(--secondary);">🎮</div>
                        <div style="font-size: 0.9rem; color: var(--text-secondary);">Grids de juegos</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 1.2rem; font-weight: bold; color: var(--secondary);">🏢</div>
                        <div style="font-size: 0.9rem; color: var(--text-secondary);">Datos organizacionales</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 1.2rem; font-weight: bold; color: var(--secondary);">📊</div>
                        <div style="font-size: 0.9rem; color: var(--text-secondary);">Tablas de bases de datos</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Efectos de interacción
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input, select');
            const btn = document.querySelector('.btn');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.style.transform = 'scale(1)';
                });
            });
            
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
            
            // Efecto hover en celdas de tabla
            const celdas = document.querySelectorAll('.array-table td');
            celdas.forEach(celda => {
                celda.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(236, 72, 153, 0.3)';
                });
                
                celda.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });
        });
    </script>
</body>
</html>