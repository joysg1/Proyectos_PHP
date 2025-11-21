<?php
// Frontend - charts.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficos - Análisis Conflictos Bélicos</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <i class="fas fa-chart-line"></i>
                <h1>WarEconomy Analytics</h1>
            </div>
            <nav class="nav">
                <a href="index.php" class="nav-link"><i class="fas fa-home"></i> Inicio</a>
                <a href="charts.php" class="nav-link active"><i class="fas fa-chart-bar"></i> Gráficos</a>
            </nav>
        </header>

        <!-- Pestañas de Gráficos -->
        <section class="charts-section">
            <div class="tabs">
                <button class="tab-btn active" onclick="openTab('area')">
                    <i class="fas fa-mountain"></i> Área
                </button>
                <button class="tab-btn" onclick="openTab('radar')">
                    <i class="fas fa-bullseye"></i> Radar
                </button>
                <button class="tab-btn" onclick="openTab('stacked')">
                    <i class="fas fa-chart-bar"></i> Barras Apiladas
                </button>
                <button class="tab-btn" onclick="openTab('pie')">
                    <i class="fas fa-chart-pie"></i> Pastel
                </button>
                <button class="tab-btn" onclick="openTab('ml')">
                    <i class="fas fa-brain"></i> Machine Learning
                </button>
            </div>

            <!-- Contenido de Pestañas -->
            <div class="tab-content">
                <!-- Gráfico de Área -->
                <div id="area" class="tab-pane active">
                    <div class="chart-header">
                        <h3><i class="fas fa-mountain"></i> Evolución Temporal - Área sobre la Curva</h3>
                        <p>Cambios en GDP e inflación durante periodos de conflicto (1914-2025)</p>
                    </div>
                    <div class="chart-container">
                        <div class="loading-chart" id="areaLoading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Generando gráfico...</p>
                        </div>
                        <img id="areaChart" class="chart-image" style="display: none;">
                    </div>
                </div>

                <!-- Gráfico Radar -->
                <div id="radar" class="tab-pane">
                    <div class="chart-header">
                        <h3><i class="fas fa-bullseye"></i> Comparación Regional - Radar</h3>
                        <p>Análisis multidimensional del impacto económico por regiones</p>
                    </div>
                    <div class="chart-container">
                        <div class="loading-chart" id="radarLoading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Generando gráfico radar...</p>
                        </div>
                        <img id="radarChart" class="chart-image" style="display: none;">
                    </div>
                </div>

                <!-- Barras Apiladas -->
                <div id="stacked" class="tab-pane">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-bar"></i> Indicadores por Década - Barras Apiladas</h3>
                        <p>Evolución acumulada de variables económicas clave</p>
                    </div>
                    <div class="chart-container">
                        <div class="loading-chart" id="stackedLoading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Generando gráfico de barras...</p>
                        </div>
                        <img id="stackedChart" class="chart-image" style="display: none;">
                    </div>
                </div>

                <!-- Gráfico de Pastel -->
                <div id="pie" class="tab-pane">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-pie"></i> Distribución Regional - Pastel</h3>
                        <p>Proporción de conflictos bélicos por región geográfica</p>
                    </div>
                    <div class="chart-container">
                        <div class="loading-chart" id="pieLoading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Generando gráfico de pastel...</p>
                        </div>
                        <img id="pieChart" class="chart-image" style="display: none;">
                    </div>
                </div>

                <!-- Machine Learning -->
                <div id="ml" class="tab-pane">
                    <div class="chart-header">
                        <h3><i class="fas fa-brain"></i> Análisis con Machine Learning</h3>
                        <p>Clustering y predicción de impactos económicos</p>
                    </div>
                    
                    <div class="ml-controls">
                        <button class="ml-btn" onclick="loadClusters()">
                            <i class="fas fa-project-diagram"></i> Cargar Clusters
                        </button>
                        <button class="ml-btn" onclick="loadPredictions()">
                            <i class="fas fa-crystal-ball"></i> Análisis Predictivo
                        </button>
                        <button class="ml-btn" onclick="loadTrends()">
                            <i class="fas fa-trend-up"></i> Tendencias
                        </button>
                    </div>

                    <div class="ml-results">
                        <div id="clustersResult" class="ml-result"></div>
                        <div id="predictionResult" class="ml-result"></div>
                        <div id="trendsResult" class="ml-result"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="js/main.js"></script>
    <script src="js/charts.js"></script>
</body>
</html>
