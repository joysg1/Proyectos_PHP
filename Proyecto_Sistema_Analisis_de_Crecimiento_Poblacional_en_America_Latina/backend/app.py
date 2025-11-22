from flask import Flask, jsonify, request
from flask_cors import CORS
import matplotlib
matplotlib.use('Agg')  # Usar backend no interactivo
import matplotlib.pyplot as plt
import seaborn as sns
import pandas as pd
import numpy as np
import io
import base64
import os
from database import Database
from ml_processor import MLProcessor

app = Flask(__name__)
CORS(app)

# Configuración
app.config['JSON_AS_ASCII'] = False

# Inicializar base de datos y procesador ML
db = Database()
ml_processor = MLProcessor(db)

# Configurar estilo profesional
plt.style.use('seaborn-v0_8')
sns.set_palette("husl")

@app.route('/')
def home():
    return jsonify({"message": "API de Análisis de Crecimiento Poblacional - América Latina"})

# Rutas CRUD para países
@app.route('/api/paises', methods=['GET'])
def get_paises():
    paises = db.get_all_countries()
    return jsonify(paises)

@app.route('/api/paises/<int:country_id>', methods=['GET'])
def get_pais(country_id):
    pais = db.get_country_by_id(country_id)
    if pais:
        return jsonify(pais)
    return jsonify({"error": "País no encontrado"}), 404

@app.route('/api/paises', methods=['POST'])
def add_pais():
    data = request.get_json()
    if db.add_country(data):
        return jsonify({"message": "País agregado exitosamente"})
    return jsonify({"error": "Error al agregar país"}), 500

@app.route('/api/paises/<int:country_id>', methods=['PUT'])
def update_pais(country_id):
    data = request.get_json()
    if db.update_country(country_id, data):
        return jsonify({"message": "País actualizado exitosamente"})
    return jsonify({"error": "Error al actualizar país"}), 500

@app.route('/api/paises/<int:country_id>', methods=['DELETE'])
def delete_pais(country_id):
    if db.delete_country(country_id):
        return jsonify({"message": "País eliminado exitosamente"})
    return jsonify({"error": "Error al eliminar país"}), 500

# Rutas para análisis y gráficos
@app.route('/api/analisis/regional', methods=['GET'])
def analisis_regional():
    analisis = ml_processor.get_regional_analysis()
    return jsonify(analisis)

@app.route('/api/prediccion/<string:country_name>', methods=['GET'])
def prediccion_crecimiento(country_name):
    prediccion = ml_processor.predict_growth(country_name)
    if prediccion:
        return jsonify(prediccion)
    return jsonify({"error": "País no encontrado"}), 404

@app.route('/api/clusters', methods=['GET'])
def get_clusters():
    n_clusters = request.args.get('n_clusters', 3, type=int)
    clusters = ml_processor.cluster_countries(n_clusters)
    return jsonify(clusters)

@app.route('/api/comparacion', methods=['GET'])
def comparar_paises():
    pais1 = request.args.get('pais1')
    pais2 = request.args.get('pais2')
    comparacion = ml_processor.get_country_comparison(pais1, pais2)
    if comparacion:
        return jsonify(comparacion)
    return jsonify({"error": "Uno o ambos países no encontrados"}), 404

