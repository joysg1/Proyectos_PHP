<?php
// Simulador de Gestión Militar - Ciclo WHILE
class GestorDespliegue {
    private $tropasDisponibles;
    private $tropasDesplegadas;
    private $rondasDespliegue;
    
    public function __construct($tropasIniciales) {
        $this->tropasDisponibles = $tropasIniciales;
        $this->tropasDesplegadas = 0;
        $this->rondasDespliegue = 0;
    }
    
    // Función principal que usa while para desplegar tropas
    public function desplegarTropas($tropasPorRonda, $objetivo = null) {
        echo "<div class='operacion-box'>";
        echo "<h3>🚀 INICIANDO OPERACIÓN DE DESPLIEGUE</h3>";
        echo "<div class='estado-inicial'>";
        echo "🪖 Tropas disponibles: <strong>{$this->tropasDisponibles}</strong><br>";
        echo "🎯 Objetivo: " . ($objetivo ?: "Despliegue completo") . "<br>";
        echo "📦 Tropas por ronda: <strong>$tropasPorRonda</strong>";
        echo "</div>";
        echo "</div>";
        
        // CICLO WHILE - Mientras tengamos tropas disponibles
        while ($this->tropasDisponibles > 0) {
            $this->rondasDespliegue++;
            $tropasEstaRonda = min($tropasPorRonda, $this->tropasDisponibles);
            
            echo "<div class='ronda-box'>";
            echo "<h4>🛡️ RONDA DE DESPLIEGUE #{$this->rondasDespliegue}</h4>";
            
            // Desplegar tropas
            $this->tropasDisponibles -= $tropasEstaRonda;
            $this->tropasDesplegadas += $tropasEstaRonda;
            
            echo "➡️ Desplegadas: <strong>$tropasEstaRonda</strong> soldados<br>";
            echo "📊 Restantes: <strong>{$this->tropasDisponibles}</strong> soldados<br>";
            echo "🎯 Totales desplegados: <strong>{$this->tropasDesplegadas}</strong> soldados";
            
            // Estado especial según cantidad restante
            if ($this->tropasDisponibles > 0) {
                if ($this->tropasDisponibles <= 10) {
                    echo "<br>⚠️ <em>Últimas tropas disponibles</em>";
                } elseif ($this->tropasDisponibles <= 50) {
                    echo "<br>🔸 <em>Fuerzas reducidas</em>";
                }
            } else {
                echo "<br>✅ <strong>¡TODAS LAS TROPAS DESPLEGADAS!</strong>";
            }
            
            echo "</div>";
        }
        
        return $this->mostrarResumenOperacion();
    }
    
    // While con condición adicional - desplegar hasta alcanzar objetivo
    public function desplegarHastaObjetivo($objetivo, $tropasPorRonda) {
        echo "<div class='operacion-box'>";
        echo "<h3>🎯 OPERACIÓN CON OBJETIVO ESPECÍFICO</h3>";
        echo "<div class='estado-inicial'>";
        echo "🪖 Tropas disponibles: <strong>{$this->tropasDisponibles}</strong><br>";
        echo "🎯 Objetivo requerido: <strong>$objetivo</strong> tropas<br>";
        echo "📦 Tropas por ronda: <strong>$tropasPorRonda</strong>";
        echo "</div>";
        echo "</div>";
        
        $tropasAlcanzadas = 0;
        $ronda = 0;
        
        // WHILE con múltiples condiciones
        while ($tropasAlcanzadas < $objetivo && $this->tropasDisponibles > 0) {
            $ronda++;
            $tropasEstaRonda = min($tropasPorRonda, $objetivo - $tropasAlcanzadas, $this->tropasDisponibles);
            
            echo "<div class='ronda-box'>";
            echo "<h4>🎖️ RONDA ESTRATÉGICA #$ronda</h4>";
            
            $this->tropasDisponibles -= $tropasEstaRonda;
            $tropasAlcanzadas += $tropasEstaRonda;
            
            echo "➡️ Desplegadas: <strong>$tropasEstaRonda</strong> soldados<br>";
            echo "📊 Objetivo alcanzado: <strong>$tropasAlcanzadas/$objetivo</strong><br>";
            echo "🪖 Tropas restantes: <strong>{$this->tropasDisponibles}</strong>";
            
            if ($tropasAlcanzadas >= $objetivo) {
                echo "<br>✅ <strong>¡OBJETIVO ALCANZADO!</strong>";
            } elseif ($this->tropasDisponibles == 0) {
                echo "<br>⚠️ <strong>¡SIN TROPAS DISPONIBLES!</strong>";
            }
            
            echo "</div>";
        }
        
        return $tropasAlcanzadas;
    }
    
