from flask import Flask, jsonify, request
from flask_cors import CORS
from data_processor import DataProcessor
from ml_analyzer import MLAnalyzer
import os
import logging
import json

# Configurar logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

app = Flask(__name__)
CORS(app)  # Esto permite todas las origins - para desarrollo

# Variables globales para los procesadores
data_processor = None
ml_analyzer = None

def initialize_processors():
    """Inicializar procesadores con manejo robusto de errores"""
    global data_processor, ml_analyzer
    
    try:
        data_file = 'greenhouse_gas_data.json'
        
        # Verificar que el archivo existe
        if not os.path.exists(data_file):
            logger.error(f"❌ Archivo de datos no encontrado: {os.path.abspath(data_file)}")
            logger.info("💡 Asegúrate de que el archivo greenhouse_gas_data.json esté en el directorio backend")
            return False
        
        # Verificar que el archivo se puede leer
        try:
            with open(data_file, 'r', encoding='utf-8') as f:
                test_data = json.load(f)
            logger.info("✅ Archivo de datos verificado y cargado correctamente")
        except json.JSONDecodeError as e:
            logger.error(f"❌ Error decodificando JSON: {e}")
            return False
        except Exception as e:
            logger.error(f"❌ Error leyendo archivo: {e}")
            return False
        
        # Inicializar procesadores
        data_processor = DataProcessor(data_file)
        ml_analyzer = MLAnalyzer(data_file)
        
        logger.info("✅ Procesadores de datos inicializados correctamente")
        return True
        
    except Exception as e:
        logger.error(f"❌ Error crítico inicializando procesadores: {e}")
        return False

# Inicializar al importar
initialize_processors()

@app.route('/api/health', methods=['GET'])
def health_check():
    """Endpoint de verificación de salud"""
    data_loaded = data_processor is not None and hasattr(data_processor, 'data') and data_processor.data is not None
    return jsonify({
        'status': 'healthy', 
        'message': 'API funcionando correctamente',
        'data_loaded': data_loaded,
        'data_file_exists': os.path.exists('greenhouse_gas_data.json'),
        'processors_initialized': data_processor is not None and ml_analyzer is not None,
        'timestamp': os.path.getmtime('greenhouse_gas_data.json') if os.path.exists('greenhouse_gas_data.json') else None
    })

@app.route('/api/debug/data', methods=['GET'])
def debug_data():
    """Endpoint para debugging de datos"""
    try:
        if not data_processor or not data_processor.data:
            return jsonify({
                'success': False,
                'error': 'Procesador de datos no disponible',
                'data_processor_exists': data_processor is not None,
                'data_exists': data_processor.data is not None if data_processor else False,
                'file_exists': os.path.exists('greenhouse_gas_data.json'),
                'current_directory': os.getcwd(),
                'files_in_directory': os.listdir('.')
            })
        
        # Mostrar estructura de datos
        data_structure = {
            'years': {
                'count': len(data_processor.data.get('years', [])),
                'range': f"{min(data_processor.data.get('years', []))}-{max(data_processor.data.get('years', []))}" if data_processor.data.get('years') else 'N/A'
            },
            'gases': list(data_processor.data.get('gases', {}).keys()),
            'sectors': list(data_processor.data.get('sectors', {}).keys()),
            'regions': list(data_processor.data.get('regions', {}).keys()),
            'scenarios': list(data_processor.data.get('scenarios', {}).keys()),
            'economic_indicators': list(data_processor.data.get('economic_indicators', {}).keys()),
            'energy_mix': list(data_processor.data.get('energy_mix', {}).keys()),
            'has_metadata': 'metadata' in data_processor.data
        }
        
        # Información del dataset
        dataset_info = {}
        if 'metadata' in data_processor.data:
            dataset_info = data_processor.data['metadata']
        
        return jsonify({
            'success': True,
            'data_structure': data_structure,
            'dataset_info': dataset_info,
            'sample_data': {
                'latest_year': data_processor.data.get('years', [])[-1] if data_processor.data.get('years') else None,
                'total_emissions_latest': sum(gas_data[-1] for gas_data in data_processor.data.get('gases', {}).values()) if data_processor.data.get('gases') else None,
                'gases_sample': {gas: values[-3:] for gas, values in list(data_processor.data.get('gases', {}).items())[:3]}
            }
        })
    except Exception as e:
        return jsonify({
            'success': False,
            'error': str(e),
            'traceback': str(e.__traceback__) if hasattr(e, '__traceback__') else None
        })

