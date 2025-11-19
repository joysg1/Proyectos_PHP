<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitaminaAnalytics - Sistema de Análisis Avanzado</title>
    
    <!-- Meta tags modernos -->
    <meta name="description" content="Sistema avanzado de análisis del impacto de vitaminas en glóbulos rojos con Machine Learning">
    <meta name="keywords" content="vitaminas, glóbulos rojos, machine learning, analytics, salud">
    <meta name="author" content="VitaminaAnalytics">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/icons/favicon.ico">
    
    <!-- Fuentes modernas -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Estilos -->
    <link rel="stylesheet" href="css/dark-theme.css">
    
    <!-- Scripts de gráficos -->
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
</head>
<body>
    <!-- Modal para gráficos -->
    <div id="chartModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    <!-- Navbar Moderno -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="index.php" class="logo" style="text-decoration: none;">
                    <i class="fas fa-chart-line logo-icon"></i>
                    VitaminaAnalytics
                </a>
                <ul class="nav-links">
                    <li><a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i>
                        Inicio
                    </a></li>
                    <li><a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-dashboard"></i>
                        Dashboard
                    </a></li>
                    <li><a href="index.php#analytics" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        Analytics
                    </a></li>
                    <li><a href="index.php#prediction" class="nav-link">
                        <i class="fas fa-robot"></i>
                        ML Predictor
                    </a></li>
                    <li><a href="#" class="nav-link" onclick="showSystemInfo(); return false;">
                        <i class="fas fa-info-circle"></i>
                        Sistema
                    </a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Script para manejar navegación suave y estado activo -->
    <script>
        // Manejar estado activo de navegación
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = '<?php echo basename($_SERVER['PHP_SELF']); ?>';
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href').includes(currentPage) && currentPage !== 'index.php') {
                    link.classList.add('active');
                }
                
                // Manejar clics en enlaces
                link.addEventListener('click', function(e) {
                    if (this.getAttribute('href').startsWith('#')) {
                        e.preventDefault();
                        const targetId = this.getAttribute('href').substring(1);
                        const targetElement = document.getElementById(targetId);
                        if (targetElement) {
                            targetElement.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    }
                    
                    // Actualizar estado activo
                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            // Para enlaces de anclas en la misma página
            if (currentPage === 'index.php') {
                const hash = window.location.hash;
                if (hash) {
                    const targetLink = document.querySelector(`a[href="${hash}"]`);
                    if (targetLink) {
                        navLinks.forEach(l => l.classList.remove('active'));
                        targetLink.classList.add('active');
                    }
                }
            }
        });
    </script>