# Rutas para gráficos - ESTÉTICA MEJORADA
@app.route('/api/graficos/area', methods=['GET'])
def grafico_area():
    paises = db.get_all_countries()
    
    # Preparar datos para gráfico de líneas profesional
    years = [2020, 2021, 2022, 2023]
    
    # Crear figura con estilo profesional
    fig, ax = plt.subplots(figsize=(16, 10))
    
    # Configurar estilo de fondo elegante
    fig.patch.set_facecolor('#1e1e1e')
    ax.set_facecolor('#2d2d2d')
    
    # Paleta de colores profesional
    colors = [
        '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD',
        '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9', '#F8C471', '#82E0AA'
    ]
    
    # Ordenar países por población 2023
    paises_ordenados = sorted(paises, key=lambda x: x['poblacion_2023'], reverse=True)
    
    # Crear gráfico de líneas para cada país
    for i, pais in enumerate(paises_ordenados):
        poblaciones = [
            pais['poblacion_2020'],
            pais['poblacion_2021'],
            pais['poblacion_2022'], 
            pais['poblacion_2023']
        ]
        
        # Línea principal con estilo mejorado
        line = ax.plot(years, poblaciones, 
                      label=f"{pais['nombre']}",
                      color=colors[i % len(colors)], 
                      linewidth=3.5,
                      marker='o',
                      markersize=8,
                      markerfacecolor='white',
                      markeredgecolor=colors[i % len(colors)],
                      markeredgewidth=2,
                      alpha=0.9)
        
        # Área sombreada sutil
        ax.fill_between(years, poblaciones, alpha=0.15, color=colors[i % len(colors)])
        
        # Añadir etiqueta del último punto con estilo profesional
        ax.annotate(f"{pais['nombre']}\n{format_number(poblaciones[-1])}", 
                   xy=(2023, poblaciones[-1]),
                   xytext=(15, 0), textcoords='offset points',
                   fontsize=10, fontweight='bold', color='white',
                   bbox=dict(boxstyle="round,pad=0.4", 
                            facecolor=colors[i % len(colors)], 
                            alpha=0.9,
                            edgecolor='white',
                            linewidth=1))
    
    # Configurar el gráfico con estilo profesional
    ax.set_title('🌎 Evolución Poblacional en América Latina\nTendencias de Crecimiento 2020-2023', 
                fontsize=20, fontweight='bold', pad=30, color='white',
                fontfamily='DejaVu Sans')
    ax.set_xlabel('Año', fontsize=14, fontweight='bold', color='white', fontfamily='DejaVu Sans')
    ax.set_ylabel('Población (Millones)', fontsize=14, fontweight='bold', color='white', fontfamily='DejaVu Sans')
    
    # Formatear eje Y profesionalmente
    ax.yaxis.set_major_formatter(plt.FuncFormatter(lambda x, p: f'{x/1e6:.0f}M'))
    
    # Grid sutil y elegante
    ax.grid(True, alpha=0.2, linestyle='--', color='white')
    ax.tick_params(colors='white', labelsize=11)
    
    # Configurar ejes
    ax.spines['bottom'].set_color('white')
    ax.spines['left'].set_color('white')
    ax.spines['top'].set_visible(False)
    ax.spines['right'].set_visible(False)
    
    # Leyenda profesional
    legend = ax.legend(bbox_to_anchor=(1.05, 1), loc='upper left',
                      frameon=True, fancybox=True, shadow=True,
                      fontsize=11, facecolor='#2d2d2d', edgecolor='white',
                      labelcolor='white')
    legend.get_frame().set_alpha(0.9)
    
    # Añadir anotación de calidad profesional
    ax.text(0.02, 0.98, '📈 Análisis de Tendencias Poblacionales', 
            transform=ax.transAxes, fontsize=12, fontweight='bold',
            color='#4ECDC4', va='top',
            bbox=dict(boxstyle="round,pad=0.5", facecolor='#1e1e1e', alpha=0.8))
    
    plt.tight_layout()
    
    # Convertir a base64 con alta calidad
    buffer = io.BytesIO()
    plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight', 
                facecolor='#1e1e1e', edgecolor='none',
                transparent=False)
    buffer.seek(0)
    image_base64 = base64.b64encode(buffer.getvalue()).decode()
    plt.close()
    
    return jsonify({"image": f"data:image/png;base64,{image_base64}"})

