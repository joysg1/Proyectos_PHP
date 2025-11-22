<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_TITLE; ?> - Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="logo">CellFit Analytics</div>
            <ul class="nav-links">
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="charts.php">Gráficos</a></li>
                <li><a href="trends.php">Tendencias</a></li>
                <li><a href="analysis.php">Análisis</a></li>
                <li><a href="education.php">Educación</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Sistema de Análisis de Producción Celular</h1>
                <button class="btn btn-primary" onclick="system.trainMLModel()" id="trainModel">
                    <i class="fas fa-brain"></i>
                    Entrenar Modelo ML
                </button>
            </div>
            
            <!-- Estadísticas Rápidas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <div class="stat-value" id="totalRecords">0</div>
                    <div class="stat-label">Total Registros</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dna"></i>
                    </div>
                    <div class="stat-value" id="totalCells">0</div>
                    <div class="stat-label">Células Producidas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value" id="avgDuration">0</div>
                    <div class="stat-label">Duración Promedio</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="stat-value" id="avgIntensity">0</div>
                    <div class="stat-label">Intensidad Promedio</div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab active" data-tab="data">Datos</button>
                <button class="tab" data-tab="add">Agregar Registro</button>
                <button class="tab" data-tab="analysis">Análisis Rápido</button>
            </div>

            <!-- Tab: Datos -->
            <div id="data-tab" class="tab-content active">
                <div class="table-header">
                    <h3>Registros de Actividad Física</h3>
                    <div class="table-actions">
                        <button class="btn btn-primary btn-sm" onclick="system.loadInitialData()">
                            <i class="fas fa-sync-alt"></i>
                            Actualizar
                        </button>
                    </div>
                </div>
                <div class="table-container">
                    <table class="data-table" id="dataTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Actividad</th>
                                <th>Duración (min)</th>
                                <th>Intensidad</th>
                                <th>Células Producidas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargan via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Agregar Registro -->
            <div id="add-tab" class="tab-content">
                <form id="recordForm" class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Fecha</label>
                        <input type="date" class="form-control" name="date" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Tipo de Actividad</label>
                        <select class="form-control" name="activity_type" required>
                            <option value="Running">Running</option>
                            <option value="Swimming">Swimming</option>
                            <option value="Cycling">Cycling</option>
                            <option value="Weight Training">Weight Training</option>
                            <option value="Yoga">Yoga</option>
                            <option value="HIIT">HIIT</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Duración (minutos)</label>
                        <input type="number" class="form-control" name="duration_minutes" step="0.1" min="10" max="180" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Intensidad (0.1 - 1.0)</label>
                        <input type="number" class="form-control" name="intensity" min="0.1" max="1.0" step="0.1" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Edad</label>
                        <input type="number" class="form-control" name="age" min="18" max="80" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Género</label>
                        <select class="form-control" name="gender" required>
                            <option value="Male">Masculino</option>
                            <option value="Female">Femenino</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Ritmo Cardíaco Promedio</label>
                        <input type="number" class="form-control" name="heart_rate_avg" min="60" max="200" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Calorías Quemadas</label>
                        <input type="number" class="form-control" name="calories_burned" min="50" max="1000" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Horas de Sueño</label>
                        <input type="number" class="form-control" name="sleep_hours" step="0.1" min="4" max="12" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Hidratación (Litros)</label>
                        <input type="number" class="form-control" name="hydration_liters" step="0.1" min="0.5" max="5" required>
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Agregar Registro
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab: Análisis Rápido -->
            <div id="analysis-tab" class="tab-content">
                <div class="quick-analysis-grid">
                    <div class="analysis-card">
                        <div class="analysis-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="analysis-content">
                            <h4>Eficiencia Promedio</h4>
                            <div class="analysis-value" id="avgEfficiency">0/min</div>
                            <p>Células producidas por minuto</p>
                        </div>
                    </div>
                    
                    <div class="analysis-card">
                        <div class="analysis-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="analysis-content">
                            <h4>Actividad Top</h4>
                            <div class="analysis-value" id="topActivity">-</div>
                            <p>Mayor producción celular</p>
                        </div>
                    </div>
                    
                    <div class="analysis-card">
                        <div class="analysis-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="analysis-content">
                            <h4>Tendencia</h4>
                            <div class="analysis-value" id="productionTrend">+0%</div>
                            <p>Crecimiento mensual</p>
                        </div>
                    </div>
                </div>
                
                <div class="recommendations-box">
                    <h4><i class="fas fa-lightbulb"></i> Recomendación del Sistema</h4>
                    <p id="systemRecommendation">Analizando datos para recomendaciones personalizadas...</p>
                </div>

                <div class="quick-actions">
                    <h4><i class="fas fa-rocket"></i> Acciones Rápidas</h4>
                    <div class="action-buttons-grid">
                        <button class="btn btn-primary" onclick="system.navigateTo('charts.php')">
                            <i class="fas fa-chart-bar"></i>
                            Ver Gráficos
                        </button>
                        <button class="btn btn-secondary" onclick="system.navigateTo('analysis.php')">
                            <i class="fas fa-chart-line"></i>
                            Análisis Detallado
                        </button>
                        <button class="btn btn-info" onclick="system.navigateTo('trends.php')">
                            <i class="fas fa-trending-up"></i>
                            Tendencias
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Importancia de Características -->
    <div id="featureModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Importancia de Características - Modelo ML</h2>
            <p>Las características más importantes para predecir la producción celular:</p>
            <div id="featureImportance">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>