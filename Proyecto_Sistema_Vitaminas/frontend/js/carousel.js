// carousel.js - Gestión de carruseles de gráficos
class ChartCarousel {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.inner = this.container.querySelector('.carousel-inner');
        this.items = this.container.querySelectorAll('.carousel-item');
        this.prevBtn = this.container.querySelector('.carousel-prev');
        this.nextBtn = this.container.querySelector('.carousel-next');
        this.currentIndex = 0;
        
        this.init();
    }
    
    init() {
        // Event listeners para controles
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', () => this.prev());
        }
        
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', () => this.next());
        }
        
        // Actualizar estado inicial
        this.updateControls();
    }
    
    next() {
        if (this.currentIndex < this.items.length - 1) {
            this.currentIndex++;
            this.updateCarousel();
        }
    }
    
    prev() {
        if (this.currentIndex > 0) {
            this.currentIndex--;
            this.updateCarousel();
        }
    }
    
    updateCarousel() {
        const translateX = -this.currentIndex * 100;
        this.inner.style.transform = `translateX(${translateX}%)`;
        this.updateControls();
    }
    
    updateControls() {
        if (this.prevBtn) {
            this.prevBtn.disabled = this.currentIndex === 0;
        }
        
        if (this.nextBtn) {
            this.nextBtn.disabled = this.currentIndex === this.items.length - 1;
        }
        
        // Actualizar indicadores si existen
        this.updateIndicators();
    }
    
    updateIndicators() {
        const indicators = this.container.querySelectorAll('.carousel-indicator');
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === this.currentIndex);
        });
    }
    
    goToSlide(index) {
        if (index >= 0 && index < this.items.length) {
            this.currentIndex = index;
            this.updateCarousel();
        }
    }
}

// Inicializar todos los carruseles cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    const carousels = document.querySelectorAll('.carousel');
    carousels.forEach((carousel, index) => {
        new ChartCarousel(carousel.id || `carousel-${index}`);
    });
});

// Función para crear dinámicamente un carrusel de gráficos
function createChartCarousel(chartData, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    let carouselHTML = `
        <div class="carousel">
            <div class="carousel-inner">
    `;
    
    // Agregar gráficos al carrusel
    if (chartData.area_chart) {
        carouselHTML += `
            <div class="carousel-item">
                <h3>Área sobre la Curva</h3>
                <img src="data:image/png;base64,${chartData.area_chart}" 
                     alt="Gráfico de Área - Impacto de Vitaminas">
                <p>Evolución del incremento de glóbulos rojos por dosis de vitamina</p>
            </div>
        `;
    }
    
    if (chartData.stacked_bar_chart) {
        carouselHTML += `
            <div class="carousel-item">
                <h3>Barras Apiladas</h3>
                <img src="data:image/png;base64,${chartData.stacked_bar_chart}" 
                     alt="Gráfico de Barras Apiladas - Eficiencia por Duración">
                <p>Incremento acumulado por vitamina y duración del tratamiento</p>
            </div>
        `;
    }
    
    if (chartData.pie_chart) {
        carouselHTML += `
            <div class="carousel-item">
                <h3>Distribución de Eficiencia</h3>
                <img src="data:image/png;base64,${chartData.pie_chart}" 
                     alt="Gráfico de Pastel - Eficiencia por Vitamina">
                <p>Porcentaje de eficiencia relativa entre diferentes vitaminas</p>
            </div>
        `;
    }
    
    carouselHTML += `
            </div>
            <div class="carousel-controls">
                <button class="carousel-btn carousel-prev">‹ Anterior</button>
                <button class="carousel-btn carousel-next">Siguiente ›</button>
            </div>
        </div>
    `;
    
    container.innerHTML = carouselHTML;
    
    // Inicializar el nuevo carrusel
    new ChartCarousel(containerId);
}
