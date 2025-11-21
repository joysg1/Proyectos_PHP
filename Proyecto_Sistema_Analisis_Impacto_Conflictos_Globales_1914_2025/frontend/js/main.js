// Frontend - js/main.js

class WarEconomyApp {
    constructor() {
        this.apiHandler = null;
        this.currentCarouselIndex = 0;
        this.isInitialized = false;
        this.init();
    }

    init() {
        this.initializeApiHandler();
        this.setupEventListeners();
        this.loadInitialData();
        this.isInitialized = true;
        
        // Inicializar ChartManager si estamos en charts.php
        if (window.location.pathname.includes('charts.php')) {
            setTimeout(() => this.initializeChartManager(), 100);
        }
    }

    initializeApiHandler() {
        this.apiHandler = {
            baseUrl: 'http://localhost:5000/api',
            
            async makeRequest(endpoint, method = 'GET', data = null) {
                const url = this.baseUrl + endpoint;
                const options = {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                };

                if (data && method !== 'GET') {
                    options.body = JSON.stringify(data);
                }

                try {
                    console.log(`Haciendo request a: ${url}`);
                    const response = await fetch(url, options);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const result = await response.json();
                    console.log(`Response de ${url}:`, result);
                    return result;
                } catch (error) {
                    console.error('API Request failed:', error);
                    return { 
                        success: false, 
                        error: error.message,
                        details: 'No se pudo conectar con el servidor backend. Asegúrate de que el servidor Python esté ejecutándose en el puerto 5000.'
                    };
                }
            },

            getStats: function() {
                return this.makeRequest('/stats');
            },

            getConflicts: function() {
                return this.makeRequest('/conflicts');
            },

            getChart: function(chartType) {
                return this.makeRequest('/chart/' + chartType);
            },

            getClusters: function() {
                return this.makeRequest('/ml/clusters');
            },

            getTrends: function() {
                return this.makeRequest('/ml/trends');
            },

            predictImpact: function(features, target) {
                return this.makeRequest('/ml/predict', 'POST', {
                    features: features,
                    target: target
                });
            }
        };
    }

    initializeChartManager() {
        if (typeof ChartManager !== 'undefined' && this.apiHandler) {
            window.chartManager = new ChartManager(this.apiHandler);
            console.log('ChartManager inicializado correctamente');
            
            // Cargar gráfico inicial después de un pequeño delay
            setTimeout(() => {
                const activeTab = document.querySelector('.tab-pane.active');
                if (activeTab && activeTab.id !== 'ml') {
                    console.log(`Cargando gráfico inicial: ${activeTab.id}`);
                    loadChart(activeTab.id);
                }
            }, 500);
        } else {
            console.error('No se pudo inicializar ChartManager');
        }
    }

    async loadInitialData() {
        await this.loadStats();
        await this.loadConflicts();
    }

    async loadStats() {
        const statsGrid = document.getElementById('statsGrid');
        if (!statsGrid) return;

        this.showLoading(statsGrid, 'Cargando estadísticas globales...');

        try {
            const response = await this.apiHandler.getStats();
            this.hideLoading(statsGrid);
            
            if (response.success) {
                this.displayStats(response.stats);
            } else {
                this.showError('Error cargando estadísticas: ' + (response.error || 'Error desconocido'));
                this.displayFallbackStats(statsGrid);
            }
        } catch (error) {
            console.error('Error loading stats:', error);
            this.hideLoading(statsGrid);
            this.showError('Error de conexión con el servidor');
            this.displayFallbackStats(statsGrid);
        }
    }