@app.route('/api/data/gases', methods=['GET'])
def get_gas_data():
    """Obtener datos de gases"""
    try:
        if not data_processor:
            return jsonify({'success': False, 'error': 'Procesador de datos no disponible'})
        data = data_processor.get_gas_data()
        return jsonify({'success': True, 'data': data})
    except Exception as e:
        logger.error(f"Error en get_gas_data: {e}")
        return jsonify({'success': False, 'error': str(e)})

@app.route('/api/data/sectors', methods=['GET'])
def get_sector_data():
    """Obtener datos por sector"""
    try:
        if not data_processor:
            return jsonify({'success': False, 'error': 'Procesador de datos no disponible'})
        data = data_processor.get_sector_data()
        return jsonify({'success': True, 'data': data})
    except Exception as e:
        logger.error(f"Error en get_sector_data: {e}")
        return jsonify({'success': False, 'error': str(e)})

@app.route('/api/data/regions', methods=['GET'])
def get_region_data():
    """Obtener datos por región"""
    try:
        if not data_processor:
            return jsonify({'success': False, 'error': 'Procesador de datos no disponible'})
        data = data_processor.get_region_data()
        return jsonify({'success': True, 'data': data})
    except Exception as e:
        logger.error(f"Error en get_region_data: {e}")
        return jsonify({'success': False, 'error': str(e)})

@app.route('/api/data/economic', methods=['GET'])
def get_economic_data():
    """Obtener datos económicos"""
    try:
        if not data_processor or not data_processor.data:
            return jsonify({'success': False, 'error': 'Procesador de datos no disponible'})
        
        economic_data = data_processor.data.get('economic_indicators', {})
        return jsonify({
            'success': True, 
            'data': economic_data,
            'years': data_processor.data.get('years', [])
        })
    except Exception as e:
        logger.error(f"Error en get_economic_data: {e}")
        return jsonify({'success': False, 'error': str(e)})

@app.route('/api/data/energy', methods=['GET'])
def get_energy_data():
    """Obtener datos de mix energético"""
    try:
        if not data_processor or not data_processor.data:
            return jsonify({'success': False, 'error': 'Procesador de datos no disponible'})
        
        energy_data = data_processor.data.get('energy_mix', {})
        return jsonify({
            'success': True, 
            'data': energy_data,
            'years': data_processor.data.get('years', [])
        })
    except Exception as e:
        logger.error(f"Error en get_energy_data: {e}")
        return jsonify({'success': False, 'error': str(e)})

@app.route('/api/data/climate', methods=['GET'])
def get_climate_data():
    """Obtener datos climáticos"""
    try:
        if not data_processor or not data_processor.data:
            return jsonify({'success': False, 'error': 'Procesador de datos no disponible'})
        
        climate_data = data_processor.data.get('climate_indicators', {})
        return jsonify({
            'success': True, 
            'data': climate_data,
            'years': data_processor.data.get('years', [])
        })
    except Exception as e:
        logger.error(f"Error en get_climate_data: {e}")
        return jsonify({'success': False, 'error': str(e)})

