// charts.js - Gestión de gráficos y visualizaciones - SIN GRÁFICO RADAR
class ChartManager {
    constructor() {
        this.apiBaseUrl = 'http://localhost:5000/api';
        this.currentCharts = {};
        this.initialized = false;
    }
    
    // Inicializar el manager
    async initialize() {
        if (this.initialized) return;
        
        try {
            await this.loadStatistics(); // Cargar stats primero
            await this.loadAllCharts();
            this.initialized = true;
        } catch (error) {
            console.error('Error initializing ChartManager:', error);
        }
    }
    
    // Cargar todos los gráficos
    async loadAllCharts() {
        try {
            this.showLoading('carousel-container');
            
            const response = await fetch(`${this.apiBaseUrl}/charts`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.currentCharts = data.charts;
                this.createChartCarousel(data.charts);
                this.renderCharts(data.charts);
            } else {
                throw new Error(data.error || 'Error al cargar gráficos');
            }
        } catch (error) {
            console.error('Error loading charts:', error);
            this.showError('carousel-container', 'No se pudieron cargar los gráficos. Verifique que el backend esté ejecutándose.');
        } finally {
            this.hideLoading('carousel-container');
        }
    }
    
    // Cargar estadísticas
    async loadStatistics() {
        try {
            this.showLoading('stats-container');
            
            const response = await fetch(`${this.apiBaseUrl}/stats`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.renderStatistics(data.statistics);
                this.updateModelInfo();
                this.updateSystemStatus();
            } else {
                // Si hay error pero hay datos en data, intentar renderizar igual
                if (data.data && data.statistics) {
                    this.renderStatistics(data.statistics);
                } else {
                    throw new Error(data.error || 'Error al cargar estadísticas');
                }
            }
        } catch (error) {
            console.error('Error loading statistics:', error);
            this.showError('stats-container', 
                'No se pudieron cargar las estadísticas. ' +
                'Asegúrese de que el servidor backend esté ejecutándose en http://localhost:5000'
            );
        } finally {
            this.hideLoading('stats-container');
        }
    }
    
    // Renderizar estadísticas
    renderStatistics(stats) {
        const container = document.getElementById('stats-container');
        if (!container) return;
        
        // Validar que las estadísticas existan
        if (!stats) {
            this.showError('stats-container', 'No hay datos estadísticos disponibles');
            return;
        }
        
        try {
            container.innerHTML = `
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number" data-count="${stats.total_registros || 0}">0</div>
                        <div class="stat-label">Registros Totales</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" data-count="${stats.vitaminas_unicas || 0}">0</div>
                        <div class="stat-label">Vitaminas Diferentes</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${(stats.incremento_promedio || 0).toFixed(2)}</div>
                        <div class="stat-label">Incremento Promedio (M/mL)</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${(stats.dosis_promedio || 0).toFixed(2)}</div>
                        <div class="stat-label">Dosis Diaria Promedio (mg)</div>
                    </div>
                </div>
            `;
            
            // Animar números
            this.animateNumbers();
            
        } catch (error) {
            console.error('Error rendering statistics:', error);
            this.showError('stats-container', 'Error al mostrar las estadísticas');
        }
    }
    