    displayStats(stats) {
        const statsGrid = document.getElementById('statsGrid');
        if (!statsGrid) return;

        const statsCards = [
            {
                icon: 'fas fa-fighter-jet',
                title: stats.total_conflicts || '8',
                subtitle: 'Total de Conflictos',
                color: '#ff6b6b'
            },
            {
                icon: 'fas fa-calendar-alt',
                title: stats.years_covered || '1914-2025',
                subtitle: 'Período Analizado',
                color: '#4ecdc4'
            },
            {
                icon: 'fas fa-chart-line',
                title: `${(stats.avg_gdp_change || -12.5).toFixed(1)}%`,
                subtitle: 'Cambio Promedio GDP',
                color: '#45b7d1'
            },
            {
                icon: 'fas fa-fire',
                title: `${(stats.avg_inflation || 18.5).toFixed(1)}%`,
                subtitle: 'Inflación Promedio',
                color: '#feca57'
            },
            {
                icon: 'fas fa-arrow-down',
                title: `${(stats.max_gdp_drop || -25.4).toFixed(1)}%`,
                subtitle: 'Mayor Caída GDP',
                color: '#ff9ff3'
            },
            {
                icon: 'fas fa-skull-crossbones',
                title: this.formatNumber(stats.total_civilian_casualties || 65000000),
                subtitle: 'Bajas Civiles Totales',
                color: '#ff6b6b'
            }
        ];

        statsGrid.innerHTML = statsCards.map(card => `
            <div class="stat-card">
                <div class="stat-icon" style="color: ${card.color}">
                    <i class="${card.icon}"></i>
                </div>
                <div class="stat-content">
                    <h4>${card.title}</h4>
                    <p>${card.subtitle}</p>
                </div>
            </div>
        `).join('');
    }

    displayFallbackStats(container) {
        const fallbackStats = [
            {
                icon: 'fas fa-fighter-jet',
                title: '8',
                subtitle: 'Conflictos Analizados',
                color: '#ff6b6b'
            },
            {
                icon: 'fas fa-calendar-alt',
                title: '1914-2025',
                subtitle: 'Período Histórico',
                color: '#4ecdc4'
            },
            {
                icon: 'fas fa-database',
                title: '65M+',
                subtitle: 'Registros Analizados',
                color: '#45b7d1'
            },
            {
                icon: 'fas fa-chart-bar',
                title: '5',
                subtitle: 'Tipos de Gráficos',
                color: '#feca57'
            }
        ];

        container.innerHTML = fallbackStats.map(card => `
            <div class="stat-card">
                <div class="stat-icon" style="color: ${card.color}">
                    <i class="${card.icon}"></i>
                </div>
                <div class="stat-content">
                    <h4>${card.title}</h4>
                    <p>${card.subtitle}</p>
                </div>
            </div>
        `).join('');
    }

    async loadConflicts() {
        const carouselContainer = document.querySelector('.carousel-container');
        if (!carouselContainer) return;

        this.showLoading(carouselContainer.parentElement, 'Cargando conflictos...');

        try {
            const response = await this.apiHandler.getConflicts();
            this.hideLoading(carouselContainer.parentElement);
            
            if (response.success) {
                this.displayConflicts(response.conflicts);
            } else {
                this.showError('Error cargando conflictos: ' + (response.error || 'Error desconocido'));
                this.displayFallbackConflicts(carouselContainer);
            }
        } catch (error) {
            console.error('Error loading conflicts:', error);
            this.hideLoading(carouselContainer.parentElement);
            this.showError('Error de conexión con el servidor');
            this.displayFallbackConflicts(carouselContainer);
        }
    }

    displayConflicts(conflicts) {
        const carouselContainer = document.querySelector('.carousel-container');
        if (!carouselContainer) return;

        if (!conflicts || conflicts.length === 0) {
            this.displayFallbackConflicts(carouselContainer);
            return;
        }

        carouselContainer.innerHTML = conflicts.map(conflict => `
            <div class="conflict-card" onclick="app.showConflictModal(${conflict.id}, '${this.escapeHtml(conflict.name)}')">
                <h4>${this.escapeHtml(conflict.name)}</h4>
                <p><i class="fas fa-calendar"></i> ${this.escapeHtml(conflict.years)}</p>
                <p><i class="fas fa-globe"></i> ${this.escapeHtml(conflict.region)}</p>
                <p class="conflict-description">${this.escapeHtml(conflict.description.substring(0, 100))}...</p>
                <div class="conflict-actions">
                    <button class="btn-small" onclick="event.stopPropagation(); app.analyzeConflict(${conflict.id})">
                        <i class="fas fa-chart-line"></i> Analizar
                    </button>
                </div>
            </div>
        `).join('');
    }

