// Frontend - js/charts.js

class ChartManager {
    constructor(apiHandler) {
        this.apiHandler = apiHandler;
        this.currentCharts = new Map(); // Cache de gráficos
        console.log('ChartManager inicializado con API Handler:', apiHandler);
    }

    async loadChart(chartType) {
        console.log(`[ChartManager] Cargando gráfico: ${chartType}`);
        
        const loadingElement = document.getElementById(chartType + 'Loading');
        const chartElement = document.getElementById(chartType + 'Chart');

        // Mostrar loading
        if (loadingElement) {
            loadingElement.style.display = 'flex';
            loadingElement.innerHTML = `
                <div class="loading-content">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Generando gráfico ${chartType}...</p>
                </div>
            `;
        }
        
        if (chartElement) {
            chartElement.style.display = 'none';
        }

        try {
            console.log(`[ChartManager] Haciendo request para gráfico ${chartType}`);
            const response = await this.apiHandler.getChart(chartType);
            console.log(`[ChartManager] Respuesta del servidor para ${chartType}:`, response);
            
            if (response.success && response.image) {
                if (chartElement) {
                    chartElement.src = 'data:image/png;base64,' + response.image;
                    chartElement.style.display = 'block';
                    chartElement.alt = `Gráfico ${chartType}`;
                    chartElement.onerror = () => {
                        console.error(`Error cargando imagen del gráfico ${chartType}`);
                        this.showChartError(chartType, 'Error al mostrar la imagen del gráfico');
                    };
                    
                    if (loadingElement) {
                        loadingElement.style.display = 'none';
                    }
                    
                    // Guardar en cache
                    this.currentCharts.set(chartType, response.image);
                    console.log(`[ChartManager] Gráfico ${chartType} cargado exitosamente`);
                    
                    if (window.app) {
                        window.app.showNotification(`Gráfico ${chartType} generado correctamente`, 'success');
                    }
                }
            } else {
                const errorMsg = response.error || 'Error generando gráfico';
                console.error(`[ChartManager] Error en gráfico ${chartType}:`, errorMsg);
                this.showChartError(chartType, errorMsg);
            }
        } catch (error) {
            console.error('[ChartManager] Error loading chart:', error);
            this.showChartError(chartType, 'Error de conexión: ' + error.message);
        }
    }

    showChartError(chartType, message) {
        const loadingElement = document.getElementById(chartType + 'Loading');
        const chartElement = document.getElementById(chartType + 'Chart');
        
        if (loadingElement) {
            loadingElement.innerHTML = `
                <div class="error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>${message}</p>
                    <button class="btn-small" onclick="loadChart('${chartType}')">
                        <i class="fas fa-redo"></i> Reintentar
                    </button>
                </div>
            `;
        }
        
        if (chartElement) {
            chartElement.style.display = 'none';
        }
        
        if (window.app) {
            window.app.showError(`Error en gráfico ${chartType}: ${message}`);
        }
    }

    async loadClusters() {
        const resultElement = document.getElementById('clustersResult');
        if (!resultElement) {
            console.error('[ChartManager] Elemento clustersResult no encontrado');
            return;
        }

        resultElement.innerHTML = `
            <div class="loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Analizando clusters con machine learning...</p>
            </div>
        `;

        try {
            console.log('[ChartManager] Solicitando clusters...');
            const response = await this.apiHandler.getClusters();
            console.log('[ChartManager] Respuesta clusters:', response);
            
            if (response.success && response.clusters) {
                this.displayClusters(response.clusters, resultElement);
                if (window.app) {
                    window.app.showNotification('Análisis de clusters completado', 'success');
                }
            } else {
                resultElement.innerHTML = `
                    <div class="error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Error: ${response.error || 'Error en el análisis de clusters'}</p>
                    </div>
                `;
                if (window.app) {
                    window.app.showError('Error en análisis de clusters: ' + (response.error || 'Error desconocido'));
                }
            }
        } catch (error) {
            console.error('[ChartManager] Error loading clusters:', error);
            resultElement.innerHTML = `
                <div class="error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Error de conexión con el servidor: ${error.message}</p>
                </div>
            `;
            if (window.app) {
                window.app.showError('Error de conexión en clusters: ' + error.message);
            }
        }
    }

