<?php 
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<?php include 'includes/header.php'; ?>

<!-- Efecto de partículas de fondo -->
<div id="particles" class="particles"></div>

<div class="container">
    <div style="text-align: center; margin: 2rem 0;">
        <h1 style="color: var(--accent-color);">Dashboard Completo de Análisis</h1>
        <p style="color: var(--text-secondary);">Visualizaciones avanzadas y análisis detallados</p>
    </div>

    <!-- Gráfico de Área -->
    <section style="margin-bottom: 3rem;">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-chart-area"></i>
                    Área sobre la Curva - Evolución del Impacto
                </h2>
                <button class="btn btn-primary" onclick="openModalFromChart('area')">
                    <i class="fas fa-expand"></i>
                    Ver en Grande
                </button>
            </div>
            <div id="area-chart-container">
                <div class="loading-container">
                    <div class="loading"></div>
                    <p>Cargando gráfico de área...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Gráficos en Grid -->
    <div class="grid grid-cols-2" style="margin-bottom: 3rem; gap: 2rem;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar"></i>
                    Barras Apiladas por Duración
                </h3>
                <button class="btn btn-primary" onclick="openModalFromChart('stacked')">
                    <i class="fas fa-expand"></i>
                    Ampliar
                </button>
            </div>
            <div id="stacked-chart-container">
                <div class="loading-container">
                    <div class="loading"></div>
                    <p>Cargando barras apiladas...</p>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie"></i>
                    Distribución de Eficiencia
                </h3>
                <button class="btn btn-primary" onclick="openModalFromChart('pie')">
                    <i class="fas fa-expand"></i>
                    Ampliar
                </button>
            </div>
            <div id="pie-chart-container">
                <div class="loading-container">
                    <div class="loading"></div>
                    <p>Cargando gráfico de pastel...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Insights de Machine Learning - VERSIÓN CORREGIDA -->
    <section style="margin-bottom: 3rem;">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-robot"></i>
                    Insights de Machine Learning
                </h2>
                <span class="badge badge-info">AI Analysis</span>
            </div>
            <div id="ml-insights-container">
                <div class="loading-container">
                    <div class="loading"></div>
                    <p>Generando insights de ML...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Gestión de Datos -->
    <section style="margin-bottom: 3rem;">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-database"></i>
                    Gestión de Datos
                </h2>
                <span class="badge badge-success" id="data-count-badge">Cargando...</span>
            </div>
            <div class="grid grid-cols-2" style="gap: 2rem;">
                <div>
                    <h4 style="color: var(--accent-color); margin-bottom: 1rem;">
                        <i class="fas fa-plus-circle"></i>
                        Agregar Nuevo Registro
                    </h4>
                    <form id="data-form">
                        <div class="form-group">
                            <label class="form-label" for="new_vitamina">Vitamina</label>
                            <input type="text" class="form-control" id="new_vitamina" required 
                                   placeholder="Ej: Vitamina B12">
                        </div>
                        
                        <div class="grid grid-cols-2" style="gap: 1rem;">
                            <div class="form-group">
                                <label class="form-label" for="new_dosis">Dosis Diaria (mg)</label>
                                <input type="number" class="form-control" id="new_dosis" 
                                       step="0.1" min="0" required placeholder="50.0">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="new_duracion">Duración (semanas)</label>
                                <input type="number" class="form-control" id="new_duracion" 
                                       min="1" required placeholder="4">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2" style="gap: 1rem;">
                            <div class="form-group">
                                <label class="form-label" for="new_inicio">Glóbulos Rojos Inicio</label>
                                <input type="number" class="form-control" id="new_inicio" 
                                       step="0.01" min="0" required placeholder="4.50">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="new_fin">Glóbulos Rojos Fin</label>
                                <input type="number" class="form-control" id="new_fin" 
                                       step="0.01" min="0" required placeholder="5.20">
                            </div>
                        </div>

                        <div class="grid grid-cols-2" style="gap: 1rem;">
                            <div class="form-group">
                                <label class="form-label" for="new_edad">Edad (opcional)</label>
                                <input type="number" class="form-control" id="new_edad" 
                                       min="0" max="120" placeholder="45">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="new_sexo">Sexo (opcional)</label>
                                <select class="form-control" id="new_sexo">
                                    <option value="">Seleccionar...</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 1rem;">
                            <i class="fas fa-save"></i>
                            Guardar Registro
                        </button>
                    </form>
                </div>
                
                <div>
                    <h4 style="color: var(--accent-color); margin-bottom: 1rem;">
                        <i class="fas fa-list"></i>
                        Datos Actuales
                    </h4>
                    <div id="data-list" style="max-height: 400px; overflow-y: auto; background: var(--bg-secondary); border-radius: 8px; padding: 1rem;">
                        <div class="loading-container">
                            <div class="loading"></div>
                            <p>Cargando datos...</p>
                        </div>
                    </div>
                    <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                        <button class="btn btn-outline" onclick="loadDataList()" style="flex: 1;">
                            <i class="fas fa-redo"></i>
                            Actualizar
                        </button>
                        <button class="btn btn-outline" onclick="exportData()" style="flex: 1;">
                            <i class="fas fa-download"></i>
                            Exportar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Estadísticas Detalladas -->
    <section style="margin-bottom: 3rem;">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-chart-line"></i>
                    Estadísticas Detalladas
                </h2>
            </div>
            <div id="detailed-stats">
                <div class="loading-container">
                    <div class="loading"></div>
                    <p>Cargando estadísticas detalladas...</p>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
