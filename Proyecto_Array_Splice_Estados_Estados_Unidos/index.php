<?php
require_once 'funciones.php';

// Configuración
$json_file = 'estados.json';

// Cargar estados
$estados = cargarEstados($json_file);
$resultado = "";
$operacion = "";

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'];
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;
    $nuevo_estado = isset($_POST['nuevo_estado']) ? trim($_POST['nuevo_estado']) : "";
    $nueva_region = isset($_POST['nueva_region']) ? trim($_POST['nueva_region']) : "Oeste";
    $nueva_capital = isset($_POST['nueva_capital']) ? trim($_POST['nueva_capital']) : "Ciudad Principal";
    $nueva_poblacion = isset($_POST['nueva_poblacion']) ? intval($_POST['nueva_poblacion']) : 1000000;
    
    switch ($accion) {
        case 'eliminar':
            $estados_eliminados = array_splice($estados, $offset, $cantidad);
            $operacion = "eliminar";
            $resultado = "✅ Se eliminaron " . count($estados_eliminados) . " estado(s) desde la posición $offset";
            break;
            
        case 'insertar':
            if (!empty($nuevo_estado)) {
                $nuevo = [
                    "id" => generarNuevoId($estados),
                    "nombre" => $nuevo_estado,
                    "region" => $nueva_region,
                    "capital" => $nueva_capital,
                    "poblacion" => $nueva_poblacion
                ];
                array_splice($estados, $offset, 0, [$nuevo]);
                $operacion = "insertar";
                $resultado = "✅ Se insertó '$nuevo_estado' en la posición $offset";
            }
            break;
            
        case 'reemplazar':
            if (!empty($nuevo_estado)) {
                $nuevo = [
                    "id" => generarNuevoId($estados),
                    "nombre" => $nuevo_estado,
                    "region" => $nueva_region,
                    "capital" => $nueva_capital,
                    "poblacion" => $nueva_poblacion
                ];
                $estados_eliminados = array_splice($estados, $offset, $cantidad, [$nuevo]);
                $operacion = "reemplazar";
                $resultado = "✅ Se reemplazaron $cantidad estado(s) por '$nuevo_estado' desde la posición $offset";
            }
            break;
            
        case 'reset':
            $estados = restaurarEstadosOriginales($json_file);
            $operacion = "reset";
            $resultado = "🔄 Se restauraron los 50 estados originales de USA";
            break;
    }
    
    // Guardar cambios
    guardarEstados($json_file, $estados);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Array Splice - Estados USA</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-content">
                <h1>🧩 Array Splice Visual</h1>
                <p class="subtitle">Aprende cómo funciona array_splice() con estados de USA</p>
            </div>
        </header>

        <div class="main-content">
            <!-- Panel de Estados -->
            <div class="panel estados-panel">
                <div class="panel-header">
                    <h2>🏛️ Estados Actuales</h2>
                    <div class="contador">Total: <?php echo count($estados); ?> estados</div>
                </div>
                
                <div class="panel-actions">
                    <form method="post" class="reset-form">
                        <input type="hidden" name="accion" value="reset">
                        <button type="submit" class="btn btn-reset" onclick="return confirm('¿Estás seguro de que quieres restaurar los 50 estados originales? Se perderán todos los cambios.')">
                            🔄 Restaurar 50 Estados Originales
                        </button>
                    </form>
                </div>
                
                <div class="filtros">
                    <button class="filtro-btn active" data-filtro="todos">Todos</button>
                    <button class="filtro-btn" data-filtro="oeste">Oeste</button>
                    <button class="filtro-btn" data-filtro="sur">Sur</button>
                    <button class="filtro-btn" data-filtro="noreste">Noreste</button>
                    <button class="filtro-btn" data-filtro="medio-oeste">Medio Oeste</button>
                </div>
                
                <div class="estados-grid">
                    <?php foreach ($estados as $index => $estado): ?>
                        <div class="estado-card" data-index="<?php echo $index; ?>" data-region="<?php echo strtolower(str_replace(' ', '-', $estado['region'])); ?>">
                            <div class="estado-header">
                                <span class="indice">#<?php echo $index; ?></span>
                                <span class="id">ID: <?php echo $estado['id']; ?></span>
                            </div>
                            <h3 class="estado-nombre"><?php echo htmlspecialchars($estado['nombre']); ?></h3>
                            <div class="estado-region <?php echo strtolower(str_replace(' ', '-', $estado['region'])); ?>">
                                <?php echo htmlspecialchars($estado['region']); ?>
                            </div>
                            <?php if (isset($estado['capital'])): ?>
                                <div class="estado-capital">
                                    <small>🏛️ <?php echo htmlspecialchars($estado['capital']); ?></small>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($estado['poblacion'])): ?>
                                <div class="estado-poblacion">
                                    <small>👥 <?php echo formatearPoblacion($estado['poblacion']); ?> hab.</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Panel de Control -->
            <div class="panel control-panel">
                <h2>🎮 Control de Array Splice</h2>
                
                <?php if (!empty($resultado)): ?>
                    <div class="resultado <?php echo $operacion; ?>">
                        <?php echo $resultado; ?>
                    </div>
                <?php endif; ?>

                <div class="tabs">
                    <button class="tab-button active" data-tab="eliminar">🗑️ Eliminar</button>
                    <button class="tab-button" data-tab="insertar">➕ Insertar</button>
                    <button class="tab-button" data-tab="reemplazar">🔄 Reemplazar</button>
                </div>

                <!-- Formulario Eliminar -->
                <form method="post" class="tab-content active" id="eliminar">
                    <input type="hidden" name="accion" value="eliminar">
                    
                    <div class="form-group">
                        <label>📍 Posición inicial (offset):</label>
                        <input type="number" name="offset" min="0" max="<?php echo max(0, count($estados) - 1); ?>" 
                               value="0" required class="offset-input">
                        <small>Desde 0 hasta <?php echo max(0, count($estados) - 1); ?></small>
                    </div>
                    
                    <div class="form-group">
                        <label>🔢 Cantidad a eliminar (length):</label>
                        <input type="number" name="cantidad" min="1" max="<?php echo count($estados); ?>" value="1" required>
                        <small>Máximo: <?php echo count($estados); ?> estados</small>
                    </div>
                    
                    <button type="submit" class="btn btn-eliminar">🗑️ Eliminar Estados</button>
                </form>

                <!-- Formulario Insertar -->
                <form method="post" class="tab-content" id="insertar">
                    <input type="hidden" name="accion" value="insertar">
                    
                    <div class="form-group">
                        <label>📍 Posición para insertar (offset):</label>
                        <input type="number" name="offset" min="0" max="<?php echo count($estados); ?>" 
                               value="0" required class="offset-input">
                        <small>Desde 0 hasta <?php echo count($estados); ?> (al final)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>🏛️ Nombre del nuevo estado:</label>
                        <input type="text" name="nuevo_estado" placeholder="Ej: Montana" required>
                    </div>
                    
                    <div class="form-group">
                        <label>🗺️ Región:</label>
                        <select name="nueva_region">
                            <option value="Oeste">Oeste</option>
                            <option value="Sur">Sur</option>
                            <option value="Noreste">Noreste</option>
                            <option value="Medio Oeste">Medio Oeste</option>
                            <option value="Noroeste">Noroeste</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>🏢 Capital:</label>
                        <input type="text" name="nueva_capital" placeholder="Ej: Ciudad Principal" value="Ciudad Principal">
                    </div>
                    
                    <div class="form-group">
                        <label>👥 Población:</label>
                        <input type="number" name="nueva_poblacion" min="1" max="50000000" value="1000000">
                    </div>
                    
                    <button type="submit" class="btn btn-insertar">➕ Insertar Estado</button>
                </form>

                <!-- Formulario Reemplazar -->
                <form method="post" class="tab-content" id="reemplazar">
                    <input type="hidden" name="accion" value="reemplazar">
                    
                    <div class="form-group">
                        <label>📍 Posición inicial (offset):</label>
                        <input type="number" name="offset" min="0" max="<?php echo max(0, count($estados) - 1); ?>" 
                               value="0" required class="offset-input">
                        <small>Desde 0 hasta <?php echo max(0, count($estados) - 1); ?></small>
                    </div>
                    
                    <div class="form-group">
                        <label>🔢 Cantidad a reemplazar:</label>
                        <input type="number" name="cantidad" min="1" max="<?php echo count($estados); ?>" value="1" required>
                        <small>Máximo: <?php echo count($estados); ?> estados</small>
                    </div>
                    
                    <div class="form-group">
                        <label>🏛️ Nuevo estado:</label>
                        <input type="text" name="nuevo_estado" placeholder="Ej: Montana" required>
                    </div>
                    
                    <div class="form-group">
                        <label>🗺️ Región:</label>
                        <select name="nueva_region">
                            <option value="Oeste">Oeste</option>
                            <option value="Sur">Sur</option>
                            <option value="Noreste">Noreste</option>
                            <option value="Medio Oeste">Medio Oeste</option>
                            <option value="Noroeste">Noroeste</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>🏢 Capital:</label>
                        <input type="text" name="nueva_capital" placeholder="Ej: Ciudad Principal" value="Ciudad Principal">
                    </div>
                    
                    <div class="form-group">
                        <label>👥 Población:</label>
                        <input type="number" name="nueva_poblacion" min="1" max="50000000" value="1000000">
                    </div>
                    
                    <button type="submit" class="btn btn-reemplazar">🔄 Reemplazar Estados</button>
                </form>

                <!-- Información Educativa -->
                <div class="info-educativa">
                    <h3>📚 ¿Qué hace array_splice()?</h3>
                    <div class="sintaxis">
                        <code>array_splice($array, $offset, $length, $replacement)</code>
                    </div>
                    <div class="ejemplos">
                        <div class="ejemplo">
                            <strong>Eliminar:</strong> <code>array_splice($estados, 2, 3)</code>
                            <span>→ Elimina 3 estados desde la posición 2</span>
                        </div>
                        <div class="ejemplo">
                            <strong>Insertar:</strong> <code>array_splice($estados, 1, 0, [$nuevo])</code>
                            <span>→ Inserta en posición 1 sin eliminar</span>
                        </div>
                        <div class="ejemplo">
                            <strong>Reemplazar:</strong> <code>array_splice($estados, 0, 2, [$nuevo1, $nuevo2])</code>
                            <span>→ Reemplaza 2 estados por nuevos</span>
                        </div>
                    </div>
                    
                    <div class="consejos">
                        <h4>💡 Consejos para probar:</h4>
                        <ul>
                            <li>Usa <strong>offset 0</strong> para operar al inicio</li>
                            <li>Usa <strong>offset alto</strong> para operar al final</li>
                            <li>Prueba <strong>length = 0</strong> para solo insertar</li>
                            <li>Prueba <strong>reemplazar con múltiples estados</strong></li>
                            <li>¡Usa el botón <strong>Restaurar</strong> para volver al inicio!</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sistema de pestañas
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                // Remover activo de todos
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                
                // Activar actual
                button.classList.add('active');
                document.getElementById(button.dataset.tab).classList.add('active');
            });
        });

        // Resaltar estado al cambiar offset
        function resaltarEstado(index) {
            document.querySelectorAll('.estado-card').forEach(card => {
                card.classList.remove('resaltado');
                if (parseInt(card.dataset.index) === index) {
                    card.classList.add('resaltado');
                    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }

        document.querySelectorAll('.offset-input').forEach(input => {
            input.addEventListener('input', function() {
                const index = parseInt(this.value) || 0;
                resaltarEstado(index);
            });
            
            // Resaltar al cargar la página
            const initialIndex = parseInt(input.value) || 0;
            resaltarEstado(initialIndex);
        });

        // Sistema de filtros por región
        document.querySelectorAll('.filtro-btn').forEach(button => {
            button.addEventListener('click', () => {
                // Remover activo de todos
                document.querySelectorAll('.filtro-btn').forEach(btn => btn.classList.remove('active'));
                // Activar actual
                button.classList.add('active');
                
                const filtro = button.dataset.filtro;
                document.querySelectorAll('.estado-card').forEach(card => {
                    if (filtro === 'todos' || card.dataset.region === filtro) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Mostrar información del estado al hacer hover
        document.querySelectorAll('.estado-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                const index = this.dataset.index;
                document.querySelectorAll('input.offset-input').forEach(input => {
                    input.title = `Estado actual: ${index}`;
                });
            });
        });

        // Actualizar máximos de los inputs cuando cambie el número de estados
        function actualizarMaximos() {
            const totalEstados = <?php echo count($estados); ?>;
            const maxOffset = Math.max(0, totalEstados - 1);
            const maxCantidad = totalEstados;
            
            document.querySelectorAll('input[name="offset"]').forEach(input => {
                input.max = maxOffset;
                input.nextElementSibling.innerHTML = `Desde 0 hasta ${maxOffset}`;
            });
            
            document.querySelectorAll('input[name="cantidad"]').forEach(input => {
                input.max = maxCantidad;
                input.nextElementSibling.innerHTML = `Máximo: ${maxCantidad} estados`;
            });
            
            // Para insertar, el offset puede ser hasta el final
            document.querySelector('input[name="offset"][form="insertar"]').max = totalEstados;
        }

        // Ejecutar al cargar
        document.addEventListener('DOMContentLoaded', function() {
            actualizarMaximos();
            resaltarEstado(0);
        });
    </script>
</body>
</html>