    displayFallbackConflicts(container) {
        const fallbackConflicts = [
            {
                id: 1,
                name: "Primera Guerra Mundial",
                years: "1914-1918",
                region: "Global",
                description: "Conflicto global centrado en Europa que transformó el orden económico mundial"
            },
            {
                id: 2,
                name: "Segunda Guerra Mundial",
                years: "1939-1945",
                region: "Global",
                description: "Conflicto global más devastador de la historia con profundas consecuencias económicas"
            },
            {
                id: 3,
                name: "Guerra Fría",
                years: "1947-1991",
                region: "Global",
                description: "Conflicto ideológico que generó carrera armamentística y gasto militar sostenido"
            },
            {
                id: 4,
                name: "Guerra Rusia-Ucrania",
                years: "2022-2025",
                region: "Europa Oriental",
                description: "Conflicto contemporáneo con impacto global en energía y seguridad alimentaria"
            }
        ];

        container.innerHTML = fallbackConflicts.map(conflict => `
            <div class="conflict-card" onclick="app.showConflictModal(${conflict.id}, '${this.escapeHtml(conflict.name)}')">
                <h4>${this.escapeHtml(conflict.name)}</h4>
                <p><i class="fas fa-calendar"></i> ${this.escapeHtml(conflict.years)}</p>
                <p><i class="fas fa-globe"></i> ${this.escapeHtml(conflict.region)}</p>
                <p class="conflict-description">${this.escapeHtml(conflict.description)}</p>
                <div class="conflict-actions">
                    <button class="btn-small" onclick="event.stopPropagation(); app.analyzeConflict(${conflict.id})">
                        <i class="fas fa-chart-line"></i> Analizar
                    </button>
                </div>
            </div>
        `).join('');
    }

    showConflictModal(conflictId, conflictName) {
        const modal = document.getElementById('conflictModal');
        const modalContent = document.getElementById('modalContent');
        
        if (!modal || !modalContent) return;

        modalContent.innerHTML = `
            <div class="modal-header">
                <h3>${this.escapeHtml(conflictName)}</h3>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <div class="modal-loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Cargando detalles del conflicto...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-primary" onclick="app.analyzeConflict(${conflictId})">
                    <i class="fas fa-chart-bar"></i> Análisis Detallado
                </button>
            </div>
        `;

        modal.style.display = 'block';

        // Configurar el evento de cierre para la "X" inmediatamente
        const closeBtn = modalContent.querySelector('.close');
        if (closeBtn) {
            closeBtn.onclick = () => this.closeModal();
        }

        // Simular carga de datos detallados
        setTimeout(() => {
            this.loadConflictDetails(conflictId, modalContent);
        }, 1000);
    }

    async loadConflictDetails(conflictId, modalContent) {
        try {
            // En una implementación real, aquí harías una llamada a la API
            // const response = await this.apiHandler.getConflictDetails(conflictId);
            
            // Por ahora, mostramos datos de ejemplo
            const conflictDetails = {
                name: "Detalles del Conflicto",
                economic_impact: "Impacto significativo en variables macroeconómicas globales",
                key_metrics: [
                    "Reducción promedio del GDP: -15.2%",
                    "Aumento promedio de inflación: +22.8%",
                    "Incremento en gasto militar: +35.4%",
                    "Afectación al comercio global: -18.7%"
                ],
                regions_affected: ["Europa", "América del Norte", "Asia"],
                duration: "4 años",
                total_cost: "Estimado: $3.5 trillones"
            };

            modalContent.querySelector('.modal-body').innerHTML = `
                <div class="conflict-details">
                    <div class="detail-section">
                        <h4><i class="fas fa-chart-line"></i> Impacto Económico</h4>
                        <p>${conflictDetails.economic_impact}</p>
                    </div>
                    
                    <div class="detail-section">
                        <h4><i class="fas fa-list-ol"></i> Métricas Clave</h4>
                        <ul class="metrics-list">
                            ${conflictDetails.key_metrics.map(metric => `
                                <li><i class="fas fa-chevron-right"></i> ${metric}</li>
                            `).join('')}
                        </ul>
                    </div>
                    
                    <div class="detail-section">
                        <h4><i class="fas fa-globe-americas"></i> Regiones Afectadas</h4>
                        <div class="regions-tags">
                            ${conflictDetails.regions_affected.map(region => `
                                <span class="region-tag">${region}</span>
                            `).join('')}
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h4><i class="fas fa-info-circle"></i> Información General</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Duración:</span>
                                <span class="info-value">${conflictDetails.duration}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Costo Total:</span>
                                <span class="info-value">${conflictDetails.total_cost}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Re-configurar el evento de cierre después de actualizar el contenido
            const closeBtn = modalContent.querySelector('.close');
            if (closeBtn) {
                closeBtn.onclick = () => this.closeModal();
            }

        } catch (error) {
            modalContent.querySelector('.modal-body').innerHTML = `
                <div class="error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Error cargando detalles del conflicto: ${error.message}</p>
                </div>
            `;
        }
    }

    analyzeConflict(conflictId) {
        // Redirigir a la página de gráficos con el conflicto seleccionado
        if (window.location.pathname.includes('charts.php')) {
            this.showNotification(`Análisis del conflicto ${conflictId} iniciado`, 'info');
            // Aquí podrías cargar gráficos específicos para este conflicto
        } else {
            window.location.href = 'charts.php?conflict=' + conflictId;
        }
        this.closeModal();
    }

    closeModal() {
        const modal = document.getElementById('conflictModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    showLoading(element, message = 'Cargando...') {
        if (element) {
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'loading-overlay';
            loadingDiv.innerHTML = `
                <div class="loading-content">
                    <div class="loading-spinner"></div>
                    <p>${message}</p>
                </div>
            `;
            element.style.position = 'relative';
            element.appendChild(loadingDiv);
        }
    }

    hideLoading(element) {
        if (element) {
            const loadingOverlay = element.querySelector('.loading-overlay');
            if (loadingOverlay) {
                loadingOverlay.remove();
            }
        }
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showNotification(message, type = 'info') {
        // Crear notificación
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;

        // Estilos para la notificación
        if (!document.querySelector('#notification-styles')) {
            const styles = document.createElement('style');
            styles.id = 'notification-styles';
            styles.textContent = `
                .notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: var(--background-card);
                    border: 1px solid var(--border-color);
                    border-left: 4px solid;
                    padding: 1rem;
                    border-radius: 8px;
                    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
                    z-index: 10000;
                    max-width: 400px;
                    animation: slideInRight 0.3s ease;
                }
                
                .notification-error { border-left-color: var(--primary-color); }
                .notification-success { border-left-color: var(--success-color); }
                .notification-info { border-left-color: var(--accent-color); }
                .notification-warning { border-left-color: var(--warning-color); }
                
                .notification-content {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }
                
                .notification-close {
                    background: none;
                    border: none;
                    color: var(--text-secondary);
                    cursor: pointer;
                    padding: 0.25rem;
                    margin-left: 1rem;
                }
                
                .notification-close:hover {
                    color: var(--text-primary);
                }
                
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(styles);
        }

