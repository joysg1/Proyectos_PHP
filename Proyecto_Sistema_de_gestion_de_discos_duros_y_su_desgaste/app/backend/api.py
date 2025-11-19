import os
import sys
import json
from flask import Flask, request, jsonify
from flask_cors import CORS
import base64
from io import BytesIO
import traceback

# Añadir el directorio actual al path para imports
current_dir = os.path.dirname(os.path.abspath(__file__))
sys.path.append(current_dir)

from graficos import GeneradorGraficos
from ml_processor import MLProcessor

app = Flask(__name__)
CORS(app)

# Configuración
DATA_PATH = os.path.join(os.path.dirname(current_dir), 'data', 'discos_duros.json')

# Inicializar procesadores
generador_graficos = GeneradorGraficos(DATA_PATH)
ml_processor = MLProcessor(DATA_PATH)

def limpiar_datos_json(data):
    """
    Limpiar recursivamente datos para asegurar serialización JSON
    Convierte todos los valores a tipos nativos de Python
    """
    if isinstance(data, dict):
        return {k: limpiar_datos_json(v) for k, v in data.items()}
    elif isinstance(data, list):
        return [limpiar_datos_json(item) for item in data]
    elif isinstance(data, (int, float)):
        return float(data)
    elif data is None:
        return None
    elif isinstance(data, (bool, str)):
        return data
    else:
        # Convertir cualquier otro tipo a string
        return str(data)

@app.route('/api/health', methods=['GET'])
def health_check():
    """Endpoint para verificar que la API está funcionando"""
    return jsonify({
        "status": "active", 
        "message": "API de Monitoreo de Discos Duros funcionando correctamente",
        "version": "1.0.0"
    })

@app.route('/api/discos', methods=['GET'])
def get_discos():
    """Obtener lista de discos duros"""
    try:
        datos = generador_graficos.cargar_datos()
        return jsonify({
            "success": True, 
            "discos": datos.get('discos_duros', []),
            "total": len(datos.get('discos_duros', []))
        })
    except Exception as e:
        print(f"❌ Error obteniendo discos: {str(e)}")
        return jsonify({
            "success": False, 
            "error": f"Error obteniendo datos: {str(e)}"
        })

@app.route('/api/grafico/area', methods=['GET'])
def generar_grafico_area():
    """Generar gráfico de área de desgaste"""
    try:
        print("🖼️ Generando gráfico de área...")
        img_buffer = generador_graficos.grafico_area()
        img_base64 = base64.b64encode(img_buffer.getvalue()).decode()
        print("✅ Gráfico de área generado exitosamente")
        return jsonify({
            "success": True, 
            "image": img_base64,
            "tipo": "area"
        })
    except Exception as e:
        print(f"❌ Error generando gráfico de área: {str(e)}")
        return jsonify({
            "success": False, 
            "error": f"Error generando gráfico: {str(e)}"
        })

@app.route('/api/grafico/radar', methods=['GET'])
def generar_grafico_radar():
    """Generar gráfico radar de métricas"""
    try:
        print("🖼️ Generando gráfico radar...")
        img_buffer = generador_graficos.grafico_radar()
        img_base64 = base64.b64encode(img_buffer.getvalue()).decode()
        print("✅ Gráfico radar generado exitosamente")
        return jsonify({
            "success": True, 
            "image": img_base64,
            "tipo": "radar"
        })
    except Exception as e:
        print(f"❌ Error generando gráfico radar: {str(e)}")
        return jsonify({
            "success": False, 
            "error": f"Error generando gráfico radar: {str(e)}"
        })

@app.route('/api/grafico/barras', methods=['GET'])
def generar_grafico_barras():
    """Generar gráfico de barras apiladas"""
    try:
        print("🖼️ Generando gráfico de barras...")
        img_buffer = generador_graficos.grafico_barras_apiladas()
        img_base64 = base64.b64encode(img_buffer.getvalue()).decode()
        print("✅ Gráfico de barras generado exitosamente")
        return jsonify({
            "success": True, 
            "image": img_base64,
            "tipo": "barras"
        })
    except Exception as e:
        print(f"❌ Error generando gráfico de barras: {str(e)}")
        return jsonify({
            "success": False, 
            "error": f"Error generando gráfico de barras: {str(e)}"
        })

