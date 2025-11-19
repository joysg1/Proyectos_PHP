// Configuración global de Chart.js
Chart.defaults.font.family = "'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif";
Chart.defaults.color = '#cbd5e1';
Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(15, 15, 35, 0.95)';
Chart.defaults.plugins.tooltip.padding = 12;
Chart.defaults.plugins.tooltip.cornerRadius = 8;
Chart.defaults.plugins.tooltip.titleColor = '#f8fafc';
Chart.defaults.plugins.tooltip.bodyColor = '#cbd5e1';
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.padding = 20;

// Colores para gráficos
const colors = {
    primary: '#6366f1',
    secondary: '#10b981',
    accent: '#f59e0b',
    danger: '#ef4444',
    warning: '#f59e0b',
    info: '#06b6d4'
};

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando gráficos con datos:', appData);
    
    if (!appData.pythonAvailable) {
        showWarning('Servidor Python no disponible. Mostrando datos de ejemplo.');
    }
    
    renderAllCharts();
    initNavigation();
});

function renderAllCharts() {
    renderKeyMetricsChart();
    renderEconomicChart();
    renderEconomicTrendsChart();
    renderSocialChart();
    renderROCChart();
    renderTradeChart();
    renderTradeCategoriesChart();
    renderPredictionsChart();
    renderFactorsChart();
    
    if (appData.all && appData.all.countries) {
        createCountryCards(appData.all.countries);
    } else {
        createCountryCards(getExampleCountries());
    }
}

// Gráfico de métricas clave
function renderKeyMetricsChart() {
    const ctx = document.getElementById('keyMetricsChart');
    if (!ctx) return;
    
    new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Crecimiento PIB', 'Industrialización', 'Comercio', 'Urbanización'],
            datasets: [{
                label: 'Promedio América (%)',
                data: [32, 45, 68, 28],
                backgroundColor: [
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(139, 92, 246, 0.8)'
                ],
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Métricas Principales', color: '#f8fafc' },
                legend: { display: false }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: { callback: value => value + '%' }
                }
            }
        }
    });
}

// Gráfico económico principal
function renderEconomicChart() {
    const ctx = document.getElementById('economicChart');
    if (!ctx) return;
    
    let countries, preWar, postWar;
    
    if (appData.economic && appData.economic.success) {
        countries = appData.economic.country_names;
        preWar = appData.economic.actual;
        postWar = appData.economic.predicted;
    } else {
        countries = ['EE.UU.', 'Argentina', 'Brasil', 'México', 'Canadá'];
        preWar = [517, 24, 21, 18, 32];
        postWar = [680, 31, 27, 22, 45];
    }
    
    new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: countries,
            datasets: [
                {
                    label: 'PIB Pre-Guerra (M USD)',
                    data: preWar,
                    backgroundColor: 'rgba(99, 102, 241, 0.8)'
                },
                {
                    label: 'PIB Post-Guerra (M USD)',
                    data: postWar,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: { 
                    display: true, 
                    text: 'Comparativa PIB Pre/Post Guerra',
                    color: '#f8fafc',
                    font: { size: 16, weight: '600' }
                }
            }
        }
    });
}

// Tendencias económicas
function renderEconomicTrendsChart() {
    const ctx = document.getElementById('economicTrendsChart');
    if (!ctx) return;
    
    let countries, growthRates;
    
    if (appData.trends) {
        countries = appData.trends.countries;
        growthRates = appData.trends.growth_rates;
    } else if (appData.economic && appData.economic.success) {
        countries = appData.economic.country_names;
        growthRates = appData.economic.actual.map((actual, idx) => 
            ((appData.economic.predicted[idx] - actual) / actual * 100).toFixed(1)
        );
    } else {
        countries = ['EE.UU.', 'Argentina', 'Brasil', 'México', 'Canadá'];
        growthRates = [31.5, 29.2, 28.6, 22.2, 40.6];
    }
    
    new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: countries,
            datasets: [{
                label: 'Tasa de Crecimiento (%)',
                data: growthRates,
                borderColor: colors.primary,
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { 
                    display: true, 
                    text: 'Tasas de Crecimiento Económico',
                    color: '#f8fafc' 
                }
            },
            scales: {
                y: { 
                    ticks: { callback: value => value + '%' }
                }
            }
        }
    });
}

