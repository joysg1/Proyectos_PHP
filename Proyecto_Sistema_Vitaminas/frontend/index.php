<?php 
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<?php include 'includes/header.php'; ?>

<!-- Efecto de partículas de fondo -->
<div id="particles" class="particles"></div>

<!-- Hero Section Mejorada -->
<section class="hero">
    <div class="container">
        <div class="fade-in">
            <h1>Analytics de Vitaminas & Glóbulos Rojos</h1>
            <p>Plataforma avanzada de análisis con Machine Learning para estudiar el impacto de vitaminas en la producción de glóbulos rojos</p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-top: 2rem;">
                <a href="dashboard.php" class="btn btn-primary">
                    <i class="fas fa-dashboard"></i>
                    Explorar Dashboard
                </a>
                <a href="#analytics" class="btn btn-outline">
                    <i class="fas fa-chart-line"></i>
                    Ver Analytics
                </a>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <!-- Estadísticas en Tiempo Real -->
    <section id="stats" class="fade-in" style="margin-bottom: 4rem;">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-chart-bar"></i>
                    Métricas del Sistema
                </h2>
                <span class="badge badge-success" id="status-badge">Conectando...</span>
            </div>
            <div id="stats-container">
                <div class="loading-container">
                    <div class="loading"></div>
                    <p>Cargando métricas del sistema...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Gráficos Principales -->
    <section id="analytics" class="fade-in" style="margin-bottom: 4rem;">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-chart-pie"></i>
                    Visualizaciones Principales
                </h2>
                <div class="tabs">
                    <button class="tab active" onclick="switchTab('charts-carousel')">Todos</button>
                    <button class="tab" onclick="switchTab('area-tab')">Área</button>
                    <button class="tab" onclick="switchTab('stacked-tab')">Barras</button>
                    <button class="tab" onclick="switchTab('pie-tab')">Pastel</button>
                </div>
            </div>
            
            <!-- Carrusel Principal -->
            <div id="charts-carousel" class="tab-content active">
                <div id="carousel-container">
                    <div class="loading-container">
                        <div class="loading"></div>
                        <p>Generando visualizaciones...</p>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos Individuales -->
            <div id="area-tab" class="tab-content">
                <div id="area-chart-container" class="chart-container">
                    <div class="loading-container">
                        <div class="loading"></div>
                        <p>Cargando gráfico de área...</p>
                    </div>
                </div>
            </div>
            
            <div id="stacked-tab" class="tab-content">
                <div id="stacked-chart-container" class="chart-container">
                    <div class="loading-container">
                        <div class="loading"></div>
                        <p>Cargando gráfico de barras...</p>
                    </div>
                </div>
            </div>
            
            <div id="pie-tab" class="tab-content">
                <div id="pie-chart-container" class="chart-container">
                    <div class="loading-container">
                        <div class="loading"></div>
                        <p>Cargando gráfico de pastel...</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Predictor ML Mejorado -->
    <section id="prediction" class="fade-in" style="margin-bottom: 4rem;">
        <div class="grid grid-cols-2">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-robot"></i>
                        Predictor ML
                    </h2>
                    <span class="badge badge-info" id="ml-status">AI Powered</span>
                </div>
                
                <form id="prediction-form" class="grid grid-cols-2" style="gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label" for="vitamina">
                            <i class="fas fa-pills"></i>
                            Tipo de Vitamina
                        </label>
                        <select class="form-control" id="vitamina" required>
                            <option value="">Seleccionar vitamina...</option>
                            <option value="Vitamina B12">🔴 Vitamina B12</option>
                            <option value="Hierro">⚫ Hierro</option>
                            <option value="Ácido Fólico">🟢 Ácido Fólico</option>
                            <option value="Vitamina C">🟠 Vitamina C</option>
                            <option value="Vitamina E">🟡 Vitamina E</option>
                            <option value="Complejo B">🔵 Complejo B</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="dosis_diaria">
                            <i class="fas fa-syringe"></i>
                            Dosis Diaria (mg)
                        </label>
                        <input type="number" class="form-control" id="dosis_diaria" 
                               step="0.1" min="0" required placeholder="Ej: 50.0">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="duracion_semanas">
                            <i class="fas fa-calendar-week"></i>
                            Duración (semanas)
                        </label>
                        <input type="number" class="form-control" id="duracion_semanas" 
                               min="1" required placeholder="Ej: 4">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="globulos_inicio">
                            <i class="fas fa-tint"></i>
                            Glóbulos Rojos Iniciales (M/mL)
                        </label>
                        <input type="number" class="form-control" id="globulos_inicio" 
                               step="0.01" min="0" required placeholder="Ej: 4.50">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="edad_paciente">
                            <i class="fas fa-user"></i>
                            Edad del Paciente
                        </label>
                        <input type="number" class="form-control" id="edad_paciente" 
                               min="0" max="120" placeholder="Ej: 45">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="sexo">
                            <i class="fas fa-venus-mars"></i>
                            Sexo
                        </label>
                        <select class="form-control" id="sexo">
                            <option value="">Seleccionar...</option>
                            <option value="M">👨 Masculino</option>
                            <option value="F">👩 Femenino</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <button type="submit" class="btn btn-success" style="width: 100%;">
                            <i class="fas fa-bolt"></i>
                            Predecir Incremento
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-line"></i>
                        Resultado de Predicción
                    </h2>
                </div>
                
                <div id="prediction-result">
                    <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                        <i class="fas fa-brain" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <h3 style="margin-bottom: 1rem; color: var(--text-secondary);">Predicción ML</h3>
                        <p>Complete el formulario para obtener una predicción del incremento esperado basada en nuestro modelo de Machine Learning.</p>
                    </div>
                </div>
                
                <!-- Información del Modelo -->
                <div id="model-info" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <span style="font-weight: 600; color: var(--text-primary);">Estado del Modelo:</span>
                        <span id="model-status" class="badge badge-warning">Cargando...</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 600; color: var(--text-primary);">Precisión (R²):</span>
                        <span id="model-accuracy" style="color: var(--text-secondary);">-</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Información del Sistema -->
    <section id="system-info" class="fade-in">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Acerca del Sistema
                </h2>
            </div>
            <div class="grid grid-cols-3" style="gap: 2rem;">
                <div style="text-align: center;">
                    <i class="fas fa-chart-area" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1rem;"></i>
                    <h3 style="margin-bottom: 0.5rem; color: var(--text-primary);">Visualizaciones Avanzadas</h3>
                    <p style="color: var(--text-secondary);">Gráficos interactivos y dashboards en tiempo real</p>
                </div>
                <div style="text-align: center;">
                    <i class="fas fa-robot" style="font-size: 2.5rem; color: var(--success); margin-bottom: 1rem;"></i>
                    <h3 style="margin-bottom: 0.5rem; color: var(--text-primary);">Machine Learning</h3>
                    <p style="color: var(--text-secondary);">Modelos predictivos con Random Forest</p>
                </div>
                <div style="text-align: center;">
                    <i class="fas fa-database" style="font-size: 2.5rem; color: var(--info); margin-bottom: 1rem;"></i>
                    <h3 style="margin-bottom: 0.5rem; color: var(--text-primary);">Análisis en Tiempo Real</h3>
                    <p style="color: var(--text-secondary);">Procesamiento instantáneo de datos</p>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="js/modern-ui.js"></script>

<?php include 'includes/footer.php'; ?>