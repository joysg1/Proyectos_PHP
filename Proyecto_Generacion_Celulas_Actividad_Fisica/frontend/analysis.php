<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_TITLE; ?> - Análisis</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="logo">CellFit Analytics</div>
            <ul class="nav-links">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="charts.php">Gráficos</a></li>
                <li><a href="trends.php">Tendencias</a></li>
                <li><a href="analysis.php" class="active">Análisis</a></li>
                <li><a href="education.php">Educación</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">
                    <i class="fas fa-chart-line"></i>
                    Análisis e Insights Avanzados
                </h1>
                <p class="card-subtitle">Conclusiones basadas en el análisis profundo de datos</p>
            </div>

            <div class="insights-grid" id="analyticalInsights">
                <div class="loading-placeholder">
                    <div class="loading"></div>
                    <p>Generando análisis avanzados...</p>
                </div>
            </div>
        </div>

        <!-- Análisis de Performance Específicos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tachometer-alt"></i>
                    Métricas de Performance Específicas
                </h3>
                <p class="card-subtitle">Análisis detallado por categoría específica</p>
            </div>

            <div class="tabs">
                <button class="tab active" data-tab="efficiency">
                    <i class="fas fa-bolt"></i>
                    Análisis de Eficiencia
                </button>
                <button class="tab" data-tab="production">
                    <i class="fas fa-chart-bar"></i>
                    Análisis de Producción
                </button>
                <button class="tab" data-tab="intensity">
                    <i class="fas fa-fire"></i>
                    Análisis de Intensidad
                </button>
                <button class="tab" data-tab="duration">
                    <i class="fas fa-clock"></i>
                    Análisis de Duración
                </button>
            </div>

            <div id="efficiency-tab" class="tab-content active">
                <div class="chart-container">
                    <div class="chart-header">
                        <h4>Eficiencia por Tipo de Actividad</h4>
                        <p class="chart-description">
                            Comparación de la eficiencia (células producidas por minuto) entre diferentes tipos de actividades.
                            Muestra qué actividades generan mayor producción celular en menos tiempo.
                        </p>
                    </div>
                    <div id="efficiencyChart">
                        <div class="chart-placeholder">
                            <span class="loading"></span>
                            <p>Analizando eficiencia de actividades...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="production-tab" class="tab-content">
                <div class="chart-container">
                    <div class="chart-header">
                        <h4>Producción Promedio por Actividad</h4>
                        <p class="chart-description">
                            Análisis de la producción celular promedio generada por cada tipo de actividad.
                            Permite identificar qué ejercicios son más efectivos para la producción celular.
                        </p>
                    </div>
                    <div id="productionChart">
                        <div class="chart-placeholder">
                            <span class="loading"></span>
                            <p>Analizando producción por actividad...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="intensity-tab" class="tab-content">
                <div class="chart-container">
                    <div class="chart-header">
                        <h4>Impacto de la Intensidad en la Producción</h4>
                        <p class="chart-description">
                            Relación entre la intensidad del ejercicio y la producción celular resultante.
                            Ayuda a identificar el nivel óptimo de intensidad para maximizar resultados.
                        </p>
                    </div>
                    <div id="intensityChart">
                        <div class="chart-placeholder">
                            <span class="loading"></span>
                            <p>Analizando relación intensidad-producción...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="duration-tab" class="tab-content">
                <div class="chart-container">
                    <div class="chart-header">
                        <h4>Optimización de Duración del Ejercicio</h4>
                        <p class="chart-description">
                            Análisis de cómo la duración del ejercicio afecta la eficiencia celular.
                            Identifica la duración óptima para maximizar la producción por minuto.
                        </p>
                    </div>
                    <div id="durationChart">
                        <div class="chart-placeholder">
                            <span class="loading"></span>
                            <p>Analizando optimización de duración...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Análisis Comparativo por Actividad -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-search"></i>
                    Análisis Individual por Actividad
                </h3>
                <p class="card-subtitle">Evaluación detallada de cada tipo de ejercicio</p>
            </div>

            <div class="activity-analysis">
                <div class="activity-selector-large">
                    <label for="analysisActivitySelect">Seleccionar Actividad para Análisis Detallado:</label>
                    <select id="analysisActivitySelect" onchange="loadIndividualAnalysis(this.value)">
                        <option value="">Selecciona una actividad...</option>
                        <option value="Running">Running</option>
                        <option value="Swimming">Swimming</option>
                        <option value="Cycling">Cycling</option>
                        <option value="Weight Training">Weight Training</option>
                        <option value="Yoga">Yoga</option>
                        <option value="HIIT">HIIT</option>
                    </select>
                </div>

                <div id="individualAnalysisChart" class="analysis-radar-container">
                    <div class="analysis-placeholder">
                        <i class="fas fa-chart-radar"></i>
                        <h4>Análisis Individual de Actividad</h4>
                        <p>Selecciona una actividad del menú desplegable para ver un análisis detallado</p>
                        <small>Comparación con promedios generales y métricas específicas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadAnalyticalInsights();
            setupAnalysisTabs();
            loadInitialAnalysisChart();
        });

        async function loadAnalyticalInsights() {
            try {
                const response = await system.apiRequest('/analytics/insights');
                if (response.success) {
                    displayAnalyticalInsights(response.data);
                }
            } catch (error) {
                console.error('Error loading insights:', error);
                document.getElementById('analyticalInsights').innerHTML = 
                    '<div class="alert alert-error">Error cargando análisis avanzados</div>';
            }
        }

        function displayAnalyticalInsights(insights) {
            const container = document.getElementById('analyticalInsights');
            
            container.innerHTML = `
                <div class="insight-card large">
                    <div class="insight-icon primary">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="insight-content">
                        <h4>Actividad Más Eficiente</h4>
                        <p class="insight-value">${insights.performance_analysis.most_efficient_activity}</p>
                        <p>Produce más células por minuto de ejercicio según el análisis de datos</p>
                    </div>
                </div>

                <div class="insight-card">
                    <div class="insight-icon success">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <div class="insight-content">
                        <h4>Intensidad Óptima</h4>
                        <p class="insight-value">${insights.optimal_parameters.best_intensity_range.min.toFixed(1)} - ${insights.optimal_parameters.best_intensity_range.max.toFixed(1)}</p>
                        <p>Rango de intensidad que maximiza la producción celular</p>
                    </div>
                </div>

                <div class="insight-card">
                    <div class="insight-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="insight-content">
                        <h4>Duración Recomendada</h4>
                        <p class="insight-value">${insights.optimal_parameters.best_duration_range.min.toFixed(0)}-${insights.optimal_parameters.best_duration_range.max.toFixed(0)} min</p>
                        <p>Duración ideal para balancear esfuerzo y resultados</p>
                    </div>
                </div>

                <div class="insight-card">
                    <div class="insight-icon info">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="insight-content">
                        <h4>Correlaciones Clave</h4>
                        <p class="insight-value">${Object.keys(insights.key_correlations)[0]}</p>
                        <p>Variable con mayor impacto en la producción celular</p>
                    </div>
                </div>
            `;
        }

        function setupAnalysisTabs() {
            document.querySelectorAll('.tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    
                    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                    
                    this.classList.add('active');
                    document.getElementById(tabName + '-tab').classList.add('active');
                    
                    // Cargar gráfico específico para cada pestaña
                    loadSpecificAnalysisChart(tabName);
                });
            });
        }

        function loadInitialAnalysisChart() {
            // Cargar el primer gráfico por defecto
            loadSpecificAnalysisChart('efficiency');
        }

        async function loadSpecificAnalysisChart(analysisType) {
            const endpoints = {
                'efficiency': '/charts/efficiency_analysis',
                'production': '/charts/production_analysis', 
                'intensity': '/charts/intensity_analysis',
                'duration': '/charts/duration_analysis'
            };

            const chartId = analysisType + 'Chart';
            const endpoint = endpoints[analysisType];

            if (endpoint) {
                await system.loadChart(chartId, endpoint);
            }
        }

        async function loadIndividualAnalysis(activity) {
            if (!activity) {
                // Mostrar placeholder si no hay actividad seleccionada
                document.getElementById('individualAnalysisChart').innerHTML = `
                    <div class="analysis-placeholder">
                        <i class="fas fa-chart-radar"></i>
                        <h4>Análisis Individual de Actividad</h4>
                        <p>Selecciona una actividad del menú desplegable para ver un análisis detallado</p>
                        <small>Comparación con promedios generales y métricas específicas</small>
                    </div>
                `;
                return;
            }

            const container = document.getElementById('individualAnalysisChart');
            container.innerHTML = '<div class="chart-placeholder"><span class="loading"></span><p>Cargando análisis individual...</p></div>';
            
            try {
                const response = await system.apiRequest(`/charts/radar_individual/${activity}`);
                if (response.success) {
                    const figure = response.data;
                    Plotly.newPlot('individualAnalysisChart', figure.data, figure.layout, {
                        responsive: true,
                        displayModeBar: true,
                        displaylogo: false
                    });
                }
            } catch (error) {
                console.error('Error loading individual analysis:', error);
                container.innerHTML = `
                    <div class="chart-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Error al cargar el análisis individual</p>
                        <button class="btn btn-sm" onclick="loadIndividualAnalysis('${activity}')">
                            Reintentar
                        </button>
                    </div>
                `;
            }
        }
    </script>
</body>
</html>