// Gráfico social
function renderSocialChart() {
    const ctx = document.getElementById('socialChart');
    if (!ctx) return;
    
    let countries, urbanization, migration;
    
    if (appData.social && appData.social.success) {
        countries = appData.social.country_names;
        urbanization = appData.social.urbanization;
        migration = appData.social.migration;
    } else {
        countries = ['EE.UU.', 'Argentina', 'Brasil', 'México', 'Canadá'];
        urbanization = [12.5, 8.2, 7.8, 6.5, 10.2];
        migration = [125, 45, 32, 28, 18];
    }
    
    new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: countries,
            datasets: [
                {
                    label: 'Urbanización (%)',
                    data: urbanization,
                    backgroundColor: 'rgba(99, 102, 241, 0.8)'
                },
                {
                    label: 'Migración (miles)',
                    data: migration,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: { 
                    display: true, 
                    text: 'Indicadores Sociales por País',
                    color: '#f8fafc' 
                }
            }
        }
    });
}

// Curva ROC
function renderROCChart() {
    const ctx = document.getElementById('rocChart');
    if (!ctx) return;
    
    let rocData;
    
    if (appData.social && appData.social.success && appData.social.roc_curve) {
        rocData = appData.social.roc_curve;
    } else {
        rocData = {
            fpr: [0, 0.2, 0.4, 0.6, 0.8, 1],
            tpr: [0, 0.3, 0.6, 0.8, 0.9, 1],
            auc: 0.83
        };
    }
    
    new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: rocData.fpr.map((_, i) => `U${i+1}`),
            datasets: [{
                label: `Curva ROC (AUC = ${rocData.auc.toFixed(3)})`,
                data: rocData.tpr,
                borderColor: colors.primary,
                backgroundColor: 'rgba(99, 102, 241, 0.2)',
                borderWidth: 3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { 
                    display: true, 
                    text: 'Análisis de Participación - Curva ROC',
                    color: '#f8fafc' 
                }
            },
            scales: {
                y: { min: 0, max: 1 }
            }
        }
    });
}