@app.route('/api/graficos/radar', methods=['GET'])
def grafico_radar():
    paises = db.get_all_countries()
    if not paises:
        return jsonify({"error": "No hay datos disponibles"}), 404
    
    # Seleccionar indicadores para el radar
    categorias = ['tasa_crecimiento', 'expectativa_vida', 'tasa_natalidad', 
                 'poblacion_urbana', 'densidad_poblacional']
    labels = ['Crecimiento %', 'Expectativa Vida', 'Tasa Natalidad', 
             'Población Urbana', 'Densidad Pob.']
    
    # Crear figura profesional
    fig, axes = plt.subplots(2, 3, figsize=(20, 12), 
                           subplot_kw=dict(projection='polar'))
    axes = axes.flatten()
    
    # Configurar estilo de fondo
    fig.patch.set_facecolor('#1e1e1e')
    
    # Colores profesionales
    colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD']
    
    for idx, (pais, color) in enumerate(zip(paises[:6], colors)):
        ax = axes[idx]
        ax.set_facecolor('#2d2d2d')
        
        # Preparar datos normalizados
        valores_pais = []
        for categoria in categorias:
            valor = pais[categoria]
            max_val = max([p[categoria] for p in paises])
            scaled_val = valor / max_val
            valores_pais.append(scaled_val)
        
        # Completar el círculo
        valores_radar = valores_pais + [valores_pais[0]]
        angulos = np.linspace(0, 2*np.pi, len(labels), endpoint=False).tolist()
        angulos += [angulos[0]]
        
        # Dibujar radar con estilo profesional
        ax.plot(angulos, valores_radar, 'o-', linewidth=2.5, 
               color=color, markersize=6, alpha=0.9)
        ax.fill(angulos, valores_radar, alpha=0.25, color=color)
        
        # Configurar ejes con estilo profesional
        ax.set_xticks(angulos[:-1])
        ax.set_xticklabels(labels, fontsize=9, fontweight='bold', color='white')
        ax.set_ylim(0, 1)
        ax.set_yticks([0.2, 0.4, 0.6, 0.8, 1.0])
        ax.set_yticklabels(['20%', '40%', '60%', '80%', '100%'], 
                          fontsize=8, color='white', alpha=0.7)
        
        # Título del radar
        ax.set_title(f'📍 {pais["nombre"]}', size=14, fontweight='bold', 
                    ha='center', color='white', pad=20)
        
        # Grid profesional
        ax.grid(True, alpha=0.3, color='white')
        
        # Ejes transparentes
        ax.spines['polar'].set_visible(False)
    
    plt.suptitle('📊 Perfiles Poblacionales - Análisis Comparativo por País', 
                 fontsize=22, fontweight='bold', y=0.95, color='white',
                 fontfamily='DejaVu Sans')
    plt.tight_layout()
    
    # Convertir a base64
    buffer = io.BytesIO()
    plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight', 
                facecolor='#1e1e1e', edgecolor='none')
    buffer.seek(0)
    image_base64 = base64.b64encode(buffer.getvalue()).decode()
    plt.close()
    
    return jsonify({"image": f"data:image/png;base64,{image_base64}"})

