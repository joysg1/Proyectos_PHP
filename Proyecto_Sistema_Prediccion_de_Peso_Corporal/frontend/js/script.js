// HealthPredict Pro - Enhanced JavaScript
class HealthPredictApp {
    constructor() {
        this.currentChart = null;
        this.registros = [];
        this.init();
    }

    init() {
        this.initializeEventListeners();
        this.loadInitialData();
        this.checkBackendConnection();
    }

    initializeEventListeners() {
        // Inicializar después de que el DOM esté completamente cargado
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.setupEventListeners();
            });
        } else {
            this.setupEventListeners();
        }
    }

    setupEventListeners() {
        // Prediction form
        const predictionForm = document.querySelector('.prediction-form');
        if (predictionForm) {
            predictionForm.addEventListener('submit', (e) => this.handlePrediction(e));
        }

        // Add record form
        const addRecordForm = document.querySelector('.compact-form');
        if (addRecordForm) {
            addRecordForm.addEventListener('submit', (e) => this.handleAddRecord(e));
        }

        // Chart buttons
        document.querySelectorAll('.grafico-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const tipo = e.currentTarget.dataset.tipo;
                this.cargarGrafico(tipo);
            });
        });

        // Initialize tooltips
        this.initTooltips();
        
        // Initialize animations
        this.initAnimations();
    }

    async loadInitialData() {
        try {
            await this.loadRegistros();
            await this.loadEstadisticas();
        } catch (error) {
            this.showNotification('Error cargando datos iniciales', 'error');
        }
    }

    async loadRegistros() {
        try {
            const response = await fetch('api_handler.php?action=get_registros');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.registros) {
                this.registros = data.registros;
                this.renderRegistrosTable();
            } else {
                throw new Error('No se pudieron cargar los registros');
            }
        } catch (error) {
            console.error('Error loading registros:', error);
            this.showNotification('Error cargando registros', 'error');
        }
    }

    async loadEstadisticas() {
        try {
            const response = await fetch('api_handler.php?action=get_estadisticas');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (!data.error) {
                this.updateStatsCards(data);
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    renderRegistrosTable() {
        const tbody = document.querySelector('.data-table tbody');
        if (!tbody) return;

        if (this.registros.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-muted">
                        <i class="fas fa-database"></i>
                        No hay registros disponibles
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = this.registros.map(registro => `
            <tr data-id="${registro.id}">
                <td class="font-mono">${registro.id}</td>
                <td>${this.formatDate(registro.fecha)}</td>
                <td class="text-warning font-semibold">${registro.calorias} kcal</td>
                <td class="text-info font-semibold">${registro.peso} kg</td>
                <td>${registro.edad} años</td>
                <td>${registro.altura} cm</td>
                <td>
                    <span class="badge badge-${this.getActivityClass(registro.actividad)}">
                        <i class="fas fa-${this.getActivityIcon(registro.actividad)}"></i>
                        ${this.capitalize(registro.actividad)}
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-icon btn-edit" 
                                onclick="app.editRegistro(${registro.id})" 
                                data-tooltip="Editar registro">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-icon btn-delete" 
                                onclick="app.deleteRegistro(${registro.id})" 
                                data-tooltip="Eliminar registro">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        this.initTooltips();
    }

    async handlePrediction(e) {
        e.preventDefault();
        const form = e.target;
        const button = form.querySelector('button[type="submit"]');
        
        try {
            this.setButtonLoading(button, true);
            
            // Enviar el formulario normalmente - PHP lo procesará
            form.submit();
            
        } catch (error) {
            console.error('Error en predicción:', error);
            this.showNotification('Error realizando predicción', 'error');
            this.setButtonLoading(button, false);
        }
    }

    async handleAddRecord(e) {
        e.preventDefault();
        const form = e.target;
        const button = form.querySelector('button[type="submit"]');
        
        try {
            this.setButtonLoading(button, true);
            
            // Enviar el formulario normalmente - PHP lo procesará
            form.submit();
            
        } catch (error) {
            console.error('Error agregando registro:', error);
            this.showNotification('Error agregando registro', 'error');
            this.setButtonLoading(button, false);
        }
    }

    async editRegistro(id) {
        console.log('Editando registro:', id);
        const registro = this.registros.find(r => r.id === id);
        if (!registro) {
            this.showNotification('Registro no encontrado', 'error');
            return;
        }

        // Create edit modal
        const modal = this.createEditModal(registro);
        document.body.appendChild(modal);
        this.showModal(modal);
    }

    createEditModal(registro) {
        const modal = document.createElement('div');
        modal.className = 'modal-enhanced';
        modal.style.cssText = `
            display: block;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        `;
        
        modal.innerHTML = `
            <div class="modal-content-enhanced" style="max-width: 500px; margin: 5% auto; background: white; border-radius: 10px; padding: 0; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
                <div class="modal-header" style="padding: 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; color: #1e293b;">Editar Registro #${registro.id}</h3>
                    <span class="close-enhanced" onclick="app.closeModal(this)" style="cursor: pointer; font-size: 24px; color: #64748b;">&times;</span>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <form id="editForm" class="compact-form">
                        <input type="hidden" name="accion" value="actualizar_registro">
                        <input type="hidden" name="id" value="${registro.id}">
                        
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Fecha</label>
                            <input type="date" name="fecha" class="form-control" value="${registro.fecha}" required 
                                   style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 5px;">
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 15px 0;">
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Calorías</label>
                                <input type="number" name="calorias_registro" class="form-control" 
                                       value="${registro.calorias}" required 
                                       style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 5px;">
                            </div>
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Peso (kg)</label>
                                <input type="number" step="0.1" name="peso" class="form-control" 
                                       value="${registro.peso}" required 
                                       style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 5px;">
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 15px 0;">
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Edad</label>
                                <input type="number" name="edad_registro" class="form-control" 
                                       value="${registro.edad}" required 
                                       style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 5px;">
                            </div>
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Altura (cm)</label>
                                <input type="number" name="altura_registro" class="form-control" 
                                       value="${registro.altura}" required 
                                       style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 5px;">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Actividad</label>
                            <select name="actividad_registro" class="form-control" required 
                                    style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 5px;">
                                <option value="baja" ${registro.actividad === 'baja' ? 'selected' : ''}>Baja</option>
                                <option value="moderada" ${registro.actividad === 'moderada' ? 'selected' : ''}>Moderada</option>
                                <option value="alta" ${registro.actividad === 'alta' ? 'selected' : ''}>Alta</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-actions" style="padding: 20px; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn btn-outline" onclick="app.closeModal(this)" 
                            style="padding: 10px 20px; border: 1px solid #d1d5db; background: white; border-radius: 5px; cursor: pointer;">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="app.submitEditForm()"
                            style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        `;
        
        return modal;
    }

    async submitEditForm() {
        const form = document.getElementById('editForm');
        if (!form) {
            this.showNotification('Formulario no encontrado', 'error');
            return;
        }

        const button = form.closest('.modal-content-enhanced').querySelector('.btn-primary');
        
        try {
            this.setButtonLoading(button, true);
            
            const formData = new FormData(form);
            
            // Enviar via AJAX para evitar recargar la página
            const response = await fetch('', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.error) {
                throw new Error(result.error);
            }
            
            // Cerrar el modal
            this.closeModal(button);
            
            // Recargar los datos
            await this.loadRegistros();
            await this.loadEstadisticas();
            
            this.showNotification('Registro actualizado exitosamente', 'success');
            
        } catch (error) {
            console.error('Error actualizando registro:', error);
            this.showNotification('Error actualizando registro: ' + error.message, 'error');
            this.setButtonLoading(button, false);
        }
    }

    async deleteRegistro(id) {
        if (!confirm('¿Estás seguro de que quieres eliminar este registro?')) {
            return;
        }

        try {
            // Crear formulario dinámico para eliminar
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';
            
            const accionInput = document.createElement('input');
            accionInput.name = 'accion';
            accionInput.value = 'eliminar_registro';
            
            const idInput = document.createElement('input');
            idInput.name = 'id';
            idInput.value = id;
            
            form.appendChild(accionInput);
            form.appendChild(idInput);
            document.body.appendChild(form);
            
            // Enviar formulario
            form.submit();
            
        } catch (error) {
            console.error('Error eliminando registro:', error);
            this.showNotification('Error eliminando registro', 'error');
        }
    }

    async cargarGrafico(tipo) {
        console.log('Cargando gráfico:', tipo);
        const container = document.getElementById('graficoContainer');
        if (!container) {
            console.error('Contenedor de gráfico no encontrado');
            return;
        }

        // Update active button
        document.querySelectorAll('.grafico-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tipo === tipo);
        });

        container.innerHTML = `
            <div class="grafico-loading">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <p class="text-muted">Cargando gráfico...</p>
            </div>
        `;

        try {
            const timestamp = new Date().getTime();
            const url = `http://localhost:5000/api/grafico/${tipo}?t=${timestamp}`;
            console.log('URL del gráfico:', url);
            
            const response = await fetch(url);
            if (!response.ok) throw new Error('Error loading chart');

            container.innerHTML = `
                <div class="grafico-cargado slide-in">
                    <div class="grafico-header">
                        <h4>${this.getChartTitle(tipo)}</h4>
                        <button class="btn btn-outline btn-sm" onclick="app.cargarGrafico('${tipo}')">
                            <i class="fas fa-redo"></i> Recargar
                        </button>
                    </div>
                    <div class="grafico-imagen">
                        <img src="${url}" alt="${this.getChartTitle(tipo)}" 
                             style="max-width: 100%; height: auto; border: 1px solid #e2e8f0; border-radius: 8px;">
                    </div>
                </div>
            `;

        } catch (error) {
            console.error('Error cargando gráfico:', error);
            container.innerHTML = `
                <div class="grafico-error">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                    <h4>Error al cargar gráfico</h4>
                    <p class="text-muted">No se pudo cargar el gráfico de tipo: ${tipo}</p>
                    <p class="text-muted">Error: ${error.message}</p>
                    <button class="btn btn-warning" onclick="app.cargarGrafico('${tipo}')">
                        <i class="fas fa-redo"></i> Reintentar
                    </button>
                </div>
            `;
        }
    }

    updateStatsCards(stats) {
        const cards = {
            'total_registros': document.querySelector('.stat-card-modern:nth-child(1) .stat-value'),
            'promedio_calorias': document.querySelector('.stat-card-modern:nth-child(2) .stat-value'),
            'promedio_peso': document.querySelector('.stat-card-modern:nth-child(3) .stat-value')
        };

        if (cards.total_registros) {
            cards.total_registros.innerHTML = stats.total_registros || '0';
        }
        if (cards.promedio_calorias) {
            cards.promedio_calorias.innerHTML = `${stats.promedio_calorias || '0'} <small>kcal</small>`;
        }
        if (cards.promedio_peso) {
            cards.promedio_peso.innerHTML = `${stats.promedio_peso || '0'} <small>kg</small>`;
        }
    }

    async checkBackendConnection() {
        try {
            const response = await fetch('http://localhost:5000/api/health');
            const data = await response.json();
            
            const statusElement = document.querySelector('.connection-status');
            if (statusElement) {
                statusElement.className = `connection-status ${data.status === 'ok' ? 'connected' : 'disconnected'}`;
                statusElement.innerHTML = `
                    <i class="fas fa-${data.status === 'ok' ? 'check-circle' : 'exclamation-circle'}"></i>
                    ${data.status === 'ok' ? 'Conectado' : 'Desconectado'}
                `;
            }
        } catch (error) {
            const statusElement = document.querySelector('.connection-status');
            if (statusElement) {
                statusElement.className = 'connection-status disconnected';
                statusElement.innerHTML = `
                    <i class="fas fa-exclamation-circle"></i>
                    Desconectado
                `;
            }
        }
    }

    setButtonLoading(button, loading) {
        if (loading) {
            const originalText = button.innerHTML;
            button.dataset.originalText = originalText;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            button.classList.add('loading');
        } else {
            button.disabled = false;
            const originalText = button.dataset.originalText || button.textContent;
            button.innerHTML = originalText;
            button.classList.remove('loading');
        }
    }

    showModal(modal) {
        modal.style.display = 'block';
        document.addEventListener('keydown', this.handleEscapeKey);
    }

    closeModal(element) {
        const modal = element.closest('.modal-enhanced');
        if (modal) {
            modal.style.display = 'none';
            modal.remove();
        }
        document.removeEventListener('keydown', this.handleEscapeKey);
    }

    handleEscapeKey = (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-enhanced').forEach(modal => {
                modal.style.display = 'none';
                modal.remove();
            });
        }
    }

    showNotification(message, type = 'info') {
        // Remove existing notifications
        document.querySelectorAll('.notification').forEach(n => n.remove());

        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 30px;
            background: white;
            color: #1e293b;
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-left: 4px solid #3b82f6;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 10000;
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s ease;
            max-width: 350px;
        `;

        if (type === 'success') {
            notification.style.borderLeftColor = '#10b981';
        } else if (type === 'error') {
            notification.style.borderLeftColor = '#ef4444';
        } else if (type === 'warning') {
            notification.style.borderLeftColor = '#f59e0b';
        }

        notification.innerHTML = `
            <i class="fas fa-${this.getNotificationIcon(type)}"></i>
            <span>${message}</span>
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
            notification.style.opacity = '1';
        }, 100);

        // Remove after 5 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            notification.style.opacity = '0';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }

    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    // Utility methods
    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('es-ES');
    }

    getActivityClass(actividad) {
        const classes = {
            'baja': 'danger',
            'moderada': 'warning',
            'alta': 'success'
        };
        return classes[actividad] || 'warning';
    }

    getActivityIcon(actividad) {
        const icons = {
            'baja': 'walking',
            'moderada': 'running',
            'alta': 'fire'
        };
        return icons[actividad] || 'running';
    }

    getChartTitle(tipo) {
        const titles = {
            'area': 'Evolución del Peso',
            'radar': 'Perfil de Métricas',
            'barras': 'Análisis por Actividad',
            'pastel': 'Distribución de Actividad',
            'lineas': 'Tendencia de Peso'
        };
        return titles[tipo] || 'Gráfico';
    }

    capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    initTooltips() {
        // Simple tooltip implementation
        document.querySelectorAll('[data-tooltip]').forEach(element => {
            element.addEventListener('mouseenter', this.showTooltip);
            element.addEventListener('mouseleave', this.hideTooltip);
        });
    }

    showTooltip(e) {
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = e.target.getAttribute('data-tooltip');
        document.body.appendChild(tooltip);

        const rect = e.target.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
    }

    hideTooltip() {
        const tooltip = document.querySelector('.tooltip');
        if (tooltip) {
            tooltip.remove();
        }
    }

    initAnimations() {
        // Add loading animation to stats cards
        const stats = document.querySelectorAll('.stat-card-modern');
        stats.forEach((stat, index) => {
            stat.style.animationDelay = `${index * 0.1}s`;
            stat.classList.add('animate-in');
        });
    }
}

