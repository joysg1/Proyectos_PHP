<?php
// Frontend - index.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis Impacto Conflictos Bélicos en la Economía</title>
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
                <a href="index.php" class="nav-link active"><i class="fas fa-home"></i> Inicio</a>
                <a href="charts.php" class="nav-link"><i class="fas fa-chart-bar"></i> Gráficos</a>
            </nav>
        </header>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h2>Análisis del Impacto Económico de Conflictos Bélicos</h2>
                <p class="hero-subtitle">1914 - 2025</p>
                <p class="hero-description">
                    Sistema avanzado de análisis que utiliza machine learning y visualización de datos 
                    para comprender las relaciones entre conflictos armados y variables económicas globales.
                </p>
                <a href="charts.php" class="cta-button">
                    <i class="fas fa-chart-bar"></i> Explorar Gráficos
                </a>
            </div>
        </section>

        <!-- Estadísticas Rápidas -->
        <section class="stats-section">
            <h3>Estadísticas Globales</h3>
            <div class="stats-grid" id="statsGrid">
                <div class="stat-card loading">
                    <div class="stat-icon"><i class="fas fa-fighter-jet"></i></div>
                    <div class="stat-content">
                        <h4>Cargando...</h4>
                        <p>Total de Conflictos</p>
                    </div>
                </div>
                <!-- Más estadísticas se cargarán via JavaScript -->
            </div>
        </section>

        <!-- Lista de Conflictos -->
        <section class="conflicts-section">
            <h3>Conflictos Analizados</h3>
            <div class="conflicts-carousel" id="conflictsCarousel">
                <div class="carousel-container">
                    <!-- Los conflictos se cargarán aquí via JavaScript -->
                </div>
                <button class="carousel-btn prev" onclick="moveCarousel(-1)">‹</button>
                <button class="carousel-btn next" onclick="moveCarousel(1)">›</button>
            </div>
        </section>

        <!-- Información ML -->
        <section class="ml-section">
            <h3>Análisis con Machine Learning</h3>
            <div class="ml-grid">
                <div class="ml-card">
                    <i class="fas fa-brain"></i>
                    <h4>Clustering de Conflictos</h4>
                    <p>Agrupamiento automático de conflictos basado en patrones económicos similares</p>
                </div>
                <div class="ml-card">
                    <i class="fas fa-chart-line"></i>
                    <h4>Predicción de Impacto</h4>
                    <p>Modelos predictivos para estimar efectos económicos futuros</p>
                </div>
                <div class="ml-card">
                    <i class="fas fa-trend-up"></i>
                    <h4>Análisis de Tendencias</h4>
                    <p>Identificación de patrones temporales en datos históricos</p>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal -->
    <div id="conflictModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>
