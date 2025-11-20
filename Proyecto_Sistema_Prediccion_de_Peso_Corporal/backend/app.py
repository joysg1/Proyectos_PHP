from flask import Flask, request, jsonify, send_file
from flask_cors import CORS
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import seaborn as sns
import pandas as pd
import numpy as np
import json
import os
import io
from ml_model import PesoPredictor

app = Flask(__name__)
CORS(app)
predictor = PesoPredictor()

# Configuración de estilos
plt.style.use('default')
sns.set_palette("husl")

@app.route('/api/estadisticas', methods=['GET'])
def obtener_estadisticas():
    try:
        resultado = predictor.obtener_estadisticas()
        return jsonify(resultado)
    except Exception as e:
        app.logger.error(f"Error en estadísticas: {str(e)}")
        return jsonify({"error": f"Error obteniendo estadísticas: {str(e)}"}), 500

@app.route('/api/entrenar', methods=['POST'])
def entrenar_modelo():
    try:
        resultado = predictor.entrenar_modelo()
        return jsonify(resultado)
    except Exception as e:
        app.logger.error(f"Error entrenando modelo: {str(e)}")
        return jsonify({"error": f"Error entrenando modelo: {str(e)}"}), 500

@app.route('/api/predecir', methods=['POST'])
def predecir_peso():
    try:
        data = request.get_json()
        if not data:
            return jsonify({"error": "No se proporcionaron datos"}), 400
            
        # Validar campos requeridos
        required_fields = ['calorias', 'edad', 'altura', 'actividad']
        for field in required_fields:
            if field not in data:
                return jsonify({"error": f"Campo requerido faltante: {field}"}), 400
        
        resultado = predictor.predecir_cambio_peso(
            calorias=data['calorias'],
            edad=data['edad'],
            altura=data['altura'],
            actividad=data['actividad']
        )
        return jsonify(resultado)
    except Exception as e:
        app.logger.error(f"Error en predicción: {str(e)}")
        return jsonify({"error": f"Error en predicción: {str(e)}"}), 500

@app.route('/api/registros', methods=['GET', 'POST', 'PUT', 'DELETE'])
def gestionar_registros():
    try:
        if request.method == 'GET':
            datos = predictor.cargar_datos()
            return jsonify(datos)
        
        elif request.method == 'POST':
            nuevo_registro = request.get_json()
            if not nuevo_registro:
                return jsonify({"error": "No se proporcionaron datos"}), 400
            resultado = predictor.agregar_registro(nuevo_registro)
            return jsonify(resultado)
        
        elif request.method == 'PUT':
            registro_actualizado = request.get_json()
            if not registro_actualizado:
                return jsonify({"error": "No se proporcionaron datos"}), 400
            resultado = predictor.actualizar_registro(registro_actualizado)
            return jsonify(resultado)
        
        elif request.method == 'DELETE':
            data = request.get_json()
            if not data or 'id' not in data:
                return jsonify({"error": "Se requiere ID para eliminar"}), 400
            resultado = predictor.eliminar_registro(data['id'])
            return jsonify(resultado)
            
    except Exception as e:
        app.logger.error(f"Error gestionando registros: {str(e)}")
        return jsonify({"error": f"Error gestionando registros: {str(e)}"}), 500

@app.route('/api/grafico/area', methods=['GET'])
def grafico_area():
    try:
        datos = predictor.cargar_datos()
        if not datos or 'registros' not in datos or len(datos['registros']) == 0:
            return jsonify({"error": "No hay datos para generar el gráfico"}), 400
        
        df = pd.DataFrame(datos['registros'])
        
        # Asegurar que los datos sean numéricos
        df['peso'] = pd.to_numeric(df['peso'], errors='coerce')
        df = df.dropna(subset=['peso'])
        
        if len(df) == 0:
            return jsonify({"error": "No hay datos válidos para el gráfico"}), 400
        
        df = df.sort_values('fecha')
        
        fig, ax = plt.subplots(figsize=(12, 6))
        
        # Crear índice numérico para el eje X
        x = range(len(df))
        y = df['peso'].values
        
        # Gráfico de área
        ax.fill_between(x, y, alpha=0.3, color='#3b82f6')
        ax.plot(x, y, marker='o', linewidth=2, color='#1d4ed8')
        
        ax.set_title('Evolución del Peso a lo Largo del Tiempo', fontsize=14, fontweight='bold', pad=20)
        ax.set_xlabel('Número de Registro', fontsize=12)
        ax.set_ylabel('Peso (kg)', fontsize=12)
        ax.grid(True, alpha=0.3)
        
        # Mejorar estética
        ax.spines['top'].set_visible(False)
        ax.spines['right'].set_visible(False)
        
        plt.tight_layout()
        
        img = io.BytesIO()
        plt.savefig(img, format='png', dpi=100, bbox_inches='tight')
        img.seek(0)
        plt.close(fig)
        
        return send_file(img, mimetype='image/png')
    except Exception as e:
        app.logger.error(f"Error generando gráfico de área: {str(e)}")
        return jsonify({"error": f"Error generando gráfico: {str(e)}"}), 500