@app.route('/api/grafico/pastel', methods=['GET'])
def generar_grafico_pastel():
    """Generar gráfico de pastel"""
    try:
        print("🖼️ Generando gráfico de pastel...")
        img_buffer = generador_graficos.grafico_pastel()
        img_base64 = base64.b64encode(img_buffer.getvalue()).decode()
        print("✅ Gráfico de pastel generado exitosamente")
        return jsonify({
            "success": True, 
            "image": img_base64,
            "tipo": "pastel"
        })
    except Exception as e:
        print(f"❌ Error generando gráfico de pastel: {str(e)}")
        return jsonify({
            "success": False, 
            "error": f"Error generando gráfico de pastel: {str(e)}"
        })

@app.route('/api/ml/entrenar', methods=['GET'])
def entrenar_modelo():
    """Entrenar modelo de machine learning"""
    try:
        print("🤖 Iniciando entrenamiento del modelo...")
        resultado = ml_processor.entrenar_modelo_prediccion()
        
        # Limpiar datos para JSON
        resultado_limpio = limpiar_datos_json(resultado)
        
        if "error" in resultado_limpio:
            print(f"❌ Error en entrenamiento: {resultado_limpio['error']}")
            return jsonify({
                "success": False, 
                "error": resultado_limpio["error"]
            })
        
        print("✅ Modelo entrenado exitosamente")
        return jsonify({
            "success": True, 
            "result": resultado_limpio
        })
        
    except Exception as e:
        print(f"💥 Error en entrenamiento: {traceback.format_exc()}")
        return jsonify({
            "success": False, 
            "error": f"Error entrenando modelo: {str(e)}"
        })

@app.route('/api/ml/prediccion', methods=['POST'])
def predecir_desgaste():
    """Predecir desgaste de un disco duro"""
    try:
        print("🔮 Recibiendo solicitud de predicción...")
        
        if not request.json:
            return jsonify({
                "success": False, 
                "error": "No se recibieron datos en la solicitud"
            })
        
        datos_disco = request.json
        print(f"📝 Datos recibidos: {datos_disco}")
        
        resultado = ml_processor.predecir_desgaste(datos_disco)
        
        # Limpiar datos para JSON
        resultado_limpio = limpiar_datos_json(resultado)
        
        if "error" in resultado_limpio:
            print(f"❌ Error en predicción: {resultado_limpio['error']}")
            return jsonify({
                "success": False, 
                "error": resultado_limpio["error"]
            })
        
        print(f"✅ Predicción completada: {resultado_limpio}")
        return jsonify({
            "success": True, 
            "prediccion": resultado_limpio
        })
        
    except Exception as e:
        print(f"💥 Error en predicción: {traceback.format_exc()}")
        return jsonify({
            "success": False, 
            "error": f"Error realizando predicción: {str(e)}"
        })

@app.route('/api/ml/analisis', methods=['GET'])
def analizar_tendencias():
    """Analizar tendencias de desgaste - VERSIÓN CORREGIDA"""
    try:
        print("📊 Iniciando análisis de tendencias...")
        
        # Obtener análisis
        analisis = ml_processor.analizar_tendencias_desgaste()
        print(f"📈 Análisis obtenido, tipo: {type(analisis)}")
        
        # Verificar si hay error en el análisis
        if "error" in analisis:
            print(f"❌ Error en análisis: {analisis['error']}")
            return jsonify({
                "success": False, 
                "error": analisis["error"]
            })
        
        # Validar que el análisis sea serializable
        try:
            # Test de serialización con configuración estricta
            json_str = json.dumps(analisis, ensure_ascii=False, allow_nan=False)
            print(f"✅ JSON serializado correctamente, longitud: {len(json_str)}")
        except (TypeError, ValueError) as e:
            print(f"❌ Error serializando JSON: {e}")
            # Limpiar datos problemáticos
            analisis = limpiar_datos_json(analisis)
            print("✅ Datos limpiados para serialización JSON")
        
        # Crear respuesta final
        respuesta = {
            "success": True, 
            "analisis": analisis
        }
        
        # Verificación final
        try:
            json.dumps(respuesta, allow_nan=False)
            print("✅ Respuesta final válida para JSON")
            return jsonify(respuesta)
        except Exception as e:
            print(f"❌ Error en respuesta final: {e}")
            return jsonify({
                "success": False,
                "error": f"Error formateando respuesta final: {str(e)}"
            })
            
    except Exception as e:
        print(f"💥 Error general en análisis: {traceback.format_exc()}")
        return jsonify({
            "success": False, 
            "error": f"Error interno del servidor: {str(e)}"
        })

