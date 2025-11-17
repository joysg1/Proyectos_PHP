// js/script.js

// Toggle de tema mejorado
function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    // Cambiar el tema
    html.setAttribute('data-theme', newTheme);
    
    // Guardar preferencia en localStorage
    localStorage.setItem('theme', newTheme);
    
    // Actualizar icono
    const icon = document.querySelector('.theme-toggle i');
    if (newTheme === 'dark') {
        icon.className = 'fas fa-moon';
        icon.title = 'Cambiar a tema claro';
    } else {
        icon.className = 'fas fa-sun';
        icon.title = 'Cambiar a tema oscuro';
    }
    
    // Actualizar partículas para el nuevo tema
    updateParticles();
}

// Cargar tema guardado al iniciar
function loadSavedTheme() {
    const savedTheme = localStorage.getItem('theme') || 'dark';
    const html = document.documentElement;
    const icon = document.querySelector('.theme-toggle i');
    
    html.setAttribute('data-theme', savedTheme);
    
    if (savedTheme === 'dark') {
        icon.className = 'fas fa-moon';
        icon.title = 'Cambiar a tema claro';
    } else {
        icon.className = 'fas fa-sun';
        icon.title = 'Cambiar a tema oscuro';
    }
}

// Actualizar partículas según el tema
function updateParticles() {
    const particles = document.querySelectorAll('.particle');
    const currentTheme = document.documentElement.getAttribute('data-theme');
    
    particles.forEach(particle => {
        if (currentTheme === 'light') {
            particle.style.background = '#3b82f6';
            particle.style.opacity = '0.05';
        } else {
            particle.style.background = '#3b82f6';
            particle.style.opacity = '0.1';
        }
    });
}

// Partículas de fondo
function createParticles() {
    const particlesContainer = document.getElementById('particles');
    const particleCount = 30;
    
    // Limpiar partículas existentes
    particlesContainer.innerHTML = '';
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        const size = Math.random() * 4 + 1;
        const posX = Math.random() * 100;
        const posY = Math.random() * 100;
        const delay = Math.random() * 20;
        const duration = 15 + Math.random() * 10;
        
        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        particle.style.left = `${posX}%`;
        particle.style.top = `${posY}%`;
        particle.style.animationDelay = `${delay}s`;
        particle.style.animationDuration = `${duration}s`;
        
        particlesContainer.appendChild(particle);
    }
    
    updateParticles();
}

// Función auxiliar para obtener iniciales
function obtenerIniciales(nombre) {
    const palabras = nombre.split(' ');
    let iniciales = '';
    let count = 0;
    
    for (let palabra of palabras) {
        if (palabra.trim() && count < 2) {
            iniciales += palabra[0].toUpperCase();
            count++;
        }
    }
    return iniciales;
}

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            buscarGoleadores();
        });
    }
    
    initializeGallery();
    initializeModal();
    loadSavedTheme();
    createParticles();
});

// Búsqueda de goleadores
async function buscarGoleadores() {
    const form = document.getElementById('searchForm');
    const resultsContainer = document.getElementById('searchResults');
    const submitButton = form.querySelector('button[type="submit"]');
    
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<span class="loading"></span>Buscando...';
    submitButton.disabled = true;
    
    try {
        const formData = new FormData(form);
        
        const response = await fetch('procesar_busqueda.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarResultados(data.data.players);
            actualizarEstadisticas(data.data.stats);
        } else {
            throw new Error(data.error || 'Error en la búsqueda');
        }
        
    } catch (error) {
        console.error('Error:', error);
        resultsContainer.innerHTML = `
            <div class="no-results">
                <p>Error al realizar la búsqueda: ${error.message}</p>
            </div>
        `;
    } finally {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    }
}