@app.route('/api/grafico/radar', methods=['GET'])
def grafico_radar():
    try:
        datos = predictor.cargar_datos()
        if not datos or 'registros' not in datos or len(datos['registros']) == 0:
            return jsonify({"error": "No hay datos para generar el gráfico"}), 400
        
        df = pd.DataFrame(datos['registros'])
        
        # Asegurar que los datos sean numéricos
        df['calorias'] = pd.to_numeric(df['calorias'], errors='coerce')
        df['peso'] = pd.to_numeric(df['peso'], errors='coerce')
        df['edad'] = pd.to_numeric(df['edad'], errors='coerce')
        df['altura'] = pd.to_numeric(df['altura'], errors='coerce')
        df = df.dropna()
        
        if len(df) == 0:
            return jsonify({"error": "No hay datos válidos para el gráfico"}), 400
        
        metrics = ['Calorías', 'Peso', 'Actividad', 'Edad', 'Altura']
        
        # Valores normalizados con rangos realistas
        calorias_norm = min(1.0, max(0.1, df['calorias'].mean() / 3000))
        peso_norm = min(1.0, max(0.1, df['peso'].mean() / 100))
        
        # Mapeo de actividad a numérico
        actividad_map = {'baja': 0.3, 'moderada': 0.6, 'alta': 0.9}
        df['actividad_num'] = df['actividad'].map(actividad_map)
        actividad_norm = df['actividad_num'].mean()
        
        edad_norm = min(1.0, max(0.1, df['edad'].mean() / 80))
        altura_norm = min(1.0, max(0.1, df['altura'].mean() / 200))
        
        values = [calorias_norm, peso_norm, actividad_norm, edad_norm, altura_norm]
        values += values[:1]  # Cerrar el polígono
        
        angles = np.linspace(0, 2 * np.pi, len(metrics), endpoint=False).tolist()
        angles += angles[:1]
        
        fig, ax = plt.subplots(figsize=(10, 10), subplot_kw=dict(projection='polar'))
        
        # Dibujar el polígono
        ax.plot(angles, values, 'o-', linewidth=2, color='#3b82f6')
        ax.fill(angles, values, alpha=0.25, color='#3b82f6')
        
        # Configurar ejes
        ax.set_xticks(angles[:-1])
        ax.set_xticklabels(metrics, fontsize=11)
        ax.set_ylim(0, 1)
        ax.set_yticks([0.2, 0.4, 0.6, 0.8, 1.0])
        ax.set_yticklabels(['0.2', '0.4', '0.6', '0.8', '1.0'], fontsize=9)
        ax.grid(True)
        
        ax.set_title('Perfil de Métricas de Salud', fontsize=14, fontweight='bold', pad=20)
        
        plt.tight_layout()
        
        img = io.BytesIO()
        plt.savefig(img, format='png', dpi=100, bbox_inches='tight')
        img.seek(0)
        plt.close(fig)
        
        return send_file(img, mimetype='image/png')
    except Exception as e:
        app.logger.error(f"Error generando gráfico radar: {str(e)}")
        return jsonify({"error": f"Error generando gráfico radar: {str(e)}"}), 500

@app.route('/api/grafico/barras', methods=['GET'])
def grafico_barras():
    try:
        datos = predictor.cargar_datos()
        if not datos or 'registros' not in datos or len(datos['registros']) == 0:
            return jsonify({"error": "No hay datos para generar el gráfico"}), 400
        
        df = pd.DataFrame(datos['registros'])
        
        # Asegurar que los datos sean numéricos
        df['calorias'] = pd.to_numeric(df['calorias'], errors='coerce')
        df['peso'] = pd.to_numeric(df['peso'], errors='coerce')
        df = df.dropna()
        
        if len(df) == 0:
            return jsonify({"error": "No hay datos válidos para el gráfico"}), 400
        
        # Agrupar por actividad
        actividad_data = df.groupby('actividad').agg({
            'calorias': 'mean',
            'peso': 'mean'
        }).reset_index()
        
        if actividad_data.empty:
            return jsonify({"error": "No hay datos de actividad"}), 400
        
        fig, (ax1, ax2) = plt.subplots(1, 2, figsize=(14, 6))
        
        # Gráfico de calorías
        actividades = [act.capitalize() for act in actividad_data['actividad']]
        calorias = actividad_data['calorias'].values
        
        bars1 = ax1.bar(actividades, calorias, color=['#ef4444', '#f59e0b', '#10b981'], alpha=0.8)
        ax1.set_title('Calorías Promedio por Actividad', fontsize=13, fontweight='bold')
        ax1.set_ylabel('Calorías Promedio', fontsize=11)
        ax1.tick_params(axis='x', rotation=45)
        ax1.grid(True, alpha=0.3, axis='y')
        
        # Añadir valores en las barras
        for bar in bars1:
            height = bar.get_height()
            ax1.text(bar.get_x() + bar.get_width()/2., height + 50,
                    f'{height:.0f}', ha='center', va='bottom', fontweight='bold')
        
        # Gráfico de peso
        pesos = actividad_data['peso'].values
        
        bars2 = ax2.bar(actividades, pesos, color=['#ef4444', '#f59e0b', '#10b981'], alpha=0.8)
        ax2.set_title('Peso Promedio por Actividad', fontsize=13, fontweight='bold')
        ax2.set_ylabel('Peso Promedio (kg)', fontsize=11)
        ax2.tick_params(axis='x', rotation=45)
        ax2.grid(True, alpha=0.3, axis='y')
        
        # Añadir valores en las barras
        for bar in bars2:
            height = bar.get_height()
            ax2.text(bar.get_x() + bar.get_width()/2., height + 1,
                    f'{height:.1f}', ha='center', va='bottom', fontweight='bold')
        
        plt.tight_layout()
        
        img = io.BytesIO()
        plt.savefig(img, format='png', dpi=100, bbox_inches='tight')
        img.seek(0)
        plt.close(fig)
        
        return send_file(img, mimetype='image/png')
    except Exception as e:
        app.logger.error(f"Error generando gráfico de barras: {str(e)}")
        return jsonify({"error": f"Error generando gráfico de barras: {str(e)}"}), 500

