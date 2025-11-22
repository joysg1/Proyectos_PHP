<?php
// Configuración básica
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <script src="js/api.js"></script>
    <script src="js/charts.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficos - Análisis Poblacional</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">🌎 PopAnalytics</a>
                <ul class="nav-links">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="graficos.php" class="active">Gráficos</a></li>
                    <li><a href="#" onclick="openModal('infoModal')">Información</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Visualizaciones de Datos</h1>
            <p>Gráficos avanzados y análisis visual del crecimiento poblacional en América Latina</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container">
        <!-- Tabs for different charts -->
        <div class="tabs">
            <div class="tab-headers">
                <button class="tab-header active" onclick="switchTab('area')">Área</button>
                <button class="tab-header" onclick="switchTab('radar')">Radar</button>
                <button class="tab-header" onclick="switchTab('barras')">Barras Apiladas</button>
                <button class="tab-header" onclick="switchTab('pastel')">Pastel</button>
                <button class="tab-header" onclick="switchTab('comparacion')">Comparación</button>
            </div>
            
            <div class="tab-content">
                <!-- Gráfico de Área -->
                <div class="tab-pane active" id="area">
                    <div class="chart-container">
                        <h3 class="chart-title">Evolución Poblacional - Gráfico de Área</h3>
                        <div id="areaChart">
                            <div class="loading">
                                <div class="spinner"></div>
                                <p>Cargando gráfico de área...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos Radar -->
                <div class="tab-pane" id="radar">
                    <div class="chart-container">
                        <h3 class="chart-title">Indicadores Poblacionales - Gráficos Radar</h3>
                        <div id="radarChart">
                            <div class="loading">
                                <div class="spinner"></div>
                                <p>Cargando gráficos radar...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Barras Apiladas -->
                <div class="tab-pane" id="barras">
                    <div class="chart-container">
                        <h3 class="chart-title">Distribución por Grupos de Edad - Barras Apiladas</h3>
                        <div id="barChart">
                            <div class="loading">
                                <div class="spinner"></div>
                                <p>Cargando gráfico de barras apiladas...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Pastel -->
                <div class="tab-pane" id="pastel">
                    <div class="chart-container">
                        <h3 class="chart-title">Distribución Poblacional Regional - Gráfico de Pastel</h3>
                        <div id="pieChart">
                            <div class="loading">
                                <div class="spinner"></div>
                                <p>Cargando gráfico de pastel...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comparación Radar -->
                <div class="tab-pane" id="comparacion">
                    <div class="chart-container">
                        <h3 class="chart-title">Comparativa entre Países - Gráfico Radar</h3>
                        <div class="form-group">
                            <label class="form-label">Seleccionar Países para Comparar:</label>
                            <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                                <select class="form-input" id="compareCountry1">
                                    <option value="">Cargando países...</option>
                                </select>
                                <select class="form-input" id="compareCountry2">
                                    <option value="">Cargando países...</option>
                                </select>
                                <button class="btn btn-primary" onclick="loadComparisonChart()">Comparar</button>
                            </div>
                        </div>
                        <div id="comparisonChart">
                            <p>Selecciona dos países para generar la comparación visual.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Information Modal -->
    <div class="modal" id="infoModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('infoModal')">&times;</span>
            <h2>Información de Gráficos</h2>
            <div class="card">
                <h3>📊 Tipos de Visualizaciones</h3>
                
                <h4>Gráfico de Área</h4>
                <p>Muestra la evolución temporal de la población por país, ideal para visualizar tendencias y comparar crecimiento.</p>
                
                <h4>Gráficos Radar</h4>
                <p>Permite comparar múltiples indicadores simultáneamente para cada país, mostrando patrones y características únicas.</p>
                
                <h4>Barras Apiladas</h4>
                <p>Visualiza la composición de la población por grupos de edad, facilitando el análisis de la estructura demográfica.</p>
                
                <h4>Gráfico de Pastel</h4>
                <p>Representa la distribución proporcional de la población entre los diferentes países de la región.</p>
                
                <h4>Comparación Radar</h4>
                <p>Herramienta interactiva para comparar directamente dos países en todos los indicadores clave.</p>
            </div>
        </div>
    </div>

    <script>
        // API Base URL
        const API_BASE = 'http://localhost:5000/api';

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            loadCountrySelects();
            
            // Cargar gráficos automáticamente al abrir cada pestaña
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        const target = mutation.target;
                        if (target.classList.contains('active') && target.classList.contains('tab-pane')) {
                            loadChartForTab(target.id);
                        }
                    }
                });
            });

            document.querySelectorAll('.tab-pane').forEach(pane => {
                observer.observe(pane, { attributes: true });
            });

            // Cargar primer gráfico
            loadAreaChart();
        });

        // Cargar selectores de países
        async function loadCountrySelects() {
            try {
                const response = await fetch(`${API_BASE}/paises`);
                const countries = await response.json();
                
                const options = countries.map(country => 
                    `<option value="${country.nombre}">${country.nombre}</option>`
                ).join('');
                
                document.getElementById('compareCountry1').innerHTML = 
                    '<option value="">Seleccionar país 1...</option>' + options;
                document.getElementById('compareCountry2').innerHTML = 
                    '<option value="">Seleccionar país 2...</option>' + options;
            } catch (error) {
                console.error('Error loading countries:', error);
            }
        }

        // Cambiar pestaña
        function switchTab(tabName) {
            // Ocultar todos los paneles
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('active');
            });
            
            // Remover active de todos los headers
            document.querySelectorAll('.tab-header').forEach(header => {
                header.classList.remove('active');
            });
            
            // Mostrar panel seleccionado
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }

        // Cargar gráfico según pestaña activa
        function loadChartForTab(tabId) {
            switch(tabId) {
                case 'area':
                    loadAreaChart();
                    break;
                case 'radar':
                    loadRadarChart();
                    break;
                case 'barras':
                    loadBarChart();
                    break;
                case 'pastel':
                    loadPieChart();
                    break;
                case 'comparacion':
                    // No cargar automáticamente, esperar selección del usuario
                    break;
            }
        }

        // Cargar gráfico de área
        async function loadAreaChart() {
            const container = document.getElementById('areaChart');
            container.innerHTML = '<div class="loading"><div class="spinner"></div><p>Cargando gráfico de área...</p></div>';
            
            try {
                const response = await fetch(`${API_BASE}/graficos/area`);
                const data = await response.json();
                
                container.innerHTML = `<img src="${data.image}" alt="Gráfico de Área" class="chart-image">`;
            } catch (error) {
                container.innerHTML = '<p>Error cargando el gráfico de área. Asegúrate de que el servidor Python esté ejecutándose.</p>';
            }
        }

        // Cargar gráficos radar
        async function loadRadarChart() {
            const container = document.getElementById('radarChart');
            container.innerHTML = '<div class="loading"><div class="spinner"></div><p>Cargando gráficos radar...</p></div>';
            
            try {
                const response = await fetch(`${API_BASE}/graficos/radar`);
                const data = await response.json();
                
                container.innerHTML = `<img src="${data.image}" alt="Gráficos Radar" class="chart-image">`;
            } catch (error) {
                container.innerHTML = '<p>Error cargando los gráficos radar. Asegúrate de que el servidor Python esté ejecutándose.</p>';
            }
        }

        // Cargar gráfico de barras apiladas
        async function loadBarChart() {
            const container = document.getElementById('barChart');
            container.innerHTML = '<div class="loading"><div class="spinner"></div><p>Cargando gráfico de barras apiladas...</p></div>';
            
            try {
                const response = await fetch(`${API_BASE}/graficos/barras-apiladas`);
                const data = await response.json();
                
                container.innerHTML = `<img src="${data.image}" alt="Gráfico de Barras Apiladas" class="chart-image">`;
            } catch (error) {
                container.innerHTML = '<p>Error cargando el gráfico de barras apiladas. Asegúrate de que el servidor Python esté ejecutándose.</p>';
            }
        }

        // Cargar gráfico de pastel
        async function loadPieChart() {
            const container = document.getElementById('pieChart');
            container.innerHTML = '<div class="loading"><div class="spinner"></div><p>Cargando gráfico de pastel...</p></div>';
            
            try {
                const response = await fetch(`${API_BASE}/graficos/pastel`);
                const data = await response.json();
                
                container.innerHTML = `<img src="${data.image}" alt="Gráfico de Pastel" class="chart-image">`;
            } catch (error) {
                container.innerHTML = '<p>Error cargando el gráfico de pastel. Asegúrate de que el servidor Python esté ejecutándose.</p>';
            }
        }

        // Cargar gráfico de comparación
        async function loadComparisonChart() {
            const country1 = document.getElementById('compareCountry1').value;
            const country2 = document.getElementById('compareCountry2').value;
            
            if (!country1 || !country2) {
                alert('Por favor selecciona dos países para comparar.');
                return;
            }
            
            if (country1 === country2) {
                alert('Por favor selecciona dos países diferentes.');
                return;
            }
            
            const container = document.getElementById('comparisonChart');
            container.innerHTML = '<div class="loading"><div class="spinner"></div><p>Generando comparación...</p></div>';
            
            try {
                const response = await fetch(`${API_BASE}/graficos/comparacion-radar?pais1=${encodeURIComponent(country1)}&pais2=${encodeURIComponent(country2)}`);
                const data = await response.json();
                
                container.innerHTML = `
                    <h4>Comparación: ${country1} vs ${country2}</h4>
                    <img src="${data.image}" alt="Gráfico de Comparación Radar" class="chart-image">
                `;
            } catch (error) {
                container.innerHTML = '<p>Error generando la comparación. Asegúrate de que el servidor Python esté ejecutándose.</p>';
            }
        }

        // Funciones del modal
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Cerrar modal al hacer click fuera
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>