function mostrarResultados(jugadores) {
    const resultsContainer = document.getElementById('searchResults');
    
    if (!jugadores || jugadores.length === 0) {
        resultsContainer.innerHTML = `
            <div class="no-results">
                <p>No se encontraron jugadores que coincidan con los criterios de búsqueda.</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    
    jugadores.forEach(jugador => {
        const iniciales = obtenerIniciales(jugador.nombre);
        
        html += `
            <div class="player-card">
                <div class="player-image">${iniciales}</div>
                <div class="player-info">
                    <div class="player-name">${jugador.nombre}</div>
                    <div class="player-country">${jugador.pais}</div>
                    <div class="player-stats">
                        <div class="stat">
                            <div class="stat-number">${jugador.goles_totales}</div>
                            <div class="stat-type">Total</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">${jugador.tipos_goles.pie_derecho}</div>
                            <div class="stat-type">Derecho</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">${jugador.tipos_goles.pie_izquierdo}</div>
                            <div class="stat-type">Izquierdo</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">${jugador.tipos_goles.cabeza}</div>
                            <div class="stat-type">Cabeza</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">${jugador.tipos_goles.penal}</div>
                            <div class="stat-type">Penales</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">${jugador.tipos_goles.tiro_libre}</div>
                            <div class="stat-type">T. Libre</div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    resultsContainer.innerHTML = html;
}

function actualizarEstadisticas(stats) {
    if (document.getElementById('totalPlayers')) {
        document.getElementById('totalPlayers').textContent = stats.total_players;
    }
    if (document.getElementById('totalGoals')) {
        document.getElementById('totalGoals').textContent = stats.total_goals.toLocaleString();
    }
    if (document.getElementById('goalsPerGame')) {
        document.getElementById('goalsPerGame').textContent = stats.average_goals;
    }
}

// Ejecutar análisis Python
async function runPythonAnalysis() {
    const button = document.getElementById('runAnalysisBtn');
    const statusDiv = document.getElementById('analysisStatus');
    const statusMessage = statusDiv.querySelector('.status-message');
    const progressFill = statusDiv.querySelector('.progress-fill');
    
    button.disabled = true;
    button.innerHTML = '<span class="loading"></span>Ejecutando Análisis...';
    statusDiv.style.display = 'block';
    statusMessage.innerHTML = 'Iniciando análisis de datos...';
    progressFill.style.width = '10%';
    
    try {
        const response = await fetch('ejecutar_analisis.php', {
            method: 'POST'
        });
        
        progressFill.style.width = '50%';
        statusMessage.innerHTML = 'Procesando datos y generando gráficos...';
        
        const resultado = await response.json();
        
        if (resultado.success) {
            progressFill.style.width = '90%';
            statusMessage.innerHTML = 'Análisis completado exitosamente!';
            
            let detalles = `
                <div style="margin-top: 10px; padding: 10px; background: #e8f5e8; border-radius: 5px;">
                    <strong>Resultado:</strong><br>
                    - Archivos generados: ${resultado.archivos_generados.length}<br>
                    - Comando: ${resultado.python_command}<br>
                    - Hora: ${resultado.timestamp}
                </div>
            `;
            statusMessage.innerHTML += detalles;
            
            progressFill.style.width = '100%';
            
            setTimeout(() => {
                location.reload();
            }, 2000);
            
        } else {
            throw new Error(resultado.error);
        }
        
    } catch (error) {
        console.error('Error en el análisis:', error);
        statusMessage.innerHTML = `
            <div style="color: #e74c3c;">
                <strong>Error:</strong> ${error.message}
            </div>
            <div style="margin-top: 10px; font-size: 0.9rem;">
                <strong>Solución:</strong><br>
                1. Instalar Python<br>
                2. Ejecutar: pip install seaborn matplotlib pandas numpy<br>
                3. Verificar permisos del servidor
            </div>
        `;
        progressFill.style.width = '100%';
        progressFill.style.background = '#e74c3c';
        
    } finally {
        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-play"></i> Ejecutar Análisis con Python';
        }, 3000);
    }
}

function viewStatistics() {
    if (confirm('¿Quieres ver el reporte completo de estadísticas?')) {
        window.open('reporte_goleadores.txt', '_blank');
    }
}

