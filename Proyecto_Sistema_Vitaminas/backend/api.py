import os
import sys
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from flask import Flask, request, jsonify
from flask_cors import CORS
import logging
import pandas as pd

try:
    from backend.data_processor import ChartGenerator
    from backend.ml_models import VitaminPredictor, get_predictor
    from backend.utils.helpers import load_json_data, save_json_data, validate_vitamin_data, setup_logging
    from backend.config.settings import API_HOST, API_PORT, DEBUG
except ImportError as e:
    print(f"Error de importación en api.py: {e}")
    # Fallback a imports relativos
    from data_processor import ChartGenerator
    from ml_models import VitaminPredictor, get_predictor
    from utils.helpers import load_json_data, save_json_data, validate_vitamin_data, setup_logging
    from config.settings import API_HOST, API_PORT, DEBUG

# Configurar logging
setup_logging()

app = Flask(__name__)
CORS(app)

# Inicializar componentes
chart_generator = ChartGenerator()
ml_predictor = get_predictor()

@app.route('/api/health', methods=['GET'])
def health_check():
    """Endpoint de verificación de salud"""
    return jsonify({
        "status": "healthy", 
        "message": "API funcionando correctamente",
        "model_status": "entrenado" if ml_predictor.is_trained else "no entrenado"
    })

@app.route('/api/data', methods=['GET'])
def get_data():
    """Obtiene todos los datos"""
    try:
        data = load_json_data()
        return jsonify({
            "success": True, 
            "data": data, 
            "count": len(data),
            "message": f"Se encontraron {len(data)} registros"
        })
    except Exception as e:
        logging.error(f"Error getting data: {e}")
        return jsonify({"success": False, "error": str(e)}), 500

@app.route('/api/data', methods=['POST'])
def add_data():
    """Agrega nuevos datos"""
    try:
        new_data = request.get_json()
        
        if not new_data:
            return jsonify({"success": False, "error": "No se proporcionaron datos"}), 400
        
        if not validate_vitamin_data(new_data):
            return jsonify({"success": False, "error": "Datos inválidos o campos faltantes"}), 400
        
        # Cargar datos existentes
        existing_data = load_json_data()
        
        # Asignar ID automático
        new_id = max([item.get('id', 0) for item in existing_data], default=0) + 1
        new_data['id'] = new_id
        
        existing_data.append(new_data)
        
        # Guardar datos
        if save_json_data(existing_data):
            # Intentar reentrenar modelo con nuevos datos
            try:
                df = chart_generator.load_data()
                if not df.empty and len(df) >= 3:  # Mínimo 3 registros para entrenar
                    ml_predictor.train(df)
                    logging.info("Modelo reentrenado con nuevos datos")
            except Exception as training_error:
                logging.warning(f"No se pudo reentrenar el modelo: {training_error}")
            
            return jsonify({
                "success": True, 
                "message": "Datos agregados exitosamente",
                "id": new_id
            })
        else:
            return jsonify({"success": False, "error": "Error guardando datos"}), 500
            
    except Exception as e:
        logging.error(f"Error adding data: {e}")
        return jsonify({"success": False, "error": str(e)}), 500

@app.route('/api/data/<int:data_id>', methods=['DELETE'])
def delete_data(data_id):
    """Elimina un registro específico"""
    try:
        existing_data = load_json_data()
        
        # Encontrar y eliminar el registro
        initial_count = len(existing_data)
        existing_data = [item for item in existing_data if item.get('id') != data_id]
        
        if len(existing_data) == initial_count:
            return jsonify({"success": False, "error": "Registro no encontrado"}), 404
        
        # Guardar datos actualizados
        if save_json_data(existing_data):
            return jsonify({
                "success": True, 
                "message": f"Registro {data_id} eliminado exitosamente"
            })
        else:
            return jsonify({"success": False, "error": "Error guardando datos"}), 500
            
    except Exception as e:
        logging.error(f"Error deleting data: {e}")
        return jsonify({"success": False, "error": str(e)}), 500

@app.route('/api/charts', methods=['GET'])
def get_charts():
    """Obtiene todos los gráficos"""
    try:
        charts = chart_generator.generate_all_charts()
        
        # Verificar si se generaron gráficos
        successful_charts = {k: v for k, v in charts.items() if v is not None}
        if not successful_charts:
            return jsonify({
                "success": False, 
                "error": "No se pudieron generar gráficos. Verifique que hay datos disponibles."
            }), 404
        
        return jsonify({
            "success": True, 
            "charts": charts,
            "generated_charts": list(successful_charts.keys())
        })
    except Exception as e:
        logging.error(f"Error generating charts: {e}")
        return jsonify({"success": False, "error": str(e)}), 500

