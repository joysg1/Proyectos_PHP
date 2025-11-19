<?php
// Configuración básica
$api_url = 'http://localhost:5000/api';

// Función para hacer requests a la API
function call_api($endpoint) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:5000/api/{$endpoint}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200) {
        return json_decode($response, true);
    }
    return null;
}

// Verificar estado de la API y obtener estadísticas
$api_status = call_api('health');
$estadisticas = call_api('ml/analisis');
$metricas = call_api('metricas');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Predicción de Desgaste - Discos Duros</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <i class="fas fa-hard-drive"></i>
                <h1>Disk<span>Analytics</span></h1>
            </div>
            <nav class="nav">
                <a href="index.php" class="nav-link active"><i class="fas fa-home"></i> Inicio</a>
                <a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </nav>
        </header>

        <main class="main-content">
            <!-- Hero Section Mejorada -->
            <section class="hero">
                <div class="hero-content">
                    <div class="hero-text">
                        <h2>Sistema Inteligente de Monitoreo de Discos Duros</h2>
                        <p class="hero-description">
                            Plataforma avanzada que utiliza Machine Learning para predecir el desgaste 
                            y vida útil de tus discos duros. Monitorea, analiza y previene fallos críticos.
                        </p>
                        <div class="hero-actions">
                            <a href="dashboard.php" class="cta-button primary">
                                <i class="fas fa-rocket"></i>
                                Ir al Dashboard
                            </a>
                            <a href="#features" class="cta-button secondary">
                                <i class="fas fa-info-circle"></i>
                                Saber Más
                            </a>
                        </div>
                    </div>
                    <div class="hero-visual">
                        <div class="visual-card">
                            <i class="fas fa-brain"></i>
                            <h3>AI Predictiva</h3>
                            <p>Algoritmos avanzados de ML</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Estadísticas en Tiempo Real -->
            <section class="stats-section">
                <div class="section-header">
                    <h2><i class="fas fa-chart-bar"></i> Estadísticas del Sistema</h2>
                    <p>Datos en tiempo real de tu infraestructura de almacenamiento</p>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-hard-drive"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $estadisticas['analisis']['estadisticas_generales']['total_discos'] ?? '0'; ?></h3>
                            <p>Discos Monitoreados</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $estadisticas['analisis']['estadisticas_generales']['discos_en_riesgo'] ?? '0'; ?></h3>
                            <p>En Estado Crítico</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $estadisticas['analisis']['estadisticas_generales']['desgaste_promedio'] ?? '0'; ?>%</h3>
                            <p>Desgaste Promedio</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $api_status ? 'Online' : 'Offline'; ?></h3>
                            <p>Estado del Sistema</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Características Principales -->
            <section id="features" class="features-section">
                <div class="section-header">
                    <h2><i class="fas fa-star"></i> Características Principales</h2>
                    <p>Tecnología de vanguardia para la gestión de tu almacenamiento</p>
                </div>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h3>Machine Learning Predictivo</h3>
                        <p>Algoritmos avanzados que aprenden de tus datos para predecir fallos con hasta 95% de precisión.</p>
                        <ul class="feature-list">
                            <li><i class="fas fa-check"></i> Random Forest</li>
                            <li><i class="fas fa-check"></i> Gradient Boosting</li>
                            <li><i class="fas fa-check"></i> Análisis de Tendencias</li>
                        </ul>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-network"></i>
                        </div>
                        <h3>Visualizaciones Avanzadas</h3>
                        <p>Gráficos interactivos y dashboards en tiempo real para un análisis completo de tu almacenamiento.</p>
                        <ul class="feature-list">
                            <li><i class="fas fa-check"></i> Gráficos de Área</li>
                            <li><i class="fas fa-check"></i> Radar de Métricas</li>
                            <li><i class="fas fa-check"></i> Análisis Comparativo</li>
                        </ul>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3>Alertas Inteligentes</h3>
                        <p>Sistema de notificaciones proactivas que te alerta antes de que ocurran fallos críticos.</p>
                        <ul class="feature-list">
                            <li><i class="fas fa-check"></i> Alertas Tempranas</li>
                            <li><i class="fas fa-check"></i> Recomendaciones</li>
                            <li><i class="fas fa-check"></i> Planificación de Mantenimiento</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Tecnologías Soportadas -->
            <section class="tech-section">
                <div class="section-header">
                    <h2><i class="fas fa-cogs"></i> Tecnologías Soportadas</h2>
                    <p>Compatibilidad completa con todos los tipos de almacenamiento moderno</p>
                </div>
                <div class="tech-grid">
                    <div class="tech-card ssd">
                        <div class="tech-header">
                            <i class="fas fa-bolt"></i>
                            <h3>SSD</h3>
                            <span class="tech-badge">Unidades de Estado Sólido</span>
                        </div>
                        <div class="tech-content">
                            <div class="tech-specs">
                                <div class="spec">
                                    <i class="fas fa-clock"></i>
                                    <span>Vida Útil: <?php echo $metricas['metricas']['SSD']['vida_util_meses'] ?? '60'; ?> meses</span>
                                </div>
                                <div class="spec">
                                    <i class="fas fa-pen-fancy"></i>
                                    <span>Ciclos: <?php echo number_format($metricas['metricas']['SSD']['ciclos_escritura_max'] ?? 50000); ?></span>
                                </div>
                                <div class="spec">
                                    <i class="fas fa-thermometer-half"></i>
                                    <span>Temp. Máx: <?php echo $metricas['metricas']['SSD']['temperatura_max'] ?? '70'; ?>°C</span>
                                </div>
                            </div>
                            <p class="tech-description">
                                Monitoreo avanzado de celdas NAND, wear leveling y TBW (Total Bytes Written).
                            </p>
                        </div>
                    </div>
                    
                    <div class="tech-card hdd">
                        <div class="tech-header">
                            <i class="fas fa-compact-disc"></i>
                            <h3>HDD</h3>
                            <span class="tech-badge">Discos Mecánicos</span>
                        </div>
                        <div class="tech-content">
                            <div class="tech-specs">
                                <div class="spec">
                                    <i class="fas fa-clock"></i>
                                    <span>Vida Útil: <?php echo $metricas['metricas']['HDD']['vida_util_meses'] ?? '48'; ?> meses</span>
                                </div>
                                <div class="spec">
                                    <i class="fas fa-microchip"></i>
                                    <span>Sectores: Monitoreo SMART</span>
                                </div>
                                <div class="spec">
                                    <i class="fas fa-thermometer-half"></i>
                                    <span>Temp. Máx: <?php echo $metricas['metricas']['HDD']['temperatura_max'] ?? '60'; ?>°C</span>
                                </div>
                            </div>
                            <p class="tech-description">
                                Análisis de sectores dañados, tiempo de respuesta y salud mecánica.
                            </p>
                        </div>
                    </div>
                    
                    <div class="tech-card nvme">
                        <div class="tech-header">
                            <i class="fas fa-tachometer-alt"></i>
                            <h3>NVMe</h3>
                            <span class="tech-badge">Alta Velocidad</span>
                        </div>
                        <div class="tech-content">
                            <div class="tech-specs">
                                <div class="spec">
                                    <i class="fas fa-clock"></i>
                                    <span>Vida Útil: <?php echo $metricas['metricas']['NVMe']['vida_util_meses'] ?? '72'; ?> meses</span>
                                </div>
                                <div class="spec">
                                    <i class="fas fa-pen-fancy"></i>
                                    <span>Ciclos: <?php echo number_format($metricas['metricas']['NVMe']['ciclos_escritura_max'] ?? 80000); ?></span>
                                </div>
                                <div class="spec">
                                    <i class="fas fa-thermometer-half"></i>
                                    <span>Temp. Máx: <?php echo $metricas['metricas']['NVMe']['temperatura_max'] ?? '75'; ?>°C</span>
                                </div>
                            </div>
                            <p class="tech-description">
                                Optimizado para unidades NVMe con monitoreo de throttling y salud de celdas.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Proceso de Trabajo -->
            <section class="workflow-section">
                <div class="section-header">
                    <h2><i class="fas fa-sitemap"></i> ¿Cómo Funciona?</h2>
                    <p>Flujo de trabajo inteligente para la gestión predictiva de discos</p>
                </div>
                <div class="workflow-steps">
                    <div class="workflow-step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Recolección de Datos</h3>
                            <p>Captura automática de métricas SMART, temperatura, ciclos de escritura y tiempo de uso.</p>
                        </div>
                    </div>
                    <div class="workflow-step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Análisis con ML</h3>
                            <p>Procesamiento con algoritmos de Machine Learning para identificar patrones de desgaste.</p>
                        </div>
                    </div>
                    <div class="workflow-step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Predicción y Alertas</h3>
                            <p>Generación de predicciones de vida útil y alertas tempranas para mantenimiento preventivo.</p>
                        </div>
                    </div>
                    <div class="workflow-step">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Visualización y Reportes</h3>
                            <p>Dashboards interactivos y reportes detallados para la toma de decisiones informadas.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Llamada a la Acción -->
            <section class="cta-section">
                <div class="cta-content">
                    <h2>¿Listo para Proteger tu Almacenamiento?</h2>
                    <p>Comienza a monitorear tus discos duros con tecnología de inteligencia artificial predictiva.</p>
                    <div class="cta-buttons">
                        <a href="dashboard.php" class="cta-button primary large">
                            <i class="fas fa-play"></i>
                            Comenzar Ahora
                        </a>
                        <a href="#features" class="cta-button secondary large">
                            <i class="fas fa-book"></i>
                            Ver Documentación
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="footer">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="logo">
                        <i class="fas fa-hard-drive"></i>
                        <h3>Disk<span>Analytics</span></h3>
                    </div>
                    <p>Sistema avanzado de monitoreo y predicción de desgaste de discos duros usando Machine Learning.</p>
                </div>
                <div class="footer-section">
                    <h4>Enlaces Rápidos</h4>
                    <ul>
                        <li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
                        <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="#features"><i class="fas fa-star"></i> Características</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Tecnologías</h4>
                    <ul>
                        <li><i class="fab fa-python"></i> Python & Flask</li>
                        <li><i class="fas fa-brain"></i> Machine Learning</li>
                        <li><i class="fab fa-php"></i> PHP Frontend</li>
                        <li><i class="fas fa-chart-bar"></i> Visualizaciones</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 DiskAnalytics. Todos los derechos reservados. | Powered by AI & Advanced Analytics</p>
            </div>
        </footer>
    </div>

    <script src="assets/js/script.js"></script>
    <style>
        /* Estilos específicos para la página de inicio */
        .hero {
            background: linear-gradient(135deg, 
                rgba(99, 102, 241, 0.1) 0%, 
                rgba(16, 185, 129, 0.1) 50%, 
                rgba(59, 130, 246, 0.1) 100%);
            padding: 4rem 0;
            border-radius: var(--border-radius);
            margin-bottom: 3rem;
        }

        .hero-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .hero-text h2 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .hero-description {
            font-size: 1.3rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .cta-button.primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }

        .cta-button.secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .cta-button.large {
            padding: 1.2rem 2.5rem;
            font-size: 1.1rem;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .hero-visual {
            display: flex;
            justify-content: center;
        }

        .visual-card {
            background: var(--bg-card);
            padding: 2rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            text-align: center;
            transition: var(--transition);
        }

        .visual-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-color);
        }

        .visual-card i {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        /* Sección de Estadísticas */
        .stats-section {
            margin-bottom: 4rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .section-header p {
            font-size: 1.2rem;
            color: var(--text-secondary);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .stat-card {
            background: var(--bg-card);
            padding: 2rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: var(--transition);
        }

        .stat-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon i {
            font-size: 1.5rem;
            color: white;
        }

        .stat-info h3 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        /* Sección de Características */
        .features-section {
            margin-bottom: 4rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: var(--bg-card);
            padding: 2.5rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .feature-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-5px);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .feature-icon i {
            font-size: 2rem;
            color: white;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .feature-card p {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .feature-list {
            list-style: none;
        }

        .feature-list li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-secondary);
        }

        .feature-list i {
            color: var(--secondary-color);
        }

        /* Sección de Tecnologías */
        .tech-section {
            margin-bottom: 4rem;
        }

        .tech-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .tech-card {
            background: var(--bg-card);
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            overflow: hidden;
            transition: var(--transition);
        }

        .tech-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .tech-card.ssd {
            border-top: 4px solid var(--ssd-color);
        }

        .tech-card.hdd {
            border-top: 4px solid var(--hdd-color);
        }

        .tech-card.nvme {
            border-top: 4px solid var(--nvme-color);
        }

        .tech-header {
            padding: 2rem;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }

        .tech-header i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .tech-card.ssd .tech-header i {
            color: var(--ssd-color);
        }

        .tech-card.hdd .tech-header i {
            color: var(--hdd-color);
        }

        .tech-card.nvme .tech-header i {
            color: var(--nvme-color);
        }

        .tech-header h3 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .tech-badge {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .tech-content {
            padding: 2rem;
        }

        .tech-specs {
            margin-bottom: 1.5rem;
        }

        .spec {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .spec:last-child {
            border-bottom: none;
        }

        .spec i {
            color: var(--primary-color);
            width: 20px;
        }

        .tech-description {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* Workflow Section */
        .workflow-section {
            margin-bottom: 4rem;
        }

        .workflow-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .workflow-step {
            background: var(--bg-card);
            padding: 2rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            text-align: center;
            transition: var(--transition);
            position: relative;
        }

        .workflow-step:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .step-number {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0 auto 1.5rem;
        }

        .step-content h3 {
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .step-content p {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, 
                rgba(99, 102, 241, 0.1) 0%, 
                rgba(16, 185, 129, 0.1) 100%);
            padding: 4rem 2rem;
            border-radius: var(--border-radius);
            text-align: center;
            margin-bottom: 3rem;
        }

        .cta-content h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .cta-content p {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Footer Mejorado */
        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 2rem;
        }

        .footer-section h4 {
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            padding: 0.5rem 0;
        }

        .footer-section ul li a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-section ul li a:hover {
            color: var(--primary-color);
        }

        .footer-bottom {
            border-top: 1px solid var(--border-color);
            padding-top: 2rem;
            text-align: center;
            color: var(--text-muted);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-text h2 {
                font-size: 2.5rem;
            }

            .hero-actions {
                justify-content: center;
            }

            .footer-content {
                grid-template-columns: 1fr;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .cta-button.large {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</body>
</html>