    displayClusters(clustersData, container) {
        const clusters = clustersData.clusters || [];
        const features = clustersData.cluster_features || [];
        
        let html = `
            <h4><i class="fas fa-project-diagram"></i> Clusters de Conflictos Identificados</h4>
            <p class="cluster-info">Se identificaron ${clusters.length} conflictos agrupados en clusters según patrones económicos similares.</p>
            <div class="clusters-grid">
        `;

        // Agrupar conflictos por cluster
        const clustersGrouped = {};
        clusters.forEach(conflict => {
            const clusterNum = conflict.cluster;
            if (!clustersGrouped[clusterNum]) {
                clustersGrouped[clusterNum] = [];
            }
            clustersGrouped[clusterNum].push(conflict);
        });

        Object.keys(clustersGrouped).forEach(clusterKey => {
            const clusterConflicts = clustersGrouped[clusterKey];
            const clusterNumber = parseInt(clusterKey) + 1;
            
            html += `
                <div class="cluster-group">
                    <h5>Cluster ${clusterNumber} - ${this.getClusterDescription(clusterNumber)}</h5>
                    <p class="cluster-stats">${clusterConflicts.length} conflictos</p>
                    <div class="conflicts-list">
            `;

            clusterConflicts.forEach(conflict => {
                html += `
                    <div class="conflict-item">
                        <strong>${conflict.conflict_name}</strong>
                        <div class="conflict-stats">
                            <span>GDP: ${conflict.gdp_change?.toFixed(1)}%</span>
                            <span>Inflación: ${conflict.inflation?.toFixed(1)}%</span>
                            <span>Gasto Militar: ${conflict.military_spending?.toFixed(1)}%</span>
                        </div>
                    </div>
                `;
            });

            html += `
                    </div>
                </div>
            `;
        });

        html += `</div>`;
        container.innerHTML = html;
    }

    getClusterDescription(clusterNumber) {
        const descriptions = {
            1: "Alto impacto económico",
            2: "Impacto moderado prolongado", 
            3: "Conflictos regionales",
            4: "Bajo impacto global"
        };
        return descriptions[clusterNumber] || "Patrón económico específico";
    }

    async loadPredictions() {
        const resultElement = document.getElementById('predictionResult');
        if (!resultElement) {
            console.error('[ChartManager] Elemento predictionResult no encontrado');
            return;
        }

        resultElement.innerHTML = `
            <div class="loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Generando predicciones con Random Forest...</p>
            </div>
        `;

        try {
            console.log('[ChartManager] Solicitando predicciones...');
            const response = await this.apiHandler.predictImpact(
                ['duration', 'countries_involved', 'military_spending'],
                'gdp_change'
            );
            
            console.log('[ChartManager] Respuesta predicciones:', response);
            
            if (response.success && response.prediction) {
                this.displayPredictions(response.prediction, resultElement);
                if (window.app) {
                    window.app.showNotification('Análisis predictivo completado', 'success');
                }
            } else {
                resultElement.innerHTML = `
                    <div class="error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Error: ${response.error || 'Error en la predicción'}</p>
                    </div>
                `;
                if (window.app) {
                    window.app.showError('Error en predicciones: ' + (response.error || 'Error desconocido'));
                }
            }
        } catch (error) {
            console.error('[ChartManager] Error loading predictions:', error);
            resultElement.innerHTML = `
                <div class="error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Error de conexión con el servidor: ${error.message}</p>
                </div>
            `;
            if (window.app) {
                window.app.showError('Error de conexión en predicciones: ' + error.message);
            }
        }
    }

