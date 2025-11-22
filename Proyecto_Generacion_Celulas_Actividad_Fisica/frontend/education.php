<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_TITLE; ?> - Educación</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="logo">CellFit Analytics</div>
            <ul class="nav-links">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="charts.php">Gráficos</a></li>
                <li><a href="trends.php">Tendencias</a></li>
                <li><a href="analysis.php">Análisis</a></li>
                <li><a href="education.php" class="active">Educación</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">
                    <i class="fas fa-graduation-cap"></i>
                    Centro de Educación Científica
                </h1>
                <p class="card-subtitle">Información educativa sobre producción celular y ejercicio</p>
            </div>

            <div class="educational-content">
                <div class="education-section">
                    <h3><i class="fas fa-dna"></i> Fundamentos Científicos</h3>
                    <div class="education-grid">
                        <div class="education-card">
                            <h4>¿Qué es la producción celular?</h4>
                            <p>La producción celular se refiere al proceso mediante el cual el cuerpo genera nuevas células para reparar tejidos, fortalecer el sistema inmunológico y mantener la homeostasis.</p>
                            <ul>
                                <li><strong>Neurogénesis:</strong> Creación de nuevas neuronas</li>
                                <li><strong>Eritropoyesis:</strong> Producción de glóbulos rojos</li>
                                <li><strong>Angiogénesis:</strong> Formación de nuevos vasos sanguíneos</li>
                            </ul>
                        </div>

                        <div class="education-card">
                            <h4>Ejercicio y Regeneración Celular</h4>
                            <p>El ejercicio físico estimula múltiples mecanismos que promueven la producción celular:</p>
                            <ul>
                                <li>Aumento del flujo sanguíneo y oxigenación</li>
                                <li>Liberación de factores de crecimiento</li>
                                <li>Reducción del estrés oxidativo</li>
                                <li>Estimulación del sistema inmunológico</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="education-section">
                    <h3><i class="fas fa-running"></i> Tipos de Ejercicio y Sus Beneficios</h3>
                    <div class="activity-benefits">
                        <div class="benefit-card">
                            <div class="benefit-icon" style="background: linear-gradient(135deg, #FF6B6B, #FF8E8E);">
                                <i class="fas fa-running"></i>
                            </div>
                            <div class="benefit-content">
                                <h4>Ejercicio Aeróbico</h4>
                                <ul>
                                    <li>Mejora la circulación sanguínea</li>
                                    <li>Aumenta la capacidad pulmonar</li>
                                    <li>Estimula la producción de células sanguíneas</li>
                                    <li>Promueve la neurogénesis</li>
                                </ul>
                            </div>
                        </div>

                        <div class="benefit-card">
                            <div class="benefit-icon" style="background: linear-gradient(135deg, #4ECDC4, #6ED9D2);">
                                <i class="fas fa-dumbbell"></i>
                            </div>
                            <div class="benefit-content">
                                <h4>Entrenamiento de Fuerza</h4>
                                <ul>
                                    <li>Estimula la síntesis proteica</li>
                                    <li>Fortalece el tejido muscular</li>
                                    <li>Mejora la densidad ósea</li>
                                    <li>Aumenta la producción de hormonas anabólicas</li>
                                </ul>
                            </div>
                        </div>

                        <div class="benefit-card">
                            <div class="benefit-icon" style="background: linear-gradient(135deg, #45B7D1, #67C7DE);">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div class="benefit-content">
                                <h4>HIIT (High Intensity Interval Training)</h4>
                                <ul>
                                    <li>Máxima eficiencia en tiempo</li>
                                    <li>Alto consumo de oxígeno post-ejercicio</li>
                                    <li>Estimula múltiples sistemas simultáneamente</li>
                                    <li>Mejora la capacidad mitocondrial</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="education-section">
                    <h3><i class="fas fa-chart-line"></i> Factores que Influyen en la Producción Celular</h3>
                    <div class="factors-grid">
                        <div class="factor-item">
                            <i class="fas fa-sliders-h"></i>
                            <h4>Intensidad del Ejercicio</h4>
                            <p>La intensidad moderada-alta (70-85% FCmax) produce los mejores resultados en producción celular.</p>
                        </div>

                        <div class="factor-item">
                            <i class="fas fa-clock"></i>
                            <h4>Duración Óptima</h4>
                            <p>Sesiones de 30-60 minutos maximizan la producción sin causar estrés excesivo.</p>
                        </div>

                        <div class="factor-item">
                            <i class="fas fa-calendar-alt"></i>
                            <h4>Frecuencia</h4>
                            <p>3-5 sesiones por semana permiten la recuperación y adaptación celular óptima.</p>
                        </div>

                        <div class="factor-item">
                            <i class="fas fa-utensils"></i>
                            <h4>Nutrición</h4>
                            <p>Proteínas, antioxidantes y micronutrientes son esenciales para la reparación celular.</p>
                        </div>

                        <div class="factor-item">
                            <i class="fas fa-bed"></i>
                            <h4>Descanso</h4>
                            <p>7-8 horas de sueño son cruciales para los procesos de regeneración celular.</p>
                        </div>

                        <div class="factor-item">
                            <i class="fas fa-tint"></i>
                            <h4>Hidratación</h4>
                            <p>Mantener una hidratación adecuada optimiza el transporte de nutrientes a las células.</p>
                        </div>
                    </div>
                </div>

                <div class="education-section">
                    <h3><i class="fas fa-lightbulb"></i> Recomendaciones Prácticas</h3>
                    <div class="recommendations-list">
                        <div class="recommendation-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <h4>Combina Diferentes Tipos de Ejercicio</h4>
                                <p>Integra ejercicios aeróbicos, de fuerza y flexibilidad para beneficios completos.</p>
                            </div>
                        </div>

                        <div class="recommendation-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <h4>Escucha a Tu Cuerpo</h4>
                                <p>Aprende a reconocer las señales de fatiga y permite la recuperación adecuada.</p>
                            </div>
                        </div>

                        <div class="recommendation-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <h4>Mantén la Consistencia</h4>
                                <p>La regularidad es más importante que la intensidad ocasional.</p>
                            </div>
                        </div>

                        <div class="recommendation-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <h4>Progresión Gradual</h4>
                                <p>Aumenta la intensidad y duración gradualmente para evitar lesiones.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>