@app.route('/api/data/complete', methods=['GET'])
def get_complete_data():
    """Obtener todos los datos disponibles"""
    try:
        if not data_processor or not data_processor.data:
            return jsonify({'success': False, 'error': 'Procesador de datos no disponible'})
        
        return jsonify({
            'success': True,
            'data': {
                'metadata': data_processor.data.get('metadata', {}),
                'years': data_processor.data.get('years', []),
                'gases': data_processor.data.get('gases', {}),
                'sectors': data_processor.data.get('sectors', {}),
                'regions': data_processor.data.get('regions', {}),
                'scenarios': data_processor.data.get('scenarios', {}),
                'economic_indicators': data_processor.data.get('economic_indicators', {}),
                'energy_mix': data_processor.data.get('energy_mix', {}),
                'climate_indicators': data_processor.data.get('climate_indicators', {}),
                'policy_indicators': data_processor.data.get('policy_indicators', {}),
                'technological_indicators': data_processor.data.get('technological_indicators', {})
            }
        })
    except Exception as e:
        logger.error(f"Error en get_complete_data: {e}")
        return jsonify({'success': False, 'error': str(e)})

# Endpoints de Gráficos
@app.route('/api/charts/all', methods=['GET'])
def generate_all_charts():
    """Generar todos los gráficos al mismo tiempo"""
    try:
        if not data_processor:
            return jsonify({'success': False, 'error': 'Procesador de datos no disponible'})
        
        charts = {}
        
        # Generar todos los gráficos
        area_chart = data_processor.create_area_chart()
        radar_chart = data_processor.create_radar_chart()
        stacked_chart = data_processor.create_stacked_bar_chart()
        pie_chart = data_processor.create_pie_chart()
        trend_chart = data_processor.create_trend_comparison()
        
        if area_chart:
            charts['area'] = f"data:image/png;base64,{area_chart}"
        if radar_chart:
            charts['radar'] = f"data:image/png;base64,{radar_chart}"
        if stacked_chart:
            charts['stacked_bar'] = f"data:image/png;base64,{stacked_chart}"
        if pie_chart:
            charts['pie'] = f"data:image/png;base64,{pie_chart}"
        if trend_chart:
            charts['trend'] = f"data:image/png;base64,{trend_chart}"
        
        return jsonify({
            'success': True,
            'charts': charts,
            'count': len(charts),
            'available_charts': list(charts.keys())
        })
    except Exception as e:
        logger.error(f"Error generando todos los gráficos: {e}")
        return jsonify({'success': False, 'error': str(e)})

@app.route('/api/charts/area', methods=['GET'])
def generate_area_chart():
    """Generar gráfico de área"""
    try:
        if not data_processor:
            return jsonify({'success': False, 'error': 'Procesador de datos no disponible'})
        chart_base64 = data_processor.create_area_chart()
        if chart_base64:
            return jsonify({
                'success': True, 
                'chart': f"data:image/png;base64,{chart_base64}",
                'type': 'area',
                'description': 'Escenarios de emisiones 2024-2100'
            })
        else:
            return jsonify({'success': False, 'error': 'Error generando gráfico de área'})
    except Exception as e:
        logger.error(f"Error en generate_area_chart: {e}")
        return jsonify({'success': False, 'error': str(e)})

@app.route('/api/charts/radar', methods=['GET'])
def generate_radar_chart():
    """Generar gráfico radar"""
    try:
        if not data_processor:
            return jsonify({'success': False, 'error': 'Procesador de datos no disponible'})
        chart_base64 = data_processor.create_radar_chart()
        if chart_base64:
            return jsonify({
                'success': True, 
                'chart': f"data:image/png;base64,{chart_base64}",
                'type': 'radar',
                'description': 'Potencial de Calentamiento Global (GWP) comparativo'
            })
        else:
            return jsonify({'success': False, 'error': 'Error generando gráfico radar'})
    except Exception as e:
        logger.error(f"Error en generate_radar_chart: {e}")
        return jsonify({'success': False, 'error': str(e)})