        document.body.appendChild(notification);

        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    getNotificationIcon(type) {
        const icons = {
            error: 'exclamation-triangle',
            success: 'check-circle',
            info: 'info-circle',
            warning: 'exclamation-circle'
        };
        return icons[type] || 'info-circle';
    }

    formatNumber(num) {
        if (num >= 1000000000) {
            return (num / 1000000000).toFixed(1) + 'B';
        }
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        }
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toString();
    }

    escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    setupEventListeners() {
        this.setupModal();
        this.setupSmoothScrolling();
        this.setupKeyboardShortcuts();
    }

    setupModal() {
        const modal = document.getElementById('conflictModal');
        if (!modal) return;

        // Configurar el evento de cierre cuando se hace clic fuera del modal
        window.onclick = (event) => {
            if (event.target === modal) {
                this.closeModal();
            }
        };

        // Cerrar modal con ESC
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal.style.display === 'block') {
                this.closeModal();
            }
        });
    }

    setupSmoothScrolling() {
        // Smooth scroll para enlaces internos
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
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
    }

    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (event) => {
            // Ctrl + 1: Ir a Inicio
            if (event.ctrlKey && event.key === '1') {
                event.preventDefault();
                window.location.href = 'index.php';
            }
            // Ctrl + 2: Ir a Gráficos
            if (event.ctrlKey && event.key === '2') {
                event.preventDefault();
                window.location.href = 'charts.php';
            }
            // Ctrl + ?: Mostrar ayuda
            if (event.ctrlKey && event.key === '?') {
                event.preventDefault();
                this.showHelp();
            }
        });
    }

    showHelp() {
        const modal = document.getElementById('conflictModal');
        const modalContent = document.getElementById('modalContent');
        
        if (!modal || !modalContent) return;

        modalContent.innerHTML = `
            <div class="modal-header">
                <h3><i class="fas fa-question-circle"></i> Ayuda - Atajos de Teclado</h3>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <div class="help-section">
                    <h4>Navegación</h4>
                    <div class="shortcut-item">
                        <kbd>Ctrl + 1</kbd>
                        <span>Ir a página de Inicio</span>
                    </div>
                    <div class="shortcut-item">
                        <kbd>Ctrl + 2</kbd>
                        <span>Ir a página de Gráficos</span>
                    </div>
                    <div class="shortcut-item">
                        <kbd>ESC</kbd>
                        <span>Cerrar modal o diálogo</span>
                    </div>
                </div>
                
                <div class="help-section">
                    <h4>Gráficos</h4>
                    <div class="shortcut-item">
                        <kbd>Click en conflicto</kbd>
                        <span>Ver detalles del conflicto</span>
                    </div>
                    <div class="shortcut-item">
                        <kbd>Flechas del carrusel</kbd>
                        <span>Navegar entre conflictos</span>
                    </div>
                </div>
                
                <div class="help-section">
                    <h4>General</h4>
                    <div class="shortcut-item">
                        <kbd>Ctrl + ?</kbd>
                        <span>Mostrar esta ayuda</span>
                    </div>
                    <div class="shortcut-item">
                        <kbd>F5</kbd>
                        <span>Recargar la página</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-primary" onclick="app.closeModal()">
                    <i class="fas fa-check"></i> Entendido
                </button>
            </div>
        `;

        // Configurar el evento de cierre para la "X" en el modal de ayuda
        const closeBtn = modalContent.querySelector('.close');
        if (closeBtn) {
            closeBtn.onclick = () => this.closeModal();
        }

        modal.style.display = 'block';
    }

    // Método para verificar el estado del servidor
    async checkServerStatus() {
        try {
            const response = await this.apiHandler.getStats();
            return response.success;
        } catch (error) {
            return false;
        }
    }

    // Método para recargar datos
    async reloadData() {
        this.showNotification('Actualizando datos...', 'info');
        await this.loadStats();
        await this.loadConflicts();
        this.showNotification('Datos actualizados correctamente', 'success');
    }
}