@app.route('/api/grafico/pastel', methods=['GET'])
def grafico_pastel():
    try:
        datos = predictor.cargar_datos()
        if not datos or 'registros' not in datos or len(datos['registros']) == 0:
            return jsonify({"error": "No hay datos para generar el gráfico"}), 400
        
        df = pd.DataFrame(datos['registros'])
        actividad_counts = df['actividad'].value_counts()
        
        if actividad_counts.empty:
            return jsonify({"error": "No hay datos de actividad"}), 400
        
        fig, ax = plt.subplots(figsize=(8, 8))
        
        colors = ['#ef4444', '#f59e0b', '#10b981']
        wedges, texts, autotexts = ax.pie(
            actividad_counts.values, 
            labels=[act.capitalize() for act in actividad_counts.index],
            autopct='%1.1f%%', 
            colors=colors[:len(actividad_counts)], 
            startangle=90,
            textprops={'fontsize': 12}
        )
        
        # Mejorar los textos de porcentaje
        for autotext in autotexts:
            autotext.set_color('white')
            autotext.set_fontweight('bold')
        
        ax.set_title('Distribución por Nivel de Actividad', fontsize=14, fontweight='bold', pad=20)
        
        plt.tight_layout()
        
        img = io.BytesIO()
        plt.savefig(img, format='png', dpi=100, bbox_inches='tight')
        img.seek(0)
        plt.close(fig)
        
        return send_file(img, mimetype='image/png')
    except Exception as e:
        app.logger.error(f"Error generando gráfico de pastel: {str(e)}")
        return jsonify({"error": f"Error generando gráfico de pastel: {str(e)}"}), 500

@app.route('/api/grafico/lineas', methods=['GET'])
def grafico_lineas():
    try:
        datos = predictor.cargar_datos()
        if not datos or 'registros' not in datos or len(datos['registros']) == 0:
            return jsonify({"error": "No hay datos para generar el gráfico"}), 400
        
        df = pd.DataFrame(datos['registros'])
        
        # Asegurar que los datos sean numéricos
        df['peso'] = pd.to_numeric(df['peso'], errors='coerce')
        df = df.dropna(subset=['peso'])
        
        if len(df) == 0:
            return jsonify({"error": "No hay datos válidos para el gráfico"}), 400
        
        df = df.sort_values('fecha')
        
        fig, ax = plt.subplots(figsize=(12, 6))
        
        x = range(len(df))
        y = df['peso'].values
        
        ax.plot(x, y, marker='o', linewidth=2, color='#3b82f6', markersize=4)
        ax.set_title('Tendencia de Peso a lo Largo del Tiempo', fontsize=14, fontweight='bold', pad=20)
        ax.set_xlabel('Número de Registro', fontsize=12)
        ax.set_ylabel('Peso (kg)', fontsize=12)
        ax.grid(True, alpha=0.3)
        
        # Mejorar estética
        ax.spines['top'].set_visible(False)
        ax.spines['right'].set_visible(False)
        
        plt.tight_layout()
        
        img = io.BytesIO()
        plt.savefig(img, format='png', dpi=100, bbox_inches='tight')
        img.seek(0)
        plt.close(fig)
        
        return send_file(img, mimetype='image/png')
    except Exception as e:
        app.logger.error(f"Error generando gráfico de líneas: {str(e)}")
        return jsonify({"error": f"Error generando gráfico de líneas: {str(e)}"}), 500

@app.route('/api/health', methods=['GET'])
def health_check():
    try:
        datos = predictor.cargar_datos()
        total_registros = len(datos.get('registros', []))
        
        return jsonify({
            "status": "ok", 
            "message": "API funcionando correctamente",
            "model_loaded": predictor.model_trained,
            "total_registros": total_registros
        })
    except Exception as e:
        app.logger.error(f"Error en health check: {str(e)}")
        return jsonify({
            "status": "error",
            "message": f"Error en health check: {str(e)}"
        }), 500

if __name__ == '__main__':
    print("Iniciando servidor...")
    app.run(host='0.0.0.0', port=5000, debug=True)