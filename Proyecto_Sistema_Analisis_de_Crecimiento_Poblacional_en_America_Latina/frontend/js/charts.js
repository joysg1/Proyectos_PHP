// frontend/js/charts.js - JavaScript para graficos.php

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
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        
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
        showChartError('compareCountry1', 'Error cargando lista de países');
        showChartError('compareCountry2', 'Error cargando lista de países');
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

// Cargar gráfico de área (ahora líneas con área)
async function loadAreaChart() {
    const container = document.getElementById('areaChart');
    if (!container) return;
    
    container.innerHTML = '<div class="loading"><div class="spinner"></div><p>Cargando gráfico de evolución poblacional...</p></div>';
    
    try {
        const response = await fetch(`${API_BASE}/graficos/area`);
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        
        const data = await response.json();
        
        container.innerHTML = `
            <div class="chart-image-container">
                <img src="${data.image}" alt="Gráfico de Evolución Poblacional - Tendencias 2020-2023" class="chart-image">
                <p class="chart-description">Evolución de la población por país desde 2020 hasta 2023. Cada línea representa un país con su tasa de crecimiento.</p>
                <div class="chart-actions">
                    <button class="btn btn-secondary" onclick="loadAreaChart()">🔄 Actualizar</button>
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Error loading area chart:', error);
        showChartError('areaChart', 'Error cargando el gráfico de evolución. Asegúrate de que el servidor Python esté ejecutándose.');
    }
}

// Cargar gráficos radar
async function loadRadarChart() {
    const container = document.getElementById('radarChart');
    if (!container) return;
    
    container.innerHTML = '<div class="loading"><div class="spinner"></div><p>Cargando gráficos radar...</p></div>';
    
    try {
        const response = await fetch(`${API_BASE}/graficos/radar`);
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        
        const data = await response.json();
        
        container.innerHTML = `
            <div class="chart-image-container">
                <img src="${data.image}" alt="Gráficos Radar - Indicadores Poblacionales" class="chart-image">
                <p class="chart-description">Comparación de múltiples indicadores poblacionales por país. Cada radar muestra el perfil único de un país.</p>
                <div class="chart-actions">
                    <button class="btn btn-secondary" onclick="loadRadarChart()">🔄 Actualizar</button>
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Error loading radar chart:', error);
        showChartError('radarChart', 'Error cargando los gráficos radar. Asegúrate de que el servidor Python esté ejecutándose.');
    }
}

// Cargar gráfico de barras apiladas (ahora pirámide poblacional horizontal)
async function loadBarChart() {
    const container = document.getElementById('barChart');
    if (!container) return;
    
    container.innerHTML = '<div class="loading"><div class="spinner"></div><p>Cargando pirámide poblacional...</p></div>';
    
    try {
        const response = await fetch(`${API_BASE}/graficos/barras-apiladas`);
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        
        const data = await response.json();
        
        container.innerHTML = `
            <div class="chart-image-container">
                <img src="${data.image}" alt="Pirámide Poblacional - Distribución por Grupos de Edad" class="chart-image">
                <p class="chart-description">Distribución de la población por grupos de edad en cada país. Muestra la estructura demográfica actual.</p>
                <div class="chart-actions">
                    <button class="btn btn-secondary" onclick="loadBarChart()">🔄 Actualizar</button>
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Error loading bar chart:', error);
        showChartError('barChart', 'Error cargando la pirámide poblacional. Asegúrate de que el servidor Python esté ejecutándose.');
    }
}

// Cargar gráfico de pastel
async function loadPieChart() {
    const container = document.getElementById('pieChart');
    if (!container) return;
    
    container.innerHTML = '<div class="loading"><div class="spinner"></div><p>Cargando gráfico de pastel...</p></div>';
    
    try {
        const response = await fetch(`${API_BASE}/graficos/pastel`);
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        
        const data = await response.json();
        
        container.innerHTML = `
            <div class="chart-image-container">
                <img src="${data.image}" alt="Gráfico de Pastel - Distribución Poblacional Regional" class="chart-image">
                <p class="chart-description">Distribución porcentual de la población entre los países de América Latina.</p>
                <div class="chart-actions">
                    <button class="btn btn-secondary" onclick="loadPieChart()">🔄 Actualizar</button>
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Error loading pie chart:', error);
        showChartError('pieChart', 'Error cargando el gráfico de pastel. Asegúrate de que el servidor Python esté ejecutándose.');
    }
}

// Cargar gráfico de comparación
async function loadComparisonChart() {
    const country1 = document.getElementById('compareCountry1').value;
    const country2 = document.getElementById('compareCountry2').value;
    
    if (!country1 || !country2) {
        showNotification('Por favor selecciona dos países para comparar.', 'warning');
        return;
    }
    
    if (country1 === country2) {
        showNotification('Por favor selecciona dos países diferentes.', 'warning');
        return;
    }
    
    const container = document.getElementById('comparisonChart');
    container.innerHTML = '<div class="loading"><div class="spinner"></div><p>Generando comparación...</p></div>';
    
    try {
        const response = await fetch(`${API_BASE}/graficos/comparacion-radar?pais1=${encodeURIComponent(country1)}&pais2=${encodeURIComponent(country2)}`);
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        
        const data = await response.json();
        
        container.innerHTML = `
            <div class="chart-image-container">
                <h4>Comparación: ${country1} vs ${country2}</h4>
                <img src="${data.image}" alt="Gráfico de Comparación Radar" class="chart-image">
                <p class="chart-description">Comparación directa de indicadores clave entre ${country1} y ${country2}.</p>
                <div class="chart-actions">
                    <button class="btn btn-secondary" onclick="loadComparisonChart()">🔄 Comparar Otros</button>
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Error loading comparison chart:', error);
        showChartError('comparisonChart', 'Error generando la comparación. Asegúrate de que el servidor Python esté ejecutándose.');
    }
}

// Funciones de utilidad para gráficos
function showChartError(containerId, message) {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = `
            <div class="chart-error">
                <p>${message}</p>
                <button class="btn btn-secondary" onclick="retryLoadChart('${containerId}')">Reintentar</button>
            </div>
        `;
    }
}

function retryLoadChart(containerId) {
    switch(containerId) {
        case 'areaChart':
            loadAreaChart();
            break;
        case 'radarChart':
            loadRadarChart();
            break;
        case 'barChart':
            loadBarChart();
            break;
        case 'pieChart':
            loadPieChart();
            break;
        case 'comparisonChart':
            loadComparisonChart();
            break;
    }
}

function showNotification(message, type = 'info') {
    // Crear notificación temporal
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <p>${message}</p>
        <button onclick="this.parentElement.remove()">×</button>
    `;
    
    // Estilos para la notificación
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'warning' ? '#f39c12' : '#2c3e50'};
        color: white;
        padding: 1rem;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 10000;
        max-width: 300px;
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Funciones del modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
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

// Exportar gráficos (función adicional)
function exportChart(chartType) {
    const containers = {
        'area': 'areaChart',
        'radar': 'radarChart', 
        'barras': 'barChart',
        'pastel': 'pieChart',
        'comparacion': 'comparisonChart'
    };
    
    const containerId = containers[chartType];
    const img = document.querySelector(`#${containerId} img`);
    
    if (img) {
        const link = document.createElement('a');
        link.download = `grafico-${chartType}-${new Date().toISOString().split('T')[0]}.png`;
        link.href = img.src;
        link.click();
    } else {
        showNotification('No hay gráfico disponible para exportar', 'warning');
    }
}

// Manejar errores no capturados
window.addEventListener('error', function(e) {
    console.error('Error no capturado en charts:', e.error);
});