    displayPredictions(predictionData, container) {
        const featureImportance = predictionData.feature_importance || {};
        const r2 = predictionData.r2_score || 0;
        const mse = predictionData.mse || 0;

        let html = `
            <h4><i class="fas fa-crystal-ball"></i> Análisis Predictivo - Impacto en GDP</h4>
            <div class="prediction-stats">
                <div class="stat">
                    <label>Precisión del Modelo (R²):</label>
                    <span class="value ${r2 > 0.7 ? 'positive' : r2 > 0.5 ? 'neutral' : 'negative'}">${(r2 * 100).toFixed(1)}%</span>
                </div>
                <div class="stat">
                    <label>Error Cuadrático Medio:</label>
                    <span class="value">${mse.toFixed(4)}</span>
                </div>
            </div>
            
            <h5>Importancia de Variables Predictivas:</h5>
            <p class="feature-info">Las siguientes variables son las más relevantes para predecir el impacto en GDP:</p>
            <div class="feature-importance">
        `;

        // Ordenar características por importancia
        const sortedFeatures = Object.entries(featureImportance)
            .sort((a, b) => b[1] - a[1]);

        sortedFeatures.forEach(([feature, importance]) => {
            const percentage = (importance * 100).toFixed(1);
            html += `
                <div class="feature-bar">
                    <div class="feature-label">${this.formatFeatureName(feature)}</div>
                    <div class="feature-bar-container">
                        <div class="feature-bar-fill" style="width: ${percentage}%"></div>
                        <span class="feature-percentage">${percentage}%</span>
                    </div>
                </div>
            `;
        });

        html += `</div>`;
        container.innerHTML = html;
    }

    async loadTrends() {
        const resultElement = document.getElementById('trendsResult');
        if (!resultElement) {
            console.error('[ChartManager] Elemento trendsResult no encontrado');
            return;
        }

        resultElement.innerHTML = `
            <div class="loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Analizando tendencias temporales...</p>
            </div>
        `;

        try {
            console.log('[ChartManager] Solicitando tendencias...');
            const response = await this.apiHandler.getTrends();
            console.log('[ChartManager] Respuesta tendencias:', response);
            
            if (response.success && response.trends) {
                this.displayTrends(response.trends, resultElement);
                if (window.app) {
                    window.app.showNotification('Análisis de tendencias completado', 'success');
                }
            } else {
                resultElement.innerHTML = `
                    <div class="error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Error: ${response.error || 'Error en el análisis de tendencias'}</p>
                    </div>
                `;
                if (window.app) {
                    window.app.showError('Error en tendencias: ' + (response.error || 'Error desconocido'));
                }
            }
        } catch (error) {
            console.error('[ChartManager] Error loading trends:', error);
            resultElement.innerHTML = `
                <div class="error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Error de conexión con el servidor: ${error.message}</p>
                </div>
            `;
            if (window.app) {
                window.app.showError('Error de conexión en tendencias: ' + error.message);
            }
        }
    }

    displayTrends(trendsData, container) {
        const gdpSlope = trendsData.gdp_slope || 0;
        const inflationSlope = trendsData.inflation_slope || 0;
        const yearlyData = trendsData.yearly_data || [];

        let html = `
            <h4><i class="fas fa-trend-up"></i> Análisis de Tendencias Temporales</h4>
            <div class="trend-stats">
                <div class="trend-stat ${gdpSlope >= 0 ? 'positive' : 'negative'}">
                    <label>Tendencia GDP:</label>
                    <span class="value">${gdpSlope.toFixed(3)} por año</span>
                    <i class="fas ${gdpSlope >= 0 ? 'fa-arrow-up positive' : 'fa-arrow-down negative'}"></i>
                </div>
                <div class="trend-stat ${inflationSlope >= 0 ? 'negative' : 'positive'}">
                    <label>Tendencia Inflación:</label>
                    <span class="value">${inflationSlope.toFixed(3)} por año</span>
                    <i class="fas ${inflationSlope >= 0 ? 'fa-arrow-up negative' : 'fa-arrow-down positive'}"></i>
                </div>
            </div>
            
            <h5>Resumen por Década:</h5>
            <div class="decade-summary">
        `;

        // Agrupar por década
        const decades = {};
        yearlyData.forEach(year => {
            const decade = Math.floor(year.year / 10) * 10;
            if (!decades[decade]) {
                decades[decade] = { years: [], gdp: [], inflation: [], conflicts: [] };
            }
            decades[decade].years.push(year.year);
            decades[decade].gdp.push(year.gdp_change);
            decades[decade].inflation.push(year.inflation);
            decades[decade].conflicts.push(year.conflict_id);
        });

        Object.keys(decades).sort().forEach(decade => {
            const data = decades[decade];
            const avgGdp = data.gdp.reduce((a, b) => a + b, 0) / data.gdp.length;
            const avgInflation = data.inflation.reduce((a, b) => a + b, 0) / data.inflation.length;
            const uniqueConflicts = [...new Set(data.conflicts)].length;
            
            html += `
                <div class="decade-card">
                    <h6>${decade}s</h6>
                    <div class="decade-stats">
                        <span>GDP: ${avgGdp.toFixed(1)}%</span>
                        <span>Inflación: ${avgInflation.toFixed(1)}%</span>
                        <span>Conflictos: ${uniqueConflicts}</span>
                    </div>
                </div>
            `;
        });

        html += `</div>`;
        container.innerHTML = html;
    }