@app.route('/api/charts/<chart_type>', methods=['GET'])
def get_specific_chart(chart_type):
    """Obtiene un gráfico específico"""
    try:
        chart_methods = {
            'area': chart_generator.generate_area_chart,
            'radar': chart_generator.generate_radar_chart,
            'stacked': chart_generator.generate_stacked_bar_chart,
            'pie': chart_generator.generate_pie_chart,
            'ml_insights': chart_generator.generate_ml_insights_chart
        }
        
        if chart_type in chart_methods:
            chart_data = chart_methods[chart_type]()
            if chart_data is not None:
                return jsonify({"success": True, "chart": chart_data})
            else:
                return jsonify({
                    "success": False, 
                    "error": f"No se pudo generar el gráfico {chart_type}. Verifique los datos."
                }), 404
        else:
            return jsonify({
                "success": False, 
                "error": f"Tipo de gráfico no válido: {chart_type}. Tipos válidos: {list(chart_methods.keys())}"
            }), 400
            
    except Exception as e:
        logging.error(f"Error generating {chart_type} chart: {e}")
        return jsonify({"success": False, "error": str(e)}), 500

@app.route('/api/predict', methods=['POST'])
def predict():
    """Realiza predicciones usando ML"""
    try:
        input_data = request.get_json()
        
        if not input_data:
            return jsonify({"success": False, "error": "No se proporcionaron datos para la predicción"}), 400
        
        # Validar datos de entrada
        is_valid, validation_msg = ml_predictor.validate_prediction_input(input_data)
        if not is_valid:
            return jsonify({"success": False, "error": validation_msg}), 400
        
        # Cargar datos para entrenar si el modelo no está listo
        df = chart_generator.load_data()
        if not ml_predictor.is_trained:
            logging.info("Modelo no entrenado, intentando entrenar con datos disponibles...")
            if not df.empty and len(df) >= 3:  # Mínimo 3 registros para entrenar
                if ml_predictor.train(df):
                    logging.info("Modelo entrenado exitosamente para predicción")
                else:
                    return jsonify({
                        "success": False, 
                        "error": "No se pudo entrenar el modelo. Se necesitan al menos 3 registros válidos."
                    }), 500
            else:
                return jsonify({
                    "success": False, 
                    "error": "No hay suficientes datos para entrenar el modelo. Se necesitan al menos 3 registros."
                }), 400
        
        if ml_predictor.is_trained:
            prediction = ml_predictor.predict(input_data)
            if prediction is not None:
                # Obtener información del modelo para el response
                model_info = ml_predictor.get_model_info()
                
                return jsonify({
                    "success": True, 
                    "prediction": float(prediction),
                    "prediction_rounded": round(prediction, 2),
                    "message": f"Predicción: Incremento de {prediction:.2f} millones/mL en glóbulos rojos",
                    "model_metrics": model_info.get('metrics', {}),
                    "confidence": "alta" if model_info.get('metrics', {}).get('r2_score', 0) > 0.7 else "media"
                })
        
        return jsonify({
            "success": False, 
            "error": "No se pudo realizar la predicción. El modelo podría no estar adecuadamente entrenado."
        }), 500
        
    except Exception as e:
        logging.error(f"Error making prediction: {e}")
        return jsonify({"success": False, "error": str(e)}), 500

@app.route('/api/model/info', methods=['GET'])
def get_model_info():
    """Obtiene información del modelo de ML"""
    try:
        model_info = ml_predictor.get_model_info()
        
        return jsonify({
            "success": True,
            "model_info": model_info,
            "status": "entrenado" if ml_predictor.is_trained else "no entrenado"
        })
    except Exception as e:
        logging.error(f"Error getting model info: {e}")
        return jsonify({"success": False, "error": str(e)}), 500

@app.route('/api/stats', methods=['GET'])
def get_statistics():
    """Obtiene estadísticas de los datos - VERSIÓN CORREGIDA"""
    try:
        df = chart_generator.load_data()
        if df.empty:
            return jsonify({
                "success": False, 
                "error": "No hay datos disponibles",
                "message": "Agregue algunos datos primero para ver estadísticas"
            }), 404
        
        # Calcular incremento
        df['incremento'] = df['globulos_rojos_fin'] - df['globulos_rojos_inicio']
        df['eficiencia'] = df['incremento'] / (df['dosis_diaria'] * df['duracion_semanas'])
        
        stats = {
            'total_registros': int(len(df)),
            'vitaminas_unicas': int(df['vitamina'].nunique()),
            'incremento_promedio': float(df['incremento'].mean()),
            'incremento_maximo': float(df['incremento'].max()),
            'incremento_minimo': float(df['incremento'].min()),
            'dosis_promedio': float(df['dosis_diaria'].mean()),
            'duracion_promedio': float(df['duracion_semanas'].mean()),
            'eficiencia_promedio': float(df['eficiencia'].mean()),
            'globulos_inicio_promedio': float(df['globulos_rojos_inicio'].mean()),
            'globulos_fin_promedio': float(df['globulos_rojos_fin'].mean())
        }
        
        # Estadísticas por vitamina - CORREGIDO: Evitar tuplas en el diccionario
        stats_por_vitamina = {}
        if not df.empty:
            vitamin_stats = df.groupby('vitamina').agg({
                'incremento': ['mean', 'count'],
                'eficiencia': 'mean',
                'dosis_diaria': 'mean'
            }).round(3)
            
            # Convertir el MultiIndex a un diccionario serializable
            for vitamina in vitamin_stats.index:
                stats_por_vitamina[vitamina] = {
                    'incremento_promedio': float(vitamin_stats.loc[vitamina, ('incremento', 'mean')]),
                    'total_registros': int(vitamin_stats.loc[vitamina, ('incremento', 'count')]),
                    'eficiencia_promedio': float(vitamin_stats.loc[vitamina, ('eficiencia', 'mean')]),
                    'dosis_promedio': float(vitamin_stats.loc[vitamina, ('dosis_diaria', 'mean')])
                }
        
        return jsonify({
            "success": True, 
            "statistics": stats,
            "statistics_por_vitamina": stats_por_vitamina,
            "dataset_info": {
                "size": f"{len(df)} registros",
                "vitaminas": list(df['vitamina'].unique()),
                "rango_duracion": f"{int(df['duracion_semanas'].min())}-{int(df['duracion_semanas'].max())} semanas",
                "rango_dosis": f"{df['dosis_diaria'].min():.1f}-{df['dosis_diaria'].max():.1f} mg"
            }
        })
    except Exception as e:
        logging.error(f"Error calculating statistics: {e}")
        import traceback
        logging.error(traceback.format_exc())
        return jsonify({"success": False, "error": str(e)}), 500