@app.route('/api/grafico/prediccion', methods=['POST'])
def generar_grafico_prediccion():
    """Generar gráfico de predicción personalizado"""
    try:
        print("🖼️ Generando gráfico de predicción...")
        
        if not request.json:
            return jsonify({
                "success": False, 
                "error": "No se recibieron datos para la predicción"
            })
        
        datos_prediccion = request.json
        img_buffer = generador_graficos.grafico_prediccion_desgaste(datos_prediccion)
        img_base64 = base64.b64encode(img_buffer.getvalue()).decode()
        
        print("✅ Gráfico de predicción generado exitosamente")
        return jsonify({
            "success": True, 
            "image": img_base64,
            "tipo": "prediccion"
        })
        
    except Exception as e:
        print(f"❌ Error generando gráfico de predicción: {str(e)}")
        return jsonify({
            "success": False, 
            "error": f"Error generando gráfico de predicción: {str(e)}"
        })

@app.route('/api/metricas', methods=['GET'])
def get_metricas_tipo():
    """Obtener métricas por tipo de disco"""
    try:
        print("📋 Obteniendo métricas por tipo...")
        datos = generador_graficos.cargar_datos()
        metricas = datos.get('metricas_por_tipo', {})
        
        print("✅ Métricas obtenidas exitosamente")
        return jsonify({
            "success": True, 
            "metricas": metricas
        })
        
    except Exception as e:
        print(f"❌ Error obteniendo métricas: {str(e)}")
        return jsonify({
            "success": False, 
            "error": f"Error obteniendo métricas: {str(e)}"
        })

@app.route('/api/estado', methods=['GET'])
def get_estado_sistema():
    """Obtener estado completo del sistema"""
    try:
        # Verificar conexión con datos
        datos = generador_graficos.cargar_datos()
        total_discos = len(datos.get('discos_duros', []))
        
        # Verificar modelo
        modelo_entrenado = ml_processor.is_trained
        
        estado = {
            "api": "activa",
            "base_datos": "conectada" if total_discos > 0 else "vacía",
            "total_discos": total_discos,
            "modelo_ml": "entrenado" if modelo_entrenado else "no entrenado",
            "endpoints_disponibles": [
                "/api/health",
                "/api/discos", 
                "/api/grafico/area",
                "/api/grafico/radar", 
                "/api/grafico/barras",
                "/api/grafico/pastel",
                "/api/ml/entrenar",
                "/api/ml/prediccion",
                "/api/ml/analisis",
                "/api/metricas",
                "/api/estado"
            ]
        }
        
        return jsonify({
            "success": True,
            "estado": estado
        })
        
    except Exception as e:
        return jsonify({
            "success": False,
            "error": f"Error obteniendo estado del sistema: {str(e)}"
        })

