<?php
// Función auxiliar para obtener iniciales
function obtenerIniciales($nombre) {
    $palabras = explode(' ', $nombre);
    $iniciales = '';
    $count = 0;
    foreach ($palabras as $palabra) {
        if (!empty(trim($palabra)) && $count < 2) {
            $iniciales .= strtoupper($palabra[0]);
            $count++;
        }
    }
    return $iniciales;
}

// Cargar datos para estadísticas y obtener lista dinámica de países
$total_jugadores = 0;
$total_goles = 0;
$promedio_goles = 0;
$jugadores_ejemplo = [];
$paises_disponibles = [];

if (file_exists('goleadores.json')) {
    $data = file_get_contents('goleadores.json');
    $players = json_decode($data, true);
    
    if (is_array($players) && !empty($players)) {
        $total_jugadores = count($players);
        $total_goles = array_sum(array_column($players, 'goles_totales'));
        $promedio_goles = $total_jugadores > 0 ? $total_goles / $total_jugadores : 0;
        
        // Obtener lista única de países
        $paises_disponibles = array_unique(array_column($players, 'pais'));
        sort($paises_disponibles);
        
        // Ordenar por goles y tomar primeros 5 para ejemplo
        usort($players, function($a, $b) {
            return $b['goles_totales'] - $a['goles_totales'];
        });
        $jugadores_ejemplo = array_slice($players, 0, 5);
    }
}

// Verificar gráficos existentes
$graficos = [
    'grafico_top10.png' => [
        'title' => 'Top 10 Goleadores',
        'description' => 'Los 10 jugadores con más goles en la historia'
    ],
    'grafico_distribucion.png' => [
        'title' => 'Distribución de Tipos de Goles',
        'description' => 'Porcentaje de goles por tipo (derecho, izquierdo, cabeza, etc.)'
    ],
    'grafico_paises.png' => [
        'title' => 'Goles por País',
        'description' => 'Total de goles agrupados por nacionalidad'
    ],
    'grafico_eficiencia.png' => [
        'title' => 'Eficiencia de Jugadores',
        'description' => 'Relación entre goles totales y promedio por partido'
    ],
    'grafico_detalle_jugadores.png' => [
        'title' => 'Distribución por Jugador',
        'description' => 'Desglose detallado de tipos de goles por jugador (Top 8)'
    ],
    'grafico_correlacion.png' => [
        'title' => 'Análisis de Correlación',
        'description' => 'Relación estadística entre diferentes tipos de goles'
    ]
];

