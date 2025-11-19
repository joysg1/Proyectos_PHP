// Configuración
const API_BASE_URL = 'http://localhost:5000/api';
let currentSlide = 0;
const totalSlides = document.querySelectorAll('.carousel-slide').length;

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    initializeCarousel();
    initializeModal();
    initializePredictionForm();
    initializeGestionDiscos();
    checkAPIStatus();
});

// Verificar estado de la API
async function checkAPIStatus() {
    try {
        const response = await fetch(`${API_BASE_URL}/health`);
        if (!response.ok) throw new Error('API no disponible');
        console.log('✅ API de discos duros conectada correctamente');
    } catch (error) {
        console.error('❌ Error conectando con la API:', error);
        showNotification('Error conectando con el servidor Python', 'error');
    }
}

// Sistema de notificaciones
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 
                          type === 'success' ? 'check-circle' : 'info-circle'}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">&times;</button>
    `;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'error' ? '#ef4444' : 
                     type === 'success' ? '#10b981' : '#6366f1'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        z-index: 1000;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideInRight 0.3s ease-out;
        max-width: 400px;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Carrusel de gráficos
function initializeCarousel() {
    updateCarousel();
}

function navigateCarousel(direction) {
    currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
    updateCarousel();
}

function goToSlide(index) {
    currentSlide = index;
    updateCarousel();
}

function updateCarousel() {
    const slides = document.querySelectorAll('.carousel-slide');
    const indicators = document.querySelectorAll('.indicator');
    
    slides.forEach((slide, index) => {
        slide.classList.toggle('active', index === currentSlide);
    });
    
    indicators.forEach((indicator, index) => {
        indicator.classList.toggle('active', index === currentSlide);
    });
}

// Modal para gráficos
function initializeModal() {
    const modal = document.getElementById('graphicModal');
    const closeBtn = document.querySelector('.close-modal');
    
    closeBtn.onclick = () => modal.style.display = 'none';
    
    window.onclick = (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
}

function openModal(imageSrc) {
    const modal = document.getElementById('graphicModal');
    const modalImage = document.getElementById('modalImage');
    
    modalImage.src = imageSrc;
    modal.style.display = 'block';
}

// Cargar gráficos
async function cargarGrafico(tipo) {
    const placeholder = document.getElementById(`${tipo}-chart`);
    const originalContent = placeholder.innerHTML;
    
    placeholder.innerHTML = `
        <i class="fas fa-spinner"></i>
        <p>Generando gráfico...</p>
    `;
    placeholder.classList.add('loading');
    
    try {
        const response = await fetch(`${API_BASE_URL}/grafico/${tipo}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            const img = document.createElement('img');
            img.src = `data:image/png;base64,${data.image}`;
            img.alt = `Gráfico de ${tipo}`;
            img.className = 'graphic-image';
            img.onclick = () => openModal(img.src);
            
            placeholder.innerHTML = '';
            placeholder.appendChild(img);
            placeholder.classList.remove('loading');
            
            showNotification('Gráfico generado exitosamente', 'success');
            
            const slideIndex = getSlideIndexByType(tipo);
            if (slideIndex !== -1) {
                goToSlide(slideIndex);
            }
        } else {
            throw new Error(data.error || 'Error desconocido del servidor');
        }
    } catch (error) {
        console.error('Error cargando gráfico:', error);
        placeholder.innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Error cargando gráfico: ${error.message}</p>
            </div>
        `;
        placeholder.classList.remove('loading');
        showNotification(`Error generando gráfico: ${error.message}`, 'error');
    }
}

function getSlideIndexByType(tipo) {
    const slideTypes = ['area', 'radar', 'barras', 'pastel'];
    return slideTypes.indexOf(tipo);
}

// Formulario de predicción
function initializePredictionForm() {
    const form = document.getElementById('prediction-form');
    form.addEventListener('submit', handlePredictionSubmit);
}

async function handlePredictionSubmit(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const datosDisco = Object.fromEntries(formData);
    
    // Convertir números
    datosDisco.capacidad_gb = parseInt(datosDisco.capacidad_gb);
    datosDisco.tiempo_uso_meses = parseInt(datosDisco.tiempo_uso_meses);
    datosDisco.horas_encendido = parseInt(datosDisco.horas_encendido);
    datosDisco.ciclos_escritura = parseInt(datosDisco.ciclos_escritura);
    datosDisco.temperatura_promedio = parseInt(datosDisco.temperatura_promedio);
    datosDisco.bad_sectors = parseInt(datosDisco.bad_sectors);
    
    const resultDiv = document.getElementById('prediction-result');
    resultDiv.innerHTML = '<div class="loading"></div>';
    resultDiv.classList.add('active');
    
    try {
        const response = await fetch(`${API_BASE_URL}/ml/prediccion`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(datosDisco)
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            const prediccion = data.prediccion;
            displayPredictionResult(prediccion, datosDisco);
            showNotification('Predicción generada exitosamente', 'success');
        } else {
            throw new Error(data.error || 'Error desconocido del servidor');
        }
    } catch (error) {
        console.error('Error en predicción:', error);
        resultDiv.innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <h4>Error en la predicción</h4>
                <p>${error.message}</p>
            </div>
        `;
        showNotification('Error generando predicción', 'error');
    }
}

