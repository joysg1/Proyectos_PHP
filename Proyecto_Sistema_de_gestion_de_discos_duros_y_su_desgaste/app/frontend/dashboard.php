<?php
// Configuración básica
$api_url = 'http://localhost:5000/api';

// Función para hacer requests a la API
function call_api($endpoint, $method = 'GET', $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:5000/api/{$endpoint}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        }
    } elseif ($method === 'PUT' || $method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        }
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200) {
        return json_decode($response, true);
    }
    return null;
}

// Verificar estado de la API
$api_status = call_api('health');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - DiskAnalytics</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <i class="fas fa-hard-drive"></i>
                <h1>Disk<span>Analytics</span></h1>
            </div>
            <nav class="nav">
                <a href="index.php" class="nav-link"><i class="fas fa-home"></i> Inicio</a>
                <a href="dashboard.php" class="nav-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </nav>
        </header>

        <main class="main-content">
            <!-- Estado del Sistema -->
            <div class="status-bar">
                <div class="status-item <?php echo $api_status ? 'online' : 'offline'; ?>">
                    <i class="fas fa-server"></i>
                    <span>API Python: <?php echo $api_status ? 'Online' : 'Offline'; ?></span>
                </div>
                <div class="status-item online">
                    <i class="fas fa-database"></i>
                    <span>Base de Datos: Conectada</span>
                </div>
            </div>

            <!-- Controles de Gráficos -->
            <section class="controls">
                <h2><i class="fas fa-chart-line"></i> Visualizaciones de Desgaste</h2>
                <div class="control-grid">
                    <button class="control-btn" onclick="cargarGrafico('area')">
                        <i class="fas fa-chart-area"></i>
                        Desgaste por Tipo
                    </button>
                    <button class="control-btn" onclick="cargarGrafico('radar')">
                        <i class="fas fa-bullseye"></i>
                        Métricas de Salud
                    </button>
                    <button class="control-btn" onclick="cargarGrafico('barras')">
                        <i class="fas fa-chart-bar"></i>
                        Estado por Tipo
                    </button>
                    <button class="control-btn" onclick="cargarGrafico('pastel')">
                        <i class="fas fa-chart-pie"></i>
                        Distribución
                    </button>
                </div>
            </section>

            <!-- Área de Gráficos con Carrusel -->
            <section class="graphics-section">
                <div class="carousel-container">
                    <div class="carousel-nav">
                        <button class="carousel-btn prev" onclick="navigateCarousel(-1)">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="carousel-btn next" onclick="navigateCarousel(1)">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    
                    <div class="carousel-track">
                        <div class="carousel-slide active">
                            <div class="graphic-container">
                                <h3>Evolución del Desgaste por Tipo</h3>
                                <div class="graphic-placeholder" id="area-chart">
                                    <i class="fas fa-chart-area"></i>
                                    <p>Haz clic en "Desgaste por Tipo" para cargar</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="carousel-slide">
                            <div class="graphic-container">
                                <h3>Métricas de Salud - Radar</h3>
                                <div class="graphic-placeholder" id="radar-chart">
                                    <i class="fas fa-bullseye"></i>
                                    <p>Haz clic en "Métricas de Salud" para cargar</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="carousel-slide">
                            <div class="graphic-container">
                                <h3>Estado de Discos por Tipo</h3>
                                <div class="graphic-placeholder" id="barras-chart">
                                    <i class="fas fa-chart-bar"></i>
                                    <p>Haz clic en "Estado por Tipo" para cargar</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="carousel-slide">
                            <div class="graphic-container">
                                <h3>Distribución de Discos</h3>
                                <div class="graphic-placeholder" id="pastel-chart">
                                    <i class="fas fa-chart-pie"></i>
                                    <p>Haz clic en "Distribución" para cargar</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="carousel-indicators">
                        <span class="indicator active" onclick="goToSlide(0)"></span>
                        <span class="indicator" onclick="goToSlide(1)"></span>
                        <span class="indicator" onclick="goToSlide(2)"></span>
                        <span class="indicator" onclick="goToSlide(3)"></span>
                    </div>
                </div>
            </section>

            <!-- Predicción de Desgaste -->
            <section class="prediction-section">
                <h2><i class="fas fa-crystal-ball"></i> Predicción de Desgaste</h2>
                <div class="prediction-container">
                    <div class="prediction-form">
                        <h3>Analizar Nuevo Disco</h3>
                        <form id="prediction-form">
                            <div class="form-group">
                                <label for="tipo">Tipo de Disco:</label>
                                <select id="tipo" name="tipo" required>
                                    <option value="SSD">SSD</option>
                                    <option value="HDD">HDD</option>
                                    <option value="NVMe">NVMe</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="marca">Marca:</label>
                                <input type="text" id="marca" name="marca" value="Samsung" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="capacidad">Capacidad (GB):</label>
                                <input type="number" id="capacidad" name="capacidad_gb" value="1000" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="tiempo_uso">Tiempo de Uso (meses):</label>
                                <input type="number" id="tiempo_uso" name="tiempo_uso_meses" value="12" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="horas_encendido">Horas Encendido:</label>
                                <input type="number" id="horas_encendido" name="horas_encendido" value="8640" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="ciclos_escritura">Ciclos de Escritura:</label>
                                <input type="number" id="ciclos_escritura" name="ciclos_escritura" value="5000" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="temperatura">Temperatura Promedio (°C):</label>
                                <input type="number" id="temperatura" name="temperatura_promedio" value="45" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="bad_sectors">Sectores Dañados:</label>
                                <input type="number" id="bad_sectors" name="bad_sectors" value="0" required>
                            </div>
                            
                            <button type="submit" class="predict-btn">
                                <i class="fas fa-calculator"></i>
                                Predecir Desgaste
                            </button>
                        </form>
                    </div>
                    
                    <div class="prediction-result" id="prediction-result">
                        <div class="result-placeholder">
                            <i class="fas fa-chart-line"></i>
                            <p>Ingresa los datos del disco para obtener la predicción</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Gestión de Discos Duros -->
            <section class="gestion-section">
                <h2><i class="fas fa-plus-circle"></i> Gestión de Discos Duros</h2>
                
                <div class="gestion-container">
                    <!-- Formulario para agregar disco -->
                    <div class="form-agregar">
                        <h3>Agregar Nuevo Disco</h3>
                        <form id="form-agregar-disco">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nuevo_tipo">Tipo:</label>
                                    <select id="nuevo_tipo" name="tipo" required>
                                        <option value="SSD">SSD</option>
                                        <option value="HDD">HDD</option>
                                        <option value="NVMe">NVMe</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="nuevo_marca">Marca:</label>
                                    <input type="text" id="nuevo_marca" name="marca" placeholder="Ej: Samsung" required>
                                </div>
                                <div class="form-group">
                                    <label for="nuevo_modelo">Modelo:</label>
                                    <input type="text" id="nuevo_modelo" name="modelo" placeholder="Ej: 870 EVO" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nuevo_capacidad">Capacidad (GB):</label>
                                    <input type="number" id="nuevo_capacidad" name="capacidad_gb" placeholder="1000" required>
                                </div>
                                <div class="form-group">
                                    <label for="nuevo_tiempo_uso">Tiempo Uso (meses):</label>
                                    <input type="number" id="nuevo_tiempo_uso" name="tiempo_uso_meses" placeholder="12" required>
                                </div>
                                <div class="form-group">
                                    <label for="nuevo_desgaste">Desgaste (%):</label>
                                    <input type="number" id="nuevo_desgaste" name="porcentaje_desgaste" min="0" max="100" placeholder="15" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nuevo_horas">Horas Encendido:</label>
                                    <input type="number" id="nuevo_horas" name="horas_encendido" placeholder="Auto-calculado">
                                </div>
                                <div class="form-group">
                                    <label for="nuevo_ciclos">Ciclos Escritura:</label>
                                    <input type="number" id="nuevo_ciclos" name="ciclos_escritura" placeholder="Auto-calculado">
                                </div>
                                <div class="form-group">
                                    <label for="nuevo_temperatura">Temperatura (°C):</label>
                                    <input type="number" id="nuevo_temperatura" name="temperatura_promedio" placeholder="45">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nuevo_bad_sectors">Sectores Dañados:</label>
                                    <input type="number" id="nuevo_bad_sectors" name="bad_sectors" value="0">
                                </div>
                                <div class="form-group">
                                    <label for="nuevo_fecha">Fecha Instalación:</label>
                                    <input type="date" id="nuevo_fecha" name="fecha_instalacion">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn-agregar">
                                <i class="fas fa-plus"></i> Agregar Disco
                            </button>
                        </form>
                        <div id="resultado-agregar" class="resultado-operacion"></div>
                    </div>
                    
                    <!-- Lista de discos existentes -->
                    <div class="lista-discos">
                        <h3>Discos Existentes</h3>
                        <div class="discos-container" id="lista-discos">
                            <div class="cargando">Cargando discos...</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Análisis con Machine Learning -->
            <section class="ml-section">
                <h2><i class="fas fa-brain"></i> Análisis con Machine Learning</h2>
                <div class="ml-grid">
                    <div class="ml-card">
                        <h3><i class="fas fa-project-diagram"></i> Entrenar Modelo</h3>
                        <p>Entrenar algoritmo de predicción</p>
                        <button class="ml-btn" onclick="entrenarModelo()">
                            <i class="fas fa-play"></i>
                            Entrenar Modelo
                        </button>
                        <div class="ml-result" id="entrenamiento-result"></div>
                    </div>
                    
                    <div class="ml-card">
                        <h3><i class="fas fa-chart-line"></i> Tendencias</h3>
                        <p>Análisis de patrones de desgaste</p>
                        <button class="ml-btn" onclick="analizarTendencias()">
                            <i class="fas fa-chart-line"></i>
                            Analizar Tendencias
                        </button>
                        <div class="ml-result" id="tendencias-result"></div>
                    </div>
                    
                    <div class="ml-card">
                        <h3><i class="fas fa-database"></i> Métricas por Tipo</h3>
                        <p>Estadísticas por tecnología</p>
                        <button class="ml-btn" onclick="obtenerMetricas()">
                            <i class="fas fa-table"></i>
                            Ver Métricas
                        </button>
                        <div class="ml-result" id="metricas-result"></div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="footer">
            <p>&copy; 2024 DiskAnalytics. Powered by Python ML & PHP.</p>
        </footer>
    </div>

    <!-- Modal para gráficos -->
    <div id="graphicModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="modal-body">
                <img id="modalImage" src="" alt="Gráfico ampliado">
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>