// Initialize the application
const app = new HealthPredictApp();

// Add tooltip styles dynamically
const tooltipStyles = document.createElement('style');
tooltipStyles.textContent = `
    .tooltip {
        position: fixed;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        z-index: 10000;
        pointer-events: none;
        white-space: nowrap;
    }
    
    .animate-in {
        animation: slideUp 0.6s ease-out forwards;
        opacity: 0;
        transform: translateY(30px);
    }
    
    @keyframes slideUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .slide-in {
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
`;
document.head.appendChild(tooltipStyles);

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modals = document.getElementsByClassName('modal-enhanced');
    for (let modal of modals) {
        if (event.target === modal) {
            modal.style.display = 'none';
            modal.remove();
        }
    }
});

// Smooth scrolling for navigation
document.querySelectorAll('.nav-link').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Form validation enhancements
document.addEventListener('DOMContentLoaded', function() {
    // Add real-time validation to forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const inputs = this.querySelectorAll('input[required], select[required]');
            let valid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.style.borderColor = '#ef4444';
                    // Add shake animation
                    input.style.animation = 'shake 0.5s ease-in-out';
                    setTimeout(() => {
                        input.style.animation = '';
                    }, 500);
                } else {
                    input.style.borderColor = '#d1d5db';
                }
            });
            
            if (!valid) {
                e.preventDefault();
                app.showNotification('Por favor, complete todos los campos requeridos.', 'error');
            }
        });
    });
    
    // Add input focus effects
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'translateY(-2px)';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'translateY(0)';
        });
    });
});