// Funciones globales para interactividad
function moveCarousel(direction) {
    const container = document.querySelector('.carousel-container');
    if (container) {
        const scrollAmount = 320; // Ancho aproximado de tarjeta + gap
        container.scrollLeft += direction * scrollAmount;
    }
}

function openTab(tabName) {
    console.log(`Cambiando a pestaña: ${tabName}`);
    
    // Ocultar todas las pestañas
    const tabPanes = document.querySelectorAll('.tab-pane');
    tabPanes.forEach(pane => {
        pane.classList.remove('active');
        console.log(`Ocultando pestaña: ${pane.id}`);
    });
    
    // Remover active de todos los botones
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => btn.classList.remove('active'));
    
    // Mostrar pestaña seleccionada
    const selectedPane = document.getElementById(tabName);
    const selectedBtn = document.querySelector(`[onclick="openTab('${tabName}')"]`);
    
    if (selectedPane && selectedBtn) {
        selectedPane.classList.add('active');
        selectedBtn.classList.add('active');
        console.log(`Pestaña ${tabName} activada`);
        
        // Cargar gráfico si es una pestaña de gráficos
        if (tabName !== 'ml') {
            console.log(`Iniciando carga de gráfico: ${tabName}`);
            setTimeout(() => {
                if (typeof loadChart === 'function') {
                    loadChart(tabName);
                } else {
                    console.error('loadChart no está definida');
                }
            }, 300);
        } else {
            console.log('Pestaña ML - no se carga gráfico automáticamente');
        }
    } else {
        console.error(`No se pudo encontrar la pestaña: ${tabName}`);
    }
}

