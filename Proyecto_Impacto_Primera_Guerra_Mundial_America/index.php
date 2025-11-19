<?php
require_once 'config.php';

// Verificar estado de Python
$pythonAvailable = checkPythonAPI();

// Cargar datos solo si Python está disponible
$allData = $pythonAvailable ? getPythonData('data') : null;
$economicData = $pythonAvailable ? getPythonData('analysis/economic') : null;
$socialData = $pythonAvailable ? getPythonData('analysis/social') : null;
$predictionsData = $pythonAvailable ? getPythonData('predictions') : null;
$tradeData = $pythonAvailable ? getPythonData('charts/trade-categories') : null;
$trendsData = $pythonAvailable ? getPythonData('charts/economic-trends') : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo PROJECT_TITLE; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header class="header">
        <div class="container">
            <h1><?php echo PROJECT_TITLE; ?></h1>
            <p class="subtitle">Análisis mediante algoritmos de Machine Learning - Período 1914-1920</p>
            
            <?php if (!$pythonAvailable): ?>
            <div class="warning-banner">
                ⚠️ Servidor Python no disponible. Los gráficos mostrarán datos de ejemplo.
            </div>
            <?php endif; ?>
        </div>
    </header>

    <nav class="navbar">
        <div class="container">
            <ul class="nav-links">
                <li><a href="#overview" class="active">Visión General</a></li>
                <li><a href="#economic">Impacto Económico</a></li>
                <li><a href="#social">Impacto Social</a></li>
                <li><a href="#trade">Análisis Comercial</a></li>
                <li><a href="#countries">Análisis por País</a></li>
                <li><a href="#predictions">Predicciones</a></li>
            </ul>
        </div>
    </nav>

    <main class="main-content">
        <!-- Sección: Visión General -->
        <section id="overview" class="section">
            <div class="container">
                <h2>Visión General del Impacto</h2>
                <div class="grid-container">
                    <div class="card">
                        <h3>Resumen Ejecutivo</h3>
                        <p>La Primera Guerra Mundial (1914-1918) transformó profundamente las economías y sociedades americanas, acelerando procesos de industrialización y redefiniendo relaciones internacionales.</p>
                        <div class="stats-grid">
                            <div class="stat">
                                <span class="stat-value">+68%</span>
                                <span class="stat-label">Crecimiento económico EE.UU.</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value">1.2M</span>
                                <span class="stat-label">Migración regional</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value">$8.5B</span>
                                <span class="stat-label">Comercio transatlántico</span>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <h3>Métricas Principales</h3>
                        <div class="chart-container">
                            <canvas id="keyMetricsChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección: Impacto Económico -->
        <section id="economic" class="section">
            <div class="container">
                <h2>Impacto Económico</h2>
                <div class="grid-container">
                    <div class="card">
                        <h3>Comparativa PIB Pre/Post Guerra</h3>
                        <div class="chart-container">
                            <canvas id="economicChart" width="600" height="400"></canvas>
                        </div>
                    </div>
                    <div class="card">
                        <h3>Tendencias de Crecimiento</h3>
                        <div class="chart-container">
                            <canvas id="economicTrendsChart" width="600" height="400"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección: Impacto Social -->
        <section id="social" class="section">
            <div class="container">
                <h2>Impacto Social y Demográfico</h2>
                <div class="grid-container">
                    <div class="card">
                        <h3>Indicadores Sociales por País</h3>
                        <div class="chart-container">
                            <canvas id="socialChart" width="600" height="400"></canvas>
                        </div>
                    </div>
                    <div class="card">
                        <h3>Análisis de Participación</h3>
                        <div class="chart-container">
                            <canvas id="rocChart" width="600" height="400"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección: Análisis Comercial -->
        <section id="trade" class="section">
            <div class="container">
                <h2>Análisis Comercial</h2>
                <div class="grid-container">
                    <div class="card">
                        <h3>Flujos Comerciales por País</h3>
                        <div class="chart-container">
                            <canvas id="tradeChart" width="600" height="400"></canvas>
                        </div>
                    </div>
                    <div class="card">
                        <h3>Comercio por Categorías</h3>
                        <div class="chart-container">
                            <canvas id="tradeCategoriesChart" width="600" height="400"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección: Análisis por País -->
        <section id="countries" class="section">
            <div class="container">
                <h2>Análisis por País</h2>
                <div class="country-grid" id="countryGrid">
                    <!-- Las tarjetas se generarán con JavaScript -->
                </div>
            </div>
        </section>

        <!-- Sección: Predicciones -->
        <section id="predictions" class="section">
            <div class="container">
                <h2>Predicciones Post-Guerra (1920-1925)</h2>
                <div class="grid-container">
                    <div class="card">
                        <h3>Crecimiento Económico Proyectado</h3>
                        <div class="chart-container">
                            <canvas id="predictionsChart" width="600" height="400"></canvas>
                        </div>
                    </div>
                    <div class="card">
                        <h3>Factores de Crecimiento</h3>
                        <div class="chart-container">
                            <canvas id="factorsChart" width="600" height="400"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Análisis Histórico con Machine Learning.</p>
            <?php if ($pythonAvailable): ?>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                ✅ Sistema PHP + Python funcionando correctamente
            </p>
            <?php else: ?>
            <p style="color: var(--warning-color); font-size: 0.9rem;">
                ⚠️ Ejecuta: <code>python python_backend/app.py</code> para datos en tiempo real
            </p>
            <?php endif; ?>
        </div>
    </footer>

    <!-- Pasar datos de PHP a JavaScript -->
    <script>
        const appData = {
            pythonAvailable: <?php echo $pythonAvailable ? 'true' : 'false'; ?>,
            economic: <?php echo json_encode($economicData ?: []); ?>,
            social: <?php echo json_encode($socialData ?: []); ?>,
            predictions: <?php echo json_encode($predictionsData ?: []); ?>,
            trade: <?php echo json_encode($tradeData ?: []); ?>,
            trends: <?php echo json_encode($trendsData ?: []); ?>,
            all: <?php echo json_encode($allData ?: []); ?>
        };
        
        console.log('Datos cargados:', appData);
    </script>

    <!-- Scripts -->
    <script src="js/modal-carousel.js"></script>
    <script src="js/charts.js"></script>
</body>
</html>
