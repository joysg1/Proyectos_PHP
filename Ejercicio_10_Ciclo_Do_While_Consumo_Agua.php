<?php
// Simulador de Flujo de Agua - Ciclo DO-WHILE
class SistemaAgua {
    private $aguaDisponible;
    private $aguaConsumida;
    private $nivelMinimo;
    private $aguaInicial;
    
    public function __construct($aguaInicial, $nivelMinimo = 10) {
        $this->aguaInicial = $aguaInicial;
        $this->aguaDisponible = $aguaInicial;
        $this->aguaConsumida = 0;
        $this->nivelMinimo = $nivelMinimo;
    }
    
    // Simulación de DO-WHILE: Primero se consume, luego se verifica
    public function consumirAgua($consumoPorCiclo) {
        echo "<div class='operacion-box'>";
        echo "<h3>💧 INICIANDO CICLO DE CONSUMO DE AGUA</h3>";
        echo "<div class='estado-inicial'>";
        echo "🚰 Agua disponible: <strong>{$this->aguaDisponible}L</strong><br>";
        echo "🥤 Consumo por ciclo: <strong>{$consumoPorCiclo}L</strong><br>";
        echo "⚠️ Nivel mínimo: <strong>{$this->nivelMinimo}L</strong>";
        echo "</div>";
        echo "</div>";
        
        $ciclo = 0;
        
        // SIMULACIÓN DE DO-WHILE en PHP
        do {
            $ciclo++;
            
            echo "<div class='ciclo-box' id='ciclo-{$ciclo}'>";
            echo "<h4>🌀 CICLO #$ciclo</h4>";
            
            // DO: Primero se consume el agua
            $aguaInicialCiclo = $this->aguaDisponible;
            $consumoReal = min($consumoPorCiclo, $this->aguaDisponible);
            $this->aguaDisponible -= $consumoReal;
            $this->aguaConsumida += $consumoReal;
            
            echo "➡️ Consumido: <strong>{$consumoReal}L</strong> de agua<br>";
            echo "📊 Agua restante: <strong>{$this->aguaDisponible}L</strong><br>";
            echo "🎯 Total consumido: <strong>{$this->aguaConsumida}L</strong>";
            
            // Mostrar animación integrada en el ciclo
            $this->mostrarAnimacionCiclo($ciclo, $aguaInicialCiclo, $consumoReal, $this->aguaDisponible, 0);
            
            // Verificar estado del agua
            if ($this->aguaDisponible <= $this->nivelMinimo && $this->aguaDisponible > 0) {
                echo "<br>⚠️ <em>Nivel bajo de agua - reabastecer pronto</em>";
            } elseif ($this->aguaDisponible == 0) {
                echo "<br>🔴 <strong>¡SIN AGUA DISPONIBLE!</strong>";
            }
            
            echo "</div>";
            
            // Pequeño delay visual entre ciclos
            echo "<script>setTimeout(() => { document.getElementById('ciclo-{$ciclo}').scrollIntoView({ behavior: 'smooth' }); }, 300);</script>";
            
        // WHILE: Luego se verifica si hay suficiente agua para continuar
        } while ($this->aguaDisponible > $this->nivelMinimo);
        
        // Mensaje final
        if ($this->aguaDisponible > 0) {
            echo "<div class='advertencia-box'>";
            echo "🟡 <strong>CICLO DETENIDO POR NIVEL MÍNIMO</strong><br>";
            echo "Quedan <strong>{$this->aguaDisponible}L</strong> disponibles (nivel mínimo: {$this->nivelMinimo}L)";
            echo "</div>";
        } else {
            echo "<div class='advertencia-box'>";
            echo "🔴 <strong>DEPÓSITO VACÍO</strong><br>";
            echo "Se ha agotado toda el agua disponible";
            echo "</div>";
        }
        
        return $this->mostrarResumenConsumo();
    }
    
