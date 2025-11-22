// JavaScript para el sistema de análisis de datos - Versión Mejorada
class FitnessAnalysisSystem {
    constructor() {
        this.currentData = [];
        this.chartCache = {};
        this.analyticalInsights = {};
        this.init();
    }

    init() {
        this.loadInitialData();
        this.setupEventListeners();
        this.setupTabs();
        this.loadAnalyticalInsights();
    }

    setupEventListeners() {
        // Navegación entre pestañas
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.navigateTo(link.getAttribute('href'));
            });
        });

        // Formulario de nuevo registro
        const recordForm = document.getElementById('recordForm');
        if (recordForm) {
            recordForm.addEventListener('submit', (e) => this.handleRecordSubmit(e));
        }

        // Botón de entrenar modelo ML
        const trainBtn = document.getElementById('trainModel');
        if (trainBtn) {
            trainBtn.addEventListener('click', () => this.trainMLModel());
        }

        // Botón de recargar todos los gráficos
        const refreshBtn = document.getElementById('refreshCharts');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => this.refreshAllCharts());
        }
    }

    setupTabs() {
        const tabs = document.querySelectorAll('.tab');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabName = tab.getAttribute('data-tab');
                this.switchTab(tabName);
            });
        });
    }

    switchTab(tabName) {
        // Actualizar tabs activos
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });

        // Activar tab seleccionado
        const selectedTab = document.querySelector(`[data-tab="${tabName}"]`);
        const selectedContent = document.getElementById(`${tabName}-tab`);
        
        if (selectedTab) selectedTab.classList.add('active');
        if (selectedContent) selectedContent.classList.add('active');

        // Cargar contenido específico según la pestaña
        if (tabName === 'charts') {
            this.loadAllCharts();
        }
        
        if (tabName === 'trends') {
            this.loadTrendsCharts();
        }
        
        if (tabName === 'analysis') {
            this.loadAnalyticalContent();
        }
        
        if (tabName === 'analysis') {
            this.updateQuickAnalysis();
        }
    }

    async loadInitialData() {
        try {
            this.showAlert('Cargando datos iniciales...', 'info');
            const response = await this.apiRequest('/data');
            if (response.success) {
                this.currentData = response.data;
                this.updateDataTable();
                this.updateStats();
                this.updateQuickAnalysis();
                this.showAlert('Datos cargados exitosamente', 'success');
            }
        } catch (error) {
            console.error('Error loading data:', error);
            this.showAlert('Error al cargar los datos. Verifique que el servidor esté ejecutándose.', 'error');
        }
    }

    async loadAnalyticalInsights() {
        try {
            const response = await this.apiRequest('/analytics/insights');
            if (response.success) {
                this.analyticalInsights = response.data;
                this.updateQuickAnalysis();
            }
        } catch (error) {
            console.error('Error loading insights:', error);
        }
    }

    updateDataTable() {
        const tbody = document.querySelector('#dataTable tbody');
        if (!tbody) return;

        tbody.innerHTML = '';
        
        this.currentData.slice(0, 50).forEach(record => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${record.id}</td>
                <td>${record.date}</td>
                <td>
                    <span class="activity-badge activity-${record.activity_type.toLowerCase().replace(' ', '-')}">
                        ${record.activity_type}
                    </span>
                </td>
                <td>${record.duration_minutes}</td>
                <td>
                    <div class="intensity-bar">
                        <div class="intensity-fill" style="width: ${record.intensity * 100}%"></div>
                        <span>${record.intensity}</span>
                    </div>
                </td>
                <td class="cells-count">${record.cells_produced.toLocaleString()}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-danger btn-sm" onclick="system.deleteRecord(${record.id})" title="Eliminar registro">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="system.viewRecordDetails(${record.id})" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    updateStats() {
        if (this.currentData.length === 0) return;

        const totalCells = this.currentData.reduce((sum, record) => sum + record.cells_produced, 0);
        const avgDuration = this.currentData.reduce((sum, record) => sum + record.duration_minutes, 0) / this.currentData.length;
        const avgIntensity = this.currentData.reduce((sum, record) => sum + record.intensity, 0) / this.currentData.length;
        
        // Calcular eficiencia promedio
        const totalEfficiency = this.currentData.reduce((sum, record) => {
            return sum + (record.cells_produced / record.duration_minutes);
        }, 0);
        const avgEfficiency = totalEfficiency / this.currentData.length;

        document.getElementById('totalRecords').textContent = this.currentData.length.toLocaleString();
        document.getElementById('totalCells').textContent = Math.round(totalCells / 1000000) + 'M';
        document.getElementById('avgDuration').textContent = avgDuration.toFixed(1) + ' min';
        document.getElementById('avgIntensity').textContent = avgIntensity.toFixed(2);
    }

    updateQuickAnalysis() {
        if (!this.analyticalInsights.performance_analysis) return;

        const efficiencyElement = document.getElementById('avgEfficiency');
        const topActivityElement = document.getElementById('topActivity');
        const trendElement = document.getElementById('productionTrend');
        const recommendationElement = document.getElementById('systemRecommendation');

        if (efficiencyElement) {
            // Calcular eficiencia promedio actual
            const avgEfficiency = this.currentData.reduce((sum, record) => {
                return sum + (record.cells_produced / record.duration_minutes);
            }, 0) / this.currentData.length;
            efficiencyElement.textContent = Math.round(avgEfficiency).toLocaleString() + '/min';
        }

        if (topActivityElement) {
            topActivityElement.textContent = this.analyticalInsights.performance_analysis.most_efficient_activity;
        }

        if (trendElement) {
            trendElement.textContent = '+12.5%';
            trendElement.style.color = 'var(--success-color)';
        }

        if (recommendationElement && this.analyticalInsights.recommendations) {
            recommendationElement.textContent = this.analyticalInsights.recommendations[0];
        }
    }

    async handleRecordSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        const record = {
            date: formData.get('date'),
            activity_type: formData.get('activity_type'),
            duration_minutes: parseFloat(formData.get('duration_minutes')),
            intensity: parseFloat(formData.get('intensity')),
            age: parseInt(formData.get('age')),
            gender: formData.get('gender'),
            heart_rate_avg: parseInt(formData.get('heart_rate_avg')),
            calories_burned: parseInt(formData.get('calories_burned')),
            sleep_hours: parseFloat(formData.get('sleep_hours')),
            hydration_liters: parseFloat(formData.get('hydration_liters'))
        };

        try {
            this.showAlert('Agregando registro...', 'info');
            const response = await this.apiRequest('/data', 'POST', record);
            if (response.success) {
                this.showAlert('✅ Registro agregado exitosamente', 'success');
                e.target.reset();
                await this.loadInitialData();
                await this.loadAnalyticalInsights();
            }
        } catch (error) {
            console.error('Error adding record:', error);
            this.showAlert('❌ Error al agregar el registro. Verifique la conexión con el servidor.', 'error');
        }
    }

    async deleteRecord(id) {
        if (!confirm('¿Estás seguro de que quieres eliminar este registro?')) return;

        try {
            this.showAlert('Eliminando registro...', 'info');
            const response = await this.apiRequest(`/data/${id}`, 'DELETE');
            if (response.success) {
                this.showAlert('✅ Registro eliminado exitosamente', 'success');
                await this.loadInitialData();
                await this.loadAnalyticalInsights();
            }
        } catch (error) {
            console.error('Error deleting record:', error);
            this.showAlert('❌ Error al eliminar el registro', 'error');
        }
    }

    viewRecordDetails(id) {
        const record = this.currentData.find(r => r.id === id);
        if (!record) return;

        const modal = document.getElementById('recordModal') || this.createRecordModal();
        const modalContent = document.getElementById('recordModalContent');
        
        const efficiency = Math.round(record.cells_produced / record.duration_minutes);
        
        modalContent.innerHTML = `
            <h2>Detalles del Registro #${record.id}</h2>
            <div class="record-details">
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Fecha:</label>
                        <span>${record.date}</span>
                    </div>
                    <div class="detail-item">
                        <label>Actividad:</label>
                        <span class="activity-badge activity-${record.activity_type.toLowerCase().replace(' ', '-')}">
                            ${record.activity_type}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Duración:</label>
                        <span>${record.duration_minutes} minutos</span>
                    </div>
                    <div class="detail-item">
                        <label>Intensidad:</label>
                        <span>${record.intensity}</span>
                    </div>
                    <div class="detail-item">
                        <label>Células Producidas:</label>
                        <span class="cells-count">${record.cells_produced.toLocaleString()}</span>
                    </div>
                    <div class="detail-item">
                        <label>Eficiencia:</label>
                        <span>${efficiency.toLocaleString()} células/min</span>
                    </div>
                    <div class="detail-item">
                        <label>Ritmo Cardíaco:</label>
                        <span>${record.heart_rate_avg} BPM</span>
                    </div>
                    <div class="detail-item">
                        <label>Calorías:</label>
                        <span>${record.calories_burned}</span>
                    </div>
                </div>
            </div>
        `;
        
        this.openModal(modal);
    }

    createRecordModal() {
        const modal = document.createElement('div');
        modal.id = 'recordModal';
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <div id="recordModalContent"></div>
            </div>
        `;
        document.body.appendChild(modal);
        return modal;
    }

    async trainMLModel() {
        const btn = document.getElementById('trainModel');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<span class="loading"></span> Entrenando Modelo...';
        btn.disabled = true;

        try {
            this.showAlert('🧠 Entrenando modelo de Machine Learning...', 'info');
            const response = await this.apiRequest('/ml/train');
            if (response.success) {
                const results = response.data;
                this.showAlert(
                    `✅ Modelo entrenado exitosamente. Precisión (R²): ${(results.r2_score * 100).toFixed(1)}%`,
                    'success'
                );
                
                // Mostrar importancia de características
                this.showFeatureImportance(results.feature_importance);
            }
        } catch (error) {
            console.error('Error training model:', error);
            this.showAlert('❌ Error al entrenar el modelo. Verifique la conexión con el servidor.', 'error');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    showFeatureImportance(features) {
        const modal = document.getElementById('featureModal') || this.createFeatureModal();
        const content = document.getElementById('featureImportance');
        
        // Ordenar características por importancia
        const sortedFeatures = Object.entries(features)
            .sort(([,a], [,b]) => b - a)
            .slice(0, 10); // Mostrar solo las 10 más importantes
        
        content.innerHTML = sortedFeatures.map(([feature, importance]) => `
            <div class="feature-item">
                <div class="feature-header">
                    <span class="feature-name">${this.formatFeatureName(feature)}</span>
                    <span class="feature-percentage">${(importance * 100).toFixed(1)}%</span>
                </div>
                <div class="feature-bar">
                    <div class="feature-fill" style="width: ${importance * 100}%"></div>
                </div>
            </div>
        `).join('');
            
        this.openModal(modal);
    }

    formatFeatureName(feature) {
        const names = {
            'duration_minutes': 'Duración (minutos)',
            'intensity': 'Intensidad del Ejercicio',
            'age': 'Edad',
            'heart_rate_avg': 'Ritmo Cardíaco Promedio',
            'calories_burned': 'Calorías Quemadas',
            'sleep_hours': 'Horas de Sueño',
            'hydration_liters': 'Hidratación (Litros)',
            'activity_type_encoded': 'Tipo de Actividad',
            'gender_encoded': 'Género'
        };
        return names[feature] || feature;
    }

    createFeatureModal() {
        const modal = document.createElement('div');
        modal.id = 'featureModal';
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <h2>Importancia de Características - Modelo ML</h2>
                <p>Las características más importantes para predecir la producción celular:</p>
                <div id="featureImportance"></div>
            </div>
        `;
        document.body.appendChild(modal);
        return modal;
    }

    async loadAllCharts() {
        const charts = [
            { id: 'areaChart', endpoint: '/charts/area' },
            { id: 'stackedBarChart', endpoint: '/charts/stacked_bar_percentage', special: true },
            { id: 'pieChart', endpoint: '/charts/pie' },
            { id: 'correlationChart', endpoint: '/charts/correlation' }
        ];

        this.showAlert('📊 Cargando visualizaciones...', 'info');

        for (const chart of charts) {
            if (chart.special) {
                await this.loadStackedBarWithLegend();
            } else {
                await this.loadChart(chart.id, chart.endpoint);
            }
            // Pequeña pausa entre requests para no saturar el servidor
            await new Promise(resolve => setTimeout(resolve, 500));
        }
        
        this.showAlert('✅ Todos los gráficos cargados', 'success');
    }

    async loadTrendsCharts() {
        const charts = [
            { id: 'trendsChart', endpoint: '/charts/trends' },
            { id: 'performanceChart', endpoint: '/charts/performance_metrics' }
        ];

        for (const chart of charts) {
            await this.loadChart(chart.id, chart.endpoint);
            await new Promise(resolve => setTimeout(resolve, 300));
        }
    }

    async loadAnalyticalContent() {
        // Cargar el primer gráfico de análisis por defecto
        await this.loadChart('efficiencyChart', '/charts/efficiency_analysis');
    }

    async loadStackedBarWithLegend() {
        try {
            const response = await this.apiRequest('/charts/stacked_bar_percentage');
            if (response.success) {
                // Mostrar la imagen del gráfico
                const container = document.getElementById('stackedBarChart');
                container.innerHTML = `
                    <div class="chart-image-container">
                        <img src="${response.data.image}" alt="stackedBarChart" class="chart-image">
                    </div>
                `;
                
                // Actualizar la leyenda con valores absolutos
                this.updateStackedBarLegend(response.data.legend_data);
            }
        } catch (error) {
            console.error('Error loading stacked bar:', error);
            const container = document.getElementById('stackedBarChart');
            if (container) {
                container.innerHTML = `
                    <div class="chart-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Error al cargar el gráfico de barras</p>
                        <small>${error.message}</small>
                        <button class="btn btn-sm" onclick="system.loadStackedBarWithLegend()" style="margin-top: 1rem;">
                            <i class="fas fa-redo"></i> Reintentar
                        </button>
                    </div>
                `;
            }
        }
    }

    updateStackedBarLegend(legendData) {
        const tbody = document.getElementById('legendTableBody');
        if (!tbody) return;
        
        tbody.innerHTML = legendData.map(item => `
            <tr>
                <td><strong>${item.activity}</strong></td>
                <td>${item.male_cells} (${item.male_percentage})</td>
                <td>${item.female_cells} (${item.female_percentage})</td>
                <td><strong>${item.total_cells}</strong></td>
            </tr>
        `).join('');
    }

    async loadChart(chartId, endpoint) {
        const container = document.getElementById(chartId);
        if (!container) return;

        // Mostrar estado de carga mejorado
        container.innerHTML = `
            <div class="chart-loading">
                <div class="loading-spinner">
                    <div class="loading"></div>
                </div>
                <p>${this.getChartDescription(endpoint)}</p>
                <small>Procesando datos y generando visualización...</small>
            </div>
        `;

        try {
            const response = await this.apiRequest(endpoint);
            if (response.success) {
                if (response.data.image) {
                    // Gráfico estático (imagen)
                    container.innerHTML = `
                        <div class="chart-image-container">
                            <img src="${response.data.image}" alt="${chartId}" class="chart-image" 
                                 onload="this.style.opacity='1'" 
                                 onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\\'chart-error\\'><i class=\\'fas fa-exclamation-triangle\\'></i><p>Error al cargar la imagen del gráfico</p></div>'">
                        </div>
                    `;
                } else {
                    // Gráfico interactivo (Plotly)
                    try {
                        const figure = response.data;
                        Plotly.newPlot(chartId, figure.data, figure.layout, {
                            responsive: true,
                            displayModeBar: true,
                            displaylogo: false,
                            modeBarButtonsToRemove: ['pan2d', 'lasso2d', 'select2d'],
                            modeBarButtonsToAdd: ['downloadImage']
                        });
                        
                        // Agregar evento de redimensionamiento
                        window.addEventListener('resize', () => {
                            Plotly.Plots.resize(chartId);
                        });
                    } catch (plotlyError) {
                        console.error('Error rendering Plotly chart:', plotlyError);
                        container.innerHTML = `
                            <div class="chart-error">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p>Error al renderizar el gráfico interactivo</p>
                                <small>${plotlyError.message}</small>
                            </div>
                        `;
                    }
                }
                
                // Guardar en cache
                this.chartCache[chartId] = response.data;
            }
        } catch (error) {
            console.error(`Error loading chart ${chartId}:`, error);
            container.innerHTML = `
                <div class="chart-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Error al cargar el gráfico</p>
                    <small>${error.message}</small>
                    <button class="btn btn-sm" onclick="system.loadChart('${chartId}', '${endpoint}')" style="margin-top: 1rem;">
                        <i class="fas fa-redo"></i> Reintentar
                    </button>
                </div>
            `;
        }
    }

    getChartDescription(endpoint) {
        const descriptions = {
            '/charts/area': 'Analizando evolución temporal de la producción...',
            '/charts/radar': 'Generando análisis comparativo multidimensional...',
            '/charts/stacked_bar': 'Procesando distribución por género...',
            '/charts/stacked_bar_percentage': 'Calculando distribución porcentual...',
            '/charts/pie': 'Analizando contribución por actividad...',
            '/charts/correlation': 'Estudiando relaciones estadísticas...',
            '/charts/trends': 'Generando tendencias y proyecciones...',
            '/charts/performance_metrics': 'Calculando métricas avanzadas...',
            '/charts/efficiency_analysis': 'Analizando eficiencia por actividad...',
            '/charts/production_analysis': 'Estudiando producción promedio...',
            '/charts/intensity_analysis': 'Examinando impacto de la intensidad...',
            '/charts/duration_analysis': 'Optimizando duración del ejercicio...',
            '/charts/radar_individual': 'Preparando análisis individual...'
        };
        return descriptions[endpoint] || 'Procesando datos y generando visualización...';
    }

    async downloadChart(chartId, endpoint) {
        try {
            const response = await this.apiRequest(endpoint);
            if (response.success && response.data.image) {
                const link = document.createElement('a');
                link.href = response.data.image;
                link.download = `${chartId}_${new Date().toISOString().split('T')[0]}.png`;
                link.click();
                this.showAlert('📥 Gráfico descargado', 'success');
            }
        } catch (error) {
            console.error('Error downloading chart:', error);
            this.showAlert('❌ Error al descargar el gráfico', 'error');
        }
    }

    refreshAllCharts() {
        this.showAlert('🔄 Actualizando todas las visualizaciones...', 'info');
        this.loadAllCharts();
    }

    async apiRequest(endpoint, method = 'GET', data = null) {
        const url = `http://localhost:5000/api${endpoint}`;
        
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            },
        };

        if (data && ['POST', 'PUT'].includes(method)) {
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, options);
            
            if (!response.ok) {
                throw new Error(`Error HTTP! estado: ${response.status}`);
            }
            
            const result = await response.json();
            
            return {
                success: response.ok,
                data: result,
                code: response.status
            };
        } catch (error) {
            console.error('API Request failed:', error);
            throw new Error(`No se pudo conectar con el servidor. Verifique que esté ejecutándose en http://localhost:5000`);
        }
    }

    showAlert(message, type = 'info') {
        // Eliminar alertas existentes
        document.querySelectorAll('.alert').forEach(alert => alert.remove());
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.innerHTML = `
            <div class="alert-content">
                <i class="fas fa-${this.getAlertIcon(type)}"></i>
                <span>${message}</span>
            </div>
        `;
        
        const container = document.querySelector('.container') || document.body;
        container.insertBefore(alert, container.firstChild);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (alert.parentNode) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => alert.remove(), 300);
            }
        }, 5000);
    }

    getAlertIcon(type) {
        const icons = {
            'success': 'check-circle',
            'error': 'exclamation-triangle',
            'info': 'info-circle',
            'warning': 'exclamation-circle'
        };
        return icons[type] || 'info-circle';
    }

    openModal(modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    closeModal(modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    navigateTo(page) {
        window.location.href = page;
    }
}

// Inicializar el sistema cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.system = new FitnessAnalysisSystem();
});