function displayPredictionResult(prediccion, datosDisco) {
    const resultDiv = document.getElementById('prediction-result');
    
    const estadoClass = `estado-${prediccion.estado_predicho.toLowerCase()}`;
    const riesgoClass = `riesgo-${prediccion.vida_util_restante.riesgo.toLowerCase()}`;
    
    resultDiv.innerHTML = `
        <div class="prediction-summary">
            <h3>Resultado de la Predicción</h3>
            <div class="prediction-main">
                <div class="prediction-value ${estadoClass}">
                    <span class="percentage">${prediccion.porcentaje_desgaste_predicho}%</span>
                    <span class="label">Desgaste Predicho</span>
                </div>
                <div class="prediction-details">
                    <div class="detail-item">
                        <strong>Estado:</strong>
                        <span class="${estadoClass}">${prediccion.estado_predicho}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Vida Útil Restante:</strong>
                        <span class="${riesgoClass}">${prediccion.vida_util_restante.meses_restantes} meses</span>
                    </div>
                    <div class="detail-item">
                        <strong>Nivel de Riesgo:</strong>
                        <span class="${riesgoClass}">${prediccion.vida_util_restante.riesgo}</span>
                    </div>
                </div>
            </div>
            <div class="recommendation">
                <h4>Recomendación:</h4>
                <p>${prediccion.vida_util_restante.recomendacion}</p>
            </div>
        </div>
    `;
}

// Gestión de Discos
function initializeGestionDiscos() {
    // Formulario agregar disco
    const formAgregar = document.getElementById('form-agregar-disco');
    formAgregar.addEventListener('submit', handleAgregarDisco);
    
    // Cargar lista inicial
    cargarListaDiscos();
    
    // Auto-calcular campos
    const tiempoUsoInput = document.getElementById('nuevo_tiempo_uso');
    tiempoUsoInput.addEventListener('input', calcularCamposAutomaticos);
}

function calcularCamposAutomaticos() {
    const tiempoUso = parseInt(document.getElementById('nuevo_tiempo_uso').value) || 0;
    
    if (tiempoUso > 0) {
        // Calcular horas encendido (720 horas por mes)
        const horasEncendido = tiempoUso * 720;
        document.getElementById('nuevo_horas').value = horasEncendido;
        
        // Calcular ciclos escritura (500 ciclos por mes)
        const ciclosEscritura = tiempoUso * 500;
        document.getElementById('nuevo_ciclos').value = ciclosEscritura;
        
        // Calcular desgaste estimado (2% por mes, máximo 95%)
        const desgasteEstimado = Math.min(95, tiempoUso * 2);
        document.getElementById('nuevo_desgaste').value = desgasteEstimado;
    }
}

function cargarListaDiscos() {
    const container = document.getElementById('lista-discos');
    container.innerHTML = '<div class="cargando">Cargando discos...</div>';
    
    fetch(`${API_BASE_URL}/discos`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarDiscos(data.discos);
            } else {
                container.innerHTML = `<div class="error">Error cargando discos: ${data.error}</div>`;
            }
        })
        .catch(error => {
            container.innerHTML = `<div class="error">Error: ${error.message}</div>`;
        });
}

