// main.js - SISTEMA DE PESTAÑAS PROFESIONAL CORREGIDO
class GreenhouseGasApp {
    constructor() {
        this.currentTab = 0;
        this.tabs = ['area', 'radar', 'stacked_bar', 'pie', 'trend'];
        this.charts = {};
        this.isLoading = false;
        this.init();
    }

    async init() {
        console.log('🚀 Inicializando aplicación...');
        this.bindEvents();
        await this.checkAPIHealth();
    }

    bindEvents() {
        // Botón de carga de gráficos
        const loadBtn = document.getElementById('loadChartsBtn');
        if (loadBtn) {
            console.log('✅ Botón encontrado, agregando event listener...');
            loadBtn.addEventListener('click', () => {
                console.log('🎯 Click en botón detectado');
                this.loadAllCharts();
            });
        } else {
            console.error('❌ Botón loadChartsBtn no encontrado');
        }

        // Navegación de pestañas
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const tabName = e.currentTarget.getAttribute('data-tab');
                this.switchTab(tabName);
            });
        });

        // Controles de navegación
        document.getElementById('prevTab')?.addEventListener('click', () => {
            this.prevTab();
        });

        document.getElementById('nextTab')?.addEventListener('click', () => {
            this.nextTab();
        });

        // Navegación por teclado
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') this.prevTab();
            if (e.key === 'ArrowRight') this.nextTab();
        });
    }

    async checkAPIHealth() {
        try {
            console.log('🔍 Verificando salud de la API...');
            const response = await this.makeRequest('health_check');
            const statusElement = document.getElementById('apiStatus');
            
            if (statusElement) {
                if (response.status === 'healthy' && response.data_loaded) {
                    statusElement.innerHTML = '<span class="status-dot"></span> Sistema Conectado ✓';
                    statusElement.className = 'status-indicator status-online';
                    console.log('✅ API conectada correctamente con datos cargados');
                    return true;
                } else {
                    statusElement.innerHTML = '<span class="status-dot"></span> Sistema Parcialmente Conectado';
                    statusElement.className = 'status-indicator status-offline';
                    console.warn('⚠️ API conectada pero hay problemas con los datos:', response);
                    return false;
                }
            }
        } catch (error) {
            console.error('❌ Error checking API health:', error);
            const statusElement = document.getElementById('apiStatus');
            if (statusElement) {
                statusElement.innerHTML = '<span class="status-dot"></span> Sistema Desconectado ✗';
                statusElement.className = 'status-indicator status-offline';
            }
            this.showError('El servidor de Python no está disponible. Asegúrate de que esté ejecutándose en http://localhost:5000');
            return false;
        }
    }

    async loadAllCharts() {
        if (this.isLoading) {
            console.log('⏳ Ya se está cargando, ignorando click...');
            return;
        }
        
        this.isLoading = true;
        const loadBtn = document.getElementById('loadChartsBtn');
        const loadingElement = document.getElementById('loadingCharts');
        
        console.log('🔄 Iniciando carga de gráficos...');
        
        // Actualizar estado del botón
        if (loadBtn) {
            loadBtn.innerHTML = '<span class="btn-icon">⏳</span> Generando Visualizaciones...';
            loadBtn.disabled = true;
            console.log('✅ Botón actualizado a estado de carga');
        }
        
        if (loadingElement) {
            loadingElement.style.display = 'flex';
            console.log('✅ Loading overlay mostrado');
        }

        try {
            console.log('📡 Enviando solicitud a /api/charts/all...');
            const response = await this.makeRequest('all_charts');
            console.log('📨 Respuesta recibida:', response);
            
            if (response.success && response.charts) {
                this.charts = response.charts;
                console.log(`✅ ${Object.keys(this.charts).length} gráficos cargados exitosamente:`, Object.keys(this.charts));
                
                // Mostrar el contenedor de pestañas
                const tabsContainer = document.getElementById('tabsContainer');
                if (tabsContainer) {
                    tabsContainer.style.display = 'block';
                    console.log('✅ Contenedor de pestañas mostrado');
                }
                
                // Ocultar controles de carga
                if (loadBtn) {
                    loadBtn.style.display = 'none';
                    console.log('✅ Botón de carga ocultado');
                }
                
                // Cargar imágenes en las pestañas
                this.loadChartImages();
                
                // Cargar predicciones ML
                this.loadMLPredictions();
                
                console.log('🎯 Sistema de pestañas activado correctamente');
            } else {
                console.error('❌ Error en respuesta:', response);
                throw new Error(response.error || 'Error cargando gráficos');
            }
        } catch (error) {
            console.error('❌ Error cargando gráficos:', error);
            this.showError(`Error al cargar los gráficos: ${error.message}`);
            
            // Restaurar botón
            if (loadBtn) {
                loadBtn.innerHTML = '<span class="btn-icon">🔄</span> Reintentar Carga';
                loadBtn.disabled = false;
                console.log('✅ Botón restaurado para reintentar');
            }
        } finally {
            this.isLoading = false;
            if (loadingElement) {
                loadingElement.style.display = 'none';
                console.log('✅ Loading overlay ocultado');
            }
        }
    }

    loadChartImages() {
        console.log('🖼️ Cargando imágenes de gráficos...');
        
        // Cargar cada gráfico en su pestaña correspondiente
        Object.entries(this.charts).forEach(([chartType, chartData]) => {
            const imgElement = document.getElementById(`chart-${chartType}`);
            if (imgElement) {
                console.log(`📊 Cargando gráfico ${chartType}...`);
                imgElement.src = chartData;
                imgElement.onload = () => {
                    console.log(`✅ Gráfico ${chartType} cargado correctamente`);
                    // Ocultar loading cuando la imagen se carga
                    const loadingElement = imgElement.parentElement.querySelector('.chart-loading');
                    if (loadingElement) {
                        loadingElement.style.display = 'none';
                    }
                };
                imgElement.onerror = () => {
                    console.error(`❌ Error cargando gráfico ${chartType}`);
                    this.showError(`Error cargando el gráfico ${chartType}`);
                };
            } else {
                console.warn(`⚠️ Elemento chart-${chartType} no encontrado`);
            }
        });
    }

    switchTab(tabName) {
        console.log(`📊 Cambiando a pestaña: ${tabName}`);
        
        // Remover clase active de todas las pestañas y botones
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('active');
        });
        
        // Activar pestaña seleccionada
        const targetBtn = document.querySelector(`[data-tab="${tabName}"]`);
        const targetPane = document.getElementById(`tab-${tabName}`);
        
        if (targetBtn && targetPane) {
            targetBtn.classList.add('active');
            targetPane.classList.add('active');
            this.currentTab = this.tabs.indexOf(tabName);
            this.updateTabIndicator();
            console.log(`✅ Pestaña ${tabName} activada`);
        } else {
            console.error(`❌ No se pudo encontrar pestaña ${tabName}`);
        }
    }

    nextTab() {
        this.currentTab = (this.currentTab + 1) % this.tabs.length;
        this.switchTab(this.tabs[this.currentTab]);
    }

    prevTab() {
        this.currentTab = (this.currentTab - 1 + this.tabs.length) % this.tabs.length;
        this.switchTab(this.tabs[this.currentTab]);
    }

    updateTabIndicator() {
        const currentElement = document.getElementById('currentTab');
        const totalElement = document.getElementById('totalTabs');
        
        if (currentElement) {
            currentElement.textContent = this.currentTab + 1;
        }
        if (totalElement) {
            totalElement.textContent = this.tabs.length;
        }
    }

    async loadMLPredictions() {
        try {
            console.log('🤖 Cargando predicciones de Machine Learning...');
            const response = await this.makeRequest('ml_predictions');
            if (response.success) {
                this.displayMLResults(response.predictions, response.trends);
                console.log('✅ Predicciones ML cargadas exitosamente');
            } else {
                console.warn('⚠️ Error cargando predicciones ML:', response.error);
            }
        } catch (error) {
            console.error('❌ Error cargando predicciones ML:', error);
        }
    }

    displayMLResults(predictions, trends) {
        const resultsContainer = document.getElementById('mlResults');
        if (!resultsContainer) {
            console.warn('⚠️ Contenedor mlResults no encontrado');
            return;
        }
        
        console.log('📈 Mostrando resultados ML...');
        
        let predictionsHTML = '';
        if (predictions && Object.keys(predictions).length > 0) {
            for (const [modelName, data] of Object.entries(predictions)) {
                if (modelName !== 'confidence_intervals' && modelName !== 'feature_importance') {
                    const latestPrediction = data.predictions[data.predictions.length - 1];
                    predictionsHTML += `
                        <div class="prediction-card fade-in">
                            <h4>${this.formatModelName(modelName)}</h4>
                            <div class="trend-value">${Math.round(latestPrediction).toLocaleString()} MtCO₂eq</div>
                            <p>Predicción para 2100</p>
                            <div class="model-metrics">
                                <small>Precisión (R²): ${data.metrics?.r2?.toFixed(3) || 'N/A'}</small>
                                <small>Error: ${data.metrics?.mse ? Math.sqrt(data.metrics.mse).toFixed(0) : 'N/A'}</small>
                            </div>
                        </div>
                    `;
                }
            }
        } else {
            predictionsHTML = '<div class="no-predictions">No hay predicciones disponibles en este momento</div>';
        }

        let trendsHTML = '';
        if (trends && Object.keys(trends).length > 0) {
            for (const [gas, trend] of Object.entries(trends)) {
                if (gas !== 'economic_correlations' && gas !== 'sectors') {
                    trendsHTML += `
                        <div class="trend-item slide-in-left">
                            <div class="trend-gas">${gas}</div>
                            <div class="trend-value ${trend.avg_growth_rate > 0 ? 'trend-positive' : 'trend-negative'}">
                                ${trend.avg_growth_rate > 0 ? '+' : ''}${trend.avg_growth_rate?.toFixed(1) || '0'}%
                            </div>
                            <div class="trend-direction ${trend.direction === 'creciente' ? 'trend-up' : 'trend-down'}">
                                ${trend.direction || 'Estable'}
                            </div>
                        </div>
                    `;
                }
            }
        } else {
            trendsHTML = '<div class="no-trends">No hay datos de tendencias disponibles</div>';
        }

        resultsContainer.innerHTML = `
            <div class="ml-section">
                <h3>🔮 Predicciones y Tendencias 2024-2100</h3>
                <div class="prediction-grid">
                    ${predictionsHTML}
                </div>
            </div>
            <div class="trends-section">
                <h4>Tendencias Anuales de Gases</h4>
                <div class="trends-grid">
                    ${trendsHTML}
                </div>
            </div>
        `;
    }

    formatModelName(modelName) {
        const names = {
            'linear': '📐 Regresión Lineal',
            'random_forest': '🌲 Random Forest',
            'gradient_boosting': '📊 Gradient Boosting',
            'svr': '🔍 Máquina de Vectores',
            'neural_network': '🧠 Red Neuronal',
            'ensemble': '🎯 Modelo Ensemble'
        };
        return names[modelName] || modelName;
    }

    async makeRequest(action) {
        try {
            console.log(`📤 Enviando solicitud para acción: ${action}`);
            const formData = new FormData();
            formData.append('action', action);

            const response = await fetch('api/api_handler.php', {
                method: 'POST',
                body: formData
            });

            console.log(`📨 Respuesta HTTP: ${response.status}`);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
            }

            const data = await response.json();
            console.log(`✅ Respuesta JSON recibida para ${action}:`, data);
            return data;
        } catch (error) {
            console.error(`❌ Error en makeRequest para ${action}:`, error);
            throw error;
        }
    }

    showError(message) {
        console.error('🚨 Mostrando error:', message);
        
        const notification = document.createElement('div');
        notification.className = 'error-notification';
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-icon">⚠️</span>
                <span class="notification-message">${message}</span>
                <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 8000);
    }

    debugInfo() {
        return {
            currentTab: this.currentTab,
            totalTabs: this.tabs.length,
            chartsLoaded: Object.keys(this.charts).length,
            isLoading: this.isLoading
        };
    }
}

// Inicializar la aplicación cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Inicializando Sistema de Análisis de GEI - Sistema de Pestañas');
    window.app = new GreenhouseGasApp();
    
    // Exponer para debugging
    window.debugApp = () => {
        console.log('🔍 Estado de la aplicación:', window.app.debugInfo());
    };
    
    console.log('✅ Aplicación inicializada correctamente');
});