// Cerrar modales
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('close-modal')) {
        const modal = e.target.closest('.modal');
        if (modal) {
            system.closeModal(modal);
        }
    }
    
    // Cerrar modal haciendo click fuera
    if (e.target.classList.contains('modal')) {
        system.closeModal(e.target);
    }
});

// Manejar tecla Escape para cerrar modales
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(modal => {
            if (modal.style.display === 'block') {
                system.closeModal(modal);
            }
        });
    }
});

// Función global para cargar radar individual
async function loadIndividualRadar(activity) {
    if (!activity) {
        // Mostrar placeholder si no hay actividad seleccionada
        const container = document.getElementById('individualRadarChart');
        if (container) {
            container.innerHTML = `
                <div class="analysis-placeholder">
                    <i class="fas fa-chart-radar"></i>
                    <h4>Análisis Individual de Actividad</h4>
                    <p>Selecciona una actividad del menú desplegable para ver un análisis detallado</p>
                    <small>Comparación con promedios generales y métricas específicas</small>
                </div>
            `;
        }
        return;
    }
    
    const container = document.getElementById('individualRadarChart');
    if (!container) return;
    
    container.innerHTML = '<div class="chart-placeholder"><span class="loading"></span><p>Cargando análisis individual...</p></div>';
    
    try {
        const response = await system.apiRequest(`/charts/radar_individual/${activity}`);
        if (response.success) {
            const figure = response.data;
            Plotly.newPlot('individualRadarChart', figure.data, figure.layout, {
                responsive: true,
                displayModeBar: true
            });
        }
    } catch (error) {
        console.error('Error loading individual radar:', error);
        container.innerHTML = `
            <div class="chart-error">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Error al cargar el análisis individual</p>
                <small>${error.message}</small>
                <button class="btn btn-sm" onclick="loadIndividualRadar('${activity}')" style="margin-top: 1rem;">
                    <i class="fas fa-redo"></i> Reintentar
                </button>
            </div>
        `;
    }
}

// Función global para cargar análisis específicos
async function loadSpecificAnalysisChart(analysisType) {
    const endpoints = {
        'efficiency': '/charts/efficiency_analysis',
        'production': '/charts/production_analysis', 
        'intensity': '/charts/intensity_analysis',
        'duration': '/charts/duration_analysis'
    };

    const chartId = analysisType + 'Chart';
    const endpoint = endpoints[analysisType];

    if (endpoint && system) {
        await system.loadChart(chartId, endpoint);
    }
}

// Exportar para uso global
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FitnessAnalysisSystem;
}