    formatFeatureName(feature) {
        const names = {
            'duration': 'Duración del Conflicto',
            'countries_involved': 'Países Involucrados',
            'military_spending': 'Gasto Militar',
            'gdp_change': 'Cambio en GDP',
            'inflation': 'Tasa de Inflación',
            'unemployment': 'Tasa de Desempleo',
            'civilian_casualties': 'Bajas Civiles'
        };
        return names[feature] || feature.replace(/_/g, ' ');
    }

    // Método para limpiar cache
    clearCache() {
        this.currentCharts.clear();
        console.log('[ChartManager] Cache limpiado');
    }

    // Método para recargar todos los gráficos
    async reloadAllCharts() {
        console.log('[ChartManager] Recargando todos los gráficos...');
        const chartTypes = ['area', 'radar', 'stacked', 'pie'];
        
        for (const chartType of chartTypes) {
            await this.loadChart(chartType);
            // Pequeña pausa entre gráficos
            await new Promise(resolve => setTimeout(resolve, 1000));
        }
        
        if (window.app) {
            window.app.showNotification('Todos los gráficos recargados', 'success');
        }
    }
}

// Inicializar ChartManager cuando el DOM esté listo
let chartManager;

document.addEventListener('DOMContentLoaded', function() {
    console.log('[charts.js] DOM cargado, verificando inicialización...');
    
    // Pequeño delay para asegurar que app esté inicializado
    setTimeout(() => {
        if (typeof app !== 'undefined' && app.apiHandler) {
            chartManager = new ChartManager(app.apiHandler);
            window.chartManager = chartManager;
            console.log('[charts.js] ChartManager inicializado correctamente');
            
            // Si estamos en una pestaña activa de gráficos, cargar automáticamente
            const activeTab = document.querySelector('.tab-pane.active');
            if (activeTab && activeTab.id !== 'ml') {
                console.log(`[charts.js] Cargando gráfico inicial: ${activeTab.id}`);
                setTimeout(() => loadChart(activeTab.id), 500);
            }
        } else {
            console.error('[charts.js] App no está inicializada. ChartManager no puede ser creado.');
            // Intentar inicializar con un API handler básico
            try {
                const basicApiHandler = {
                    baseUrl: 'http://localhost:5000/api',
                    async getChart(chartType) {
                        const response = await fetch(`${this.baseUrl}/chart/${chartType}`);
                        return await response.json();
                    },
                    async getClusters() {
                        const response = await fetch(`${this.baseUrl}/ml/clusters`);
                        return await response.json();
                    },
                    async getTrends() {
                        const response = await fetch(`${this.baseUrl}/ml/trends`);
                        return await response.json();
                    },
                    async predictImpact(features, target) {
                        const response = await fetch(`${this.baseUrl}/ml/predict`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ features, target })
                        });
                        return await response.json();
                    }
                };
                
                chartManager = new ChartManager(basicApiHandler);
                window.chartManager = chartManager;
                console.log('[charts.js] ChartManager inicializado con handler básico');
            } catch (error) {
                console.error('[charts.js] Error inicializando ChartManager:', error);
            }
        }
    }, 100);
});

