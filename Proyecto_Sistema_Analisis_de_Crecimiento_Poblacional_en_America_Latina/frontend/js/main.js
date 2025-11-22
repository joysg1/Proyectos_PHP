// frontend/js/main.js - JavaScript principal para index.php

// API Base URL
const API_BASE = 'http://localhost:5000/api';

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ main.js cargado correctamente');
    loadInitialData();
    startCarousel();
});

// Cargar datos iniciales
async function loadInitialData() {
    try {
        await Promise.all([
            loadStats(),
            loadCountries(),
            loadRegionalAnalysis(),
            loadCountrySelect()
        ]);
    } catch (error) {
        console.error('Error loading initial data:', error);
        showError('Error cargando datos iniciales');
    }
}

// Cargar estadísticas
async function loadStats() {
    try {
        const response = await fetch(`${API_BASE}/analisis/regional`);
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        
        const data = await response.json();
        
        const statsHTML = `
            <div class="stat-card">
                <span class="stat-number">${formatNumber(data.poblacion_total)}</span>
                <span class="stat-label">Población Total</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">${data.tasa_crecimiento_promedio.toFixed(2)}%</span>
                <span class="stat-label">Crecimiento Promedio</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">${data.expectativa_vida_promedio.toFixed(1)}</span>
                <span class="stat-label">Expectativa de Vida</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">${data.numero_paises}</span>
                <span class="stat-label">Países Analizados</span>
            </div>
        `;
        
        document.getElementById('statsContainer').innerHTML = statsHTML;
    } catch (error) {
        console.error('Error loading stats:', error);
        document.getElementById('statsContainer').innerHTML = 
            '<div class="stat-card"><span class="stat-label">Error cargando estadísticas</span></div>';
    }
}

// Cargar países
async function loadCountries() {
    try {
        const response = await fetch(`${API_BASE}/paises`);
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        
        const countries = await response.json();
        
        const countriesHTML = countries.map(country => `
            <div class="card">
                <h3>${country.nombre}</h3>
                <div class="stats-container">
                    <div class="stat-card">
                        <span class="stat-number">${formatNumber(country.poblacion_2023)}</span>
                        <span class="stat-label">Población 2023</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">${country.tasa_crecimiento}%</span>
                        <span class="stat-label">Crecimiento</span>
                    </div>
                </div>
                <p><strong>Expectativa de vida:</strong> ${country.expectativa_vida} años</p>
                <p><strong>Población urbana:</strong> ${country.poblacion_urbana}%</p>
                <button class="btn btn-secondary" onclick="viewCountryDetails(${country.id})">📊 Ver Detalles</button>
            </div>
        `).join('');
        
        document.getElementById('countriesGrid').innerHTML = countriesHTML;
    } catch (error) {
        console.error('Error loading countries:', error);
        document.getElementById('countriesGrid').innerHTML = 
            '<div class="card"><p>Error cargando datos de países</p></div>';
    }
}

// Cargar análisis regional
async function loadRegionalAnalysis() {
    try {
        const response = await fetch(`${API_BASE}/analisis/regional`);
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        
        const analysis = await response.json();
        
        const analysisHTML = `
            <div class="stats-container">
                <div class="stat-card">
                    <span class="stat-number">${analysis.pais_mas_poblado}</span>
                    <span class="stat-label">País Más Poblado</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number">${analysis.pais_menos_poblado}</span>
                    <span class="stat-label">País Menos Poblado</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number">${analysis.mayor_crecimiento}</span>
                    <span class="stat-label">Mayor Crecimiento</span>
                </div>
            </div>
            <p><strong>Resumen Regional:</strong> La región cuenta con ${formatNumber(analysis.poblacion_total)} habitantes, 
            con una tasa de crecimiento promedio del ${analysis.tasa_crecimiento_promedio.toFixed(2)}% anual y 
            una expectativa de vida de ${analysis.expectativa_vida_promedio.toFixed(1)} años.</p>
        `;
        
        document.getElementById('regionalAnalysis').innerHTML = analysisHTML;
    } catch (error) {
        console.error('Error loading regional analysis:', error);
        document.getElementById('regionalAnalysis').innerHTML = 
            '<p>Error cargando análisis regional</p>';
    }
}

