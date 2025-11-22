<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_TITLE; ?> - Tendencias</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="logo">CellFit Analytics</div>
            <ul class="nav-links">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="charts.php">Gráficos</a></li>
                <li><a href="trends.php" class="active">Tendencias</a></li>
                <li><a href="education.php">Educación</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">
                    <i class="fas fa-chart-line"></i>
                    Análisis de Tendencias y Proyecciones
                </h1>
                <p class="card-subtitle">Tendencias históricas y proyecciones futuras de producción celular</p>
            </div>

            <!-- Stats Rápidas -->
            <div class="stats-grid">
                <div class="stat-card trend-up">
                    <div class="stat-icon">
                        <i class="fas fa-trending-up"></i>
                    </div>
                    <div class="stat-value" id="growthRate">0%</div>
                    <div class="stat-label">Crecimiento Mensual</div>
                </div>
                <div class="stat-card trend-up">
                    <div class="stat-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="stat-value" id="mostEfficient">-</div>
                    <div class="stat-label">Actividad Más Eficiente</div>
                </div>
                <div class="stat-card trend-up">
                    <div class="stat-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="stat-value" id="projectedGrowth">0%</div>
                    <div class="stat-label">Crecimiento Proyectado</div>
                </div>
            </div>

            <!-- Tabs para análisis avanzados -->
            <div class="tabs">
                <button class="tab active" data-tab="trends">Tendencias</button>
                <button class="tab" data-tab="performance">Métricas Avanzadas</button>
                <button class="tab" data-tab="projections">Proyecciones</button>
            </div>

            <!-- Tab: Tendencias -->
            <div id="trends-tab" class="tab-content active">
                <div class="chart-container">
                    <h3>
                        <i class="fas fa-chart-line"></i>
                        Tendencias de Producción Celular
                    </h3>
                    <p class="chart-description">
                        Evolución histórica y proyección futura de la producción celular por tipo de actividad.
                        Las líneas punteadas indican proyecciones basadas en tendencias actuales.
                    </p>
                    <div id="trendsChart">
                        <div class="chart-placeholder">
                            <span class="loading"></span>
                            Cargando análisis de tendencias...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Métricas Avanzadas -->
            <div id="performance-tab" class="tab-content">
                <div class="chart-container">
                    <h3>
                        <i class="fas fa-tachometer-alt"></i>
                        Métricas de Performance Avanzadas
                    </h3>
                    <p class="chart-description">
                        Análisis detallado de eficiencia, producción máxima y relaciones entre variables clave.
                    </p>
                    <div id="performanceChart">
                        <div class="chart-placeholder">
                            <span class="loading"></span>
                            Cargando métricas avanzadas...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Proyecciones -->
            <div id="projections-tab" class="tab-content">
                <div class="projections-grid">
                    <div class="projection-card">
                        <h4>
                            <i class="fas fa-arrow-up"></i>
                            Proyección a 6 Meses
                        </h4>
                        <div class="projection-content">
                            <div class="projection-metric">
                                <span class="metric-value" id="sixMonthProjection">15%</span>
                                <span class="metric-label">Incremento esperado</span>
                            </div>
                            <ul class="projection-details">
                                <li>Basado en tendencias actuales</li>
                                <li>Considera factores estacionales</li>
                                <li>Incluye optimizaciones de eficiencia</li>
                            </ul>
                        </div>
                    </div>

                    <div class="projection-card">
                        <h4>
                            <i class="fas fa-bullseye"></i>
                            Objetivos Recomendados
                        </h4>
                        <div class="projection-content">
                            <div class="projection-metric">
                                <span class="metric-value" id="recommendedGoal">+25%</span>
                                <span class="metric-label">Meta alcanzable</span>
                            </div>
                            <ul class="projection-details">
                                <li>Optimización de rutinas</li>
                                <li>Mejora en recuperación</li>
                                <li>Estrategias nutricionales</li>
                            </ul>
                        </div>
                    </div>

                    <div class="projection-card">
                        <h4>
                            <i class="fas fa-chart-bar"></i>
                            Análisis Comparativo
                        </h4>
                        <div class="projection-content">
                            <div class="comparison-metrics">
                                <div class="comparison-item">
                                    <span class="comparison-label">Actividad Actual</span>
                                    <span class="comparison-value" id="currentEfficiency">-</span>
                                </div>
                                <div class="comparison-item">
                                    <span class="comparison-label">Potencial Optimizado</span>
                                    <span class="comparison-value optimal" id="optimalEfficiency">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="recommendations-card">
                    <h4>
                        <i class="fas fa-lightbulb"></i>
                        Recomendaciones para Maximizar Producción
                    </h4>
                    <div class="recommendations-list">
                        <div class="recommendation-item">
                            <i class="fas fa-running"></i>
                            <div>
                                <strong>Variedad de Actividades:</strong> Combina ejercicios aeróbicos y de fuerza
                            </div>
                        </div>
                        <div class="recommendation-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Frecuencia Óptima:</strong> 4-5 sesiones por semana de 45-60 minutos
                            </div>
                        </div>
                        <div class="recommendation-item">
                            <i class="fas fa-bed"></i>
                            <div>
                                <strong>Recuperación:</strong> 7-8 horas de sueño y días de descanso activo
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadTrendsCharts();
            setupTrendsTabs();
        });

        async function loadTrendsCharts() {
            // Cargar gráfico de tendencias
            await system.loadChart('trendsChart', '/charts/trends');
            
            // Cargar métricas de performance
            await system.loadChart('performanceChart', '/charts/performance_metrics');
            
            // Actualizar estadísticas
            updateTrendStats();
        }

        function setupTrendsTabs() {
            document.querySelectorAll('.tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    
                    // Actualizar tabs
                    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                    
                    this.classList.add('active');
                    document.getElementById(tabName + '-tab').classList.add('active');
                });
            });
        }

        function updateTrendStats() {
            // Simular datos de tendencias (en implementación real vendrían del backend)
            document.getElementById('growthRate').textContent = '12.5%';
            document.getElementById('mostEfficient').textContent = 'HIIT';
            document.getElementById('projectedGrowth').textContent = '18.2%';
            document.getElementById('sixMonthProjection').textContent = '15-20%';
            document.getElementById('recommendedGoal').textContent = '+25%';
            document.getElementById('currentEfficiency').textContent = '85%';
            document.getElementById('optimalEfficiency').textContent = '95%';
        }
    </script>
</body>
</html>
