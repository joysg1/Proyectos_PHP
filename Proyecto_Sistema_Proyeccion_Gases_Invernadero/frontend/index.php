<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Análisis de Gases de Efecto Invernadero</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header class="hero">
            <div class="hero-content">
                <h1>🌍 Análisis de Gases de Efecto Invernadero 2024-2100</h1>
                <p class="hero-subtitle">Visualización de escenarios futuros y análisis predictivo con Machine Learning</p>
                <div id="apiStatus" class="status-indicator status-offline">
                    <span class="status-dot"></span> Conectando con API...
                </div>
            </div>
        </header>

        <!-- Loading Overlay -->
        <div id="loadingCharts" class="loading-overlay" style="display: none;">
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <p>Generando visualizaciones avanzadas...</p>
                <small>Procesando datos para el período 2024-2100</small>
            </div>
        </div>

        <!-- Sección de Gráficos Principal con Pestañas -->
        <section class="charts-section">
            <div class="section-header">
                <h2>📊 Panel de Visualizaciones</h2>
                <p class="section-description">Explora los datos de emisiones de GEI a través de múltiples perspectivas visuales</p>
            </div>

            <!-- Controles de carga -->
            <div class="load-controls">
                <button id="loadChartsBtn" class="btn btn-large">
                    <span class="btn-icon">🚀</span>
                    Generar Todas las Visualizaciones
                </button>
                <div class="load-info">
                    <small>Se generarán 5 gráficos profesionales con análisis avanzado</small>
                </div>
            </div>

            <!-- Contenedor de pestañas -->
            <div class="tabs-container" id="tabsContainer" style="display: none;">
                <!-- Navegación de pestañas -->
                <div class="tabs-navigation">
                    <div class="tabs-scroll">
                        <button class="tab-btn active" data-tab="area">
                            <span class="tab-icon">📈</span>
                            Escenarios Futuros
                        </button>
                        <button class="tab-btn" data-tab="radar">
                            <span class="tab-icon">🎯</span>
                            Potencial GWP
                        </button>
                        <button class="tab-btn" data-tab="stacked_bar">
                            <span class="tab-icon">📊</span>
                            Sectores
                        </button>
                        <button class="tab-btn" data-tab="pie">
                            <span class="tab-icon">🥧</span>
                            Regiones
                        </button>
                        <button class="tab-btn" data-tab="trend">
                            <span class="tab-icon">📈</span>
                            Tendencias
                        </button>
                    </div>
                </div>

                <!-- Contenido de pestañas -->
                <div class="tabs-content">
                    <!-- Pestaña Escenarios Futuros -->
                    <div class="tab-pane active" id="tab-area">
                        <div class="chart-header">
                            <h3>Escenarios Futuros de Emisiones 2024-2100</h3>
                            <span class="chart-badge">Proyección</span>
                        </div>
                        <div class="chart-image-container">
                            <img id="chart-area" src="" alt="Escenarios Futuros" class="chart-image">
                            <div class="chart-loading">Generando visualización...</div>
                        </div>
                        <div class="chart-description">
                            <p>Proyección de escenarios de emisiones desde 2024 hasta 2100 bajo diferentes trayectorias climáticas. Analiza posibles futuros basados en políticas actuales y objetivos climáticos.</p>
                        </div>
                    </div>

                    <!-- Pestaña Potencial GWP -->
                    <div class="tab-pane" id="tab-radar">
                        <div class="chart-header">
                            <h3>Potencial de Calentamiento Global (GWP)</h3>
                            <span class="chart-badge">Comparación</span>
                        </div>
                        <div class="chart-image-container">
                            <img id="chart-radar" src="" alt="Potencial GWP" class="chart-image">
                            <div class="chart-loading">Generando visualización...</div>
                        </div>
                        <div class="chart-description">
                            <p>Comparación del Potencial de Calentamiento Global (GWP) de diferentes gases. El CO₂ tiene valor 1 como referencia. Datos basados en IPCC AR6 para 100 años.</p>
                        </div>
                    </div>

                    <!-- Pestaña Sectores -->
                    <div class="tab-pane" id="tab-stacked_bar">
                        <div class="chart-header">
                            <h3>Emisiones por Sector Económico</h3>
                            <span class="chart-badge">Evolución</span>
                        </div>
                        <div class="chart-image-container">
                            <img id="chart-stacked_bar" src="" alt="Emisiones por Sector" class="chart-image">
                            <div class="chart-loading">Generando visualización...</div>
                        </div>
                        <div class="chart-description">
                            <p>Evolución histórica y proyectada de las emisiones por sector económico. Muestra la contribución de cada sector al total global con espaciado temporal optimizado.</p>
                        </div>
                    </div>

                    <!-- Pestaña Regiones -->
                    <div class="tab-pane" id="tab-pie">
                        <div class="chart-header">
                            <h3>Distribución Regional de Emisiones</h3>
                            <span class="chart-badge">Distribución</span>
                        </div>
                        <div class="chart-image-container">
                            <img id="chart-pie" src="" alt="Distribución Regional" class="chart-image">
                            <div class="chart-loading">Generando visualización...</div>
                        </div>
                        <div class="chart-description">
                            <p>Distribución porcentual de las emisiones globales por región geográfica para el año 2024. Revela las contribuciones regionales al problema climático global.</p>
                        </div>
                    </div>

                    <!-- Pestaña Tendencias -->
                    <div class="tab-pane" id="tab-trend">
                        <div class="chart-header">
                            <h3>Tendencias Comparativas de Gases</h3>
                            <span class="chart-badge">Tendencias</span>
                        </div>
                        <div class="chart-image-container">
                            <img id="chart-trend" src="" alt="Tendencias Comparativas" class="chart-image">
                            <div class="chart-loading">Generando visualización...</div>
                        </div>
                        <div class="chart-description">
                            <p>Tendencias comparativas de los diferentes gases normalizadas al nivel de 2024. Permite identificar patrones de crecimiento relativos entre gases.</p>
                        </div>
                    </div>
                </div>

                <!-- Controles de navegación -->
                <div class="tabs-controls">
                    <button class="btn btn-outline" id="prevTab">
                        <span class="btn-icon">◀</span>
                        Anterior
                    </button>
                    <div class="tabs-indicator">
                        <span id="currentTab">1</span> de <span id="totalTabs">5</span>
                    </div>
                    <button class="btn btn-outline" id="nextTab">
                        Siguiente
                        <span class="btn-icon">▶</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Sección Informativa sobre Gases -->
        <section class="info-section">
            <div class="section-header">
                <h2>📚 Información sobre Gases de Efecto Invernadero</h2>
                <p class="section-description">Conoce los principales gases responsables del cambio climático y sus características</p>
            </div>
            
            <div class="gases-grid">
                <!-- ... (mantener el mismo contenido de gases) ... -->
                <div class="gas-card co2">
                    <h3>🌫️ CO₂ - Dióxido de Carbono</h3>
                    <div class="gas-stats">
                        <div class="stat">
                            <span class="stat-label">GWP (100 años):</span>
                            <span class="stat-value">1 (Referencia)</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Vida Atmosférica:</span>
                            <span class="stat-value">100-300 años</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Fuentes Principales:</span>
                            <span class="stat-value">Combustibles fósiles, deforestación</span>
                        </div>
                    </div>
                    <p class="gas-description">
                        Gas de referencia para medir el potencial de calentamiento global. 
                        Principal responsable del cambio climático antropogénico.
                    </p>
                </div>

                <div class="gas-card ch4">
                    <h3>🔥 CH₄ - Metano</h3>
                    <div class="gas-stats">
                        <div class="stat">
                            <span class="stat-label">GWP (100 años):</span>
                            <span class="stat-value">28-36</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Vida Atmosférica:</span>
                            <span class="stat-value">12 años</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Fuentes Principales:</span>
                            <span class="stat-value">Ganadería, arrozales, vertederos</span>
                        </div>
                    </div>
                    <p class="gas-description">
                        28 veces más potente que el CO₂ pero con vida más corta. 
                        Reducir emisiones de metano tiene efectos rápidos en el clima.
                    </p>
                </div>

                <div class="gas-card n2o">
                    <h3>⚗️ N₂O - Óxido Nitroso</h3>
                    <div class="gas-stats">
                        <div class="stat">
                            <span class="stat-label">GWP (100 años):</span>
                            <span class="stat-value">265-298</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Vida Atmosférica:</span>
                            <span class="stat-value">114 años</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Fuentes Principales:</span>
                            <span class="stat-value">Fertilizantes, procesos industriales</span>
                        </div>
                    </div>
                    <p class="gas-description">
                        Conocido como "gas de la risa", es 265 veces más potente que el CO₂ 
                        y también contribuye a la destrucción de la capa de ozono.
                    </p>
                </div>

                <div class="gas-card hfc">
                    <h3>🏭 HFC - Hidrofluorocarbonos</h3>
                    <div class="gas-stats">
                        <div class="stat">
                            <span class="stat-label">GWP (100 años):</span>
                            <span class="stat-value">1,300-14,800</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Vida Atmosférica:</span>
                            <span class="stat-value">15-270 años</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Fuentes Principales:</span>
                            <span class="stat-value">Refrigeración, aire acondicionado</span>
                        </div>
                    </div>
                    <p class="gas-description">
                        Desarrollados para reemplazar los CFC, son potentes GEI. 
                        El Protocolo de Kigali busca eliminarlos gradualmente.
                    </p>
                </div>

                <div class="gas-card pfc">
                    <h3>💎 PFC - Perfluorocarbonos</h3>
                    <div class="gas-stats">
                        <div class="stat">
                            <span class="stat-label">GWP (100 años):</span>
                            <span class="stat-value">6,630-11,100</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Vida Atmosférica:</span>
                            <span class="stat-value">10,000-50,000 años</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Fuentes Principales:</span>
                            <span class="stat-value">Producción de aluminio, semiconductores</span>
                        </div>
                    </div>
                    <p class="gas-description">
                        Extremadamente persistentes en la atmósfera con vidas útiles 
                        de miles de años. Muy estables y difíciles de eliminar.
                    </p>
                </div>

                <div class="gas-card sf6">
                    <h3>⚡ SF₆ - Hexafluoruro de Azufre</h3>
                    <div class="gas-stats">
                        <div class="stat">
                            <span class="stat-label">GWP (100 años):</span>
                            <span class="stat-value">23,500-23,900</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Vida Atmosférica:</span>
                            <span class="stat-value">3,200 años</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Fuentes Principales:</span>
                            <span class="stat-value">Equipos eléctricos, producción de magnesio</span>
                        </div>
                    </div>
                    <p class="gas-description">
                        El gas de efecto invernadero más potente conocido. 
                        Una sola molécula equivale a 23,500 moléculas de CO₂.
                    </p>
                </div>
            </div>

            <div class="gwp-explanation">
                <h3>💡 ¿Qué es el Potencial de Calentamiento Global (GWP)?</h3>
                <p>
                    El <strong>Potencial de Calentamiento Global (GWP)</strong> es una medida que compara 
                    la cantidad de calor que atrapa un gas de efecto invernadero en relación con 
                    la misma masa de dióxido de carbono (CO₂) durante un período específico, 
                    generalmente 100 años. El CO₂ tiene un GWP de 1 por definición.
                </p>
                <div class="gwp-comparison">
                    <div class="comparison-item">
                        <span class="gas-name">CO₂</span>
                        <div class="comparison-bar" style="width: 10px; background: #ff6b6b;"></div>
                        <span class="gwp-value">1</span>
                    </div>
                    <div class="comparison-item">
                        <span class="gas-name">CH₄</span>
                        <div class="comparison-bar" style="width: 280px; background: #4ecdc4;"></div>
                        <span class="gwp-value">28</span>
                    </div>
                    <div class="comparison-item">
                        <span class="gas-name">N₂O</span>
                        <div class="comparison-bar" style="width: 530px; background: #45b7d1;"></div>
                        <span class="gwp-value">265</span>
                    </div>
                    <div class="comparison-item">
                        <span class="gas-name">SF₆</span>
                        <div class="comparison-bar" style="width: 2350px; background: #feca57; max-width: 400px;"></div>
                        <span class="gwp-value">23,500</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección de ML -->
        <section id="mlResults" class="ml-results">
            <!-- Los resultados de ML se cargarán aquí dinámicamente -->
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-content">
                <p>Sistema de Análisis de Gases de Efecto Invernadero</p>
                <p>Desarrollado con Python, Flask, Machine Learning y Visualizaciones Avanzadas</p>
                <p>Datos basados en IPCC AR6, IEA y proyecciones propias</p>
            </div>
        </footer>
    </div>

    <script src="js/main.js"></script>
</body>
</html>