"""
Sistema de Análisis de Impacto de Vitaminas en Glóbulos Rojos
Backend principal - Punto de entrada de la aplicación
"""

import os
import sys
import logging
import pandas as pd

# Agregar el directorio padre al path para imports absolutos
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

try:
    from backend.api import app
    from backend.ml_models import get_predictor
    from backend.data_processor import ChartGenerator
    from backend.utils.helpers import setup_logging, load_json_data
    from backend.config.settings import API_HOST, API_PORT, DEBUG
except ImportError as e:
    print(f"Error de importación: {e}")
    print("Asegúrate de ejecutar desde el directorio correcto")
    sys.exit(1)

def initialize_system():
    """Inicializa todos los componentes del sistema"""
    setup_logging()
    logging.info("Inicializando sistema de análisis de vitaminas...")
    
    # Inicializar componentes
    chart_generator = ChartGenerator()
    ml_predictor = get_predictor()
    
    # Cargar datos y entrenar modelo si es posible
    try:
        # Primero verificar si hay datos
        raw_data = load_json_data()
        logging.info(f"Se encontraron {len(raw_data)} registros en el archivo JSON")
        
        if raw_data:
            df = chart_generator.load_data()
            logging.info(f"Datos procesados: {len(df)} registros válidos")
            
            if not df.empty:
                # Verificar que tenemos las columnas necesarias
                required_columns = ['vitamina', 'dosis_diaria', 'duracion_semanas', 
                                  'globulos_rojos_inicio', 'globulos_rojos_fin']
                missing_columns = [col for col in required_columns if col not in df.columns]
                
                if missing_columns:
                    logging.warning(f"Columnas faltantes en los datos: {missing_columns}")
                    logging.warning("El modelo ML no se puede entrenar sin estas columnas")
                else:
                    # Verificar si tenemos suficientes datos para entrenar
                    if len(df) >= 3:
                        logging.info("Intentando entrenar modelo ML con los datos disponibles...")
                        if ml_predictor.train(df):
                            logging.info("✅ Modelo de ML entrenado exitosamente")
                            
                            # Mostrar métricas del modelo
                            model_info = ml_predictor.get_model_info()
                            metrics = model_info.get('metrics', {})
                            if metrics:
                                logging.info(f"   - R² Score: {metrics.get('r2_score', 0):.4f}")
                                logging.info(f"   - MSE: {metrics.get('mse', 0):.4f}")
                                logging.info(f"   - MAE: {metrics.get('mae', 0):.4f}")
                                logging.info(f"   - RMSE: {metrics.get('rmse', 0):.4f}")
                            
                            # Mostrar importancia de características
                            feature_importance = ml_predictor.get_feature_importance()
                            if feature_importance:
                                logging.info("   - Importancia de características:")
                                for feature, importance in feature_importance.items():
                                    logging.info(f"     * {feature}: {importance:.4f}")
                        else:
                            logging.warning("❌ No se pudo entrenar el modelo de ML")
                    else:
                        logging.warning(f"⚠️  Datos insuficientes para entrenar modelo ML. Se necesitan al menos 3 registros, hay {len(df)}")
            else:
                logging.warning("⚠️  No hay datos válidos para entrenar el modelo. Verifique el formato de los datos.")
        else:
            logging.info("📝 No hay datos disponibles. El sistema funcionará pero necesitará datos para ML.")
            
    except Exception as e:
        logging.error(f"❌ Error durante la inicialización: {e}")
        import traceback
        logging.error(traceback.format_exc())
    
    return chart_generator, ml_predictor

def print_system_banner():
    """Imprime un banner informativo del sistema"""
    banner = """
    ╔══════════════════════════════════════════════════════════════╗
    ║                                                              ║
    ║    SISTEMA DE ANÁLISIS DE VITAMINAS Y GLÓBULOS ROJOS        ║
    ║                     Backend API                             ║
    ║                                                              ║
    ║  • Visualizaciones avanzadas con Seaborn y Plotly           ║
    ║  • Modelos de Machine Learning para predicciones            ║
    ║  • API REST para integración con frontend                   ║
    ║  • Tema oscuro profesional                                  ║
    ║                                                              ║
    ╚══════════════════════════════════════════════════════════════╝
    """
    print(banner)