@app.route('/api/charts/stacked-bar', methods=['GET'])
def generate_stacked_bar_chart():
    """Generar gráfico de barras apiladas"""
    try:
        if not data_processor:
            return jsonify({'success': False, 'error': 'Procesador de datos no disponible'})
        chart_base64 = data_processor.create_stacked_bar_chart()
        if chart_base64:
            return jsonify({
                'success': True, 
                'chart': f"data:image/png;base64,{chart_base64}",
                'type': 'stacked_bar',
                'description': 'Emisiones por sector económico 2024-2100'
            })
        else:
            return jsonify({'success': False, 'error': 'Error generando gráfico de barras apiladas'})
    except Exception as e:
        logger.error(f"Error en generate_stacked_bar_chart: {e}")
        return jsonify({'success': False, 'error': str(e)})

@app.route('/api/charts/pie', methods=['GET'])
def generate_pie_chart():
    """Generar gráfico de pastel"""
    try:
        if not data_processor:
            return jsonify({'success': False, 'error': 'Procesador de datos no disponible'})
        chart_base64 = data_processor.create_pie_chart()
        if chart_base64:
            return jsonify({
                'success': True, 
                'chart': f"data:image/png;base64,{chart_base64}",
                'type': 'pie',
                'description': 'Distribución regional de emisiones 2024'
            })
        else:
            return jsonify({'success': False, 'error': 'Error generando gráfico de pastel'})
    except Exception as e:
        logger.error(f"Error en generate_pie_chart: {e}")
        return jsonify({'success': False, 'error': str(e)})

@app.route('/api/charts/trend', methods=['GET'])
def generate_trend_chart():
    """Generar gráfico de tendencias comparativas"""
    try:
        if not data_processor:
            return jsonify({'success': False, 'error': 'Procesador de datos no disponible'})
        chart_base64 = data_processor.create_trend_comparison()
        if chart_base64:
            return jsonify({
                'success': True, 
                'chart': f"data:image/png;base64,{chart_base64}",
                'type': 'trend',
                'description': 'Tendencias relativas de gases 2024-2100'
            })
        else:
            return jsonify({'success': False, 'error': 'Error generando gráfico de tendencias'})
    except Exception as e:
        logger.error(f"Error en generate_trend_chart: {e}")
        return jsonify({'success': False, 'error': str(e)})

# Endpoints de Machine Learning
@app.route('/api/ml/predictions', methods=['GET'])
def get_ml_predictions():
    """Obtener predicciones básicas de ML (para compatibilidad)"""
    try:
        if not ml_analyzer:
            return jsonify({'success': False, 'error': 'Analizador ML no disponible'})
        
        # Entrenar modelos básicos
        linear_success = ml_analyzer.train_linear_regression()
        rf_success = ml_analyzer.train_random_forest()
        
        predictions = ml_analyzer.get_predictions()
        trends = ml_analyzer.analyze_trends()
        
        return jsonify({
            'success': True,
            'predictions': predictions,
            'trends': trends,
            'models_trained': {
                'linear_regression': linear_success,
                'random_forest': rf_success
            },
            'note': 'Usando modelos básicos. Para análisis avanzado use /api/ml/advanced_predictions'
        })
    except Exception as e:
        logger.error(f"Error en get_ml_predictions: {e}")
        return jsonify({'success': False, 'error': str(e)})

@app.route('/api/ml/advanced_predictions', methods=['GET'])
def get_advanced_ml_predictions():
    """Obtener predicciones avanzadas de ML"""
    try:
        if not ml_analyzer:
            return jsonify({'success': False, 'error': 'Analizador ML no disponible'})
        
        # Entrenar modelos avanzados
        advanced_success = ml_analyzer.train_advanced_models()
        ensemble_success = ml_analyzer.train_ensemble_model()
        
        predictions = ml_analyzer.get_predictions()
        trends = ml_analyzer.analyze_trends()
        risk_assessment = ml_analyzer.get_risk_assessment()
        
        return jsonify({
            'success': True,
            'predictions': predictions,
            'trends': trends,
            'risk_assessment': risk_assessment,
            'models_trained': {
                'advanced_models': advanced_success,
                'ensemble_model': ensemble_success,
                'total_models': len(ml_analyzer.models)
            },
            'dataset_info': {
                'years_range': f"{ml_analyzer.data['years'][0]}-{ml_analyzer.data['years'][-1]}",
                'total_years': len(ml_analyzer.data['years']),
                'features_used': list(ml_analyzer.prepare_advanced_training_data()[0].columns) if advanced_success else []
            }
        })
    except Exception as e:
        logger.error(f"Error en get_advanced_ml_predictions: {e}")
        return jsonify({'success': False, 'error': str(e)})

