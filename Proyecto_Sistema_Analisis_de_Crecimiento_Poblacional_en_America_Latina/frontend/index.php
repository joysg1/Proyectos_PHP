<?php
// Configuración básica
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Análisis de Crecimiento Poblacional - América Latina</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">🌎 PopAnalytics</a>
                <ul class="nav-links">
                    <li><a href="index.php" class="active">Inicio</a></li>
                    <li><a href="graficos.php">Gráficos</a></li>
                    <li><a href="#" onclick="openModal('infoModal'); return false;">Información</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Análisis de Crecimiento Poblacional</h1>
            <p>Plataforma integral para el análisis y visualización de datos demográficos en América Latina</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container">
        <!-- Stats Overview -->
        <div class="stats-container" id="statsContainer">
            <div class="loading">
                <div class="spinner"></div>
                <p>Cargando estadísticas...</p>
            </div>
        </div>

        <!-- Country Cards -->
        <div class="cards-grid" id="countriesGrid">
            <div class="loading">
                <div class="spinner"></div>
                <p>Cargando datos de países...</p>
            </div>
        </div>

        <!-- Analysis Section -->
        <div class="tabs">
            <div class="tab-headers">
                <button class="tab-header active" onclick="switchTab('analisis')">Análisis Regional</button>
                <button class="tab-header" onclick="switchTab('predicciones')">Predicciones</button>
                <button class="tab-header" onclick="switchTab('clusters')">Agrupamientos</button>
            </div>
            
            <div class="tab-content">
                <!-- Análisis Regional -->
                <div class="tab-pane active" id="analisis">
                    <div class="card">
                        <h3>Análisis Regional Completo</h3>
                        <div id="regionalAnalysis">
                            <div class="loading">
                                <div class="spinner"></div>
                                <p>Cargando análisis regional...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Predicciones -->
                <div class="tab-pane" id="predicciones">
                    <div class="card">
                        <h3>Predicciones de Crecimiento</h3>
                        <div class="form-group">
                            <label class="form-label">Seleccionar País:</label>
                            <select class="form-input" id="countrySelect" onchange="loadPredictions()">
                                <option value="">Cargando países...</option>
                            </select>
                        </div>
                        <div id="predictionsResult"></div>
                    </div>
                </div>

                <!-- Clusters -->
                <div class="tab-pane" id="clusters">
                    <div class="card">
                        <h3>Agrupamiento de Países</h3>
                        <p>Análisis de similitud entre países basado en indicadores poblacionales</p>
                        <button class="btn btn-primary" onclick="loadClusters()">Generar Agrupamientos</button>
                        <div id="clustersResult" style="margin-top: 1rem;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Carousel -->
        <div class="carousel">
            <div class="carousel-inner" id="infoCarousel">
                <div class="carousel-item">
                    <h3>📊 Datos Confiables</h3>
                    <p>Información actualizada de fuentes oficiales y proyecciones precisas</p>
                </div>
                <div class="carousel-item">
                    <h3>🤖 Machine Learning</h3>
                    <p>Algoritmos avanzados para predicciones y análisis de tendencias</p>
                </div>
                <div class="carousel-item">
                    <h3>📈 Visualizaciones Avanzadas</h3>
                    <p>Gráficos interactivos y dashboards para mejor comprensión de datos</p>
                </div>
            </div>
            <button class="carousel-control prev" onclick="moveCarousel(-1)">‹</button>
            <button class="carousel-control next" onclick="moveCarousel(1)">›</button>
            <div class="carousel-indicators">
                <button class="carousel-indicator active" onclick="setCarousel(0)"></button>
                <button class="carousel-indicator" onclick="setCarousel(1)"></button>
                <button class="carousel-indicator" onclick="setCarousel(2)"></button>
            </div>
        </div>
    </main>

    <!-- Information Modal -->
    <div class="modal" id="infoModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('infoModal')">&times;</span>
            <h2>Información del Proyecto</h2>
            <div class="card">
                <h3>🌍 Crecimiento Poblacional en América Latina</h3>
                <p>Este sistema proporciona un análisis completo de las tendencias demográficas en los principales países de América Latina.</p>
                
                <h4>Características Principales:</h4>
                <ul style="margin: 1rem 0; padding-left: 1.5rem;">
                    <li>Análisis en tiempo real de datos poblacionales</li>
                    <li>Predicciones usando algoritmos de Machine Learning</li>
                    <li>Visualizaciones interactivas y profesionales</li>
                    <li>Comparativas entre países</li>
                    <li>Proyecciones a futuro</li>
                </ul>

                <h4>Indicadores Analizados:</h4>
                <div class="stats-container">
                    <div class="stat-card">
                        <span class="stat-number">📈</span>
                        <span class="stat-label">Tasa de Crecimiento</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">👥</span>
                        <span class="stat-label">Densidad Poblacional</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">🏙️</span>
                        <span class="stat-label">Población Urbana</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">📊</span>
                        <span class="stat-label">Distribución por Edad</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Los modales dinámicos se crearán automáticamente mediante JavaScript -->
    
    <!-- Cargar main.js al final del body para evitar conflictos -->
    <script src="js/main.js"></script>
</body>
</html>