// Función para cargar todos los gráficos del dashboard
async function loadDashboardCharts() {
    try {
        // Cargar gráfico de área
        await loadChart('area', 'area-chart-container');
        
        // Cargar gráfico de barras apiladas
        await loadChart('stacked', 'stacked-chart-container');
        
        // Cargar gráfico de pastel
        await loadChart('pie', 'pie-chart-container');
        
        // Cargar insights de ML
        await loadMLInsights();
        
    } catch (error) {
        console.error('Error loading dashboard charts:', error);
    }
}

// Función para cargar un gráfico específico
async function loadChart(chartType, containerId) {
    try {
        const response = await fetch(`http://localhost:5000/api/charts/${chartType}`);
        const data = await response.json();
        
        const container = document.getElementById(containerId);
        
        if (data.success && data.chart) {
            container.innerHTML = `
                <div class="chart-container">
                    <div class="chart-image">
                        <img src="data:image/png;base64,${data.chart}" 
                             alt="${chartType}" 
                             style="cursor: pointer; width: 100%;"
                             onclick="openModal('data:image/png;base64,${data.chart}')">
                    </div>
                </div>
            `;
        } else {
            container.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    No se pudo generar el gráfico: ${data.error || 'Error desconocido'}
                </div>
            `;
        }
    } catch (error) {
        console.error(`Error loading ${chartType} chart:`, error);
        const container = document.getElementById(containerId);
        container.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                Error al cargar el gráfico: ${error.message}
            </div>
        `;
    }
}

// Función para cargar insights de ML - VERSIÓN CORREGIDA
async function loadMLInsights() {
    try {
        const container = document.getElementById('ml-insights-container');
        
        // Primero intentar cargar el gráfico de ML
        const chartResponse = await fetch('http://localhost:5000/api/charts/ml_insights');
        const chartData = await chartResponse.json();
        
        let mlContent = '';
        
        if (chartData.success && chartData.chart) {
            mlContent += `
                <div class="chart-image" style="margin-bottom: 2rem;">
                    <img src="data:image/png;base64,${chartData.chart}" 
                         alt="ML Insights" 
                         style="cursor: pointer; width: 100%; max-width: 600px;"
                         onclick="openModal('data:image/png;base64,${chartData.chart}')">
                </div>
            `;
        } else {
            mlContent += `
                <div class="alert alert-warning" style="margin-bottom: 2rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                    No se pudo generar el gráfico de ML: ${chartData.error || 'Insights no disponibles'}
                </div>
            `;
        }
        
        // Luego cargar información del modelo
        try {
            const modelResponse = await fetch('http://localhost:5000/api/model/info');
            const modelData = await modelResponse.json();
            
            if (modelData.success) {
                const modelInfo = modelData.model_info;
                
                mlContent += `
                    <div style="background: var(--bg-secondary); padding: 1.5rem; border-radius: 12px; border-left: 4px solid var(--info);">
                        <h4 style="color: var(--info); margin-bottom: 1rem;">
                            <i class="fas fa-brain"></i>
                            Información del Modelo
                        </h4>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                            <div style="text-align: center;">
                                <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Estado</div>
                                <span class="badge ${modelInfo.is_trained ? 'badge-success' : 'badge-warning'}" style="font-size: 0.9rem;">
                                    ${modelInfo.is_trained ? 'Entrenado' : 'No Entrenado'}
                                </span>
                            </div>
                            
                            ${modelInfo.metrics?.r2_score ? `
                                <div style="text-align: center;">
                                    <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Precisión (R²)</div>
                                    <div style="font-size: 1.2rem; font-weight: bold; color: ${modelInfo.metrics.r2_score > 0.7 ? 'var(--success)' : modelInfo.metrics.r2_score > 0.5 ? 'var(--warning)' : 'var(--danger)'};">
                                        ${modelInfo.metrics.r2_score.toFixed(3)}
                                    </div>
                                </div>
                            ` : ''}
                            
                            ${modelInfo.metrics?.n_samples ? `
                                <div style="text-align: center;">
                                    <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Muestras</div>
                                    <div style="font-size: 1.2rem; font-weight: bold; color: var(--primary);">
                                        ${modelInfo.metrics.n_samples}
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                        
                        ${modelInfo.feature_importance && Object.keys(modelInfo.feature_importance).length > 0 ? `
                            <div>
                                <h5 style="color: var(--text-primary); margin-bottom: 1rem;">Importancia de Características</h5>
                                <div style="display: grid; gap: 0.5rem;">
                                    ${Object.entries(modelInfo.feature_importance).map(([feature, importance]) => `
                                        <div style="display: flex; justify-content: between; align-items: center; padding: 0.5rem; background: var(--bg-card); border-radius: 6px;">
                                            <span style="font-weight: 500; color: var(--text-primary);">${feature}</span>
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <div style="width: 100px; height: 8px; background: var(--border); border-radius: 4px; overflow: hidden;">
                                                    <div style="height: 100%; background: var(--primary); width: ${importance * 100}%;"></div>
                                                </div>
                                                <span style="font-size: 0.8rem; color: var(--text-secondary); min-width: 40px; text-align: right;">
                                                    ${(importance * 100).toFixed(1)}%
                                                </span>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        ` : `
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                El modelo necesita más datos para mostrar la importancia de características.
                            </div>
                        `}
                    </div>
                `;
            }
        } catch (modelError) {
            console.error('Error loading model info:', modelError);
            mlContent += `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    No se pudo cargar la información del modelo: ${modelError.message}
                </div>
            `;
        }
        
        container.innerHTML = mlContent;
        
    } catch (error) {
        console.error('Error loading ML insights:', error);
        const container = document.getElementById('ml-insights-container');
        container.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                Error al cargar insights de ML: ${error.message}
            </div>
        `;
    }
}

// Función para abrir modal desde gráfico específico
function openModalFromChart(chartType) {
    fetch(`http://localhost:5000/api/charts/${chartType}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                openModal(`data:image/png;base64,${data.chart}`);
            } else {
                alert('Error al cargar el gráfico: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error loading chart:', error);
            alert('Error al cargar el gráfico');
        });
}

// Cargar lista de datos
async function loadDataList() {
    try {
        const response = await fetch('http://localhost:5000/api/data');
        const data = await response.json();
        
        if (data.success) {
            const container = document.getElementById('data-list');
            const countBadge = document.getElementById('data-count-badge');
            
            if (countBadge) {
                countBadge.textContent = `${data.count} registros`;
            }
            
            if (data.data.length === 0) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                        <i class="fas fa-database" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <p>No hay datos registrados</p>
                        <p style="font-size: 0.9rem; margin-top: 0.5rem;">Agrega el primer registro usando el formulario</p>
                    </div>
                `;
                return;
            }
            
            let html = '<div style="display: grid; gap: 0.75rem;">';
            data.data.forEach((item, index) => {
                const incremento = (item.globulos_rojos_fin - item.globulos_rojos_inicio).toFixed(2);
                const eficiencia = (incremento / (item.dosis_diaria * item.duracion_semanas)).toFixed(4);
                
                html += `
                    <div style="background: var(--bg-card); padding: 1rem; border-radius: 8px; border-left: 4px solid var(--primary);">
                        <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 0.5rem;">
                            <div style="flex: 1;">
                                <div style="font-weight: bold; color: var(--primary); font-size: 1.1rem;">${item.vitamina}</div>
                                <div style="font-size: 0.9rem; color: var(--text-secondary);">
                                    ID: ${item.id} | Dosis: ${item.dosis_diaria}mg | Duración: ${item.duracion_semanas} semanas
                                </div>
                            </div>
                            <button class="btn btn-danger btn-sm" onclick="deleteData(${item.id})" style="padding: 0.25rem 0.5rem;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.85rem;">
                            <div>
                                <strong>Inicio:</strong> ${item.globulos_rojos_inicio}M/mL<br>
                                <strong>Fin:</strong> ${item.globulos_rojos_fin}M/mL
                            </div>
                            <div>
                                <strong>Incremento:</strong> +${incremento}M/mL<br>
                                <strong>Eficiencia:</strong> ${eficiencia}
                            </div>
                        </div>
                        ${item.edad_paciente || item.sexo ? `
                            <div style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--border); font-size: 0.8rem; color: var(--text-muted);">
                                ${item.edad_paciente ? `Edad: ${item.edad_paciente} años` : ''}
                                ${item.edad_paciente && item.sexo ? ' • ' : ''}
                                ${item.sexo ? `Sexo: ${item.sexo === 'M' ? 'Masculino' : 'Femenino'}` : ''}
                            </div>
                        ` : ''}
                    </div>
                `;
            });
            html += '</div>';
            container.innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading data list:', error);
        document.getElementById('data-list').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                Error al cargar datos: ${error.message}
            </div>
        `;
    }
}

// Eliminar datos
async function deleteData(dataId) {
    if (!confirm('¿Está seguro de que desea eliminar este registro?')) {
        return;
    }
    
    try {
        const response = await fetch(`http://localhost:5000/api/data/${dataId}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Registro eliminado exitosamente');
            loadDataList();
            // Recargar gráficos y ML insights
            loadDashboardCharts();
            loadDetailedStats();
        } else {
            alert('Error al eliminar registro: ' + data.error);
        }
    } catch (error) {
        console.error('Error deleting data:', error);
        alert('Error al eliminar registro');
    }
}

// Exportar datos
function exportData() {
    fetch('http://localhost:5000/api/data')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const csvContent = convertToCSV(data.data);
                downloadCSV(csvContent, 'datos_vitaminas.csv');
            } else {
                alert('Error al exportar datos: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error exporting data:', error);
            alert('Error al exportar datos');
        });
}

function convertToCSV(data) {
    const headers = ['ID', 'Vitamina', 'Dosis Diaria', 'Duración Semanas', 'Glóbulos Inicio', 'Glóbulos Fin', 'Edad', 'Sexo'];
    const csvRows = [headers.join(',')];
    
    data.forEach(item => {
        const row = [
            item.id,
            `"${item.vitamina}"`,
            item.dosis_diaria,
            item.duracion_semanas,
            item.globulos_rojos_inicio,
            item.globulos_rojos_fin,
            item.edad_paciente || '',
            item.sexo || ''
        ];
        csvRows.push(row.join(','));
    });
    
    return csvRows.join('\n');
}

function downloadCSV(csvContent, filename) {
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Cargar estadísticas detalladas
async function loadDetailedStats() {
    try {
        const response = await fetch('http://localhost:5000/api/stats');
        const data = await response.json();
        
        if (data.success) {
            const stats = data.statistics;
            const container = document.getElementById('detailed-stats');
            
            container.innerHTML = `
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">${stats.total_registros}</div>
                        <div class="stat-label">Total Registros</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${stats.vitaminas_unicas}</div>
                        <div class="stat-label">Vitaminas Únicas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${stats.incremento_promedio.toFixed(2)}</div>
                        <div class="stat-label">Incremento Promedio (M/mL)</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${stats.dosis_promedio.toFixed(2)}</div>
                        <div class="stat-label">Dosis Promedio (mg)</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${stats.duracion_promedio.toFixed(1)}</div>
                        <div class="stat-label">Duración Promedio (semanas)</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${stats.eficiencia_promedio.toFixed(4)}</div>
                        <div class="stat-label">Eficiencia Promedio</div>
                    </div>
                </div>
                
                ${data.statistics_por_vitamina && Object.keys(data.statistics_por_vitamina).length > 0 ? `
                    <div style="margin-top: 2rem;">
                        <h4 style="margin-bottom: 1rem; color: var(--text-primary);">Estadísticas por Vitamina</h4>
                        <div style="display: grid; gap: 1rem;">
                            ${Object.entries(data.statistics_por_vitamina).map(([vitamina, stats]) => `
                                <div style="background: var(--bg-secondary); padding: 1rem; border-radius: 8px; border-left: 4px solid var(--primary);">
                                    <div style="font-weight: bold; color: var(--primary); margin-bottom: 0.5rem;">${vitamina}</div>
                                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; font-size: 0.9rem;">
                                        <div>
                                            <strong>Registros:</strong> ${stats.total_registros}
                                        </div>
                                        <div>
                                            <strong>Incremento:</strong> ${stats.incremento_promedio.toFixed(2)}M/mL
                                        </div>
                                        <div>
                                            <strong>Eficiencia:</strong> ${stats.eficiencia_promedio.toFixed(4)}
                                        </div>
                                        <div>
                                            <strong>Dosis:</strong> ${stats.dosis_promedio.toFixed(2)}mg
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
            `;
        }
    } catch (error) {
        console.error('Error loading detailed stats:', error);
        document.getElementById('detailed-stats').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                Error al cargar estadísticas: ${error.message}
            </div>
        `;
    }
}

// Configurar formulario de datos
document.addEventListener('DOMContentLoaded', function() {
    const dataForm = document.getElementById('data-form');
    if (dataForm) {
        dataForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = {
                vitamina: document.getElementById('new_vitamina').value,
                dosis_diaria: parseFloat(document.getElementById('new_dosis').value),
                duracion_semanas: parseInt(document.getElementById('new_duracion').value),
                globulos_rojos_inicio: parseFloat(document.getElementById('new_inicio').value),
                globulos_rojos_fin: parseFloat(document.getElementById('new_fin').value),
                edad_paciente: document.getElementById('new_edad').value ? 
                    parseInt(document.getElementById('new_edad').value) : null,
                sexo: document.getElementById('new_sexo').value || null
            };
            
            try {
                const response = await fetch('http://localhost:5000/api/data', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Registro agregado exitosamente!');
                    dataForm.reset();
                    loadDataList();
                    // Recargar gráficos y ML insights
                    loadDashboardCharts();
                    loadDetailedStats();
                } else {
                    alert('Error al agregar registro: ' + data.error);
                }
            } catch (error) {
                console.error('Error adding data:', error);
                alert('Error al agregar registro: ' + error.message);
            }
        });
    }
    
    // Cargar datos iniciales
    loadDashboardCharts();
    loadDataList();
    loadDetailedStats();
});
</script>

<script src="js/modern-ui.js"></script>

<?php include 'includes/footer.php'; ?>