    // Sistema con recarga automática
    public function sistemaConRecarga($consumoPorCiclo, $probabilidadRecarga = 30) {
        echo "<div class='operacion-box'>";
        echo "<h3>🔄 SISTEMA CON RECARGA AUTOMÁTICA</h3>";
        echo "<div class='estado-inicial'>";
        echo "🚰 Agua inicial: <strong>{$this->aguaDisponible}L</strong><br>";
        echo "🥤 Consumo por ciclo: <strong>{$consumoPorCiclo}L</strong><br>";
        echo "🎲 Probabilidad de recarga: <strong>{$probabilidadRecarga}%</strong>";
        echo "</div>";
        echo "</div>";
        
        $ciclo = 0;
        $recargasRealizadas = 0;
        
        do {
            $ciclo++;
            
            echo "<div class='ciclo-box' id='ciclo-{$ciclo}'>";
            echo "<h4>💦 CICLO #$ciclo</h4>";
            
            // DO: Consumir agua
            $aguaInicialCiclo = $this->aguaDisponible;
            $consumoReal = min($consumoPorCiclo, $this->aguaDisponible);
            $this->aguaDisponible -= $consumoReal;
            $this->aguaConsumida += $consumoReal;
            
            echo "➡️ Consumido: <strong>{$consumoReal}L</strong><br>";
            echo "📊 Agua restante: <strong>{$this->aguaDisponible}L</strong>";
            
            // Posible recarga (simulando lluvia o reabastecimiento)
            $recarga = 0;
            if (rand(1, 100) <= $probabilidadRecarga && $this->aguaDisponible < 50) {
                $recarga = rand(20, 50);
                $this->aguaDisponible += $recarga;
                $recargasRealizadas++;
                
                echo "<br>🌧️ <strong>¡RECARGA NATURAL! +{$recarga}L</strong>";
                echo "<br>🚰 Nuevo total: <strong>{$this->aguaDisponible}L</strong>";
            }
            
            // Mostrar animación integrada en el ciclo
            $this->mostrarAnimacionCiclo($ciclo, $aguaInicialCiclo, $consumoReal, $this->aguaDisponible, $recarga);
            
            echo "</div>";
            
            // Pequeño delay visual entre ciclos
            echo "<script>setTimeout(() => { document.getElementById('ciclo-{$ciclo}').scrollIntoView({ behavior: 'smooth' }); }, 300);</script>";
            
            // Límite de seguridad
            if ($ciclo >= 15) {
                echo "<div class='advertencia-box'>";
                echo "⚠️ <strong>LÍMITE DE SEGURIDAD ALCANZADO</strong><br>";
                echo "El sistema se detuvo después de 15 ciclos";
                echo "</div>";
                break;
            }
            
        // WHILE: Continuar mientras haya agua disponible
        } while ($this->aguaDisponible > 0);
        
        echo "<div class='resumen-box'>";
        echo "<h3>📊 RESUMEN DEL SISTEMA</h3>";
        echo "💧 Agua consumida total: <strong>{$this->aguaConsumida}L</strong><br>";
        echo "🔄 Ciclos ejecutados: <strong>{$ciclo}</strong><br>";
        echo "🌧️ Recargas naturales: <strong>{$recargasRealizadas}</strong><br>";
        echo "📈 Eficiencia: <strong>" . round($this->aguaConsumida / $ciclo, 2) . "L/ciclo</strong>";
        echo "</div>";
        
        return [
            'ciclos' => $ciclo,
            'consumo_total' => $this->aguaConsumida,
            'recargas' => $recargasRealizadas
        ];
    }
    
    // Mostrar animación integrada en el ciclo
    private function mostrarAnimacionCiclo($ciclo, $aguaInicial, $consumo, $aguaRestante, $recarga = 0) {
        $porcentajeInicial = ($aguaInicial / $this->aguaInicial) * 100;
        $porcentajeFinal = ($aguaRestante / $this->aguaInicial) * 100;
        
        echo "<div class='animacion-integrada'>";
        echo "<div class='info-animacion'>";
        echo "<span class='consumo-indicador'>Consumo: {$consumo}L</span>";
        if ($recarga > 0) {
            echo "<span class='recarga-indicador'> | Recarga: +{$recarga}L</span>";
        }
        echo "<span class='restante-indicador'> | Restante: {$aguaRestante}L</span>";
        echo "</div>";
        
        echo "<div class='tanque-animacion'>";
        echo "<div class='nivel-agua-animacion' style='height: {$porcentajeInicial}%' id='nivel-inicial-{$ciclo}'>";
        echo "<div class='indicador-nivel-animacion'>{$aguaInicial}L</div>";
        echo "</div>";
        echo "<div class='gotas-container-animacion' id='gotas-{$ciclo}'>";
        // Las gotas se generarán con JavaScript
        echo "</div>";
        echo "</div>";
        
        echo "<div class='controles-animacion'>";
        echo "<button onclick='iniciarAnimacionCiclo({$ciclo}, {$aguaInicial}, {$consumo}, {$aguaRestante}, {$recarga}, {$this->aguaInicial})' class='btn-animacion'>";
        echo "▶️ Reproducir Animación";
        echo "</button>";
        echo "</div>";
        echo "</div>";
        
        // Script para auto-iniciar la animación después de un breve delay
        echo "<script>";
        echo "setTimeout(() => { ";
        echo "iniciarAnimacionCiclo({$ciclo}, {$aguaInicial}, {$consumo}, {$aguaRestante}, {$recarga}, {$this->aguaInicial});";
        echo " }, 500);";
        echo "</script>";
    }
    
