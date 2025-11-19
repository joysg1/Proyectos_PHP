// modern-ui.js - Mejoras modernas para la interfaz
class ModernUI {
    constructor() {
        this.init();
    }
    
    init() {
        this.initParticles();
        this.initAnimations();
        this.initSmoothScrolling();
        this.initTooltips();
        this.initCountUp();
    }
    
    // Efecto de partículas de fondo
    initParticles() {
        const particlesContainer = document.getElementById('particles');
        if (!particlesContainer) return;
        
        const particleCount = 50;
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            
            // Posición y tamaño aleatorios
            const size = Math.random() * 4 + 1;
            const posX = Math.random() * 100;
            const delay = Math.random() * 20;
            
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            particle.style.left = `${posX}%`;
            particle.style.animationDelay = `${delay}s`;
            particle.style.background = `rgba(99, 102, 241, ${Math.random() * 0.3 + 0.1})`;
            
            particlesContainer.appendChild(particle);
        }
    }
    
    // Animaciones al hacer scroll
    initAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        }, observerOptions);
        
        // Observar elementos para animación
        document.querySelectorAll('.card, .stat-card, .chart-container').forEach(el => {
            observer.observe(el);
        });
    }
    
    // Scroll suave
    initSmoothScrolling() {
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
    
    // Tooltips
    initTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        
        tooltipElements.forEach(el => {
            el.addEventListener('mouseenter', this.showTooltip);
            el.addEventListener('mouseleave', this.hideTooltip);
        });
    }
    
    showTooltip(e) {
        const tooltipText = this.getAttribute('data-tooltip');
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = tooltipText;
        tooltip.style.cssText = `
            position: absolute;
            background: rgba(15, 23, 42, 0.95);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            white-space: nowrap;
            z-index: 1000;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        `;
        
        document.body.appendChild(tooltip);
        
        const rect = this.getBoundingClientRect();
        tooltip.style.left = `${rect.left + rect.width / 2 - tooltip.offsetWidth / 2}px`;
        tooltip.style.top = `${rect.top - tooltip.offsetHeight - 10}px`;
        
        this.tooltip = tooltip;
    }
    
    hideTooltip() {
        if (this.tooltip) {
            this.tooltip.remove();
            this.tooltip = null;
        }
    }
    
    // Animación de números
    initCountUp() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateValue(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        });
        
        document.querySelectorAll('[data-count]').forEach(el => {
            observer.observe(el);
        });
    }
    
    animateValue(element) {
        const target = parseInt(element.getAttribute('data-count'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                element.textContent = target.toLocaleString();
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current).toLocaleString();
            }
        }, 16);
    }
}

// Efectos de glassmorphism
function applyGlassEffect(element) {
    element.style.background = 'rgba(255, 255, 255, 0.1)';
    element.style.backdropFilter = 'blur(10px)';
    element.style.border = '1px solid rgba(255, 255, 255, 0.2)';
}

// Theme switcher (para futuro)
class ThemeManager {
    constructor() {
        this.currentTheme = 'dark';
        this.init();
    }
    
    init() {
        // Podría extenderse para soporte de temas claro/oscuro
        this.loadTheme();
    }
    
    loadTheme() {
        const savedTheme = localStorage.getItem('theme') || 'dark';
        this.setTheme(savedTheme);
    }
    
    setTheme(theme) {
        this.currentTheme = theme;
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
    }
    
    toggleTheme() {
        const newTheme = this.currentTheme === 'dark' ? 'light' : 'dark';
        this.setTheme(newTheme);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.modernUI = new ModernUI();
    window.themeManager = new ThemeManager();
    
    // Añadir efectos de hover a las cards
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Mejorar los formularios con validación visual
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
    });
});