// Gráfico de comercio por país
function renderTradeChart() {
    const ctx = document.getElementById('tradeChart');
    if (!ctx) return;
    
    let tradeData;
    
    if (appData.all && appData.all.trade_data) {
        tradeData = {};
        appData.all.trade_data.forEach(item => {
            tradeData[item.from] = (tradeData[item.from] || 0) + item.value;
        });
    } else {
        tradeData = {
            'Estados Unidos': 7700,
            'Argentina': 1800,
            'Brasil': 2150,
            'Canadá': 2100
        };
    }
    
    const total = Object.values(tradeData).reduce((a, b) => a + b, 0);
    
    new Chart(ctx.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(tradeData).map(country => {
                const percent = ((tradeData[country] / total) * 100).toFixed(1);
                return `${country} (${percent}%)`;
            }),
            datasets: [{
                data: Object.values(tradeData),
                backgroundColor: [
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(139, 92, 246, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { 
                    display: true, 
                    text: 'Comercio por País Exportador',
                    color: '#f8fafc' 
                }
            }
        }
    });
}

// Gráfico de categorías de comercio
function renderTradeCategoriesChart() {
    const ctx = document.getElementById('tradeCategoriesChart');
    if (!ctx) return;
    
    let categoriesData;
    
    if (appData.trade && appData.trade.success) {
        categoriesData = appData.trade;
    } else if (appData.all && appData.all.trade_data) {
        categoriesData = {};
        appData.all.trade_data.forEach(item => {
            categoriesData[item.category] = (categoriesData[item.category] || 0) + item.value;
        });
    } else {
        categoriesData = {
            'Manufacturas': 9800,
            'Alimentos': 1800,
            'Materias Primas': 2150
        };
    }
    
    const total = Object.values(categoriesData).reduce((a, b) => a + b, 0);
    
    new Chart(ctx.getContext('2d'), {
        type: 'pie',
        data: {
            labels: Object.keys(categoriesData).map(cat => {
                const percent = ((categoriesData[cat] / total) * 100).toFixed(1);
                return `${cat} (${percent}%)`;
            }),
            datasets: [{
                data: Object.values(categoriesData),
                backgroundColor: [
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { 
                    display: true, 
                    text: 'Comercio por Categorías',
                    color: '#f8fafc' 
                }
            }
        }
    });
}

// Gráfico de predicciones
function renderPredictionsChart() {
    const ctx = document.getElementById('predictionsChart');
    if (!ctx) return;
    
    let predictionsData;
    
    if (appData.predictions && appData.predictions.success) {
        predictionsData = appData.predictions.predictions;
    } else {
        predictionsData = {
            'Estados Unidos': { predicted_growth: 0.085 },
            'Argentina': { predicted_growth: 0.072 },
            'Brasil': { predicted_growth: 0.069 },
            'México': { predicted_growth: 0.058 },
            'Canadá': { predicted_growth: 0.091 }
        };
    }
    
    const countries = Object.keys(predictionsData);
    const growthRates = countries.map(country => 
        (predictionsData[country].predicted_growth * 100).toFixed(1)
    );
    
    new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: countries,
            datasets: [{
                label: 'Crecimiento Proyectado (%)',
                data: growthRates,
                backgroundColor: 'rgba(16, 185, 129, 0.8)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { 
                    display: true, 
                    text: 'Crecimiento Económico Proyectado (1920-1925)',
                    color: '#f8fafc' 
                }
            },
            scales: {
                y: { 
                    ticks: { callback: value => value + '%' }
                }
            }
        }
    });
}

// Factores de crecimiento
function renderFactorsChart() {
    const ctx = document.getElementById('factorsChart');
    if (!ctx) return;
    
    new Chart(ctx.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Industrialización (42%)', 'Comercio (35%)', 'Capital Humano (23%)'],
            datasets: [{
                data: [42, 35, 23],
                backgroundColor: [
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { 
                    display: true, 
                    text: 'Factores de Crecimiento Post-Guerra',
                    color: '#f8fafc' 
                }
            }
        }
    });
}

// Tarjetas de países
function createCountryCards(countries) {
    const container = document.getElementById('countryGrid');
    if (!container) return;
    
    container.innerHTML = '';
    
    countries.forEach(country => {
        const growth = ((country.economic_data.post_war_gdp - country.economic_data.pre_war_gdp) / 
                       country.economic_data.pre_war_gdp * 100).toFixed(1);
        
        const card = document.createElement('div');
        card.className = 'country-card';
        card.innerHTML = `
            <h4>${country.name}</h4>
            <span class="region">${country.region}</span>
            <div class="stats">
                <span class="badge">PIB: $${country.economic_data.post_war_gdp}M</span>
                <span class="badge secondary">Crecimiento: +${growth}%</span>
                <span class="badge accent">Participación: ${country.social_data.war_participation > 0 ? 'Sí' : 'No'}</span>
            </div>
            <p class="mt-1">Click para ver análisis detallado</p>
        `;
        
        container.appendChild(card);
    });
}

// Datos de ejemplo
function getExampleCountries() {
    return [
        {
            name: "Estados Unidos",
            region: "Norteamérica",
            economic_data: { pre_war_gdp: 517, post_war_gdp: 680 },
            social_data: { war_participation: 2000000, urbanization_rate: 12.5, migration: 1250000 }
        },
        {
            name: "Argentina",
            region: "Sudamérica", 
            economic_data: { pre_war_gdp: 24, post_war_gdp: 31 },
            social_data: { war_participation: 0, urbanization_rate: 8.2, migration: 450000 }
        }
    ];
}

// Navegación
function initNavigation() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
}

// Notificaciones
function showWarning(message) {
    console.warn(message);
}

// Hacer funciones globales
window.reloadCharts = renderAllCharts;