    // While con reinicios - simulando refuerzos
    public function despliegueConRefuerzos($tropasPorRonda, $refuerzosCada) {
        echo "<div class='operacion-box'>";
        echo "<h3>🔄 OPERACIÓN CON REFUERZOS</h3>";
        echo "<div class='estado-inicial'>";
        echo "🪖 Tropas iniciales: <strong>{$this->tropasDisponibles}</strong><br>";
        echo "📦 Tropas por ronda: <strong>$tropasPorRonda</strong><br>";
        echo "🛡️ Refuerzos cada: <strong>$refuerzosCada</strong> rondas";
        echo "</div>";
        echo "</div>";
        
        $ronda = 0;
        $refuerzosRecibidos = 0;
        
        while ($this->tropasDisponibles > 0) {
            $ronda++;
            
            // Verificar si llegan refuerzos
            if ($ronda % $refuerzosCada == 0) {
                $refuerzos = rand(50, 150);
                $this->tropasDisponibles += $refuerzos;
                $refuerzosRecibidos += $refuerzos;
                
                echo "<div class='refuerzo-box'>";
                echo "<h4>🎁 REFUERZOS RECIBIDOS - Ronda $ronda</h4>";
                echo "➕ <strong>$refuerzos</strong> soldados de refuerzo<br>";
                echo "🪖 Total disponible ahora: <strong>{$this->tropasDisponibles}</strong>";
                echo "</div>";
            }
            
            // Desplegar tropas
            $tropasEstaRonda = min($tropasPorRonda, $this->tropasDisponibles);
            $this->tropasDisponibles -= $tropasEstaRonda;
            $this->tropasDesplegadas += $tropasEstaRonda;
            
            echo "<div class='ronda-box'>";
            echo "<h4>⚔️ RONDA #$ronda</h4>";
            echo "➡️ Desplegadas: <strong>$tropasEstaRonda</strong> soldados<br>";
            echo "📊 Restantes: <strong>{$this->tropasDisponibles}</strong> soldados<br>";
            echo "🎯 Totales desplegados: <strong>{$this->tropasDesplegadas}</strong>";
            
            if ($this->tropasDisponibles == 0) {
                echo "<br>🏁 <strong>OPERACIÓN COMPLETADA</strong>";
            }
            
            echo "</div>";
            
            // Límite de seguridad para evitar loops infinitos
            if ($ronda >= 20) {
                echo "<div class='advertencia-box'>";
                echo "⚠️ <strong>LÍMITE DE SEGURIDAD ALCANZADO</strong><br>";
                echo "La operación se detuvo después de 20 rondas";
                echo "</div>";
                break;
            }
        }
        
        return $this->mostrarResumenOperacion();
    }
    
    private function mostrarResumenOperacion() {
        echo "<div class='resumen-box'>";
        echo "<h3>📊 RESUMEN DE LA OPERACIÓN</h3>";
        echo "🪖 Tropas desplegadas: <strong>{$this->tropasDesplegadas}</strong><br>";
        echo "🛡️ Rondas ejecutadas: <strong>{$this->rondasDespliegue}</strong><br>";
        echo "📈 Eficiencia: <strong>" . round($this->tropasDesplegadas / $this->rondasDespliegue, 2) . "</strong> tropas/ronda";
        echo "</div>";
        
        return [
            'tropas_desplegadas' => $this->tropasDesplegadas,
            'rondas' => $this->rondasDespliegue
        ];
    }
}