function mostrarDiscos(discos) {
    const container = document.getElementById('lista-discos');
    
    if (discos.length === 0) {
        container.innerHTML = '<div class="sin-discos">No hay discos registrados</div>';
        return;
    }
    
    const html = discos.map(disco => `
        <div class="disco-item" data-id="${disco.id}">
            <div class="disco-info">
                <div class="disco-header">
                    <span class="disco-tipo ${disco.tipo.toLowerCase()}">${disco.tipo}</span>
                    <span class="disco-marca">${disco.marca}</span>
                    <span class="disco-modelo">${disco.modelo}</span>
                </div>
                <div class="disco-details">
                    <div class="disco-spec">
                        <span>Capacidad:</span>
                        <strong>${disco.capacidad_gb} GB</strong>
                    </div>
                    <div class="disco-spec">
                        <span>Tiempo uso:</span>
                        <strong>${disco.tiempo_uso_meses} meses</strong>
                    </div>
                    <div class="disco-spec">
                        <span>Desgaste:</span>
                        <strong class="desgaste-${disco.estado.toLowerCase()}">${disco.porcentaje_desgaste}%</strong>
                    </div>
                    <div class="disco-spec">
                        <span>Estado:</span>
                        <strong class="estado-${disco.estado.toLowerCase()}">${disco.estado}</strong>
                    </div>
                </div>
            </div>
            <div class="disco-actions">
                <button class="btn-eliminar" onclick="eliminarDisco(${disco.id})">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = html;
}

async function handleAgregarDisco(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const datosDisco = Object.fromEntries(formData);
    
    // Convertir números
    datosDisco.capacidad_gb = parseInt(datosDisco.capacidad_gb);
    datosDisco.tiempo_uso_meses = parseInt(datosDisco.tiempo_uso_meses);
    datosDisco.porcentaje_desgaste = parseInt(datosDisco.porcentaje_desgaste);
    datosDisco.horas_encendido = datosDisco.horas_encendido ? parseInt(datosDisco.horas_encendido) : datosDisco.tiempo_uso_meses * 720;
    datosDisco.ciclos_escritura = datosDisco.ciclos_escritura ? parseInt(datosDisco.ciclos_escritura) : datosDisco.tiempo_uso_meses * 500;
    datosDisco.temperatura_promedio = datosDisco.temperatura_promedio ? parseInt(datosDisco.temperatura_promedio) : 45;
    datosDisco.bad_sectors = parseInt(datosDisco.bad_sectors);
    
    const resultadoDiv = document.getElementById('resultado-agregar');
    resultadoDiv.innerHTML = '<div class="cargando">Agregando disco...</div>';
    
    try {
        const response = await fetch(`${API_BASE_URL}/discos/agregar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(datosDisco)
        });
        
        const data = await response.json();
        
        if (data.success) {
            resultadoDiv.innerHTML = `<div class="exito">✅ ${data.mensaje} (ID: ${data.id})</div>`;
            event.target.reset();
            cargarListaDiscos(); // Recargar lista
            showNotification('Disco agregado exitosamente', 'success');
        } else {
            resultadoDiv.innerHTML = `<div class="error">❌ ${data.error}</div>`;
            showNotification(`Error: ${data.error}`, 'error');
        }
    } catch (error) {
        resultadoDiv.innerHTML = `<div class="error">❌ Error: ${error.message}</div>`;
        showNotification(`Error: ${error.message}`, 'error');
    }
}

async function eliminarDisco(id) {
    if (!confirm(`¿Estás seguro de que quieres eliminar el disco ID: ${id}?`)) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE_URL}/discos/eliminar/${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.mensaje, 'success');
            cargarListaDiscos(); // Recargar lista
        } else {
            showNotification(`Error: ${data.error}`, 'error');
        }
    } catch (error) {
        showNotification(`Error: ${error.message}`, 'error');
    }
}

