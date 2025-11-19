from flask import Flask, jsonify, request
from flask_cors import CORS
import json
import os
import sys

# Agregar el directorio actual al path
sys.path.append(os.path.dirname(__file__))

try:
    from ml_analysis import analyze_impact, generate_predictions, get_economic_trends, get_trade_categories
    print("✅ Módulos de ml_analysis importados correctamente")
except ImportError as e:
    print(f"❌ Error importando módulos: {e}")
    exit(1)

app = Flask(__name__)
CORS(app)

# Configuración
DATA_FILE = os.path.join(os.path.dirname(__file__), 'data', 'ww1_data.json')

def load_data():
    """Cargar datos desde el archivo JSON"""
    try:
        with open(DATA_FILE, 'r', encoding='utf-8') as f:
            data = json.load(f)
        print(f"✅ Datos cargados: {len(data['countries'])} países")
        return data
    except Exception as e:
        print(f"❌ Error cargando datos: {e}")
        return {"error": f"Error cargando datos: {str(e)}"}

@app.route('/api/data', methods=['GET'])
def get_all_data():
    """Endpoint para obtener todos los datos"""
    data = load_data()
    return jsonify(data)

@app.route('/api/analysis/economic', methods=['GET'])
def economic_analysis():
    """Endpoint para análisis económico"""
    data = load_data()
    if 'error' in data:
        return jsonify(data), 500
    
    analysis = analyze_impact(data, 'economic')
    return jsonify(analysis)

@app.route('/api/analysis/social', methods=['GET'])
def social_analysis():
    """Endpoint para análisis social"""
    data = load_data()
    if 'error' in data:
        return jsonify(data), 500
    
    analysis = analyze_impact(data, 'social')
    return jsonify(analysis)

@app.route('/api/predictions', methods=['GET'])
def get_predictions():
    """Endpoint para predicciones"""
    data = load_data()
    if 'error' in data:
        return jsonify(data), 500
    
    predictions = generate_predictions(data)
    return jsonify(predictions)

@app.route('/api/charts/economic-trends', methods=['GET'])
def economic_trends():
    """Endpoint para tendencias económicas"""
    data = load_data()
    if 'error' in data:
        return jsonify(data), 500
    
    trends_data = get_economic_trends(data)
    return jsonify(trends_data)

@app.route('/api/charts/trade-categories', methods=['GET'])
def trade_categories():
    """Endpoint para categorías de comercio"""
    data = load_data()
    if 'error' in data:
        return jsonify(data), 500
    
    categories_data = get_trade_categories(data)
    return jsonify(categories_data)

@app.route('/api/health', methods=['GET'])
def health_check():
    """Endpoint para verificar el estado del API"""
    return jsonify({
        'status': 'healthy', 
        'message': 'API Python funcionando correctamente',
        'endpoints': {
            '/api/data': 'Todos los datos',
            '/api/analysis/economic': 'Análisis económico',
            '/api/analysis/social': 'Análisis social',
            '/api/predictions': 'Predicciones ML',
            '/api/charts/economic-trends': 'Tendencias económicas',
            '/api/charts/trade-categories': 'Categorías de comercio',
            '/api/health': 'Estado del API'
        }
    })

if __name__ == '__main__':
    print("🚀 Iniciando servidor API Python en http://localhost:5000")
    print("📊 Endpoints disponibles:")
    
    # Verificar archivo de datos
    if os.path.exists(DATA_FILE):
        print(f"✅ Archivo de datos encontrado: {DATA_FILE}")
    else:
        print(f"❌ Archivo de datos NO encontrado: {DATA_FILE}")
    
    app.run(debug=True, port=5000, host='0.0.0.0')