// Cargar selector de países
async function loadCountrySelect() {
    try {
        const response = await fetch(`${API_BASE}/paises`);
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        
        const countries = await response.json();
        
        const options = countries.map(country => 
            `<option value="${country.nombre}">${country.nombre}</option>`
        ).join('');
        
        document.getElementById('countrySelect').innerHTML = 
            '<option value="">Seleccionar país...</option>' + options;
    } catch (error) {
        console.error('Error loading country select:', error);
        document.getElementById('countrySelect').innerHTML = 
            '<option value="">Error cargando países</option>';
    }
}

// Cargar predicciones
async function loadPredictions() {
    const country = document.getElementById('countrySelect').value;
    if (!country) return;
    
    const resultDiv = document.getElementById('predictionsResult');
    resultDiv.innerHTML = '<div class="loading"><div class="spinner"></div><p>Cargando predicciones...</p></div>';
    
    try {
        const response = await fetch(`${API_BASE}/prediccion/${encodeURIComponent(country)}`);
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        
        const prediction = await response.json();
        
        const predictionsHTML = `
            <div class="card">
                <h4>Predicciones de Crecimiento para ${country}</h4>
                <p><strong>Tasa de crecimiento estimada:</strong> ${prediction.tasa_crecimiento_estimada.toFixed(2)}% anual</p>
                <div class="stats-container">
                    ${Object.entries(prediction.predicciones).map(([year, pop]) => `
                        <div class="stat-card">
                            <span class="stat-number">${formatNumber(pop)}</span>
                            <span class="stat-label">${year}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
        
        resultDiv.innerHTML = predictionsHTML;
    } catch (error) {
        console.error('Error loading predictions:', error);
        resultDiv.innerHTML = '<p>Error cargando predicciones. Asegúrate de que el servidor Python esté ejecutándose.</p>';
    }
}

// Cargar clusters
async function loadClusters() {
    const resultDiv = document.getElementById('clustersResult');
    resultDiv.innerHTML = '<div class="loading"><div class="spinner"></div><p>Generando agrupamientos...</p></div>';
    
    try {
        const response = await fetch(`${API_BASE}/clusters?n_clusters=3`);
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        
        const clusters = await response.json();
        
        const clustersHTML = clusters.map(cluster => `
            <div class="card" style="margin-bottom: 1rem;">
                <h4>Grupo ${cluster.cluster + 1} (${cluster.tamaño} países)</h4>
                <p><strong>Países:</strong> ${cluster.paises.join(', ')}</p>
                <p><strong>Características promedio:</strong></p>
                <ul>
                    <li>Tasa crecimiento: ${cluster.caracteristicas_promedio.tasa_crecimiento.toFixed(2)}%</li>
                    <li>Expectativa vida: ${cluster.caracteristicas_promedio.expectativa_vida.toFixed(1)} años</li>
                    <li>Población urbana: ${cluster.caracteristicas_promedio.poblacion_urbana.toFixed(1)}%</li>
                </ul>
            </div>
        `).join('');
        
        resultDiv.innerHTML = clustersHTML;
    } catch (error) {
        console.error('Error loading clusters:', error);
        resultDiv.innerHTML = '<p>Error generando agrupamientos</p>';
    }
}

// Función para ver detalles del país
async function viewCountryDetails(countryId) {
    console.log('🚀 viewCountryDetails ejecutándose para ID:', countryId);
    
    try {
        // Mostrar loading inmediatamente
        showNotification('🔄 Cargando detalles del país...', 'info');
        
        const response = await fetch(`${API_BASE}/paises/${countryId}`);
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        
        const country = await response.json();
        console.log('✅ Datos del país cargados:', country);
        
        // Llamar a la función que muestra el modal
        showCountryModal(country);
        
    } catch (error) {
        console.error('❌ Error loading country details:', error);
        showError('Error cargando detalles del país: ' + error.message);
    }
}