// Machine Learning - Entrenar Modelo
async function entrenarModelo() {
    const resultDiv = document.getElementById('entrenamiento-result');
    resultDiv.innerHTML = '<div class="loading"></div>';
    resultDiv.classList.add('show');
    
    try {
        const response = await fetch(`${API_BASE_URL}/ml/entrenar`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            const result = data.result;
            let featureHTML = '';
            
            if (result.caracteristicas_importantes && typeof result.caracteristicas_importantes === 'object') {
                featureHTML = `
                <div class="feature-importance">
                    <h5>Importancia de Características:</h5>
                    ${Object.entries(result.caracteristicas_importantes)
                        .map(([feature, importance]) => `
                            <div class="feature-item">
                                <span>${feature}:</span>
                                <span>${importance}</span>
                            </div>
                        `).join('')}
                </div>
                `;
            }
            
            resultDiv.innerHTML = `
                <h4>Modelo Entrenado Exitosamente:</h4>
                <div class="model-info">
                    <p><strong>Algoritmo:</strong> ${result.modelo}</p>
                    <p><strong>Precisión (R²):</strong> ${result.r2_score}</p>
                    <p><strong>Error Absoluto Medio:</strong> ${result.mae}%</p>
                    <p><strong>Muestras Entrenamiento:</strong> ${result.muestras_entrenamiento}</p>
                    <p><strong>Muestras Prueba:</strong> ${result.muestras_prueba}</p>
                </div>
                ${featureHTML}
            `;
            showNotification('Modelo entrenado exitosamente', 'success');
        } else {
            throw new Error(data.error || 'Error desconocido del servidor');
        }
    } catch (error) {
        console.error('Error entrenando modelo:', error);
        resultDiv.innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Error: ${error.message}</p>
            </div>
        `;
        showNotification('Error entrenando modelo', 'error');
    }
}

// Machine Learning - Analizar Tendencias
async function analizarTendencias() {
    const resultDiv = document.getElementById('tendencias-result');
    resultDiv.innerHTML = '<div class="loading"></div>';
    resultDiv.classList.add('show');
    
    try {
        const response = await fetch(`${API_BASE_URL}/ml/analisis`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (jsonError) {
            console.error('Error parseando JSON:', jsonError);
            throw new Error('Error en el formato de respuesta del servidor');
        }
        
        if (data.success) {
            const analisis = data.analisis;
            
            let html = '<h4>Análisis de Tendencias de Desgaste</h4>';
            
            // Estadísticas generales
            if (analisis.estadisticas_generales) {
                const stats = analisis.estadisticas_generales;
                html += `
                    <div class="stats-grid">
                        <div class="stat">
                            <strong>Total Discos:</strong> ${stats.total_discos || 0}
                        </div>
                        <div class="stat">
                            <strong>Desgaste Promedio:</strong> ${stats.desgaste_promedio || 0}%
                        </div>
                        <div class="stat">
                            <strong>Discos en Riesgo:</strong> ${stats.discos_en_riesgo || 0}
                        </div>
                    </div>
                `;
            }
            
            // Desgaste por tipo
            if (analisis.desgaste_por_tipo && Object.keys(analisis.desgaste_por_tipo).length > 0) {
                html += `
                    <div class="desgaste-por-tipo">
                        <h5>Desgaste por Tipo de Disco:</h5>
                        ${Object.entries(analisis.desgaste_por_tipo).map(([tipo, metrics]) => `
                            <div class="tipo-metrics">
                                <strong>${tipo}:</strong>
                                Promedio: ${metrics.mean?.toFixed(1) || 'N/A'}%, 
                                Desviación: ${metrics.std?.toFixed(1) || 'N/A'}, 
                                Muestras: ${metrics.count || 0}
                            </div>
                        `).join('')}
                    </div>
                `;
            } else {
                html += '<p>No hay datos de desgaste por tipo disponibles</p>';
            }
            
            // Correlaciones
            if (analisis.correlaciones && Object.keys(analisis.correlaciones).length > 0) {
                html += `
                    <div class="correlations">
                        <h5>Correlaciones con Desgaste:</h5>
                        ${Object.entries(analisis.correlaciones)
                            .map(([metric, corr]) => {
                                let correlationClass = 'low-correlation';
                                const absCorr = Math.abs(corr);
                                if (absCorr > 0.5) correlationClass = 'high-correlation';
                                else if (absCorr > 0.3) correlationClass = 'medium-correlation';
                                
                                return `
                                    <div class="correlation-item">
                                        <span>${metric}:</span>
                                        <span class="${correlationClass}">${corr}</span>
                                    </div>
                                `;
                            }).join('')}
                    </div>
                `;
            }
            
            // Discos críticos
            if (analisis.discos_criticos && analisis.discos_criticos.length > 0) {
                html += `
                    <div class="discos-criticos">
                        <h5>Discos en Estado Crítico (≥70% desgaste):</h5>
                        ${analisis.discos_criticos.map(disco => `
                            <div class="disco-critico">
                                <strong>${disco.tipo} ${disco.marca}</strong> - ${disco.modelo}: 
                                <span class="estado-critico">${disco.porcentaje_desgaste}% desgaste</span>
                            </div>
                        `).join('')}
                    </div>
                `;
            } else {
                html += '<p>No hay discos en estado crítico</p>';
            }
            
            resultDiv.innerHTML = html;
            showNotification('Análisis de tendencias completado', 'success');
            
        } else {
            throw new Error(data.error || 'Error desconocido del servidor');
        }
        
    } catch (error) {
        console.error('Error en análisis de tendencias:', error);
        
        let errorMessage = error.message;
        if (error.message.includes('Failed to fetch')) {
            errorMessage = 'No se puede conectar al servidor. Verifica que el servidor Python esté ejecutándose en puerto 5000.';
        } else if (error.message.includes('JSON')) {
            errorMessage = 'Error en el formato de respuesta del servidor. El servidor podría estar devolviendo HTML en lugar de JSON.';
        }
        
        resultDiv.innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <h4>Error en el análisis</h4>
                <p>${errorMessage}</p>
                <div class="debug-info">
                    <p><strong>Para solucionar:</strong></p>
                    <ul>
                        <li>Verifica que el servidor Python esté ejecutándose</li>
                        <li>Revisa la consola del servidor para errores</li>
                        <li>Prueba acceder directamente a: ${API_BASE_URL}/ml/analisis</li>
                    </ul>
                </div>
            </div>
        `;
        showNotification('Error en análisis de tendencias', 'error');
    }
}