@app.route('/api/system/status', methods=['GET'])
def get_system_status():
    """Obtiene el estado completo del sistema"""
    try:
        # Cargar datos
        data = load_json_data()
        df = chart_generator.load_data()
        model_info = ml_predictor.get_model_info()
        
        system_status = {
            "data": {
                "total_registros": len(data),
                "registros_validos": len(df),
                "estado": "ok" if len(data) > 0 else "sin datos"
            },
            "model": {
                "entrenado": ml_predictor.is_trained,
                "metricas": model_info.get('metrics', {}),
                "caracteristicas": model_info.get('feature_names', []),
                "estado": "listo" if ml_predictor.is_trained else "necesita entrenamiento"
            },
            "charts": {
                "disponibles": ['area', 'radar', 'stacked', 'pie', 'ml_insights'],
                "estado": "ok" if len(df) >= 2 else "necesita más datos"
            },
            "api": {
                "estado": "activa",
                "endpoints_disponibles": [
                    "/api/health",
                    "/api/data", 
                    "/api/charts",
                    "/api/predict",
                    "/api/stats",
                    "/api/model/info",
                    "/api/system/status"
                ]
            }
        }
        
        return jsonify({
            "success": True,
            "system_status": system_status,
            "timestamp": pd.Timestamp.now().isoformat()
        })
        
    except Exception as e:
        logging.error(f"Error getting system status: {e}")
        return jsonify({"success": False, "error": str(e)}), 500

@app.errorhandler(404)
def not_found(error):
    """Maneja errores 404"""
    return jsonify({
        "success": False,
        "error": "Endpoint no encontrado",
        "message": "Verifique la URL e intente nuevamente"
    }), 404

@app.errorhandler(500)
def internal_error(error):
    """Maneja errores 500"""
    return jsonify({
        "success": False,
        "error": "Error interno del servidor",
        "message": "Por favor contacte al administrador del sistema"
    }), 500

@app.errorhandler(405)
def method_not_allowed(error):
    """Maneja errores 405"""
    return jsonify({
        "success": False,
        "error": "Método no permitido",
        "message": "Verifique el método HTTP (GET, POST, etc.)"
    }), 405

if __name__ == '__main__':
    # Mostrar información de inicio
    logging.info("=" * 50)
    logging.info("Sistema de Análisis de Vitaminas - Backend API")
    logging.info("=" * 50)
    
    # Intentar cargar datos iniciales para mostrar estado
    try:
        data = load_json_data()
        df = chart_generator.load_data()
        logging.info(f"Datos cargados: {len(data)} registros totales, {len(df)} registros válidos")
        
        model_info = ml_predictor.get_model_info()
        if ml_predictor.is_trained:
            logging.info(f"Modelo ML: Entrenado (R²: {model_info.get('metrics', {}).get('r2_score', 'N/A'):.3f})")
        else:
            logging.info("Modelo ML: No entrenado (se entrenará con los primeros datos)")
            
    except Exception as e:
        logging.warning(f"Error cargando datos iniciales: {e}")
    
    logging.info(f"Servidor iniciando en: http://{API_HOST}:{API_PORT}")
    logging.info("Endpoints disponibles:")
    logging.info("  GET  /api/health          - Estado del sistema")
    logging.info("  GET  /api/data            - Obtener todos los datos")
    logging.info("  POST /api/data            - Agregar nuevo registro")
    logging.info("  GET  /api/charts          - Obtener todos los gráficos")
    logging.info("  GET  /api/charts/{tipo}   - Obtener gráfico específico")
    logging.info("  POST /api/predict         - Realizar predicción ML")
    logging.info("  GET  /api/stats           - Estadísticas de datos")
    logging.info("  GET  /api/model/info      - Información del modelo ML")
    logging.info("  GET  /api/system/status   - Estado completo del sistema")
    logging.info("=" * 50)
    
    app.run(host=API_HOST, port=API_PORT, debug=DEBUG)