@app.route('/api/graficos/barras-apiladas', methods=['GET'])
def grafico_barras_apiladas():
    paises = db.get_all_countries()
    
    # Preparar datos para gráfico de pirámide poblacional horizontal mejorado
    nombres_paises = [pais['nombre'] for pais in paises]
    
    # Datos para grupos de edad
    jovenes = [pais['grupos_edad']['0_14'] for pais in paises]
    adultos = [pais['grupos_edad']['15_64'] for pais in paises]
    mayores = [pais['grupos_edad']['65_plus'] for pais in paises]
    
    # Crear figura profesional
    fig, ax = plt.subplots(figsize=(18, 12))
    
    # Configurar estilo de fondo elegante
    fig.patch.set_facecolor('#1e1e1e')
    ax.set_facecolor('#2d2d2d')
    
    # Posiciones de las barras
    y_pos = np.arange(len(nombres_paises))
    bar_height = 0.28
    
    # Colores profesionales para grupos de edad
    colors = ['#FF6B6B', '#4ECDC4', '#45B7D1']
    
    # Crear barras horizontales con estilo profesional
    bars1 = ax.barh(y_pos - bar_height, jovenes, bar_height, 
                   label='👶 0-14 años', color=colors[0], alpha=0.85, 
                   edgecolor='white', linewidth=1.5)
    bars2 = ax.barh(y_pos, adultos, bar_height, 
                   label='👥 15-64 años', color=colors[1], alpha=0.85, 
                   edgecolor='white', linewidth=1.5)
    bars3 = ax.barh(y_pos + bar_height, mayores, bar_height, 
                   label='👵 65+ años', color=colors[2], alpha=0.85, 
                   edgecolor='white', linewidth=1.5)
    
    # Añadir valores en las barras con estilo profesional
    for bars, color in zip([bars1, bars2, bars3], colors):
        for bar in bars:
            width = bar.get_width()
            if width > 3:
                ax.text(width + 0.8, bar.get_y() + bar.get_height()/2,
                       f'{width:.1f}%', ha='left', va='center',
                       fontsize=10, fontweight='bold', color='white',
                       bbox=dict(boxstyle="round,pad=0.3", facecolor=color, 
                                alpha=0.8, edgecolor='white'))
    
    # Configurar el gráfico con estilo profesional
    ax.set_xlabel('Porcentaje de Población (%)', fontsize=14, fontweight='bold', 
                 color='white', fontfamily='DejaVu Sans')
    ax.set_ylabel('Países', fontsize=14, fontweight='bold', color='white',
                 fontfamily='DejaVu Sans')
    ax.set_yticks(y_pos)
    ax.set_yticklabels(nombres_paises, color='white', fontsize=11, fontweight='bold')
    ax.tick_params(colors='white', labelsize=11)
    
    ax.set_title('🏛️  Estructura Poblacional por Grupos de Edad\nAnálisis Demográfico de América Latina', 
                fontsize=20, fontweight='bold', pad=40, color='white',
                fontfamily='DejaVu Sans')
    
    # Grid sutil y profesional
    ax.grid(True, alpha=0.15, axis='x', linestyle='-', color='white')
    ax.set_axisbelow(True)
    
    # Configurar ejes
    ax.spines['bottom'].set_color('white')
    ax.spines['left'].set_color('white')
    ax.spines['top'].set_visible(False)
    ax.spines['right'].set_visible(False)
    
    # Leyenda profesional
    legend = ax.legend(bbox_to_anchor=(1.05, 1), loc='upper left',
                      frameon=True, fancybox=True, shadow=True,
                      fontsize=12, facecolor='#2d2d2d', edgecolor='white',
                      labelcolor='white')
    legend.get_frame().set_alpha(0.9)
    
    # Añadir anotación explicativa profesional
    ax.text(0.02, 0.98, '📋 Composición Demográfica', 
            transform=ax.transAxes, fontsize=12, fontweight='bold',
            color='#4ECDC4', va='top',
            bbox=dict(boxstyle="round,pad=0.5", facecolor='#1e1e1e', alpha=0.8))
    
    plt.tight_layout()
    
    # Convertir a base64
    buffer = io.BytesIO()
    plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight', 
                facecolor='#1e1e1e', edgecolor='none')
    buffer.seek(0)
    image_base64 = base64.b64encode(buffer.getvalue()).decode()
    plt.close()
    
    return jsonify({"image": f"data:image/png;base64,{image_base64}"})

@app.route('/api/graficos/pastel', methods=['GET'])
def grafico_pastel():
    paises = db.get_all_countries()
    
    # Calcular distribución regional
    poblaciones = [pais['poblacion_2023'] for pais in paises]
    nombres = [pais['nombre'] for pais in paises]
    total = sum(poblaciones)
    
    # Crear gráfico de pastel profesional
    fig, ax = plt.subplots(figsize=(14, 10))
    fig.patch.set_facecolor('#1e1e1e')
    
    # Colores profesionales
    colors = plt.cm.Set3(np.linspace(0, 1, len(paises)))
    
    # Explode para destacar el país más poblado
    explode = [0.05 if pop == max(poblaciones) else 0 for pop in poblaciones]
    
    wedges, texts, autotexts = ax.pie(
        poblaciones, 
        labels=nombres, 
        colors=colors,
        autopct='%1.1f%%',
        startangle=90,
        explode=explode,
        shadow=True,
        textprops={'fontsize': 11, 'color': 'white', 'fontweight': 'bold'},
        wedgeprops={'edgecolor': 'white', 'linewidth': 2, 'alpha': 0.9}
    )
    
    # Mejorar porcentajes
    for autotext in autotexts:
        autotext.set_color('white')
        autotext.set_fontweight('bold')
        autotext.set_fontsize(10)
    
    ax.set_title('🌐 Distribución Poblacional Regional 2023\nAmérica Latina', 
                fontsize=18, fontweight='bold', pad=30, color='white',
                fontfamily='DejaVu Sans')
    
    # Añadir anotación del total
    ax.text(0, -1.2, f'Población Total: {format_number(total)} habitantes', 
            ha='center', va='center', fontsize=12, fontweight='bold',
            color='#4ECDC4', 
            bbox=dict(boxstyle="round,pad=0.5", facecolor='#2d2d2d', alpha=0.8))
    
    plt.tight_layout()
    
    # Convertir a base64
    buffer = io.BytesIO()
    plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight', 
                facecolor='#1e1e1e', edgecolor='none')
    buffer.seek(0)
    image_base64 = base64.b64encode(buffer.getvalue()).decode()
    plt.close()
    
    return jsonify({"image": f"data:image/png;base64,{image_base64}"})