# Endpoints de Gestión de Discos
@app.route('/api/discos/agregar', methods=['POST'])
def agregar_disco():
    """Agregar un nuevo disco al JSON"""
    try:
        print("➕ Recibiendo solicitud para agregar disco...")
        
        if not request.json:
            return jsonify({
                "success": False, 
                "error": "No se recibieron datos del disco"
            })
        
        nuevo_disco = request.json
        
        # Validar datos requeridos
        campos_requeridos = ['tipo', 'marca', 'modelo', 'capacidad_gb', 'tiempo_uso_meses']
        for campo in campos_requeridos:
            if campo not in nuevo_disco:
                return jsonify({
                    "success": False,
                    "error": f"Campo requerido faltante: {campo}"
                })
        
        # Cargar datos actuales
        with open(DATA_PATH, 'r', encoding='utf-8') as f:
            datos = json.load(f)
        
        # Generar nuevo ID
        if datos['discos_duros']:
            nuevo_id = max(disco['id'] for disco in datos['discos_duros']) + 1
        else:
            nuevo_id = 1
        
        # Preparar disco completo
        disco_completo = {
            "id": nuevo_id,
            "tipo": nuevo_disco['tipo'],
            "marca": nuevo_disco['marca'],
            "modelo": nuevo_disco['modelo'],
            "capacidad_gb": int(nuevo_disco['capacidad_gb']),
            "tiempo_uso_meses": int(nuevo_disco['tiempo_uso_meses']),
            "horas_encendido": int(nuevo_disco.get('horas_encendido', nuevo_disco['tiempo_uso_meses'] * 720)),
            "ciclos_escritura": int(nuevo_disco.get('ciclos_escritura', nuevo_disco['tiempo_uso_meses'] * 500)),
            "temperatura_promedio": int(nuevo_disco.get('temperatura_promedio', 45)),
            "bad_sectors": int(nuevo_disco.get('bad_sectors', 0)),
            "porcentaje_desgaste": int(nuevo_disco.get('porcentaje_desgaste', min(95, nuevo_disco['tiempo_uso_meses'] * 2))),
            "estado": nuevo_disco.get('estado', 'Excelente'),
            "fecha_instalacion": nuevo_disco.get('fecha_instalacion', '2023-01-01')
        }
        
        # Calcular estado automáticamente si no se proporciona
        if 'estado' not in nuevo_disco:
            desgaste = disco_completo['porcentaje_desgaste']
            if desgaste < 20:
                disco_completo['estado'] = 'Excelente'
            elif desgaste < 40:
                disco_completo['estado'] = 'Bueno'
            elif desgaste < 60:
                disco_completo['estado'] = 'Moderado'
            elif desgaste < 80:
                disco_completo['estado'] = 'Alto'
            else:
                disco_completo['estado'] = 'Crítico'
        
        # Agregar disco
        datos['discos_duros'].append(disco_completo)
        
        # Guardar datos
        with open(DATA_PATH, 'w', encoding='utf-8') as f:
            json.dump(datos, f, indent=2, ensure_ascii=False)
        
        print(f"✅ Disco agregado exitosamente: ID {nuevo_id}")
        
        return jsonify({
            "success": True,
            "mensaje": "Disco agregado exitosamente",
            "id": nuevo_id,
            "disco": disco_completo
        })
        
    except Exception as e:
        print(f"❌ Error agregando disco: {str(e)}")
        return jsonify({
            "success": False, 
            "error": f"Error agregando disco: {str(e)}"
        })

@app.route('/api/discos/actualizar/<int:disco_id>', methods=['PUT'])
def actualizar_disco(disco_id):
    """Actualizar un disco existente"""
    try:
        print(f"✏️ Actualizando disco ID: {disco_id}")
        
        if not request.json:
            return jsonify({
                "success": False, 
                "error": "No se recibieron datos para actualizar"
            })
        
        datos_actualizados = request.json
        
        # Cargar datos actuales
        with open(DATA_PATH, 'r', encoding='utf-8') as f:
            datos = json.load(f)
        
        # Buscar disco
        disco_index = None
        for i, disco in enumerate(datos['discos_duros']):
            if disco['id'] == disco_id:
                disco_index = i
                break
        
        if disco_index is None:
            return jsonify({
                "success": False,
                "error": f"Disco con ID {disco_id} no encontrado"
            })
        
        # Actualizar disco
        for key, value in datos_actualizados.items():
            if key in datos['discos_duros'][disco_index]:
                datos['discos_duros'][disco_index][key] = value
        
        # Recalcular estado si se actualizó el desgaste
        if 'porcentaje_desgaste' in datos_actualizados:
            desgaste = datos['discos_duros'][disco_index]['porcentaje_desgaste']
            if desgaste < 20:
                datos['discos_duros'][disco_index]['estado'] = 'Excelente'
            elif desgaste < 40:
                datos['discos_duros'][disco_index]['estado'] = 'Bueno'
            elif desgaste < 60:
                datos['discos_duros'][disco_index]['estado'] = 'Moderado'
            elif desgaste < 80:
                datos['discos_duros'][disco_index]['estado'] = 'Alto'
            else:
                datos['discos_duros'][disco_index]['estado'] = 'Crítico'
        
        # Guardar datos
        with open(DATA_PATH, 'w', encoding='utf-8') as f:
            json.dump(datos, f, indent=2, ensure_ascii=False)
        
        print(f"✅ Disco {disco_id} actualizado exitosamente")
        
        return jsonify({
            "success": True,
            "mensaje": f"Disco {disco_id} actualizado exitosamente",
            "disco": datos['discos_duros'][disco_index]
        })
        
    except Exception as e:
        print(f"❌ Error actualizando disco: {str(e)}")
        return jsonify({
            "success": False, 
            "error": f"Error actualizando disco: {str(e)}"
        })