    private function mostrarResumenConsumo() {
        echo "<div class='resumen-box'>";
        echo "<h3>📊 RESUMEN DE CONSUMO</h3>";
        echo "💧 Agua consumida: <strong>{$this->aguaConsumida}L</strong><br>";
        echo "🚰 Agua restante: <strong>{$this->aguaDisponible}L</strong><br>";
        echo "⚡ Eficiencia: <strong>" . ($this->aguaConsumida > 0 ? "Óptima" : "Sin consumo") . "</strong>";
        echo "</div>";
        
        return [
            'consumo_total' => $this->aguaConsumida,
            'agua_restante' => $this->aguaDisponible
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Agua - Ciclo DO-WHILE</title>
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
            max-width: 1200px;
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
            background: linear-gradient(135deg, #4fc3f7, #29b6f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .info-box {
            background: rgba(255, 255, 255, 0.05);
            padding: 25px;
            border-radius: 12px;
            border-left: 4px solid #29b6f6;
            margin: 25px 0;
            backdrop-filter: blur(10px);
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

        button {
            padding: 15px;
            background: linear-gradient(135deg, #29b6f6, #0288d1);
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
            box-shadow: 0 5px 15px rgba(41, 182, 246, 0.4);
        }

        .btn-secundario {
            background: linear-gradient(135deg, #6c757d, #495057) !important;
            margin-top: 10px;
        }

        .btn-animacion {
            background: linear-gradient(135deg, #4caf50, #45a049) !important;
            width: auto !important;
            padding: 8px 15px !important;
            font-size: 14px !important;
            margin-top: 8px !important;
        }

        /* Estilos para las operaciones de agua */
        .operacion-box {
            background: rgba(41, 182, 246, 0.1);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(41, 182, 246, 0.3);
            margin: 20px 0;
        }

        .ciclo-box {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #4fc3f7;
            transition: all 0.3s ease;
        }

        .ciclo-box:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(5px);
        }

        .resumen-box {
            background: rgba(76, 175, 80, 0.1);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(76, 175, 80, 0.3);
            margin: 20px 0;
        }

        .estado-inicial {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }

        .advertencia-box {
            background: rgba(255, 152, 0, 0.1);
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #FF9800;
            margin: 10px 0;
        }

        .instrucciones-box {
            background: rgba(255, 193, 7, 0.1);
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #FFC107;
            margin: 20px 0;
        }

        /* Animación integrada en cada ciclo */
        .animacion-integrada {
            background: rgba(255, 255, 255, 0.03);
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border: 1px solid rgba(41, 182, 246, 0.2);
        }

        .info-animacion {
            text-align: center;
            margin-bottom: 10px;
            font-size: 0.9em;
        }

        .consumo-indicador {
            color: #ff6b6b;
            font-weight: bold;
        }

        .recarga-indicador {
            color: #4caf50;
            font-weight: bold;
        }

        .restante-indicador {
            color: #4fc3f7;
            font-weight: bold;
        }

        .tanque-animacion {
            width: 150px;
            height: 200px;
            background: #1a237e;
            border: 2px solid #4fc3f7;
            border-radius: 8px;
            margin: 0 auto 10px;
            position: relative;
            overflow: hidden;
        }

        .nivel-agua-animacion {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: linear-gradient(180deg, #4fc3f7 0%, #29b6f6 100%);
            transition: height 1.5s ease-in-out;
            border-radius: 0 0 6px 6px;
        }

        .indicador-nivel-animacion {
            position: absolute;
            top: 5px;
            left: 5px;
            color: white;
            font-weight: bold;
            font-size: 0.8em;
            background: rgba(0, 0, 0, 0.5);
            padding: 2px 6px;
            border-radius: 4px;
        }

        .gotas-container-animacion {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .gota-consumo {
            width: 6px;
            height: 9px;
            background: #ff6b6b;
            border-radius: 50% 50% 50% 50%;
            position: absolute;
            animation: caerConsumo 1.5s linear;
        }

        .gota-recarga {
            width: 6px;
            height: 9px;
            background: #4caf50;
            border-radius: 50% 50% 50% 50%;
            position: absolute;
            animation: caerRecarga 2s ease-in;
        }

        @keyframes caerConsumo {
            0% { transform: translateY(0) scale(1); opacity: 1; }
            50% { transform: translateY(100px) scale(1.2); opacity: 0.7; }
            100% { transform: translateY(200px) scale(1); opacity: 0; }
        }

        @keyframes caerRecarga {
            0% { transform: translateY(-10px) scale(0.8); opacity: 0; }
            50% { transform: translateY(100px) scale(1.1); opacity: 1; }
            100% { transform: translateY(200px) scale(1); opacity: 0; }
        }

        .controles-animacion {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💧 Sistema de Flujo de Agua</h1>
            <p>Simulación del ciclo DO-WHILE con animación integrada por ciclo</p>
        </div>
        
        <div class="info-box">
            <h3>💡 Animación Integrada por Ciclo</h3>
            <p>Cada ciclo ahora muestra automáticamente una animación que representa:</p>
            <ul>
                <li><span style="color: #4fc3f7;">🔵 Nivel inicial de agua</span></li>
                <li><span style="color: #ff6b6b;">🔴 Consumo del ciclo (gotas rojas)</span></li>
                <li><span style="color: #4caf50;">🟢 Recargas naturales (gotas verdes)</span></li>
                <li><span style="color: #4fc3f7;">🔵 Nivel final después del ciclo</span></li>
            </ul>
        </div>
        
        <div class="form-container">
            <h2 style="color: #29b6f6; margin-bottom: 20px;">🎮 Simulador de Consumo</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="agua_inicial">Agua disponible inicialmente (litros):</label>
                    <select name="agua_inicial" id="agua_inicial" required>
                        <option value="">-- Selecciona cantidad --</option>
                        <option value="50">50L (Tanque pequeño)</option>
                        <option value="100">100L (Tanque familiar)</option>
                        <option value="200">200L (Tanque grande)</option>
                        <option value="500">500L (Cisterna)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="consumo_ciclo">Consumo por ciclo (litros):</label>
                    <select name="consumo_ciclo" id="consumo_ciclo" required>
                        <option value="">-- Selecciona consumo --</option>
                        <option value="5">5L (Vaso de agua)</option>
                        <option value="10">10L (Botellón)</option>
                        <option value="15">15L (Uso moderado)</option>
                        <option value="25">25L (Uso intensivo)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="tipo_sistema">Tipo de sistema:</label>
                    <select name="tipo_sistema" id="tipo_sistema" required>
                        <option value="">-- Selecciona sistema --</option>
                        <option value="basico">💧 Sistema básico con nivel mínimo</option>
                        <option value="recarga">🔄 Sistema con recarga natural</option>
                    </select>
                </div>
                
                <div class="form-group" id="nivel_minimo_group" style="display: none;">
                    <label for="nivel_minimo">Nivel mínimo de agua (litros):</label>
                    <select name="nivel_minimo" id="nivel_minimo">
                        <option value="5">5L</option>
                        <option value="10">10L</option>
                        <option value="15">15L</option>
                        <option value="20">20L</option>
                    </select>
                </div>
                
                <button type="submit" style="width: 100%;">💦 Iniciar Simulación</button>
                <button type="button" onclick="window.location.href=window.location.href" class="btn-secundario" style="width: 100%;">
                    🔄 Reiniciar Simulador
                </button>
            </form>
        </div>
        
        <?php
        // Procesar formulario de simulación
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $aguaInicial = intval($_POST['agua_inicial']);
            $consumoCiclo = intval($_POST['consumo_ciclo']);
            $tipoSistema = $_POST['tipo_sistema'];
            $nivelMinimo = isset($_POST['nivel_minimo']) ? intval($_POST['nivel_minimo']) : 10;
            
            if ($aguaInicial > 0 && $consumoCiclo > 0 && !empty($tipoSistema)) {
                
                $sistema = new SistemaAgua($aguaInicial, $nivelMinimo);
                
                echo "<div class='operacion-box'>";
                echo "<h2>🌀 SIMULACIÓN DO-WHILE EN EJECUCIÓN</h2>";
                
                switch ($tipoSistema) {
                    case 'basico':
                        $sistema->consumirAgua($consumoCiclo);
                        break;
                    case 'recarga':
                        $sistema->sistemaConRecarga($consumoCiclo, 35);
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
            // Mostrar instrucciones
            echo "<div class='instrucciones-box'>";
            echo "<h3>📋 Instrucciones de Uso</h3>";
            echo "<p><strong>Novedad:</strong> Cada ciclo ahora muestra automáticamente una animación que representa el consumo y cambios en el nivel de agua.</p>";
            echo "<ol>";
            echo "<li>Configura los parámetros del sistema</li>";
            echo "<li>Inicia la simulación</li>";
            echo "<li>Observa cómo cada ciclo muestra su propia animación</li>";
            echo "<li>Las gotas rojas representan consumo, las verdes recargas</li>";
            echo "</ol>";
            echo "</div>";
        }
        ?>
    </div>

    <script>
        // Mostrar/ocultar campo de nivel mínimo
        document.getElementById('tipo_sistema').addEventListener('change', function() {
            const nivelMinimoGroup = document.getElementById('nivel_minimo_group');
            nivelMinimoGroup.style.display = this.value === 'basico' ? 'block' : 'none';
        });

        // Función para iniciar animación del ciclo
        function iniciarAnimacionCiclo(ciclo, aguaInicial, consumo, aguaRestante, recarga, aguaMaxima) {
            const nivelAgua = document.getElementById(`nivel-inicial-${ciclo}`);
            const gotasContainer = document.getElementById(`gotas-${ciclo}`);
            const indicador = nivelAgua.querySelector('.indicador-nivel-animacion');
            
            // Calcular porcentajes
            const porcentajeInicial = (aguaInicial / aguaMaxima) * 100;
            const porcentajeFinal = (aguaRestante / aguaMaxima) * 100;
            
            // Configurar nivel inicial
            nivelAgua.style.height = porcentajeInicial + '%';
            indicador.textContent = aguaInicial + 'L';
            
            // Limpiar gotas anteriores
            gotasContainer.innerHTML = '';
            
            // Crear gotas de consumo (rojas)
            for (let i = 0; i < Math.min(consumo * 2, 8); i++) {
                const gota = document.createElement('div');
                gota.className = 'gota-consumo';
                gota.style.left = (20 + Math.random() * 60) + '%';
                gota.style.animationDelay = (i * 0.2) + 's';
                gotasContainer.appendChild(gota);
            }
            
            // Animar consumo
            setTimeout(() => {
                nivelAgua.style.height = Math.max(0, porcentajeFinal) + '%';
                indicador.textContent = aguaRestante + 'L';
                
                // Si hay recarga, animarla después del consumo
                if (recarga > 0) {
                    setTimeout(() => {
                        // Crear gotas de recarga (verdes)
                        for (let i = 0; i < Math.min(recarga, 6); i++) {
                            const gota = document.createElement('div');
                            gota.className = 'gota-recarga';
                            gota.style.left = (10 + Math.random() * 80) + '%';
                            gota.style.animationDelay = (i * 0.3) + 's';
                            gotasContainer.appendChild(gota);
                        }
                        
                        // Actualizar nivel final con recarga
                        const porcentajeConRecarga = ((aguaRestante + recarga) / aguaMaxima) * 100;
                        nivelAgua.style.height = Math.min(100, porcentajeConRecarga) + '%';
                        indicador.textContent = (aguaRestante + recarga) + 'L';
                        
                    }, 800);
                }
            }, 500);
        }

        // Inicializar el formulario
        document.addEventListener('DOMContentLoaded', function() {
            // Configuración inicial si hay valores en el formulario
            <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            document.getElementById('agua_inicial').value = '<?php echo $_POST['agua_inicial'] ?? ''; ?>';
            document.getElementById('consumo_ciclo').value = '<?php echo $_POST['consumo_ciclo'] ?? ''; ?>';
            document.getElementById('tipo_sistema').value = '<?php echo $_POST['tipo_sistema'] ?? ''; ?>';
            <?php if (isset($_POST['nivel_minimo'])): ?>
            document.getElementById('nivel_minimo').value = '<?php echo $_POST['nivel_minimo'] ?? ''; ?>';
            <?php endif; ?>
            
            if (document.getElementById('tipo_sistema').value === 'basico') {
                document.getElementById('nivel_minimo_group').style.display = 'block';
            }
            <?php endif; ?>
        });
    </script>
</body>
</html>