@app.route('/api/graficos/comparacion-radar', methods=['GET'])
def grafico_comparacion_radar():
    pais1 = request.args.get('pais1')
    pais2 = request.args.get('pais2')
    
    if not pais1 or not pais2:
        return jsonify({"error": "Se requieren dos países para comparar"}), 400
    
    comparacion = ml_processor.get_country_comparison(pais1, pais2)
    if not comparacion:
        return jsonify({"error": "Países no encontrados"}), 404
    
    # Preparar datos para radar profesional
    categorias = ['tasa_crecimiento', 'expectativa_vida', 'tasa_natalidad', 
                 'poblacion_urbana', 'densidad_poblacional']
    labels = ['Crecimiento %', 'Expectativa Vida', 'Tasa Natalidad', 
             'Población Urbana', 'Densidad Pob.']
    
    # Crear figura profesional
    fig, ax = plt.subplots(figsize=(12, 9), subplot_kw=dict(projection='polar'))
    fig.patch.set_facecolor('#1e1e1e')
    ax.set_facecolor('#2d2d2d')
    
    # Colores profesionales
    colors = ['#FF6B6B', '#4ECDC4']
    
    for i, pais in enumerate(comparacion['paises']):
        valores_pais = []
        for categoria in categorias:
            valor = comparacion[categoria][i]
            max_val = max(comparacion[categoria])
            scaled_val = valor / max_val if max_val > 0 else 0
            valores_pais.append(scaled_val)
        
        valores_radar = valores_pais + [valores_pais[0]]
        angulos = np.linspace(0, 2*np.pi, len(labels), endpoint=False).tolist()
        angulos += [angulos[0]]
        
        # Línea con estilo profesional
        ax.plot(angulos, valores_radar, 'o-', linewidth=3, 
               label=pais, color=colors[i], markersize=8,
               markerfacecolor='white', markeredgecolor=colors[i],
               markeredgewidth=2)
        ax.fill(angulos, valores_radar, alpha=0.25, color=colors[i])
    
    # Configurar radar profesional
    ax.set_xticks(angulos[:-1])
    ax.set_xticklabels(labels, fontsize=11, fontweight='bold', color='white')
    ax.set_ylim(0, 1)
    ax.set_yticks([0.2, 0.4, 0.6, 0.8, 1.0])
    ax.set_yticklabels(['20%', '40%', '60%', '80%', '100%'], 
                      fontsize=9, color='white', alpha=0.7)
    ax.grid(True, alpha=0.3, color='white')
    ax.spines['polar'].set_visible(False)
    
    ax.set_title(f'⚖️ Comparación Poblacional\n{pais1} vs {pais2}', 
                size=16, fontweight='bold', ha='center', color='white', pad=20,
                fontfamily='DejaVu Sans')
    
    # Leyenda profesional
    legend = ax.legend(bbox_to_anchor=(1.2, 1), loc='upper right',
                      frameon=True, fancybox=True, shadow=True,
                      fontsize=12, facecolor='#2d2d2d', edgecolor='white',
                      labelcolor='white')
    legend.get_frame().set_alpha(0.9)
    
    plt.tight_layout()
    
    # Convertir a base64
    buffer = io.BytesIO()
    plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight', 
                facecolor='#1e1e1e', edgecolor='none')
    buffer.seek(0)
    image_base64 = base64.b64encode(buffer.getvalue()).decode()
    plt.close()
    
    return jsonify({"image": f"data:image/png;base64,{image_base64}"})

# Función auxiliar para formatear números
def format_number(num):
    if num >= 1e6:
        return f'{num/1e6:.1f}M'
    elif num >= 1e3:
        return f'{num/1e3:.0f}K'
    return str(num)

if __name__ == '__main__':
    app.run(debug=True, port=5000, host='0.0.0.0')