def check_dependencies():
    """Verifica que todas las dependencias estén disponibles"""
    try:
        import flask
        import flask_cors
        import pandas
        import numpy
        import sklearn
        import seaborn
        import matplotlib
        import plotly
        import joblib
        
        logging.info("✅ Todas las dependencias están disponibles")
        return True
    except ImportError as e:
        logging.error(f"❌ Dependencia faltante: {e}")
        logging.error("Instala las dependencias con: pip install -r requirements.txt")
        return False

if __name__ == '__main__':
    # Imprimir banner del sistema
    print_system_banner()
    
    # Verificar dependencias
    if not check_dependencies():
        sys.exit(1)
    
    # Inicializar sistema
    chart_gen, predictor = initialize_system()
    
    # Mostrar información del sistema
    logging.info("=" * 60)
    logging.info("INFORMACIÓN DEL SISTEMA")
    logging.info("=" * 60)
    
    # Información del modelo
    model_info = predictor.get_model_info()
    if model_info['is_trained']:
        logging.info("🤖 MODELO ML: Entrenado y listo")
        metrics = model_info.get('metrics', {})
        if metrics.get('r2_score', 0) > 0.7:
            status_emoji = "✅"
        elif metrics.get('r2_score', 0) > 0.5:
            status_emoji = "⚠️ "
        else:
            status_emoji = "🔴"
        logging.info(f"   {status_emoji} R² Score: {metrics.get('r2_score', 0):.4f}")
        logging.info(f"   📊 Registros de entrenamiento: {metrics.get('n_samples', 0)}")
    else:
        logging.info("🤖 MODELO ML: No entrenado (se entrenará con los primeros datos)")
    
    # Información de datos
    try:
        data = load_json_data()
        df = chart_gen.load_data()
        logging.info(f"📁 DATOS: {len(data)} registros totales, {len(df)} válidos")
        
        if not df.empty:
            vitaminas_unicas = df['vitamina'].nunique()
            logging.info(f"   🍎 Vitaminas diferentes: {vitaminas_unicas}")
            logging.info(f"   📈 Rango de dosis: {df['dosis_diaria'].min():.1f} - {df['dosis_diaria'].max():.1f} mg")
            logging.info(f"   ⏱️  Rango de duración: {df['duracion_semanas'].min()} - {df['duracion_semanas'].max()} semanas")
    except Exception as e:
        logging.warning(f"   ❌ Error cargando información de datos: {e}")
    
    # Información de la API
    logging.info("🌐 API ENDPOINTS DISPONIBLES:")
    endpoints = [
        ("GET    ", "/api/health", "Estado del sistema"),
        ("GET    ", "/api/data", "Obtener todos los datos"),
        ("POST   ", "/api/data", "Agregar nuevo registro"),
        ("GET    ", "/api/charts", "Obtener todos los gráficos"),
        ("GET    ", "/api/charts/{tipo}", "Gráfico específico"),
        ("POST   ", "/api/predict", "Predicción ML"),
        ("GET    ", "/api/stats", "Estadísticas"),
        ("GET    ", "/api/model/info", "Info del modelo ML"),
        ("GET    ", "/api/system/status", "Estado completo")
    ]
    
    for method, path, description in endpoints:
        logging.info(f"   {method} {path:30} {description}")
    
    logging.info("=" * 60)
    logging.info(f"🚀 Iniciando servidor en: http://{API_HOST}:{API_PORT}")
    logging.info("📍 Presiona CTRL+C para detener el servidor")
    logging.info("=" * 60)
    
    try:
        # Ejecutar aplicación Flask
        app.run(host=API_HOST, port=API_PORT, debug=DEBUG)
    except KeyboardInterrupt:
        logging.info("👋 Servidor detenido por el usuario")
    except Exception as e:
        logging.error(f"💥 Error crítico: {e}")
        sys.exit(1)