// Funciones globales para las pestañas de gráficos
function loadChart(chartType) {
    console.log(`[loadChart] Llamada para cargar: ${chartType}`);
    if (chartManager) {
        chartManager.loadChart(chartType);
    } else {
        console.error('[loadChart] ChartManager no está disponible');
        // Mostrar error al usuario
        const loadingElement = document.getElementById(chartType + 'Loading');
        if (loadingElement) {
            loadingElement.innerHTML = `
                <div class="error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Error: Sistema de gráficos no disponible</p>
                    <button class="btn-small" onclick="location.reload()">
                        <i class="fas fa-redo"></i> Recargar Página
                    </button>
                </div>
            `;
        }
        
        // Intentar inicializar ChartManager
        setTimeout(() => {
            if (window.chartManager) {
                console.log('[loadChart] ChartManager ahora disponible, reintentando...');
                loadChart(chartType);
            }
        }, 1000);
    }
}

function loadClusters() {
    console.log('[loadClusters] Llamada para cargar clusters');
    if (chartManager) {
        chartManager.loadClusters();
    } else {
        console.error('[loadClusters] ChartManager no está disponible');
        alert('ChartManager no está disponible. Recarga la página.');
    }
}

function loadPredictions() {
    console.log('[loadPredictions] Llamada para cargar predicciones');
    if (chartManager) {
        chartManager.loadPredictions();
    } else {
        console.error('[loadPredictions] ChartManager no está disponible');
        alert('ChartManager no está disponible. Recarga la página.');
    }
}

function loadTrends() {
    console.log('[loadTrends] Llamada para cargar tendencias');
    if (chartManager) {
        chartManager.loadTrends();
    } else {
        console.error('[loadTrends] ChartManager no está disponible');
        alert('ChartManager no está disponible. Recarga la página.');
    }
}

function reloadAllCharts() {
    console.log('[reloadAllCharts] Llamada para recargar todos los gráficos');
    if (chartManager) {
        chartManager.reloadAllCharts();
    } else {
        console.error('[reloadAllCharts] ChartManager no está disponible');
        alert('ChartManager no está disponible. Recarga la página.');
    }
}

// Función de debug para testing
async function testCharts() {
    console.log('=== INICIANDO PRUEBA DE GRÁFICOS ===');
    
    // Probar conexión API básica
    try {
        const response = await fetch('http://localhost:5000/api/stats');
        const data = await response.json();
        console.log('✓ Conexión API OK:', data);
    } catch (error) {
        console.error('✗ Error conexión API:', error);
        alert('Error de conexión con el backend: ' + error.message);
        return;
    }
    
    // Probar cada gráfico individualmente
    const chartTypes = ['area', 'radar', 'stacked', 'pie'];
    
    for (const chartType of chartTypes) {
        console.log(`Probando gráfico: ${chartType}`);
        try {
            const response = await fetch(`http://localhost:5000/api/chart/${chartType}`);
            const data = await response.json();
            console.log(`Respuesta ${chartType}:`, data);
            
            if (data.success) {
                console.log(`✓ Gráfico ${chartType} OK`);
            } else {
                console.error(`✗ Gráfico ${chartType} ERROR:`, data.error);
            }
        } catch (error) {
            console.error(`✗ Gráfico ${chartType} EXCEPCIÓN:`, error);
        }
        
        // Pequeña pausa
        await new Promise(resolve => setTimeout(resolve, 500));
    }
    
    console.log('=== PRUEBA COMPLETADA ===');
    alert('Prueba completada. Revisa la consola para ver los resultados.');
}

// Exportar para uso global
window.loadChart = loadChart;
window.loadClusters = loadClusters;
window.loadPredictions = loadPredictions;
window.loadTrends = loadTrends;
window.reloadAllCharts = reloadAllCharts;
window.testCharts = testCharts;