// Función para demostrar diferentes usos del while (SOLO cuando se solicite)
function demostrarEjemplosWhile() {
    echo "<div class='demo-container'>";
    echo "<h2>🎯 DEMOSTRACIONES DEL CICLO WHILE</h2>";
    
    // Ejemplo 1: Despliegue básico
    echo "<div class='ejemplo-box'>";
    echo "<h3>1. Despliegue Básico</h3>";
    $gestor1 = new GestorDespliegue(100);
    $gestor1->desplegarTropas(15);
    echo "</div>";
    
    // Ejemplo 2: Con objetivo específico
    echo "<div class='ejemplo-box'>";
    echo "<h3>2. Despliegue con Objetivo</h3>";
    $gestor2 = new GestorDespliegue(200);
    $gestor2->desplegarHastaObjetivo(75, 20);
    echo "</div>";
    
    // Ejemplo 3: Con refuerzos
    echo "<div class='ejemplo-box'>";
    echo "<h3>3. Despliegue con Refuerzos</h3>";
    $gestor3 = new GestorDespliegue(80);
    $gestor3->despliegueConRefuerzos(25, 3);
    echo "</div>";
    
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor Militar - Ciclo WHILE</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #0a0a0a;
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            padding: 20px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .header h1 {
            font-size: 2.5em;
            background: linear-gradient(135deg, #ff6b6b, #ffa726);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .header p {
            color: #b0b0b0;
            font-size: 1.1em;
        }

        .info-box {
            background: rgba(255, 255, 255, 0.05);
            padding: 25px;
            border-radius: 12px;
            border-left: 4px solid #ff6b6b;
            margin: 25px 0;
            backdrop-filter: blur(10px);
        }

        .info-box h3 {
            color: #ff6b6b;
            margin-bottom: 15px;
        }

        .code-example {
            background: #1a1a1a;
            color: #e0e0e0;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            margin: 15px 0;
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow-x: auto;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #cccccc;
        }

        select, input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #e0e0e0;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        select:focus, input:focus {
            outline: none;
            border-color: #ff6b6b;
            box-shadow: 0 0 0 2px rgba(255, 107, 107, 0.2);
        }

        select option {
            background: #2d2d2d;
            color: #e0e0e0;
        }

        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #ff6b6b, #ffa726);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
        }

        .btn-secundario {
            background: linear-gradient(135deg, #6c757d, #495057) !important;
            margin-top: 10px;
        }

        .btn-secundario:hover {
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4) !important;
        }

        /* Estilos para las operaciones militares */
        .operacion-box {
            background: rgba(255, 107, 107, 0.1);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(255, 107, 107, 0.3);
            margin: 20px 0;
        }

        .ronda-box {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #4CAF50;
            transition: all 0.3s ease;
        }

        .ronda-box:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(5px);
        }

        .refuerzo-box {
            background: rgba(255, 193, 7, 0.1);
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #FFC107;
        }

        .resumen-box {
            background: rgba(33, 150, 243, 0.1);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(33, 150, 243, 0.3);
            margin: 20px 0;
        }

        .estado-inicial {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }

        .advertencia-box {
            background: rgba(244, 67, 54, 0.1);
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #F44336;
            margin: 10px 0;
        }

        .demo-container {
            margin-top: 40px;
        }

        .ejemplo-box {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .instrucciones-box {
            background: rgba(255, 193, 7, 0.1);
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #FFC107;
            margin: 20px 0;
        }

        /* Scrollbar personalizado */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        ::-webkit-scrollbar-thumb {
            background: #ff6b6b;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #ff5252;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🪖 Gestor de Despliegue Militar</h1>
            <p>Sistema de gestión de tropas usando el ciclo WHILE en PHP</p>
        </div>
        
        <div class="info-box">
            <h3>💡 ¿Qué es el ciclo WHILE?</h3>
            <p>El ciclo <strong>while</strong> ejecuta un bloque de código <strong>mientras</strong> una condición sea verdadera:</p>
            <div class="code-example">
                while (condición) {<br>
                &nbsp;&nbsp;// Código a ejecutar mientras la condición sea verdadera<br>
                &nbsp;&nbsp;// Normalmente se modifica la condición dentro del ciclo<br>
                }
            </div>
            <p><strong>Características:</strong> Verifica la condición ANTES de ejecutar, puede no ejecutarse nunca si la condición es falsa inicialmente.</p>
        </div>
        
        <div class="form-container">
            <h2 style="color: #ff6b6b; margin-bottom: 20px;">🎮 Simulador de Despliegue</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="tropas_iniciales">Tropas disponibles inicialmente:</label>
                    <select name="tropas_iniciales" id="tropas_iniciales" required>
                        <option value="">-- Selecciona cantidad --</option>
                        <option value="50">50 soldados (Escuadrón pequeño)</option>
                        <option value="100">100 soldados (Pelotón)</option>
                        <option value="250">250 soldados (Compañía)</option>
                        <option value="500">500 soldados (Batallón)</option>
                        <option value="1000">1000 soldados (Regimiento)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="tropas_por_ronda">Tropas a desplegar por ronda:</label>
                    <select name="tropas_por_ronda" id="tropas_por_ronda" required>
                        <option value="">-- Selecciona cantidad --</option>
                        <option value="5">5 soldados (Equipo táctico)</option>
                        <option value="10">10 soldados (Escuadra)</option>
                        <option value="25">25 soldados (Sección)</option>
                        <option value="50">50 soldados (Pelotón ligero)</option>
                        <option value="100">100 soldados (Pelotón completo)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="tipo_operacion">Tipo de operación:</label>
                    <select name="tipo_operacion" id="tipo_operacion" required>
                        <option value="">-- Selecciona operación --</option>
                        <option value="completa">🚀 Despliegue completo</option>
                        <option value="objetivo">🎯 Despliegue con objetivo</option>
                        <option value="refuerzos">🔄 Despliegue con refuerzos</option>
                    </select>
                </div>
                
                <div class="form-group" id="objetivo_group" style="display: none;">
                    <label for="objetivo_tropas">Objetivo de tropas a desplegar:</label>
                    <select name="objetivo_tropas" id="objetivo_tropas">
                        <option value="">-- Selecciona objetivo --</option>
                        <option value="30">30 soldados</option>
                        <option value="75">75 soldados</option>
                        <option value="150">150 soldados</option>
                        <option value="300">300 soldados</option>
                    </select>
                </div>
                
                <button type="submit">🎖️ Iniciar Operación</button>
                <button type="button" onclick="window.location.href=window.location.href" class="btn-secundario">
                    🔄 Reiniciar Simulador
                </button>
            </form>
        </div>
        
        <?php
        // Procesar formulario de simulación SOLO cuando se haya enviado
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tropasIniciales = intval($_POST['tropas_iniciales']);
            $tropasPorRonda = intval($_POST['tropas_por_ronda']);
            $tipoOperacion = $_POST['tipo_operacion'];
            $objetivoTropas = isset($_POST['objetivo_tropas']) ? intval($_POST['objetivo_tropas']) : null;
            
            // Validar que todos los campos estén completos
            if ($tropasIniciales > 0 && $tropasPorRonda > 0 && !empty($tipoOperacion)) {
                
                $gestor = new GestorDespliegue($tropasIniciales);
                
                echo "<div class='operacion-box'>";
                echo "<h2>🎮 SIMULACIÓN EN EJECUCIÓN</h2>";
                
                switch ($tipoOperacion) {
                    case 'completa':
                        $gestor->desplegarTropas($tropasPorRonda);
                        break;
                    case 'objetivo':
                        if ($objetivoTropas && $objetivoTropas > 0) {
                            $gestor->desplegarHastaObjetivo($objetivoTropas, $tropasPorRonda);
                        } else {
                            echo "<div class='advertencia-box'>";
                            echo "⚠️ <strong>OBJETIVO NO VÁLIDO</strong><br>";
                            echo "Por favor selecciona un objetivo válido para la operación.";
                            echo "</div>";
                        }
                        break;
                    case 'refuerzos':
                        $gestor->despliegueConRefuerzos($tropasPorRonda, 3);
                        break;
                }
                
                echo "</div>";
                
            } else {
                echo "<div class='advertencia-box'>";
                echo "⚠️ <strong>FORMULARIO INCOMPLETO</strong><br>";
                echo "Por favor completa todos los campos del formulario.";
                echo "</div>";
            }
            
        } else {
            // Mostrar instrucciones en lugar de ejemplos automáticos
            echo "<div class='instrucciones-box'>";
            echo "<h3>📋 Instrucciones de Uso</h3>";
            echo "<p>Para comenzar una simulación:</p>";
            echo "<ol>";
            echo "<li>Selecciona la cantidad de tropas disponibles</li>";
            echo "<li>Elige cuántas tropas desplegar por ronda</li>";
            echo "<li>Selecciona el tipo de operación a realizar</li>";
            echo "<li>Si eliges 'Despliegue con objetivo', especifica el objetivo</li>";
            echo "<li>Haz clic en <strong>🎖️ Iniciar Operación</strong></li>";
            echo "</ol>";
            echo "<p>El sistema mostrará el despliegue paso a paso usando el ciclo <strong>while</strong>.</p>";
            echo "</div>";
            
            // Opcional: Botón para ver ejemplos demostrativos
            echo "<div style='text-align: center; margin: 30px 0;'>";
            echo "<button type='button' onclick='mostrarEjemplos()' class='btn-secundario' style='width: auto; padding: 10px 20px;'>";
            echo "👀 Ver Ejemplos Demostrativos";
            echo "</button>";
            echo "</div>";
            
            // Contenedor para ejemplos (oculto inicialmente)
            echo "<div id='ejemplos-container' style='display: none;'>";
            demostrarEjemplosWhile();
            echo "</div>";
        }
        ?>
    </div>

    <script>
        // Mostrar/ocultar campo de objetivo según tipo de operación
        document.getElementById('tipo_operacion').addEventListener('change', function() {
            const objetivoGroup = document.getElementById('objetivo_group');
            objetivoGroup.style.display = this.value === 'objetivo' ? 'block' : 'none';
        });

        // Función para mostrar ejemplos demostrativos
        function mostrarEjemplos() {
            const container = document.getElementById('ejemplos-container');
            container.style.display = container.style.display === 'none' ? 'block' : 'none';
        }

        // Mantener valores seleccionados después del envío
        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
        document.getElementById('tropas_iniciales').value = '<?php echo $_POST['tropas_iniciales'] ?? ''; ?>';
        document.getElementById('tropas_por_ronda').value = '<?php echo $_POST['tropas_por_ronda'] ?? ''; ?>';
        document.getElementById('tipo_operacion').value = '<?php echo $_POST['tipo_operacion'] ?? ''; ?>';
        <?php if (isset($_POST['objetivo_tropas'])): ?>
        document.getElementById('objetivo_tropas').value = '<?php echo $_POST['objetivo_tropas'] ?? ''; ?>';
        <?php endif; ?>
        
        // Mostrar grupo de objetivo si es necesario
        if (document.getElementById('tipo_operacion').value === 'objetivo') {
            document.getElementById('objetivo_group').style.display = 'block';
        }
        <?php endif; ?>
    </script>
</body>
</html>