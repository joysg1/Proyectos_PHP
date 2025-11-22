<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información Educativa - Análisis Poblacional</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .educational-content {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .concept-card {
            background: var(--bg-card);
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            border-left: 4px solid var(--accent-color);
        }
        
        .concept-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .key-term {
            color: var(--accent-color);
            font-weight: bold;
        }
        
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            background: var(--bg-card);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .comparison-table th,
        .comparison-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        .comparison-table th {
            background: var(--secondary-color);
            color: var(--accent-color);
            font-weight: bold;
        }
        
        .fact-box {
            background: linear-gradient(135deg, var(--accent-color), var(--success-color));
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1.5rem 0;
            color: white;
        }
        
        .methodology-step {
            display: flex;
            align-items: flex-start;
            margin: 1.5rem 0;
            padding: 1.5rem;
            background: var(--bg-card);
            border-radius: 10px;
        }
        
        .step-number {
            background: var(--accent-color);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
        .info-section {
            margin: 3rem 0;
        }
        
        .highlight {
            background: linear-gradient(120deg, var(--accent-color) 0%, var(--accent-color) 100%);
            background-repeat: no-repeat;
            background-size: 100% 0.2em;
            background-position: 0 88%;
            transition: background-size 0.25s ease-in;
            padding: 0.1rem 0.2rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">🌎 PopAnalytics</a>
                <ul class="nav-links">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="graficos.php">Gráficos</a></li>
                    <li><a href="informacion.php" class="active">Información Educativa</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h1>Centro de Información Educativa</h1>
            <p>Comprendiendo el Crecimiento Poblacional en América Latina</p>
        </div>
    </section>

    <main class="container">
        <div class="educational-content">
            
            <!-- Introducción -->
            <div class="concept-card">
                <div class="concept-icon">🌍</div>
                <h2>¿Por qué Estudiar la Población?</h2>
                <p>El estudio de la dinámica poblacional es fundamental para comprender el desarrollo económico, 
                planificar servicios públicos y anticipar desafíos futuros en América Latina.</p>
                
                <div class="fact-box">
                    <h3>📊 Dato Importante</h3>
                    <p>América Latina tiene una población de más de 650 millones de habitantes y representa 
                    aproximadamente el 8.4% de la población mundial.</p>
                </div>
            </div>

            <!-- Conceptos Clave -->
            <div class="concept-card">
                <h2>📈 Conceptos Demográficos Clave</h2>
                
                <div class="methodology-step">
                    <div class="step-number">1</div>
                    <div>
                        <h3>Tasa de Crecimiento Poblacional</h3>
                        <p>Porcentaje en el que aumenta o disminuye la población anualmente. Se calcula considerando 
                        nacimientos, defunciones y migración.</p>
                        <p><span class="key-term">Fórmula:</span> (Nacimientos - Defunciones + Migración Netta) / Población Total × 100</p>
                    </div>
                </div>

                <div class="methodology-step">
                    <div class="step-number">2</div>
                    <div>
                        <h3>Estructura por Edad</h3>
                        <p>Distribución de la población en diferentes grupos de edad. Una población joven (pirámide expansiva) 
                        indica alto potencial de crecimiento, mientras que una población envejecida (pirámide contractiva) 
                        sugiere desafíos en sistemas de pensiones y salud.</p>
                    </div>
                </div>

                <div class="methodology-step">
                    <div class="step-number">3</div>
                    <div>
                        <h3>Transición Demográfica</h3>
                        <p>Proceso por el cual una sociedad pasa de altas tasas de natalidad y mortalidad a bajas tasas. 
                        América Latina se encuentra en etapas avanzadas de esta transición.</p>
                    </div>
                </div>
            </div>

            <!-- Métodos de Análisis -->
            <div class="concept-card">
                <h2>🔍 Métodos de Análisis Utilizados</h2>
                
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Método</th>
                            <th>Propósito</th>
                            <th>Aplicación en el Proyecto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="key-term">Regresión Lineal</span></td>
                            <td>Predecir tendencias futuras basadas en datos históricos</td>
                            <td>Proyecciones de población 2024-2028</td>
                        </tr>
                        <tr>
                            <td><span class="key-term">Clustering K-Means</span></td>
                            <td>Agrupar países con características similares</td>
                            <td>Identificación de patrones regionales</td>
                        </tr>
                        <tr>
                            <td><span class="key-term">Análisis Comparativo</span></td>
                            <td>Establecer comparaciones entre diferentes entidades</td>
                            <td>Gráficos radar de comparación entre países</td>
                        </tr>
                        <tr>
                            <td><span class="key-term">Visualización de Datos</span></td>
                            <td>Representar información compleja de manera accesible</td>
                            <td>Gráficos interactivos y dashboards</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tendencias Regionales -->
            <div class="concept-card">
                <h2>📊 Tendencias en América Latina</h2>
                
                <div class="cards-grid">
                    <div class="card">
                        <h3>🚀 Crecimiento Acelerado</h3>
                        <p>Países como Perú y México muestran tasas de crecimiento superiores al 1% anual, 
                        impulsados por alta natalidad y estructura poblacional joven.</p>
                    </div>
                    
                    <div class="card">
                        <h3>🏙️ Urbanización</h3>
                        <p>Más del 80% de la población latinoamericana vive en áreas urbanas, creando 
                        desafíos en infraestructura y servicios públicos.</p>
                    </div>
                    
                    <div class="card">
                        <h3>👵 Envejecimiento Poblacional</h3>
                        <p>Países como Chile y Argentina muestran transiciones hacia poblaciones más 
                        envejecidas, similar a tendencias en países desarrollados.</p>
                    </div>
                </div>
            </div>

            <!-- Implicaciones -->
            <div class="concept-card">
                <h2>🎯 Implicaciones y Aplicaciones Prácticas</h2>
                
                <div class="methodology-step">
                    <div class="step-number">🏥</div>
                    <div>
                        <h3>Planificación de Salud</h3>
                        <p>Los datos de estructura por edad permiten anticipar demandas de servicios 
                        de salud específicos para diferentes grupos poblacionales.</p>
                    </div>
                </div>

                <div class="methodology-step">
                    <div class="step-number">🏫</div>
                    <div>
                        <h3>Educación</h3>
                        <p>La distribución por edad ayuda a planificar la construcción de escuelas 
                        y la formación de docentes para futuras generaciones.</p>
                    </div>
                </div>

                <div class="methodology-step">
                    <div class="step-number">💼</div>
                    <div>
                        <h3>Desarrollo Económico</h3>
                        <p>El "bono demográfico" (alta proporción de población en edad laboral) 
                        representa una oportunidad única para el crecimiento económico.</p>
                    </div>
                </div>

                <div class="methodology-step">
                    <div class="step-number">🏠</div>
                    <div>
                        <h3>Vivienda y Urbanismo</h3>
                        <p>Las tendencias de urbanización guían las políticas de desarrollo 
                        urbano sostenible y planificación territorial.</p>
                    </div>
                </div>
            </div>

            <!-- Recursos Adicionales -->
            <div class="concept-card">
                <h2>📚 Recursos para Aprender Más</h2>
                
                <div class="fact-box">
                    <h3>🔍 Fuentes de Datos Confiables</h3>
                    <ul>
                        <li>CEPAL - Comisión Económica para América Latina</li>
                        <li>Banco Mundial - Indicadores de Desarrollo</li>
                        <li>UN DESA - División de Población de las Naciones Unidas</li>
                        <li>Institutos Nacionales de Estadística de cada país</li>
                    </ul>
                </div>
                
                <p>Este sistema integra múltiples fuentes de datos y aplica técnicas avanzadas de 
                análisis para proporcionar una visión comprehensiva de la dinámica poblacional 
                en la región.</p>
            </div>

        </div>
    </main>

    <script src="js/api.js"></script>
    <script>
        // Funciones básicas para la página de información
        document.addEventListener('DOMContentLoaded', function() {
            // Puedes añadir interactividad aquí si es necesario
            console.log('Página de información educativa cargada');
        });
    </script>
</body>
</html>