@app.route('/api/ml/trends', methods=['GET'])
def get_ml_trends():
    """Obtener análisis de tendencias"""
    try:
        if not ml_analyzer:
            return jsonify({'success': False, 'error': 'Analizador ML no disponible'})
        trends = ml_analyzer.analyze_trends()
        return jsonify({'success': True, 'trends': trends})
    except Exception as e:
        logger.error(f"Error en get_ml_trends: {e}")
        return jsonify({'success': False, 'error': str(e)})

@app.route('/api/ml/risk', methods=['GET'])
def get_ml_risk():
    """Obtener evaluación de riesgos"""
    try:
        if not ml_analyzer:
            return jsonify({'success': False, 'error': 'Analizador ML no disponible'})
        risk_assessment = ml_analyzer.get_risk_assessment()
        return jsonify({'success': True, 'risk_assessment': risk_assessment})
    except Exception as e:
        logger.error(f"Error en get_ml_risk: {e}")
        return jsonify({'success': False, 'error': str(e)})

@app.route('/api/ml/models', methods=['GET'])
def get_ml_models():
    """Obtener información sobre los modelos ML disponibles"""
    try:
        if not ml_analyzer:
            return jsonify({'success': False, 'error': 'Analizador ML no disponible'})
        
        models_info = {
            'available_models': list(ml_analyzer.models.keys()) if ml_analyzer.models else [],
            'feature_importance': ml_analyzer.feature_importance,
            'dataset_size': {
                'years': len(ml_analyzer.data.get('years', [])),
                'gases': len(ml_analyzer.data.get('gases', {})),
                'sectors': len(ml_analyzer.data.get('sectors', {})),
                'economic_indicators': len(ml_analyzer.data.get('economic_indicators', {}))
            }
        }
        
        return jsonify({
            'success': True,
            'models_info': models_info
        })
    except Exception as e:
        logger.error(f"Error en get_ml_models: {e}")
        return jsonify({'success': False, 'error': str(e)})

# Endpoints de utilidad
@app.route('/api/system/info', methods=['GET'])
def get_system_info():
    """Obtener información del sistema"""
    try:
        import platform
        import sys
        
        system_info = {
            'python_version': sys.version,
            'platform': platform.platform(),
            'processor': platform.processor(),
            'working_directory': os.getcwd(),
            'data_file_size': os.path.getsize('greenhouse_gas_data.json') if os.path.exists('greenhouse_gas_data.json') else 0,
            'data_file_modified': os.path.getmtime('greenhouse_gas_data.json') if os.path.exists('greenhouse_gas_data.json') else None
        }
        
        return jsonify({
            'success': True,
            'system_info': system_info,
            'api_endpoints': [
                {'path': '/api/health', 'method': 'GET', 'description': 'Verificar salud del sistema'},
                {'path': '/api/debug/data', 'method': 'GET', 'description': 'Información de debugging de datos'},
                {'path': '/api/charts/all', 'method': 'GET', 'description': 'Generar todos los gráficos'},
                {'path': '/api/ml/advanced_predictions', 'method': 'GET', 'description': 'Predicciones ML avanzadas'},
                {'path': '/api/data/complete', 'method': 'GET', 'description': 'Todos los datos disponibles'}
            ]
        })
    except Exception as e:
        logger.error(f"Error en get_system_info: {e}")
        return jsonify({'success': False, 'error': str(e)})