$graficos_existentes = [];
foreach ($graficos as $archivo => $info) {
    if (file_exists($archivo)) {
        $graficos_existentes[$archivo] = $info;
    }
}
?>
<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis de Goleadores de Fútbol</title>
    <link rel="stylesheet" href="styles/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="particles" id="particles"></div>
    
    <!-- Toggle de tema mejorado -->
    <div class="theme-toggle" onclick="toggleTheme()" title="Cambiar tema">
        <i class="fas fa-moon"></i>
    </div>

    <div class="container">
        <header>
            <h1>⚽ Análisis de Goleadores de Fútbol</h1>
            <p class="subtitle">Estadísticas detalladas de los mejores anotadores de todos los tiempos</p>
        </header>
        
        <div class="dashboard">
            <div class="search-panel">
                <h2><i class="fas fa-search"></i> Búsqueda de Goleadores</h2>
                <form id="searchForm" method="POST">
                    <div class="form-group">
                        <label for="playerName"><i class="fas fa-user"></i> Nombre del Jugador</label>
                        <input type="text" id="playerName" name="playerName" placeholder="Ej: Cristiano Ronaldo">
                    </div>
                    
                    <div class="form-group">
                        <label for="country"><i class="fas fa-flag"></i> País</label>
                        <select id="country" name="country">
                            <option value="">Todos los países</option>
                            <?php foreach ($paises_disponibles as $pais): ?>
                                <option value="<?php echo htmlspecialchars($pais); ?>"><?php echo htmlspecialchars($pais); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="minGoals"><i class="fas fa-futbol"></i> Goles Mínimos</label>
                        <input type="number" id="minGoals" name="minGoals" min="0" placeholder="Ej: 300">
                    </div>
                    
                    <div class="form-group">
                        <label for="goalType"><i class="fas fa-bullseye"></i> Tipo de Gol</label>
                        <select id="goalType" name="goalType">
                            <option value="">Todos los tipos</option>
                            <option value="pie_derecho">Pie derecho</option>
                            <option value="pie_izquierdo">Pie izquierdo</option>
                            <option value="cabeza">Cabeza</option>
                            <option value="penal">Penal</option>
                            <option value="tiro_libre">Tiro libre</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-block">
                        <i class="fas fa-search"></i> Buscar Goleadores
                    </button>
                </form>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-label">Total de Jugadores</div>
                        <div class="stat-value" id="totalPlayers"><?php echo $total_jugadores; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Total de Goles</div>
                        <div class="stat-value" id="totalGoals"><?php echo number_format($total_goles); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Goles por Partido</div>
                        <div class="stat-value" id="goalsPerGame"><?php echo number_format($promedio_goles, 2); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="results-panel">
                <h2><i class="fas fa-list"></i> Resultados de Búsqueda</h2>
                <div id="searchResults">
                    <?php if (!empty($jugadores_ejemplo)): ?>
                        <?php foreach ($jugadores_ejemplo as $jugador): ?>
                            <div class="player-card">
                                <div class="player-image"><?php echo obtenerIniciales($jugador['nombre']); ?></div>
                                <div class="player-info">
                                    <div class="player-name"><?php echo $jugador['nombre']; ?></div>
                                    <div class="player-country"><?php echo $jugador['pais']; ?></div>
                                    <div class="player-stats">
                                        <div class="stat">
                                            <div class="stat-number"><?php echo $jugador['goles_totales']; ?></div>
                                            <div class="stat-type">Total</div>
                                        </div>
                                        <div class="stat">
                                            <div class="stat-number"><?php echo $jugador['tipos_goles']['pie_derecho']; ?></div>
                                            <div class="stat-type">Derecho</div>
                                        </div>
                                        <div class="stat">
                                            <div class="stat-number"><?php echo $jugador['tipos_goles']['pie_izquierdo']; ?></div>
                                            <div class="stat-type">Izquierdo</div>
                                        </div>
                                        <div class="stat">
                                            <div class="stat-number"><?php echo $jugador['tipos_goles']['cabeza']; ?></div>
                                            <div class="stat-type">Cabeza</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-results">
                            <p>No hay datos de jugadores disponibles.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="chart-container">
                    <h3><i class="fas fa-chart-line"></i> Galería de Análisis</h3>
                    <div id="goalTypeChart">
                        <?php if (!empty($graficos_existentes)): ?>
                            <div class="gallery-container">
                                <div class="gallery-main" id="galleryMain">
                                    <?php
                                    $primer_grafico = array_key_first($graficos_existentes);
                                    $modTime = filemtime($primer_grafico);
                                    ?>
                                    <div class="gallery-counter" id="galleryCounter">1/<?php echo count($graficos_existentes); ?></div>
                                    <img src="<?php echo $primer_grafico . '?t=' . $modTime; ?>" 
                                         alt="<?php echo $graficos_existentes[$primer_grafico]['title']; ?>" 
                                         class="gallery-image" 
                                         id="galleryImage">
                                </div>
                                
                                <div class="gallery-controls">
                                    <div class="gallery-nav">
                                        <button class="gallery-btn" onclick="previousGraph()" id="prevBtn">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <button class="gallery-btn" onclick="nextGraph()" id="nextBtn">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="gallery-info">
                                        <div class="gallery-title" id="galleryTitle"><?php echo $graficos_existentes[$primer_grafico]['title']; ?></div>
                                        <div class="gallery-description" id="galleryDescription"><?php echo $graficos_existentes[$primer_grafico]['description']; ?></div>
                                    </div>
                                </div>
                                
                                <div class="gallery-thumbnails" id="galleryThumbnails">
                                    <?php $index = 0; ?>
                                    <?php foreach ($graficos_existentes as $archivo => $info): ?>
                                        <?php $modTime = filemtime($archivo); ?>
                                        <?php $active = $index === 0 ? 'active' : ''; ?>
                                        <img src="<?php echo $archivo . '?t=' . $modTime; ?>" 
                                             alt="<?php echo $info['title']; ?>"
                                             class="thumbnail <?php echo $active; ?>"
                                             data-index="<?php echo $index; ?>"
                                             data-src="<?php echo $archivo; ?>"
                                             data-title="<?php echo $info['title']; ?>"
                                             data-description="<?php echo $info['description']; ?>">
                                        <?php $index++; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <?php
                            $archivos_graficos = array_keys($graficos_existentes);
                            $timestamps = array_map('filemtime', $archivos_graficos);
                            $ultimo_analisis = max($timestamps);
                            ?>
                            <div class="chart-header">
                                <p><i class="fas fa-clock"></i> <strong>Última actualización:</strong> <?php echo date('d/m/Y H:i:s', $ultimo_analisis); ?></p>
                            </div>
                        <?php else: ?>
                            <div class="chart-placeholder">
                                <div class="gallery-loading">
                                    <div class="loading"></div>
                                    <p><i class="fas fa-chart-bar"></i> No hay gráficos disponibles.</p>
                                    <p>Ejecute el análisis para generar visualizaciones avanzadas.</p>
                                    <div style="margin-top: 15px; padding: 10px; background: var(--bg-secondary); border-radius: 5px;">
                                        <p style="font-size: 0.9rem; margin: 0; color: var(--text-muted);">
                                            <strong>Para solucionar problemas:</strong><br>
                                            1. Verifique que Python esté instalado<br>
                                            2. Ejecute: <code>pip install seaborn matplotlib pandas numpy</code><br>
                                            3. Verifique los permisos de escritura en el directorio<br>
                                            4. <a href="verificar_graficos.php" style="color: var(--accent-primary);">Ejecutar verificador de gráficos</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="actions-panel">
            <h2><i class="fas fa-chart-line"></i> Análisis de Datos</h2>
            <div class="action-buttons">
                <button class="btn btn-secondary" id="runAnalysisBtn" onclick="runPythonAnalysis()">
                    <i class="fas fa-play"></i> Ejecutar Análisis con Python
                </button>
                <button class="btn btn-secondary" onclick="viewStatistics()">
                    <i class="fas fa-chart-bar"></i> Ver Estadísticas Completas
                </button>
                <?php if (file_exists('reporte_goleadores.txt')): ?>
                <a href="reporte_goleadores.txt" class="btn btn-secondary" target="_blank">
                    <i class="fas fa-file-alt"></i> Ver Reporte Completo
                </a>
                <?php endif; ?>
                <button class="btn btn-secondary" onclick="refreshCharts()">
                    <i class="fas fa-sync-alt"></i> Actualizar Gráficos
                </button>
                <a href="verificar_graficos.php" class="btn btn-secondary" target="_blank">
                    <i class="fas fa-bug"></i> Verificar Gráficos
                </a>
            </div>
            
            <div id="analysisStatus" style="margin-top: 15px; display: none;">
                <div class="status-message"></div>
                <div class="progress-bar">
                    <div class="progress-fill"></div>
                </div>
            </div>
            
            <?php if (file_exists('reporte_goleadores.txt')): ?>
                <?php $modTime = filemtime('reporte_goleadores.txt'); ?>
                <div style="margin-top: 15px; padding: 15px; background: var(--bg-secondary); border-radius: 5px; border: 1px solid var(--border-color);">
                    <p style="margin: 0; font-size: 0.9rem; color: var(--text-secondary);">
                        <i class="fas fa-check-circle" style="color: var(--accent-success);"></i>
                        <strong>Último análisis completado:</strong> <?php echo date('d/m/Y H:i:s', $modTime); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
        
        <footer>
            <p><i class="fas fa-futbol"></i> Sistema de Análisis de Goleadores de Fútbol &copy; 2023</p>
        </footer>
    </div>

    <!-- Modal para imágenes ampliadas -->
    <div class="modal" id="imageModal">
        <span class="modal-close">&times;</span>
        <button class="modal-nav modal-prev" onclick="modalPrevious()">&#10094;</button>
        <button class="modal-nav modal-next" onclick="modalNext()">&#10095;</button>
        <div class="modal-counter" id="modalCounter"></div>
        <div class="modal-loading" id="modalLoading">
            <div class="loading"></div>
            <p>Cargando imagen...</p>
        </div>
        <img class="modal-content" id="modalImage">
        <div class="modal-info">
            <div class="modal-title" id="modalTitle"></div>
            <div class="modal-description" id="modalDescription"></div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>