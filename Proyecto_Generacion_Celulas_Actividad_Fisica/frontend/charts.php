<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_TITLE; ?> - Gráficos</title>
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
                <li><a href="charts.php" class="active">Gráficos</a></li>
                <li><a href="trends.php">Tendencias</a></li>
                <li><a href="analysis.php">Análisis</a></li>
                <li><a href="education.php">Educación</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">
                    <i class="fas fa-chart-bar"></i>
                    Visualizaciones Avanzadas
                </h1>
                <p class="card-subtitle">Análisis visual completo de la producción celular</p>
            </div>

            <!-- Tabs para diferentes gráficos -->
            <div class="tabs">
                <button class="tab active" data-tab="area">
                    <i class="fas fa-chart-area"></i>
                    Evolución Temporal
                </button>
                <button class="tab" data-tab="radar">
                    <i class="fas fa-chart-pie"></i>
                    Análisis Radar
                </button>
                <button class="tab" data-tab="stacked">
                    <i class="fas fa-chart-bar"></i>
                    Distribución
                </button>
                <button class="tab" data-tab="pie">
                    <i class="fas fa-pie-chart"></i>
                    Contribución
                </button>
                <button class="tab" data-tab="correlation">
                    <i class="fas fa-fire"></i>
                    Correlaciones
                </button>
            </div>

            <!-- Gráfico de Área -->
            <div id="area-tab" class="tab-content active">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Evolución Temporal de la Producción</h3>
                    </div>
                    <div id="areaChart">
                        <div class="chart-placeholder">
                            <span class="loading"></span>
                            <p>Cargando análisis temporal...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos Radar Individuales -->
            <div id="radar-tab" class="tab-content">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Análisis Individual por Actividad</h3>
                        <div class="activity-selector">
                            <select id="radarActivitySelect" onchange="loadIndividualRadar(this.value)">
                                <option value="">Seleccionar actividad...</option>
                                <option value="Running">Running</option>
                                <option value="Swimming">Swimming</option>
                                <option value="Cycling">Cycling</option>
                                <option value="Weight Training">Weight Training</option>
                                <option value="Yoga">Yoga</option>
                                <option value="HIIT">HIIT</option>
                            </select>
                        </div>
                    </div>
                    <div id="individualRadarChart">
                        <div class="chart-placeholder">
                            <i class="fas fa-search"></i>
                            <p>Selecciona una actividad para ver su análisis detallado</p>
                            <small>Comparación con el promedio general</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Barras Apiladas con Porcentajes -->
            <div id="stacked-tab" class="tab-content">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Distribución Porcentual por Actividad y Género</h3>
                    </div>
                    <div id="stackedBarChart">
                        <div class="chart-placeholder">
                            <span class="loading"></span>
                            <p>Calculando distribución porcentual...</p>
                        </div>
                    </div>
                    
                    <!-- Leyenda con valores absolutos -->
                    <div class="detailed-legend" id="stackedBarLegend">
                        <h4><i class="fas fa-table"></i> Valores Absolutos por Actividad</h4>
                        <div class="legend-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Actividad</th>
                                        <th>Masculino</th>
                                        <th>Femenino</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="legendTableBody">
                                    <!-- Los datos se cargan via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Pastel -->
            <div id="pie-tab" class="tab-content">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Contribución por Tipo de Actividad</h3>
                    </div>
                    <div id="pieChart">
                        <div class="chart-placeholder">
                            <span class="loading"></span>
                            <p>Calculando contribuciones...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mapa de Calor de Correlaciones -->
            <div id="correlation-tab" class="tab-content">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Relaciones entre Variables</h3>
                    </div>
                    <div id="correlationChart">
                        <div class="chart-placeholder">
                            <span class="loading"></span>
                            <p>Analizando correlaciones...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            system.loadAllCharts();
            setupChartTabs();
        });

        function setupChartTabs() {
            document.querySelectorAll('.tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    
                    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                    
                    this.classList.add('active');
                    document.getElementById(tabName + '-tab').classList.add('active');
                    
                    // Cargar gráfico específico si es necesario
                    if (tabName === 'radar') {
                        // No cargar hasta que se seleccione una actividad
                    } else {
                        const chartId = tabName + 'Chart';
                        const endpoint = getChartEndpoint(tabName);
                        system.loadChart(chartId, endpoint);
                    }
                });
            });
        }

        function getChartEndpoint(tabName) {
            const endpoints = {
                'area': '/charts/area',
                'stacked': '/charts/stacked_bar_percentage',
                'pie': '/charts/pie',
                'correlation': '/charts/correlation'
            };
            return endpoints[tabName] || '/charts/area';
        }

        async function loadIndividualRadar(activity) {
            if (!activity) return;
            
            const container = document.getElementById('individualRadarChart');
            container.innerHTML = '<div class="chart-placeholder"><span class="loading"></span><p>Cargando análisis...</p></div>';
            
            try {
                const response = await system.apiRequest(`/charts/radar_individual/${activity}`);
                if (response.success) {
                    const figure = response.data;
                    Plotly.newPlot('individualRadarChart', figure.data, figure.layout, {
                        responsive: true,
                        displayModeBar: true
                    });
                }
            } catch (error) {
                console.error('Error loading individual radar:', error);
                container.innerHTML = '<div class="chart-error">Error al cargar el análisis</div>';
            }
        }
    </script>
</body>
</html>