function refreshCharts() {
    const chartContainer = document.getElementById('goalTypeChart');
    const img = chartContainer.querySelector('img');
    
    if (img) {
        const timestamp = new Date().getTime();
        img.src = img.src.split('?')[0] + '?t=' + timestamp;
        
        const message = document.createElement('div');
        message.innerHTML = '<p style="color: #27ae60; text-align: center;">Gráficos actualizados</p>';
        message.style.marginTop = '10px';
        chartContainer.appendChild(message);
        
        setTimeout(() => {
            message.remove();
        }, 2000);
    }
}

// Carrusel de gráficos
let currentGraphIndex = 0;
let graphs = [];

function initializeGallery() {
    const thumbnails = document.querySelectorAll('.thumbnail');
    graphs = Array.from(thumbnails).map(thumb => ({
        src: thumb.getAttribute('data-src'),
        title: thumb.getAttribute('data-title'),
        description: thumb.getAttribute('data-description'),
        index: parseInt(thumb.getAttribute('data-index'))
    }));
    
    if (graphs.length > 0) {
        updateGalleryControls();
        makeImagesClickable();
    }
}

function showGraph(index) {
    if (index < 0 || index >= graphs.length) return;
    
    const galleryMain = document.getElementById('galleryMain');
    const galleryImage = document.getElementById('galleryImage');
    const galleryTitle = document.getElementById('galleryTitle');
    const galleryDescription = document.getElementById('galleryDescription');
    const galleryCounter = document.getElementById('galleryCounter');
    
    if (!galleryMain || !galleryImage) return;
    
    galleryMain.style.opacity = '0.5';
    
    setTimeout(() => {
        const graph = graphs[index];
        const timestamp = new Date().getTime();
        
        galleryImage.src = `${graph.src}?t=${timestamp}`;
        galleryImage.alt = graph.title;
        if (galleryTitle) galleryTitle.textContent = graph.title;
        if (galleryDescription) galleryDescription.textContent = graph.description;
        if (galleryCounter) galleryCounter.textContent = `${index + 1}/${graphs.length}`;
        
        document.querySelectorAll('.thumbnail').forEach((thumb, i) => {
            thumb.classList.toggle('active', i === index);
        });
        
        currentGraphIndex = index;
        
        galleryMain.style.opacity = '1';
        
        updateGalleryControls();
    }, 300);
}

function nextGraph() {
    const nextIndex = (currentGraphIndex + 1) % graphs.length;
    showGraph(nextIndex);
}

function previousGraph() {
    const prevIndex = (currentGraphIndex - 1 + graphs.length) % graphs.length;
    showGraph(prevIndex);
}

function updateGalleryControls() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    if (prevBtn && nextBtn) {
        prevBtn.disabled = graphs.length <= 1;
        nextBtn.disabled = graphs.length <= 1;
    }
}

// Hacer las imágenes clickeables
function makeImagesClickable() {
    const galleryImages = document.querySelectorAll('.gallery-image');
    const thumbnails = document.querySelectorAll('.thumbnail');
    
    galleryImages.forEach((img, index) => {
        img.style.cursor = 'pointer';
        img.onclick = () => openModal(index);
    });
    
    thumbnails.forEach((thumb, index) => {
        thumb.style.cursor = 'pointer';
        thumb.onclick = (e) => {
            e.stopPropagation();
            showGraph(index);
            openModal(index);
        };
    });
}

// Modal para imágenes ampliadas
let modalCurrentIndex = 0;
const modal = document.getElementById('imageModal');