@app.route('/api/discos/eliminar/<int:disco_id>', methods=['DELETE'])
def eliminar_disco(disco_id):
    """Eliminar un disco"""
    try:
        print(f"🗑️ Eliminando disco ID: {disco_id}")
        
        # Cargar datos actuales
        with open(DATA_PATH, 'r', encoding='utf-8') as f:
            datos = json.load(f)
        
        # Buscar disco
        disco_index = None
        for i, disco in enumerate(datos['discos_duros']):
            if disco['id'] == disco_id:
                disco_index = i
                break
        
        if disco_index is None:
            return jsonify({
                "success": False,
                "error": f"Disco con ID {disco_id} no encontrado"
            })
        
        # Eliminar disco
        disco_eliminado = datos['discos_duros'].pop(disco_index)
        
        # Guardar datos
        with open(DATA_PATH, 'w', encoding='utf-8') as f:
            json.dump(datos, f, indent=2, ensure_ascii=False)
        
        print(f"✅ Disco {disco_id} eliminado exitosamente")
        
        return jsonify({
            "success": True,
            "mensaje": f"Disco {disco_id} eliminado exitosamente",
            "disco_eliminado": disco_eliminado
        })
        
    except Exception as e:
        print(f"❌ Error eliminando disco: {str(e)}")
        return jsonify({
            "success": False, 
            "error": f"Error eliminando disco: {str(e)}"
        })

# Manejo global de errores
@app.errorhandler(404)
def not_found(error):
    return jsonify({
        "success": False,
        "error": "Endpoint no encontrado",
        "message": "La ruta solicitada no existe en esta API"
    }), 404

@app.errorhandler(405)
def method_not_allowed(error):
    return jsonify({
        "success": False,
        "error": "Método no permitido",
        "message": "El método HTTP no está permitido para este endpoint"
    }), 405

@app.errorhandler(500)
def internal_server_error(error):
    return jsonify({
        "success": False,
        "error": "Error interno del servidor",
        "message": "Ocurrió un error inesperado en el servidor"
    }), 500

# Middleware para logging de requests
@app.before_request
def log_request_info():
    if request.method != 'OPTIONS':  # Ignorar preflight requests de CORS
        print(f"📍 [{request.method}] {request.path} - IP: {request.remote_addr}")

@app.after_request
def log_response_info(response):
    if request.method != 'OPTIONS':
        print(f"📍 Response: {response.status_code} - {request.path}")
    return response

# Ruta de información de la API
@app.route('/api', methods=['GET'])
def api_info():
    """Información general de la API"""
    info = {
        "nombre": "Sistema de Predicción de Desgaste de Discos Duros",
        "version": "1.0.0",
        "descripcion": "API para monitoreo y predicción del desgaste de discos duros usando Machine Learning",
        "tecnologias": [
            "Python Flask",
            "Scikit-learn",
            "Pandas",
            "Seaborn/Matplotlib",
            "PHP Frontend"
        ],
        "endpoints_principales": {
            "graficos": [
                "/api/grafico/area - Gráfico de área de desgaste",
                "/api/grafico/radar - Gráfico radar de métricas", 
                "/api/grafico/barras - Gráfico de barras apiladas",
                "/api/grafico/pastel - Gráfico de pastel de distribución"
            ],
            "machine_learning": [
                "/api/ml/entrenar - Entrenar modelo predictivo",
                "/api/ml/prediccion - Predecir desgaste (POST)",
                "/api/ml/analisis - Análisis de tendencias"
            ],
            "datos": [
                "/api/discos - Lista de discos",
                "/api/metricas - Métricas por tipo de disco",
                "/api/estado - Estado del sistema"
            ],
            "gestion": [
                "/api/discos/agregar - Agregar nuevo disco (POST)",
                "/api/discos/actualizar/<id> - Actualizar disco (PUT)",
                "/api/discos/eliminar/<id> - Eliminar disco (DELETE)"
            ]
        },
        "desarrollado_por": "Sistema de Análisis de Discos Duros"
    }
    
    return jsonify(info)

if __name__ == '__main__':
    print("🚀 Iniciando servidor Flask...")
    print("📍 API de Predicción de Desgaste de Discos Duros")
    print("📍 Endpoints disponibles:")
    print("   - http://localhost:5000/api/health")
    print("   - http://localhost:5000/api/discos") 
    print("   - http://localhost:5000/api/ml/analisis")
    print("   - http://localhost:5000/api/grafico/area")
    print("   - http://localhost:5000/api/metricas")
    print("📍 Frontend PHP: http://localhost/app/frontend/")
    print("🔧 Modo debug: activado")
    
    app.run(host='0.0.0.0', port=5000, debug=True)