    // Animación de números
    animateNumbers() {
        const numberElements = document.querySelectorAll('[data-count]');
        numberElements.forEach(element => {
            const target = parseInt(element.getAttribute('data-count'));
            const duration = 2000;
            const steps = 60;
            const step = target / steps;
            let current = 0;
            let stepCount = 0;
            
            const timer = setInterval(() => {
                current += step;
                stepCount++;
                
                if (stepCount >= steps) {
                    element.textContent = target.toLocaleString();
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current).toLocaleString();
                }
            }, duration / steps);
        });
    }
    
    // Crear carrusel con gráficos estáticos - SIN RADAR
    createChartCarousel(charts) {
        const container = document.getElementById('carousel-container');
        if (!container) return;
        
        // Verificar qué gráficos están disponibles (excluyendo radar)
        const availableCharts = [];
        
        if (charts.area_chart) {
            availableCharts.push({
                type: 'area',
                title: 'Área sobre la Curva',
                description: 'Evolución del incremento de glóbulos rojos por dosis de vitamina',
                image: charts.area_chart
            });
        }
        
        if (charts.stacked_bar_chart) {
            availableCharts.push({
                type: 'stacked',
                title: 'Barras Apiladas',
                description: 'Incremento acumulado por vitamina y duración del tratamiento',
                image: charts.stacked_bar_chart
            });
        }
        
        if (charts.pie_chart) {
            availableCharts.push({
                type: 'pie',
                title: 'Distribución de Eficiencia',
                description: 'Porcentaje de eficiencia relativa entre diferentes vitaminas',
                image: charts.pie_chart
            });
        }
        
        if (charts.ml_insights) {
            availableCharts.push({
                type: 'ml',
                title: 'Insights de ML',
                description: 'Importancia de características en el modelo de Machine Learning',
                image: charts.ml_insights
            });
        }
        
        // Si no hay gráficos, mostrar mensaje
        if (availableCharts.length === 0) {
            container.innerHTML = `
                <div class="loading-container">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: var(--warning);"></i>
                    <p>No hay gráficos disponibles. Agrega algunos datos primero.</p>
                    <button class="btn btn-primary" onclick="window.location.href='dashboard.php'">
                        <i class="fas fa-plus"></i>
                        Agregar Datos
                    </button>
                </div>
            `;
            return;
        }
        
        let carouselHTML = `
            <div class="carousel" id="main-carousel">
                <div class="carousel-inner">
        `;
        
        // Generar HTML del carrusel
        availableCharts.forEach((item, index) => {
            carouselHTML += `
                <div class="carousel-item ${index === 0 ? 'active' : ''}">
                    <h3 style="margin-bottom: 1rem; color: var(--text-primary);">${item.title}</h3>
                    <div class="chart-image">
                        <img src="data:image/png;base64,${item.image}" 
                             alt="${item.title}" 
                             onclick="openModal('data:image/png;base64,${item.image}')"
                             style="cursor: pointer; max-height: 350px; border-radius: 12px;">
                    </div>
                    <p style="color: var(--text-secondary); margin-top: 1rem; max-width: 600px;">${item.description}</p>
                    <button class="btn btn-primary mt-2" onclick="openModal('data:image/png;base64,${item.image}')">
                        <i class="fas fa-expand"></i>
                        Ver en Grande
                    </button>
                </div>
            `;
        });
        
        carouselHTML += `
                </div>
                <div class="carousel-controls">
                    <button class="carousel-btn carousel-prev">
                        <i class="fas fa-chevron-left"></i>
                        Anterior
                    </button>
                    
                    <div class="carousel-indicators">
                        ${availableCharts.map((_, index) => 
                            `<div class="carousel-indicator ${index === 0 ? 'active' : ''}" 
                                  onclick="window.mainCarousel.goToSlide(${index})"></div>`
                        ).join('')}
                    </div>
                    
                    <button class="carousel-btn carousel-next">
                        Siguiente
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        `;
        
        container.innerHTML = carouselHTML;
        
        // Inicializar el carrusel
        window.mainCarousel = new Carousel('main-carousel');
    }
    
    // Renderizar gráficos individuales - SIN RADAR
    renderCharts(charts) {
        // Gráfico de área
        if (charts.area_chart) {
            this.renderBase64Chart('area-chart-container', charts.area_chart, 
                'Área sobre la Curva - Impacto de Vitaminas');
        } else {
            this.showError('area-chart-container', 'No se pudo generar el gráfico de área');
        }
        
        // Gráfico de barras apiladas
        if (charts.stacked_bar_chart) {
            this.renderBase64Chart('stacked-chart-container', charts.stacked_bar_chart,
                'Barras Apiladas - Incremento por Duración');
        } else {
            this.showError('stacked-chart-container', 'No se pudo generar el gráfico de barras');
        }
        
        // Gráfico de pastel
        if (charts.pie_chart) {
            this.renderBase64Chart('pie-chart-container', charts.pie_chart,
                'Distribución de Eficiencia por Vitamina');
        } else {
            this.showError('pie-chart-container', 'No se pudo generar el gráfico de pastel');
        }
    }
    
    // Renderizar gráfico base64
    renderBase64Chart(containerId, base64Data, title) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.innerHTML = `
            <div class="chart-container">
                <h4>${title}</h4>
                <div class="chart-image">
                    <img src="data:image/png;base64,${base64Data}" 
                         alt="${title}" 
                         style="cursor: pointer;"
                         onclick="openModal('data:image/png;base64,${base64Data}')">
                </div>
                <div class="text-center mt-2">
                    <button class="btn btn-primary" onclick="openModal('data:image/png;base64,${base64Data}')">
                        <i class="fas fa-expand"></i>
                        Ver en Grande
                    </button>
                </div>
            </div>
        `;
    }
    
    // Cargar gráfico específico para tabs - SIN RADAR
    async loadSpecificChart(chartType) {
        const containerId = `${chartType}-chart-container`;
        
        try {
            this.showLoading(containerId);
            
            const response = await fetch(`${this.apiBaseUrl}/charts/${chartType}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success && data.chart) {
                this.renderBase64Chart(containerId, data.chart, this.getChartTitle(chartType));
            } else {
                throw new Error(data.error || `No se pudo generar el gráfico ${chartType}`);
            }
        } catch (error) {
            console.error(`Error loading ${chartType} chart:`, error);
            this.showError(containerId, `Error al cargar el gráfico: ${error.message}`);
        }
    }
    
    getChartTitle(chartType) {
        const titles = {
            'area': 'Área sobre la Curva - Impacto de Vitaminas',
            'stacked': 'Barras Apiladas - Incremento por Duración',
            'pie': 'Distribución de Eficiencia por Vitamina',
            'ml_insights': 'Insights de Machine Learning'
        };
        return titles[chartType] || 'Gráfico';
    }
    
    // Actualizar información del modelo
    async updateModelInfo() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/model/info`);
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    const modelInfo = data.model_info;
                    const modelStatus = document.getElementById('model-status');
                    const modelAccuracy = document.getElementById('model-accuracy');
                    
                    if (modelStatus && modelAccuracy) {
                        if (modelInfo.is_trained) {
                            modelStatus.textContent = 'Entrenado';
                            modelStatus.className = 'badge badge-success';
                            const r2 = modelInfo.metrics?.r2_score || 0;
                            modelAccuracy.textContent = r2.toFixed(3);
                        } else {
                            modelStatus.textContent = 'No Entrenado';
                            modelStatus.className = 'badge badge-warning';
                            modelAccuracy.textContent = '-';
                        }
                    }
                }
            }
        } catch (error) {
            console.error('Error updating model info:', error);
        }
    }
    
    // Actualizar estado del sistema
    async updateSystemStatus() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/health`);
            if (response.ok) {
                const statusBadge = document.getElementById('status-badge');
                if (statusBadge) {
                    statusBadge.textContent = 'En Línea';
                    statusBadge.className = 'badge badge-success';
                }
            }
        } catch (error) {
            console.error('Error updating system status:', error);
            const statusBadge = document.getElementById('status-badge');
            if (statusBadge) {
                statusBadge.textContent = 'Offline';
                statusBadge.className = 'badge badge-danger';
            }
        }
    }
    
    // Realizar predicción
    async makePrediction(formData) {
        try {
            this.showLoading('prediction-result');
            
            const response = await fetch(`${this.apiBaseUrl}/predict`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showPredictionResult(data);
                this.updateModelInfo();
            } else {
                throw new Error(data.error || 'Error en la predicción');
            }
        } catch (error) {
            console.error('Error making prediction:', error);
            this.showError('prediction-result', 'Error en la predicción: ' + error.message);
        } finally {
            this.hideLoading('prediction-result');
        }
    }
    
    // Mostrar resultado de predicción
    showPredictionResult(data) {
        const container = document.getElementById('prediction-result');
        const confidence = data.confidence === 'alta' ? 'success' : 
                          data.confidence === 'media' ? 'warning' : 'danger';
        
        container.innerHTML = `
            <div class="alert alert-${confidence}">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <i class="fas fa-robot" style="font-size: 2rem;"></i>
                    <div style="flex: 1;">
                        <h4 style="margin: 0; color: inherit;">Predicción ML Completada</h4>
                        <p style="margin: 0; opacity: 0.9;">${data.message}</p>
                    </div>
                </div>
                
                <div style="background: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 12px; margin-top: 1rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div style="text-align: center;">
                            <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Incremento Estimado</div>
                            <div style="font-size: 2rem; font-weight: bold; color: var(--${confidence});">
                                +${data.prediction_rounded} M/mL
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Nivel de Confianza</div>
                            <span class="badge badge-${confidence}" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                ${data.confidence}
                            </span>
                        </div>
                    </div>
                </div>
                
                ${data.model_metrics?.r2_score ? `
                    <div style="margin-top: 1rem; font-size: 0.9rem; opacity: 0.8; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-info-circle"></i>
                        Modelo entrenado con R²: ${data.model_metrics.r2_score.toFixed(3)}
                    </div>
                ` : ''}
            </div>
        `;
    }
    
    // Mostrar error
    showError(containerId, message) {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = `
                <div class="alert alert-warning">
                    <div style="display: flex; align-items: flex-start; gap: 1rem;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; margin-top: 0.25rem;"></i>
                        <div style="flex: 1;">
                            <h4 style="margin: 0 0 0.5rem 0; color: inherit;">Atención</h4>
                            <p style="margin: 0; opacity: 0.9;">${message}</p>
                            ${containerId === 'stats-container' ? `
                                <div style="margin-top: 1rem;">
                                    <button class="btn btn-primary btn-sm" onclick="window.chartManager.loadStatistics()">
                                        <i class="fas fa-redo"></i>
                                        Reintentar
                                    </button>
                                    <button class="btn btn-outline btn-sm" onclick="window.location.href='dashboard.php'">
                                        <i class="fas fa-plus"></i>
                                        Agregar Datos
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }
    }
    
    // Mostrar loading
    showLoading(containerId) {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = `
                <div class="loading-container">
                    <div class="loading"></div>
                    <p>Procesando...</p>
                </div>
            `;
        }
    }
    
    // Ocultar loading
    hideLoading(containerId) {
        // Se maneja al reemplazar el contenido
    }
}

// Clase Carousel (sin cambios)
class Carousel {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        if (!this.container) return;
        
        this.inner = this.container.querySelector('.carousel-inner');
        this.items = this.container.querySelectorAll('.carousel-item');
        this.prevBtn = this.container.querySelector('.carousel-prev');
        this.nextBtn = this.container.querySelector('.carousel-next');
        this.indicators = this.container.querySelectorAll('.carousel-indicator');
        this.currentIndex = 0;
        
        this.init();
    }
    
    init() {
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', () => this.prev());
        }
        
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', () => this.next());
        }
        
        this.indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => this.goToSlide(index));
        });
        
        this.updateControls();
    }
    
    next() {
        if (this.currentIndex < this.items.length - 1) {
            this.currentIndex++;
            this.updateCarousel();
        }
    }
    
    prev() {
        if (this.currentIndex > 0) {
            this.currentIndex--;
            this.updateCarousel();
        }
    }
    
    updateCarousel() {
        const translateX = -this.currentIndex * 100;
        this.inner.style.transform = `translateX(${translateX}%)`;
        this.updateControls();
    }
    
    updateControls() {
        if (this.prevBtn) {
            this.prevBtn.disabled = this.currentIndex === 0;
        }
        
        if (this.nextBtn) {
            this.nextBtn.disabled = this.currentIndex === this.items.length - 1;
        }
        
        this.indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === this.currentIndex);
        });
        
        this.items.forEach((item, index) => {
            item.classList.toggle('active', index === this.currentIndex);
        });
    }
    
    goToSlide(index) {
        if (index >= 0 && index < this.items.length) {
            this.currentIndex = index;
            this.updateCarousel();
        }
    }
}

// Funciones globales para tabs - SIN RADAR
function switchTab(tabName) {
    // Ocultar todos los tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Mostrar tab seleccionado
    const targetTab = document.getElementById(tabName);
    if (targetTab) {
        targetTab.classList.add('active');
    }
    
    // Actualizar botones de tab
    document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Cargar gráficos específicos si es necesario
    if (tabName === 'area-tab' && !window.areaChartLoaded) {
        window.chartManager.loadSpecificChart('area');
        window.areaChartLoaded = true;
    } else if (tabName === 'stacked-tab' && !window.stackedChartLoaded) {
        window.chartManager.loadSpecificChart('stacked');
        window.stackedChartLoaded = true;
    } else if (tabName === 'pie-tab' && !window.pieChartLoaded) {
        window.chartManager.loadSpecificChart('pie');
        window.pieChartLoaded = true;
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.chartManager = new ChartManager();
    window.Carousel = Carousel;
    
    // Inicializar variables de estado para tabs
    window.areaChartLoaded = false;
    window.stackedChartLoaded = false;
    window.pieChartLoaded = false;
    
    // Inicializar el chart manager
    window.chartManager.initialize();
    
    // Configurar formulario de predicción
    const predictionForm = document.getElementById('prediction-form');
    if (predictionForm) {
        predictionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                vitamina: document.getElementById('vitamina').value,
                dosis_diaria: parseFloat(document.getElementById('dosis_diaria').value),
                duracion_semanas: parseInt(document.getElementById('duracion_semanas').value),
                globulos_rojos_inicio: parseFloat(document.getElementById('globulos_inicio').value),
                edad_paciente: document.getElementById('edad_paciente').value ? 
                    parseInt(document.getElementById('edad_paciente').value) : null,
                sexo: document.getElementById('sexo').value || null
            };
            
            window.chartManager.makePrediction(formData);
        });
    }
    
    // Configurar navegación por anclas
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const targetId = href.substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
});