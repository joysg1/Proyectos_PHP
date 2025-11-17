<?php
// verificar_graficos.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificador de Gráficos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        pre {
            background: #f8f8f8;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .file-list {
            list-style: none;
            padding: 0;
        }
        .file-list li {
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verificador de Gráficos - Sistema de Análisis</h1>
        <p><a href="index.php">← Volver al sistema principal</a></p>

        <?php
        function ejecutarComando($comando) {
            $output = [];
            $return_code = 0;
            exec($comando . ' 2>&1', $output, $return_code);
            return [
                'output' => implode("\n", $output),
                'return_code' => $return_code
            ];
        }

        // Información del sistema
        echo "<div class='section'>";
        echo "<h2>🖥️ Información del Sistema</h2>";
        echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
        echo "<p><strong>Servidor:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "</p>";
        echo "<p><strong>Sistema:</strong> " . php_uname() . "</p>";
        echo "</div>";

        // Verificar archivos
        echo "<div class='section'>";
        echo "<h2>📁 Archivos del Sistema</h2>";
        
        $archivos = [
            'goleadores.json' => 'Base de datos',
            'analisis_goleadores.py' => 'Script Python',
            'ejecutar_analisis.php' => 'Ejecutor PHP',
            'index.php' => 'Página principal'
        ];
        
        $graficos = [
            'grafico_top10.png',
            'grafico_distribucion.png', 
            'grafico_paises.png',
            'grafico_eficiencia.png',
            'grafico_detalle_jugadores.png',
            'grafico_correlacion.png',
            'reporte_goleadores.txt'
        ];
        
        echo "<h3>Archivos base:</h3>";
        echo "<ul class='file-list'>";
        foreach ($archivos as $archivo => $desc) {
            $existe = file_exists($archivo);
            $clase = $existe ? 'success' : 'error';
            $estado = $existe ? '✅ EXISTE' : '❌ NO EXISTE';
            $tamano = $existe ? " (" . filesize($archivo) . " bytes)" : "";
            echo "<li><span class='$clase'>$archivo</span> - $desc $estado$tamano</li>";
        }
        echo "</ul>";
        
        echo "<h3>Archivos de gráficos:</h3>";
        echo "<ul class='file-list'>";
        foreach ($graficos as $archivo) {
            $existe = file_exists($archivo);
            $clase = $existe ? 'success' : 'warning';
            $estado = $existe ? '✅ EXISTE' : '⚠️ NO EXISTE';
            $tamano = $existe ? " (" . filesize($archivo) . " bytes)" : "";
            echo "<li><span class='$clase'>$archivo</span> $estado$tamano</li>";
        }
        echo "</ul>";
        echo "</div>";

        // Verificar Python
        echo "<div class='section'>";
        echo "<h2>🐍 Verificación de Python</h2>";
        
        $python_check = ejecutarComando('python --version');
        if (strpos($python_check['output'], 'Python') !== false) {
            echo "<p class='success'>✅ Python encontrado: " . htmlspecialchars($python_check['output']) . "</p>";
            $python_cmd = 'python';
        } else {
            $python3_check = ejecutarComando('python3 --version');
            if (strpos($python3_check['output'], 'Python') !== false) {
                echo "<p class='success'>✅ Python3 encontrado: " . htmlspecialchars($python3_check['output']) . "</p>";
                $python_cmd = 'python3';
            } else {
                echo "<p class='error'>❌ Python no encontrado</p>";
                $python_cmd = null;
            }
        }
        
        if ($python_cmd) {
            echo "<h3>Bibliotecas Python:</h3>";
            $librerias = ['pandas', 'seaborn', 'matplotlib', 'numpy'];
            foreach ($librerias as $lib) {
                $check = ejecutarComando("$python_cmd -c \"import $lib; print('OK')\"");
                if ($check['return_code'] === 0) {
                    echo "<p class='success'>✅ $lib: Instalada</p>";
                } else {
                    echo "<p class='error'>❌ $lib: No instalada</p>";
                }
            }
        }
        echo "</div>";

        // Probar ejecución
        if ($python_cmd && file_exists('analisis_goleadores.py')) {
            echo "<div class='section'>";
            echo "<h2>🚀 Prueba de Ejecución</h2>";
            
            echo "<p>Ejecutando análisis...</p>";
            $resultado = ejecutarComando("$python_cmd analisis_goleadores.py");
            
            echo "<h3>Salida:</h3>";
            echo "<pre>" . htmlspecialchars($resultado['output']) . "</pre>";
            
            if ($resultado['return_code'] === 0) {
                echo "<p class='success'>✅ Ejecución exitosa</p>";
            } else {
                echo "<p class='error'>❌ Error en ejecución (código: " . $resultado['return_code'] . ")</p>";
            }
            
            // Mostrar archivos generados
            echo "<h3>Archivos después de ejecución:</h3>";
            echo "<ul class='file-list'>";
            foreach ($graficos as $archivo) {
                $existe = file_exists($archivo);
                $clase = $existe ? 'success' : 'error';
                $estado = $existe ? '✅ EXISTE' : '❌ NO EXISTE';
                echo "<li><span class='$clase'>$archivo</span> $estado</li>";
            }
            echo "</ul>";
            echo "</div>";
        }

        // Mostrar gráficos
        echo "<div class='section'>";
        echo "<h2>🖼️ Vista Previa de Gráficos</h2>";
        
        $graficos_existentes = array_filter($graficos, function($archivo) {
            return file_exists($archivo) && preg_match('/\.png$/', $archivo);
        });
        
        if (!empty($graficos_existentes)) {
            foreach ($graficos_existentes as $grafico) {
                echo "<h3>" . basename($grafico) . "</h3>";
                echo "<img src='$grafico' style='max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 5px;'>";
                echo "<hr>";
            }
        } else {
            echo "<p class='warning'>No hay gráficos para mostrar</p>";
        }
        echo "</div>";

        // Solución de problemas
        echo "<div class='section'>";
        echo "<h2>🔧 Solución de Problemas</h2>";
        echo "<h3>Comandos de instalación:</h3>";
        echo "<pre>pip install seaborn matplotlib pandas numpy</pre>";
        
        echo "<h3>Verificar instalación:</h3>";
        echo "<pre>python -c \"import seaborn, matplotlib, pandas, numpy; print('Todas las bibliotecas instaladas correctamente')\"</pre>";
        
        echo "<h3>Probar script manualmente:</h3>";
        echo "<pre>python analisis_goleadores.py</pre>";
        echo "</div>";
        ?>
        
        <p><a href="index.php">← Volver al sistema principal</a></p>
    </div>
</body>
</html>