// Mostrar modal con detalles del país
function showCountryModal(country) {
    console.log('🎯 Mostrando modal para:', country.nombre);
    
    // Verificar que los datos necesarios existan
    if (!country.grupos_edad) {
        country.grupos_edad = {
            '0_14': 'N/A',
            '15_64': 'N/A', 
            '65_plus': 'N/A'
        };
    }
    
    const modalHTML = `
        <div class="modal" id="countryModal">
            <div class="modal-content" style="max-width: 900px; max-height: 85vh; display: flex; flex-direction: column;">
                <div class="modal-header" style="flex-shrink: 0; text-align: center; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 2px solid rgba(52, 152, 219, 0.3);">
                    <span class="close-modal" onclick="closeModal('countryModal')" style="position: absolute; right: 1.5rem; top: 1.5rem; cursor: pointer; font-size: 2rem; color: #bdc3c7;">&times;</span>
                    <h2 style="color: #3498db; margin: 0; font-size: 2.2rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        🌎 ${country.nombre}
                    </h2>
                    <p style="color: #bdc3c7; margin: 0.5rem 0 0 0; font-size: 1.1rem;">
                        Análisis Demográfico Completo
                    </p>
                </div>

                <div class="modal-body" style="flex: 1; overflow-y: auto; padding-right: 10px;">
                    <!-- Estadísticas Principales -->
                    <div class="stats-container" style="margin: 1.5rem 0;">
                        <div class="stat-card">
                            <span class="stat-number">${formatNumber(country.poblacion_2023)}</span>
                            <span class="stat-label">Población 2023</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number">${country.tasa_crecimiento}%</span>
                            <span class="stat-label">Tasa Crecimiento</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number">${country.expectativa_vida}</span>
                            <span class="stat-label">Expectativa Vida</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number">${country.densidad_poblacional || 'N/A'}</span>
                            <span class="stat-label">Densidad/km²</span>
                        </div>
                    </div>

                    <!-- Grid de Información -->
                    <div class="modal-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin: 1.5rem 0;">
                        
                        <!-- Columna Izquierda -->
                        <div class="modal-column">
                            <!-- Evolución Poblacional -->
                            <div class="info-card" style="background: linear-gradient(145deg, #2d2d2d, #252525); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; border-left: 4px solid #FF6B6B;">
                                <h3 style="color: #FF6B6B; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                    📊 Evolución Poblacional
                                </h3>
                                <div class="evolution-stats" style="display: grid; gap: 0.8rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
                                        <span style="color: #bdc3c7;">2020</span>
                                        <span style="color: #ecf0f1; font-weight: bold;">${formatNumber(country.poblacion_2020)}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
                                        <span style="color: #bdc3c7;">2021</span>
                                        <span style="color: #ecf0f1; font-weight: bold;">${formatNumber(country.poblacion_2021)}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
                                        <span style="color: #bdc3c7;">2022</span>
                                        <span style="color: #ecf0f1; font-weight: bold;">${formatNumber(country.poblacion_2022)}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0;">
                                        <span style="color: #bdc3c7; font-weight: bold;">2023</span>
                                        <span style="color: #3498db; font-weight: bold; font-size: 1.1rem;">${formatNumber(country.poblacion_2023)}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Indicadores Demográficos -->
                            <div class="info-card" style="background: linear-gradient(145deg, #2d2d2d, #252525); padding: 1.5rem; border-radius: 12px; border-left: 4px solid #4ECDC4;">
                                <h3 style="color: #4ECDC4; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                    📈 Indicadores Clave
                                </h3>
                                <div class="indicators-grid" style="display: grid; gap: 0.8rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="color: #bdc3c7;">Tasa Natalidad</span>
                                        <span style="color: #ecf0f1; font-weight: bold;">${country.tasa_natalidad || 'N/A'}‰</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="color: #bdc3c7;">Tasa Mortalidad</span>
                                        <span style="color: #ecf0f1; font-weight: bold;">${country.tasa_mortalidad || 'N/A'}‰</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="color: #bdc3c7;">Migración Neta</span>
                                        <span style="color: ${(country.migracion_neta || 0) >= 0 ? '#27ae60' : '#e74c3c'}; font-weight: bold;">
                                            ${country.migracion_neta || 'N/A'}%
                                        </span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="color: #bdc3c7;">Población Urbana</span>
                                        <span style="color: #ecf0f1; font-weight: bold;">${country.poblacion_urbana}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna Derecha -->
                        <div class="modal-column">
                            <!-- Estructura Demográfica -->
                            <div class="info-card" style="background: linear-gradient(145deg, #2d2d2d, #252525); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; border-left: 4px solid #45B7D1;">
                                <h3 style="color: #45B7D1; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                    👥 Estructura Demográfica
                                </h3>
                                <div class="age-structure" style="margin-bottom: 1rem;">
                                    <div style="margin-bottom: 1rem;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.3rem;">
                                            <span style="color: #bdc3c7;">0-14 años</span>
                                            <span style="color: #ecf0f1; font-weight: bold;">${country.grupos_edad['0_14']}%</span>
                                        </div>
                                        <div style="background: rgba(255,255,255,0.1); border-radius: 10px; overflow: hidden; height: 8px;">
                                            <div style="background: #FF6B6B; height: 100%; width: ${country.grupos_edad['0_14']}%;"></div>
                                        </div>
                                    </div>
                                    <div style="margin-bottom: 1rem;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.3rem;">
                                            <span style="color: #bdc3c7;">15-64 años</span>
                                            <span style="color: #ecf0f1; font-weight: bold;">${country.grupos_edad['15_64']}%</span>
                                        </div>
                                        <div style="background: rgba(255,255,255,0.1); border-radius: 10px; overflow: hidden; height: 8px;">
                                            <div style="background: #4ECDC4; height: 100%; width: ${country.grupos_edad['15_64']}%;"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.3rem;">
                                            <span style="color: #bdc3c7;">65+ años</span>
                                            <span style="color: #ecf0f1; font-weight: bold;">${country.grupos_edad['65_plus']}%</span>
                                        </div>
                                        <div style="background: rgba(255,255,255,0.1); border-radius: 10px; overflow: hidden; height: 8px;">
                                            <div style="background: #45B7D1; height: 100%; width: ${country.grupos_edad['65_plus']}%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Análisis de Crecimiento -->
                            <div class="info-card" style="background: linear-gradient(145deg, #2d2d2d, #252525); padding: 1.5rem; border-radius: 12px; border-left: 4px solid #96CEB4;">
                                <h3 style="color: #96CEB4; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                    💡 Análisis de Crecimiento
                                </h3>
                                <div style="color: #bdc3c7; line-height: 1.6;">
                                    <p>El país muestra una tasa de crecimiento del <strong style="color: ${country.tasa_crecimiento > 1 ? '#27ae60' : '#e74c3c'}">${country.tasa_crecimiento}%</strong> anual.</p>
                                    <p>La estructura poblacional indica <strong>${getPopulationType(country.grupos_edad)}</strong>.</p>
                                    <p>La expectativa de vida de <strong>${country.expectativa_vida} años</strong> ${getLifeExpectancyAnalysis(country.expectativa_vida)}.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="modal-footer" style="flex-shrink: 0; text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1);">
                    <button class="btn btn-primary" onclick="loadCountryPredictions('${country.nombre}')" style="padding: 1rem 2rem; font-size: 1.1rem;">
                        🚀 Ver Proyecciones Futuras
                    </button>
                </div>
            </div>
        </div>
    `;

    // Remover modal existente si hay uno
    const existingModal = document.getElementById('countryModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Agregar el modal al DOM
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Abrir el modal
    openModal('countryModal');
    
    console.log('✅ Modal de país mostrado correctamente');
}

// Cargar predicciones para el país
async function loadCountryPredictions(countryName) {
    try {
        showNotification('🔄 Cargando proyecciones...', 'info');
        
        const response = await fetch(`${API_BASE}/prediccion/${encodeURIComponent(countryName)}`);
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        
        const prediction = await response.json();
        showPredictionsModal(prediction);
        
    } catch (error) {
        console.error('Error loading predictions:', error);
        showError('Error cargando proyecciones: ' + error.message);
    }
}

// Mostrar modal con predicciones (SIN BOTÓN EXPORTAR CSV)
function showPredictionsModal(prediction) {
    const currentYear = new Date().getFullYear();
    const growthType = prediction.tasa_crecimiento_estimada > 1 ? 'acelerado' : 
                      prediction.tasa_crecimiento_estimada > 0.5 ? 'moderado' : 'estancado';
    
    const growthColor = prediction.tasa_crecimiento_estimada > 1 ? '#27ae60' : 
                       prediction.tasa_crecimiento_estimada > 0.5 ? '#f39c12' : '#e74c3c';

    const modalHTML = `
        <div class="modal" id="predictionsModal">
            <div class="modal-content" style="max-width: 800px; max-height: 85vh; display: flex; flex-direction: column;">
                <div class="modal-header" style="flex-shrink: 0; text-align: center; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 2px solid rgba(52, 152, 219, 0.3);">
                    <span class="close-modal" onclick="closeModal('predictionsModal')" style="position: absolute; right: 1.5rem; top: 1.5rem; cursor: pointer; font-size: 2rem; color: #bdc3c7;">&times;</span>
                    <h2 style="color: #3498db; margin: 0; font-size: 2rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        🚀 Proyecciones Poblacionales
                    </h2>
                    <p style="color: #bdc3c7; margin: 0.5rem 0 0 0; font-size: 1.1rem;">
                        ${prediction.pais} - ${currentYear} a ${parseInt(Object.keys(prediction.predicciones)[0]) + 4}
                    </p>
                </div>

                <div class="modal-body" style="flex: 1; overflow-y: auto; padding-right: 10px;">
                    <!-- Información Principal -->
                    <div class="prediction-main" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        
                        <!-- Tasa de Crecimiento -->
                        <div class="info-card" style="background: linear-gradient(145deg, #2d2d2d, #252525); padding: 1.5rem; border-radius: 16px; text-align: center; border: 1px solid rgba(255,255,255,0.1);">
                            <div style="font-size: 2.5rem; margin-bottom: 1rem;">📈</div>
                            <h3 style="color: #ecf0f1; margin-bottom: 0.5rem; font-size: 1.2rem;">Tasa de Crecimiento Estimada</h3>
                            <div style="font-size: 2.2rem; font-weight: 800; color: ${growthColor}; margin-bottom: 0.5rem;">
                                ${prediction.tasa_crecimiento_estimada.toFixed(2)}%
                            </div>
                            <p style="color: #bdc3c7; font-size: 0.95rem;">
                                Crecimiento ${growthType} anual
                            </p>
                        </div>

                        <!-- Resumen de Proyecciones -->
                        <div class="info-card" style="background: linear-gradient(145deg, #2d2d2d, #252525); padding: 1.5rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
                            <h3 style="color: #ecf0f1; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; font-size: 1.2rem;">
                                🎯 Resumen Proyectado
                            </h3>
                            <div style="color: #bdc3c7; line-height: 1.6; font-size: 0.95rem;">
                                <p>Basado en el análisis de datos históricos y algoritmos de machine learning.</p>
                                <p>Se proyecta un crecimiento <strong style="color: ${growthColor}">${growthType}</strong> para los próximos 5 años.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Proyecciones Detalladas -->
                    <div class="info-card" style="background: linear-gradient(145deg, #2d2d2d, #252525); padding: 1.5rem; border-radius: 16px; margin-bottom: 1.5rem; border-left: 4px solid #3498db;">
                        <h3 style="color: #3498db; margin-bottom: 1.2rem; display: flex; align-items: center; gap: 0.5rem;">
                            📅 Proyecciones de Población
                        </h3>
                        <div class="predictions-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem;">
                            ${Object.entries(prediction.predicciones).map(([year, pop]) => `
                                <div style="text-align: center; padding: 1.5rem 0.5rem; background: rgba(255,255,255,0.05); border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); min-height: 100px; display: flex; flex-direction: column; justify-content: center;">
                                    <div style="font-size: 1.4rem; font-weight: 800; color: #3498db; margin-bottom: 0.5rem; word-wrap: break-word; overflow-wrap: break-word;">
                                        ${formatNumberCompact(pop)}
                                    </div>
                                    <div style="color: #bdc3c7; font-size: 1rem; font-weight: 600;">
                                        ${year}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>

                    <!-- Análisis de Tendencia -->
                    <div class="info-card" style="background: linear-gradient(145deg, #2d2d2d, #252525); padding: 1.5rem; border-radius: 16px; border-left: 4px solid #96CEB4;">
                        <h3 style="color: #96CEB4; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                            🔍 Análisis de Tendencia
                        </h3>
                        <div style="color: #bdc3c7; line-height: 1.6;">
                            <p>La proyección indica que la población de <strong>${prediction.pais}</strong> podría 
                            ${prediction.tasa_crecimiento_estimada > 0 ? 'aumentar' : 'disminuir'} significativamente 
                            en los próximos años, influenciado por factores como:</p>
                            <ul style="margin: 1rem 0; padding-left: 1.5rem;">
                                <li>Tasas de natalidad y mortalidad actuales</li>
                                <li>Patrones migratorios recientes</li>
                                <li>Tendencias de urbanización</li>
                                <li>Políticas públicas implementadas</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Acciones SIMPLIFICADAS (sin exportar CSV) -->
                <div class="modal-footer" style="flex-shrink: 0; text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1);">
                    <button class="btn btn-primary" onclick="closeModal('predictionsModal')" style="padding: 1rem 2rem;">
                        ← Volver al Análisis
                    </button>
                </div>
            </div>
        </div>
    `;

    const existingModal = document.getElementById('predictionsModal');
    if (existingModal) {
        existingModal.remove();
    }

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    closeModal('countryModal');
    openModal('predictionsModal');
}

// Funciones auxiliares
function getPopulationType(gruposEdad) {
    if (gruposEdad['0_14'] > 30) {
        return "una población joven y en crecimiento";
    } else if (gruposEdad['65_plus'] > 15) {
        return "una población envejecida";
    } else {
        return "una población en transición";
    }
}

function getLifeExpectancyAnalysis(expectativa) {
    if (expectativa > 78) {
        return "es superior al promedio regional";
    } else if (expectativa > 72) {
        return "se encuentra en el promedio regional";
    } else {
        return "está por debajo del promedio regional";
    }
}

function formatNumber(num) {
    return new Intl.NumberFormat('es-ES').format(num);
}

function formatNumberCompact(num) {
    if (num >= 1000000000) {
        return (num / 1000000000).toFixed(1) + 'B';
    } else if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

// Funciones de modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        console.log('🔓 Abriendo modal:', modalId);
        modal.style.display = 'block';
        modal.classList.add('show', 'active');
        document.body.style.overflow = 'hidden';
        
        // Asegurar que el modal esté centrado
        setTimeout(() => {
            modal.scrollTop = 0;
        }, 10);
    } else {
        console.error('❌ Modal no encontrado:', modalId);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        console.log('🔒 Cerrando modal:', modalId);
        modal.style.display = 'none';
        modal.classList.remove('show', 'active');
        document.body.style.overflow = 'auto';
    }
}

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

// Carousel functions
let currentCarouselIndex = 0;
let carouselInterval;

function moveCarousel(direction) {
    const carouselItems = document.querySelectorAll('.carousel-item');
    const indicators = document.querySelectorAll('.carousel-indicator');
    
    currentCarouselIndex = (currentCarouselIndex + direction + carouselItems.length) % carouselItems.length;
    updateCarousel();
}

function setCarousel(index) {
    currentCarouselIndex = index;
    updateCarousel();
    resetCarouselInterval();
}

function updateCarousel() {
    const carouselInner = document.querySelector('.carousel-inner');
    const carouselItems = document.querySelectorAll('.carousel-item');
    const indicators = document.querySelectorAll('.carousel-indicator');
    
    if (carouselInner && carouselItems.length > 0) {
        carouselInner.style.transform = `translateX(-${currentCarouselIndex * 100}%)`;
        
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === currentCarouselIndex);
        });
    }
}

function startCarousel() {
    carouselInterval = setInterval(() => {
        moveCarousel(1);
    }, 5000);
}

function resetCarouselInterval() {
    clearInterval(carouselInterval);
    startCarousel();
}

// Funciones de notificación y error
function showError(message) {
    const notification = document.createElement('div');
    notification.className = 'notification notification-warning';
    notification.innerHTML = `
        <p>${message}</p>
        <button onclick="this.parentElement.remove()">×</button>
    `;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #f39c12;
        color: white;
        padding: 1rem;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 10000;
        max-width: 300px;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <p>${message}</p>
        <button onclick="this.parentElement.remove()">×</button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Cerrar modal al hacer click fuera
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            closeModal(modal.id);
        }
    });
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (modal.style.display === 'block' || modal.classList.contains('show')) {
                closeModal(modal.id);
            }
        });
    }
});

// Manejar errores no capturados
window.addEventListener('error', function(e) {
    console.error('Error no capturado:', e.error);
});

// Asegurar que las funciones estén disponibles globalmente
window.viewCountryDetails = viewCountryDetails;
window.openModal = openModal;
window.closeModal = closeModal;
window.switchTab = switchTab;
window.loadPredictions = loadPredictions;
window.loadClusters = loadClusters;
window.moveCarousel = moveCarousel;
window.setCarousel = setCarousel;
window.loadCountryPredictions = loadCountryPredictions;

console.log('🌍 Funciones de PopAnalytics cargadas correctamente');