function initializeModal() {
    if (!modal) return;
    
    // Event listeners para el modal
    const closeBtn = modal.querySelector('.modal-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    
    // Cerrar modal al hacer clic fuera de la imagen
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
}

function openModal(index) {
    if (index < 0 || index >= graphs.length || !modal) return;
    
    modalCurrentIndex = index;
    const graph = graphs[index];
    
    // Mostrar modal
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Mostrar loading
    const modalLoading = document.getElementById('modalLoading');
    const modalImage = document.getElementById('modalImage');
    
    if (modalLoading) modalLoading.style.display = 'flex';
    if (modalImage) modalImage.style.display = 'none';
    
    // Configurar imagen
    if (modalImage) {
        modalImage.onload = function() {
            if (modalLoading) modalLoading.style.display = 'none';
            modalImage.style.display = 'block';
        };
        
        modalImage.onerror = function() {
            if (modalLoading) modalLoading.style.display = 'none';
            modalImage.style.display = 'block';
            modalImage.alt = 'Error al cargar la imagen';
        };
        
        const timestamp = new Date().getTime();
        modalImage.src = `${graph.src}?t=${timestamp}`;
        modalImage.alt = graph.title;
    }
    
    // Actualizar información
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const modalCounter = document.getElementById('modalCounter');
    
    if (modalTitle) modalTitle.textContent = graph.title;
    if (modalDescription) modalDescription.textContent = graph.description;
    if (modalCounter) modalCounter.textContent = `${index + 1}/${graphs.length}`;
    
    updateModalControls();
}

function closeModal() {
    if (!modal) return;
    
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function modalNext() {
    const nextIndex = (modalCurrentIndex + 1) % graphs.length;
    openModal(nextIndex);
}

function modalPrevious() {
    const prevIndex = (modalCurrentIndex - 1 + graphs.length) % graphs.length;
    openModal(prevIndex);
}

function updateModalControls() {
    const prevBtn = modal.querySelector('.modal-prev');
    const nextBtn = modal.querySelector('.modal-next');
    
    if (prevBtn && nextBtn) {
        prevBtn.disabled = graphs.length <= 1;
        nextBtn.disabled = graphs.length <= 1;
    }
}

// Navegación con teclado
document.addEventListener('keydown', function(e) {
    // Navegación en el carrusel
    if (e.key === 'ArrowLeft') {
        previousGraph();
    } else if (e.key === 'ArrowRight') {
        nextGraph();
    }
    
    // Navegación en el modal
    if (modal && modal.style.display === 'block') {
        e.preventDefault();
        if (e.key === 'Escape') {
            closeModal();
        } else if (e.key === 'ArrowLeft') {
            modalPrevious();
        } else if (e.key === 'ArrowRight') {
            modalNext();
        }
    }
});

// Función para limpiar el formulario de búsqueda
function clearSearchForm() {
    const form = document.getElementById('searchForm');
    if (form) {
        form.reset();
        // Mostrar todos los jugadores nuevamente
        buscarGoleadores();
    }
}

// Función para exportar resultados
function exportResults() {
    const players = document.querySelectorAll('.player-card');
    if (players.length === 0) {
        alert('No hay resultados para exportar');
        return;
    }
    
    let csvContent = "Nombre,País,Goles Totales,Pie Derecho,Pie Izquierdo,Cabeza,Penales,Tiros Libres\n";
    
    players.forEach(player => {
        const name = player.querySelector('.player-name').textContent;
        const country = player.querySelector('.player-country').textContent;
        const stats = player.querySelectorAll('.stat-number');
        
        csvContent += `"${name}","${country}",${stats[0].textContent},${stats[1].textContent},${stats[2].textContent},${stats[3].textContent},${stats[4].textContent},${stats[5].textContent}\n`;
    });
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'goleadores_busqueda.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Función para ordenar resultados
function sortResults(criteria) {
    const resultsContainer = document.getElementById('searchResults');
    const players = Array.from(resultsContainer.querySelectorAll('.player-card'));
    
    players.sort((a, b) => {
        const aValue = getSortValue(a, criteria);
        const bValue = getSortValue(b, criteria);
        
        if (criteria === 'name') {
            return aValue.localeCompare(bValue);
        } else {
            return bValue - aValue; // Descendente para números
        }
    });
    
    // Limpiar y reinsertar ordenados
    resultsContainer.innerHTML = '';
    players.forEach(player => resultsContainer.appendChild(player));
}

function getSortValue(player, criteria) {
    switch(criteria) {
        case 'name':
            return player.querySelector('.player-name').textContent;
        case 'goals':
            return parseInt(player.querySelector('.stat-number').textContent);
        case 'country':
            return player.querySelector('.player-country').textContent;
        default:
            return 0;
    }
}