@app.route('/api/data/statistics', methods=['GET'])
def get_data_statistics():
    """Obtener estadísticas de los datos"""
    try:
        if not data_processor or not data_processor.data:
            return jsonify({'success': False, 'error': 'Procesador de datos no disponible'})
        
        # Calcular estadísticas básicas
        gases_data = data_processor.data.get('gases', {})
        total_emissions = [sum(gas_data[i] for gas_data in gases_data.values()) 
                          for i in range(len(data_processor.data.get('years', [])))]
        
        statistics = {
            'general': {
                'total_years': len(data_processor.data.get('years', [])),
                'year_range': f"{data_processor.data['years'][0]}-{data_processor.data['years'][-1]}" if data_processor.data.get('years') else 'N/A',
                'total_gases': len(gases_data),
                'total_sectors': len(data_processor.data.get('sectors', {})),
                'total_regions': len(data_processor.data.get('regions', {}))
            },
            'emissions': {
                'current_total': total_emissions[-1] if total_emissions else 0,
                'peak_emissions': max(total_emissions) if total_emissions else 0,
                'peak_year': data_processor.data['years'][total_emissions.index(max(total_emissions))] if total_emissions else None,
                'growth_since_1990': ((total_emissions[-1] - total_emissions[0]) / total_emissions[0] * 100) if total_emissions and len(total_emissions) > 1 else 0
            },
            'gases_share': {
                gas: (values[-1] / total_emissions[-1] * 100) if total_emissions and total_emissions[-1] > 0 else 0
                for gas, values in gases_data.items()
            }
        }
        
        return jsonify({
            'success': True,
            'statistics': statistics
        })
    except Exception as e:
        logger.error(f"Error en get_data_statistics: {e}")
        return jsonify({'success': False, 'error': str(e)})

# Manejo de errores global
@app.errorhandler(404)
def not_found(error):
    return jsonify({
        'success': False,
        'error': 'Endpoint no encontrado',
        'available_endpoints': [
            '/api/health',
            '/api/charts/all', 
            '/api/ml/advanced_predictions',
            '/api/data/complete',
            '/api/system/info'
        ]
    }), 404

@app.errorhandler(500)
def internal_error(error):
    return jsonify({
        'success': False,
        'error': 'Error interno del servidor',
        'message': 'Contacte al administrador del sistema'
    }), 500

@app.errorhandler(Exception)
def handle_exception(error):
    return jsonify({
        'success': False,
        'error': 'Error inesperado',
        'message': str(error)
    }), 500

if __name__ == '__main__':
    print("=" * 70)
    print("🌍 SISTEMA DE ANÁLISIS DE GASES DE EFECTO INVERNADERO")
    print("=" * 70)
    print(f"📁 Directorio actual: {os.getcwd()}")
    print(f"📊 Archivo de datos: {os.path.abspath('greenhouse_gas_data.json')}")
    
    # Verificar archivo de datos
    if not os.path.exists('greenhouse_gas_data.json'):
        print("❌ ERROR: Archivo greenhouse_gas_data.json no encontrado")
        print("💡 Solución: Asegúrate de que el archivo esté en el directorio backend")
        exit(1)
    
    # Verificar procesadores
    if data_processor is None or ml_analyzer is None:
        print("❌ ERROR: No se pudieron inicializar los procesadores de datos")
        exit(1)
    
    print("✅ Procesadores de datos inicializados correctamente")
    print("✅ Iniciando servidor Flask en http://localhost:5000")
    print("\n📋 ENDPOINTS PRINCIPALES:")
    print("  🔍 /api/health              - Verificar salud del sistema")
    print("  📊 /api/charts/all          - Generar todos los gráficos")
    print("  🤖 /api/ml/advanced_predictions - Predicciones ML avanzadas")
    print("  📈 /api/data/complete       - Todos los datos disponibles")
    print("  ℹ️  /api/system/info         - Información del sistema")
    print("  🐛 /api/debug/data          - Debugging de datos")
    print("\n🔄 Los gráficos se cargarán automáticamente al acceder al frontend")
    print("=" * 70)
    
    # Configuración para desarrollo
    app.run(
        debug=True, 
        host='0.0.0.0', 
        port=5000, 
        threaded=True,
        use_reloader=True
    )