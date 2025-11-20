<?php
require_once 'api/api_handler.php';
$api = new ApiHandler();

// Procesar formularios - CORREGIDO
$resultado = null;
$accion_realizada = null;
$mensaje_error = null;
$mensaje_exito = null;

// Excluir la acción de actualizar_registro del procesamiento normal si es AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_POST && isset($_POST['accion']) && !($isAjax && $_POST['accion'] === 'actualizar_registro')) {
    $accion_realizada = $_POST['accion'];
    switch ($_POST['accion']) {
        case 'predecir':
            $resultado = $api->predecirPeso(
                $_POST['calorias'] ?? 0,
                $_POST['edad'] ?? 0,
                $_POST['altura'] ?? 0,
                $_POST['actividad'] ?? 'moderada'
            );
            break;
            
        case 'agregar_registro':
            $nuevo_registro = [
                'fecha' => $_POST['fecha'] ?? date('Y-m-d'),
                'calorias' => $_POST['calorias_registro'] ?? 0,
                'peso' => $_POST['peso'] ?? 0,
                'edad' => $_POST['edad_registro'] ?? 0,
                'altura' => $_POST['altura_registro'] ?? 0,
                'actividad' => $_POST['actividad_registro'] ?? 'moderada'
            ];
            $resultado = $api->agregarRegistro($nuevo_registro);
            break;
                
        case 'eliminar_registro':
            $resultado = $api->eliminarRegistro($_POST['id'] ?? 0);
            break;
    }
    
    // Manejar mensajes de resultado
    if (isset($resultado['error'])) {
        $mensaje_error = $resultado['error'];
    } elseif (isset($resultado['mensaje'])) {
        $mensaje_exito = $resultado['mensaje'];
    }
}

// Obtener datos iniciales
$estadisticas = $api->getEstadisticas();
$registros_data = $api->getRegistros();
$registros = $registros_data['registros'] ?? [];
$error_registros = $registros_data['error'] ?? null;