// Funciones de utilidad globales
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// Inicializar aplicación cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando aplicación...');
    window.app = new WarEconomyApp();
    
    // Agregar estilos adicionales dinámicamente
    if (!document.querySelector('#dynamic-styles')) {
        const styles = document.createElement('style');
        styles.id = 'dynamic-styles';
        styles.textContent = `
            .conflict-actions {
                margin-top: 1rem;
                display: flex;
                gap: 0.5rem;
            }
            
            .btn-small {
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid var(--border-color);
                color: var(--text-primary);
                padding: 0.5rem 1rem;
                border-radius: 6px;
                cursor: pointer;
                font-size: 0.8rem;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 0.25rem;
            }
            
            .btn-small:hover {
                background: var(--primary-color);
                border-color: var(--primary-color);
            }
            
            .modal-header {
                border-bottom: 1px solid var(--border-color);
                padding-bottom: 1rem;
                margin-bottom: 1rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .modal-header .close {
                color: var(--text-secondary);
                font-size: 2rem;
                font-weight: bold;
                cursor: pointer;
                transition: color 0.3s ease;
                line-height: 1;
                padding: 0;
                background: none;
                border: none;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .modal-header .close:hover {
                color: var(--primary-color);
                background: rgba(255, 107, 107, 0.1);
                border-radius: 50%;
            }
            
            .modal-body {
                max-height: 60vh;
                overflow-y: auto;
            }
            
            .modal-footer {
                border-top: 1px solid var(--border-color);
                padding-top: 1rem;
                margin-top: 1rem;
                display: flex;
                gap: 1rem;
                justify-content: flex-end;
            }
            
            .btn-primary, .btn-secondary {
                padding: 0.75rem 1.5rem;
                border-radius: 6px;
                cursor: pointer;
                font-weight: 500;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                border: none;
            }
            
            .btn-primary {
                background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
                color: white;
            }
            
            .btn-secondary {
                background: rgba(255, 255, 255, 0.1);
                color: var(--text-primary);
                border: 1px solid var(--border-color);
            }
            
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
            }
            
            .btn-secondary:hover {
                background: rgba(255, 255, 255, 0.2);
            }
            
            .conflict-details {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }
            
            .detail-section h4 {
                color: var(--primary-color);
                margin-bottom: 0.5rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            
            .metrics-list {
                list-style: none;
                padding: 0;
            }
            
            .metrics-list li {
                padding: 0.5rem 0;
                border-bottom: 1px solid var(--border-color);
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            
            .metrics-list li:last-child {
                border-bottom: none;
            }
            
            .regions-tags {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .region-tag {
                background: rgba(78, 205, 196, 0.2);
                color: var(--secondary-color);
                padding: 0.25rem 0.75rem;
                border-radius: 20px;
                font-size: 0.8rem;
                border: 1px solid var(--secondary-color);
            }
            
            .info-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
            }
            
            .info-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.5rem;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 6px;
            }
            
            .info-label {
                color: var(--text-secondary);
                font-size: 0.9rem;
            }
            
            .info-value {
                color: var(--text-primary);
                font-weight: 500;
            }
            
            .help-section {
                margin-bottom: 2rem;
            }
            
            .help-section h4 {
                color: var(--primary-color);
                margin-bottom: 1rem;
            }
            
            .shortcut-item {
                display: flex;
                align-items: center;
                gap: 1rem;
                padding: 0.5rem 0;
                border-bottom: 1px solid var(--border-color);
            }
            
            .shortcut-item:last-child {
                border-bottom: none;
            }
            
            kbd {
                background: var(--background-dark);
                border: 1px solid var(--border-color);
                border-radius: 4px;
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
                font-family: monospace;
                min-width: 60px;
                text-align: center;
            }

            .loading-spinner {
                display: inline-block;
                width: 20px;
                height: 20px;
                border: 3px solid rgba(255,255,255,.3);
                border-radius: 50%;
                border-top-color: var(--primary-color);
                animation: spin 1s ease-in-out infinite;
            }

            @keyframes spin {
                to { transform: rotate(360deg); }
            }

            .loading-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.7);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1000;
                border-radius: 15px;
            }

            .loading-content {
                text-align: center;
                color: white;
            }

            .error {
                color: var(--primary-color);
                text-align: center;
                padding: 2rem;
            }

            .error i {
                font-size: 3rem;
                margin-bottom: 1rem;
            }

            /* Estilos mejorados para el modal */
            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.8);
                backdrop-filter: blur(5px);
                animation: fadeIn 0.3s ease;
            }

            .modal-content {
                background: var(--background-card);
                margin: 5% auto;
                padding: 2rem;
                border-radius: 15px;
                width: 90%;
                max-width: 600px;
                position: relative;
                border: 1px solid var(--border-color);
                animation: modalSlideIn 0.3s ease;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            @keyframes modalSlideIn {
                from { transform: translateY(-50px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
        `;
        document.head.appendChild(styles);
    }
});

// Manejar errores no capturados
window.addEventListener('error', function(e) {
    console.error('Error global:', e.error);
    if (window.app) {
        window.app.showError('Ha ocurrido un error inesperado. Por favor, recarga la página.');
    }
});

// Manejar promesas rechazadas no capturadas
window.addEventListener('unhandledrejection', function(e) {
    console.error('Promesa rechazada:', e.reason);
    e.preventDefault();
    if (window.app) {
        window.app.showError('Error en una operación asíncrona: ' + (e.reason?.message || 'Error desconocido'));
    }
});