// Obtener métricas por tipo
async function obtenerMetricas() {
    const resultDiv = document.getElementById('metricas-result');
    resultDiv.innerHTML = '<div class="loading"></div>';
    resultDiv.classList.add('show');
    
    try {
        const response = await fetch(`${API_BASE_URL}/metricas`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            const metricas = data.metricas;
            resultDiv.innerHTML = `
                <h4>Métricas por Tipo de Disco</h4>
                ${Object.entries(metricas).map(([tipo, specs]) => `
                    <div class="metricas-tipo">
                        <h5>${tipo}:</h5>
                        <div class="specs-grid">
                            <div class="spec"><strong>Vida Útil:</strong> ${specs.vida_util_meses} meses</div>
                            <div class="spec"><strong>Ciclos Escritura Máx:</strong> ${specs.ciclos_escritura_max?.toLocaleString() || 'N/A'}</div>
                            <div class="spec"><strong>Temperatura Máx:</strong> ${specs.temperatura_max}°C</div>
                            <div class="spec"><strong>Factor Desgaste:</strong> ${specs.factor_desgaste}x</div>
                        </div>
                    </div>
                `).join('')}
            `;
            showNotification('Métricas cargadas exitosamente', 'success');
        } else {
            throw new Error(data.error || 'Error desconocido del servidor');
        }
    } catch (error) {
        console.error('Error obteniendo métricas:', error);
        resultDiv.innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Error: ${error.message}</p>
            </div>
        `;
        showNotification('Error cargando métricas', 'error');
    }
}

// Estilos adicionales para resultados
const additionalStyles = `
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin: 1rem 0;
}

.stat {
    background: rgba(255, 255, 255, 0.05);
    padding: 0.75rem;
    border-radius: 8px;
    text-align: center;
}

.prediction-summary {
    text-align: center;
}

.prediction-main {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    margin: 1.5rem 0;
    flex-wrap: wrap;
}

.prediction-value {
    text-align: center;
}

.percentage {
    font-size: 3rem;
    font-weight: bold;
    display: block;
}

.label {
    font-size: 0.9rem;
    opacity: 0.8;
}

.prediction-details {
    text-align: left;
}

.detail-item {
    margin: 0.5rem 0;
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.recommendation {
    background: rgba(255, 255, 255, 0.05);
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
    border-left: 4px solid var(--primary-color);
}

.model-info {
    margin: 1rem 0;
}

.feature-importance {
    margin-top: 1rem;
}

.feature-item {
    display: flex;
    justify-content: space-between;
    padding: 0.25rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.correlations {
    margin-top: 1rem;
}

.correlation-item {
    display: flex;
    justify-content: space-between;
    padding: 0.25rem 0;
}

.high-correlation {
    color: #ef4444;
    font-weight: bold;
}

.medium-correlation {
    color: #f59e0b;
}

.low-correlation {
    color: #10b981;
}

.metricas-tipo {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.metricas-tipo:last-child {
    border-bottom: none;
}

.specs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.spec {
    background: rgba(255, 255, 255, 0.05);
    padding: 0.5rem;
    border-radius: 6px;
    font-size: 0.9rem;
}

.error-message {
    text-align: center;
    color: #ef4444;
}

.error-message i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.desgaste-por-tipo {
    margin: 1rem 0;
}

.tipo-metrics {
    background: rgba(255, 255, 255, 0.05);
    padding: 0.5rem;
    margin: 0.25rem 0;
    border-radius: 6px;
    font-size: 0.9rem;
}

.discos-criticos {
    margin: 1rem 0;
}

.disco-critico {
    background: rgba(239, 68, 68, 0.1);
    padding: 0.5rem;
    margin: 0.25rem 0;
    border-radius: 6px;
    border-left: 4px solid #ef4444;
    font-size: 0.9rem;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.notification {
    animation: slideInRight 0.3s ease-out;
}

.notification button {
    background: none;
    border: none;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    margin-left: auto;
}
`;

// Añadir estilos adicionales al documento
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);