// Verificar conexión con backend
$conexion_backend = $api->checkConnection();
$estado_conexion = isset($conexion_backend['status']) && $conexion_backend['status'] === 'ok' ? 'connected' : 'disconnected';
$mensaje_conexion = $conexion_backend['message'] ?? 'Error de conexión';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthPredict Pro - Sistema Avanzado de Predicción de Peso</title>
    <meta name="description" content="Plataforma profesional con Machine Learning para control y predicción de peso corporal">
    
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="#" class="nav-logo">
                <i class="fas fa-heartbeat"></i>
                <span>HealthPredict Pro</span>
            </a>
            <div class="nav-links">
                <a href="#dashboard" class="nav-link active">Dashboard</a>
                <a href="#analytics" class="nav-link">Analíticas</a>
                <a href="#registros" class="nav-link">Registros</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Dashboard Header -->
        <header class="dashboard-header">
            <div class="header-content">
                <h1>Sistema de Predicción de Peso Corporal</h1>
                <p>Plataforma profesional con Machine Learning para control y análisis predictivo de peso</p>
            </div>
            <div class="connection-status <?php echo $estado_conexion; ?>">
                <i class="fas fa-<?php echo $estado_conexion === 'connected' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $estado_conexion === 'connected' ? 'Backend Conectado' : 'Backend Desconectado'; ?>
            </div>
        </header>

        <!-- Mostrar mensajes de éxito/error -->
        <?php if ($mensaje_error): ?>
            <div class="notification error fade-in" style="position: relative; margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($mensaje_error); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($mensaje_exito): ?>
            <div class="notification success fade-in" style="position: relative; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($mensaje_exito); ?></span>
            </div>
        <?php endif; ?>

        <!-- Stats Grid Moderno -->
        <section class="stats-grid-modern">
            <div class="stat-card-modern">
                <div class="stat-header">
                    <div class="stat-content">
                        <h3>Total Registros</h3>
                        <div class="stat-value"><?php echo $estadisticas['total_registros'] ?? 0; ?></div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-database"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card-modern">
                <div class="stat-header">
                    <div class="stat-content">
                        <h3>Calorías Promedio</h3>
                        <div class="stat-value"><?php echo $estadisticas['promedio_calorias'] ?? 0; ?> <small>kcal</small></div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card-modern">
                <div class="stat-header">
                    <div class="stat-content">
                        <h3>Peso Promedio</h3>
                        <div class="stat-value"><?php echo $estadisticas['promedio_peso'] ?? 0; ?> <small>kg</small></div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-weight-scale"></i>
                    </div>
                </div>
            </div>
        </section>

        <div class="main-grid">
            <!-- Sidebar de Herramientas -->
            <div class="tools-sidebar">
                <!-- Predicción Rápida -->
                <div class="tool-card">
                    <h3><i class="fas fa-calculator"></i> Predicción Rápida</h3>
                    <form method="POST" class="prediction-form">
                        <input type="hidden" name="accion" value="predecir">
                        
                        <div class="form-group">
                            <label>Calorías Diarias</label>
                            <input type="number" name="calorias" class="form-control" required 
                                   value="<?php echo isset($_POST['calorias']) ? htmlspecialchars($_POST['calorias']) : '2500'; ?>" min="500" max="10000">
                        </div>
                        
                        <div class="form-group">
                            <label>Edad</label>
                            <input type="number" name="edad" class="form-control" required 
                                   value="<?php echo isset($_POST['edad']) ? htmlspecialchars($_POST['edad']) : '30'; ?>" min="10" max="120">
                        </div>
                        
                        <div class="form-group">
                            <label>Altura (cm)</label>
                            <input type="number" name="altura" class="form-control" required 
                                   value="<?php echo isset($_POST['altura']) ? htmlspecialchars($_POST['altura']) : '175'; ?>" min="100" max="250">
                        </div>
                        
                        <div class="form-group">
                            <label>Nivel de Actividad</label>
                            <select name="actividad" class="form-control" required>
                                <option value="baja" <?php echo (isset($_POST['actividad']) && $_POST['actividad'] === 'baja') ? 'selected' : ''; ?>>Baja</option>
                                <option value="moderada" <?php echo (!isset($_POST['actividad']) || (isset($_POST['actividad']) && $_POST['actividad'] === 'moderada')) ? 'selected' : ''; ?>>Moderada</option>
                                <option value="alta" <?php echo (isset($_POST['actividad']) && $_POST['actividad'] === 'alta') ? 'selected' : ''; ?>>Alta</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-bolt"></i> Predecir Cambio de Peso
                        </button>
                    </form>
                    
                    <?php if ($accion_realizada === 'predecir'): ?>
                        <div class="prediction-result <?php echo isset($resultado['error']) ? 'error' : 'success'; ?>">
                            <?php if (isset($resultado['error'])): ?>
                                <h4>Error en Predicción</h4>
                                <p class="text-danger"><?php echo htmlspecialchars($resultado['error']); ?></p>
                            <?php else: ?>
                                <h4>Resultado de Predicción</h4>
                                <div class="prediction-details">
                                    <div class="prediction-value text-success">
                                        <i class="fas fa-chart-line"></i>
                                        <?php echo $resultado['cambio_peso_predicho'] ?? 0; ?> kg
                                    </div>
                                    <p class="prediction-note"><?php echo htmlspecialchars($resultado['recomendacion'] ?? ''); ?></p>
                                    <div class="prediction-meta">
                                        <small class="text-muted">
                                            Basado en <?php echo $resultado['calorias_ingeridas'] ?? 0; ?> calorías diarias
                                        </small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Nuevo Registro -->
                <div class="tool-card">
                    <h3><i class="fas fa-plus-circle"></i> Nuevo Registro</h3>
                    <form method="POST" class="compact-form">
                        <input type="hidden" name="accion" value="agregar_registro">
                        
                        <div class="form-group">
                            <label>Fecha</label>
                            <input type="date" name="fecha" class="form-control" required 
                                   value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Calorías</label>
                                <input type="number" name="calorias_registro" class="form-control" required 
                                       value="2000" min="0">
                            </div>
                            <div class="form-group">
                                <label>Peso (kg)</label>
                                <input type="number" step="0.1" name="peso" class="form-control" required 
                                       value="70.0" min="0" step="0.1">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Edad</label>
                                <input type="number" name="edad_registro" class="form-control" required 
                                       value="25" min="1" max="120">
                            </div>
                            <div class="form-group">
                                <label>Altura (cm)</label>
                                <input type="number" name="altura_registro" class="form-control" required 
                                       value="170" min="50" max="250">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Nivel de Actividad</label>
                            <select name="actividad_registro" class="form-control" required>
                                <option value="baja">Baja</option>
                                <option value="moderada" selected>Moderada</option>
                                <option value="alta">Alta</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-save"></i> Guardar Registro
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="main-content-enhanced">
                <!-- Sección de Gráficos -->
                <section class="graficos-section" id="analytics">
                    <div class="graficos-header">
                        <div class="section-header">
                            <div>
                                <h2>Análisis Visual</h2>
                                <p>Visualización avanzada de datos de salud y métricas</p>
                            </div>
                        </div>
                    </div>

                    <div class="graficos-controls">
                        <div class="graficos-buttons-grid">
                            <button class="grafico-btn" data-tipo="area">
                                <div class="btn-icon">
                                    <i class="fas fa-chart-area"></i>
                                </div>
                                <div class="btn-content">
                                    <span class="btn-title">Evolución del Peso</span>
                                    <span class="btn-subtitle">Tendencia temporal</span>
                                </div>
                            </button>
                            
                            <button class="grafico-btn" data-tipo="radar">
                                <div class="btn-icon">
                                    <i class="fas fa-bullseye"></i>
                                </div>
                                <div class="btn-content">
                                    <span class="btn-title">Perfil de Métricas</span>
                                    <span class="btn-subtitle">Análisis comparativo</span>
                                </div>
                            </button>
                            
                            <button class="grafico-btn" data-tipo="barras">
                                <div class="btn-icon">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <div class="btn-content">
                                    <span class="btn-title">Análisis por Actividad</span>
                                    <span class="btn-subtitle">Comparación por nivel</span>
                                </div>
                            </button>
                            
                            <button class="grafico-btn" data-tipo="pastel">
                                <div class="btn-icon">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                                <div class="btn-content">
                                    <span class="btn-title">Distribución</span>
                                    <span class="btn-subtitle">Proporciones de actividad</span>
                                </div>
                            </button>
                            
                            <button class="grafico-btn" data-tipo="lineas">
                                <div class="btn-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="btn-content">
                                    <span class="btn-title">Tendencias</span>
                                    <span class="btn-subtitle">Análisis longitudinal</span>
                                </div>
                            </button>
                        </div>
                    </div>

                    <div class="grafico-display">
                        <div id="graficoContainer">
                            <div class="no-grafico">
                                <i class="fas fa-chart-bar"></i>
                                <h4>Selecciona un tipo de gráfico</h4>
                                <p class="text-muted">Haz clic en los botones superiores para cargar las visualizaciones</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Sección de Registros -->
                <section class="management-section" id="registros">
                    <div class="table-container">
                        <div class="table-header">
                            <div class="section-header">
                                <div>
                                    <h2>Gestión de Registros</h2>
                                    <p>Administra tu historial de datos de salud</p>
                                </div>
                            </div>
                            <div class="record-count">
                                <i class="fas fa-database"></i>
                                <?php echo count($registros); ?> registros
                            </div>
                        </div>

                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Calorías</th>
                                        <th>Peso</th>
                                        <th>Edad</th>
                                        <th>Altura</th>
                                        <th>Actividad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($registros)): ?>
                                        <?php foreach ($registros as $registro): ?>
                                            <tr data-id="<?php echo $registro['id']; ?>">
                                                <td class="font-mono"><?php echo $registro['id']; ?></td>
                                                <td><?php echo htmlspecialchars($registro['fecha']); ?></td>
                                                <td class="text-warning font-semibold"><?php echo $registro['calorias']; ?> kcal</td>
                                                <td class="text-info font-semibold"><?php echo $registro['peso']; ?> kg</td>
                                                <td><?php echo $registro['edad']; ?> años</td>
                                                <td><?php echo $registro['altura']; ?> cm</td>
                                                <td>
                                                    <span class="badge badge-<?php 
                                                        echo $registro['actividad'] === 'alta' ? 'success' : 
                                                             ($registro['actividad'] === 'moderada' ? 'warning' : 'danger'); 
                                                    ?>">
                                                        <i class="fas fa-<?php 
                                                            echo $registro['actividad'] === 'alta' ? 'fire' : 
                                                                 ($registro['actividad'] === 'moderada' ? 'running' : 'walking'); 
                                                        ?>"></i>
                                                        <?php echo ucfirst($registro['actividad']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <button class="btn-icon btn-edit" 
                                                                onclick="app.editRegistro(<?php echo $registro['id']; ?>)" 
                                                                data-tooltip="Editar registro">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn-icon btn-delete" 
                                                                onclick="app.deleteRegistro(<?php echo $registro['id']; ?>)" 
                                                                data-tooltip="Eliminar registro">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                <div style="padding: 40px;">
                                                    <i class="fas fa-database" style="font-size: 3rem; margin-bottom: 15px;"></i>
                                                    <h4>No hay registros disponibles</h4>
                                                    <p class="text-muted">Agrega tu primer registro usando el formulario lateral</p>
                                                    <?php if ($error_registros): ?>
                                                        <p class="text-danger">Error: <?php echo htmlspecialchars($error_registros); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>