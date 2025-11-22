from flask import Flask, request, jsonify, send_file
from flask_cors import CORS
from data_processor import DataProcessor
from ml_models import MLProcessor
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import seaborn as sns
import pandas as pd
import numpy as np
import plotly.express as px
import plotly.graph_objects as go
from plotly.subplots import make_subplots
import io
import base64
import os
from datetime import datetime, timedelta

app = Flask(__name__)
CORS(app)

# Configuración - usar path absoluto para mayor seguridad
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
DATA_FILE = os.path.join(BASE_DIR, '../data/fitness_data.json')

print(f"📍 Ruta de datos: {DATA_FILE}")

# Inicializar procesadores
try:
    data_processor = DataProcessor(DATA_FILE)
    ml_processor = MLProcessor(data_processor)
    print("✅ Procesadores inicializados correctamente")
    print(f"📊 Total de registros cargados: {len(data_processor.data)}")
except Exception as e:
    print(f"❌ Error inicializando procesadores: {e}")
    raise

# Paleta de colores profesional
COLOR_PALETTE = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD', '#98D8C8', '#F7DC6F']

@app.route('/api/health', methods=['GET'])
def health_check():
    """Endpoint de verificación de salud"""
    return jsonify({
        'status': 'healthy',
        'records_count': len(data_processor.data),
        'message': 'Sistema de análisis de producción celular funcionando correctamente'
    })

@app.route('/api/data', methods=['GET'])
def get_data():
    """Obtener todos los datos"""
    try:
        return jsonify(data_processor.data)
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/data', methods=['POST'])
def add_data():
    """Agregar nuevo registro"""
    try:
        new_record = request.get_json()
        if not new_record:
            return jsonify({'error': 'Datos no proporcionados'}), 400
        
        result = data_processor.add_record(new_record)
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/data/<int:record_id>', methods=['PUT'])
def update_data(record_id):
    """Actualizar registro"""
    try:
        updated_data = request.get_json()
        if not updated_data:
            return jsonify({'error': 'Datos no proporcionados'}), 400
        
        result = data_processor.update_record(record_id, updated_data)
        if result:
            return jsonify(result)
        else:
            return jsonify({'error': 'Registro no encontrado'}), 404
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/data/<int:record_id>', methods=['DELETE'])
def delete_data(record_id):
    """Eliminar registro"""
    try:
        result = data_processor.delete_record(record_id)
        return jsonify({'success': result})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/ml/train', methods=['GET'])
def train_ml_model():
    """Entrenar modelo de ML"""
    try:
        result = ml_processor.train_model()
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/ml/predict', methods=['POST'])
def predict_cells():
    """Predecir producción de células"""
    try:
        input_data = request.get_json()
        if not input_data:
            return jsonify({'error': 'Datos de entrada no proporcionados'}), 400
        
        prediction = ml_processor.predict_cells(input_data)
        return jsonify({'predicted_cells': prediction})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/ml/recommendations', methods=['POST'])
def get_recommendations():
    """Obtener recomendaciones de actividades optimizadas"""
    try:
        user_profile = request.get_json()
        if not user_profile:
            return jsonify({'error': 'Perfil de usuario no proporcionado'}), 400
        
        recommendations = ml_processor.get_activity_recommendations(user_profile)
        return jsonify(recommendations)
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/charts/area', methods=['GET'])
def area_chart():
    """Gráfico de área - Producción acumulada mejorado"""
    try:
        df = data_processor.get_dataframe()
        df['date'] = pd.to_datetime(df['date'])
        
        # Agrupar por semana para mejor visualización
        df_weekly = df.groupby(pd.Grouper(key='date', freq='W'))['cells_produced'].sum().reset_index()
        df_weekly['cumulative'] = df_weekly['cells_produced'].cumsum()
        
        plt.figure(figsize=(16, 8))
        
        # Gráfico de área principal
        plt.fill_between(df_weekly['date'], df_weekly['cumulative'], 
                        alpha=0.6, color=COLOR_PALETTE[1], label='Producción Acumulada')
        
        # Línea de tendencia
        plt.plot(df_weekly['date'], df_weekly['cumulative'], 
                color=COLOR_PALETTE[1], linewidth=3, alpha=0.9)
        
        # Puntos de datos importantes
        max_point = df_weekly.loc[df_weekly['cumulative'].idxmax()]
        plt.scatter(max_point['date'], max_point['cumulative'], 
                   color=COLOR_PALETTE[0], s=100, zorder=5, 
                   label=f'Máximo: {max_point["cumulative"]:,.0f} células')
        
        plt.title('Evolución de la Producción Acumulada de Células', 
                 fontsize=20, fontweight='bold', pad=30, color='white')
        plt.xlabel('Fecha', fontsize=14, color='white', labelpad=15)
        plt.ylabel('Células Producidas (Acumuladas)', fontsize=14, color='white', labelpad=15)
        
        # Configurar tema oscuro
        plt.gca().set_facecolor('#1a1a1a')
        plt.gcf().set_facecolor('#1a1a1a')
        plt.grid(True, alpha=0.2, color='white')
        plt.xticks(rotation=45, color='white', fontsize=11)
        plt.yticks(color='white', fontsize=11)
        
        # Formatear eje Y en millones
        plt.gca().yaxis.set_major_formatter(plt.FuncFormatter(lambda x, p: f'{x/1000000:.1f}M'))
        
        # Leyenda mejorada
        legend = plt.legend(fontsize=12, framealpha=0.9, loc='upper left')
        legend.get_frame().set_facecolor('#2a2a2a')
        legend.get_frame().set_edgecolor('white')
        for text in legend.get_texts():
            text.set_color('white')
        
        # Estadísticas en el gráfico
        total_cells = df_weekly['cumulative'].iloc[-1]
        avg_weekly = df_weekly['cells_produced'].mean()
        
        plt.annotate(f'Total: {total_cells/1000000:.1f}M células\n'
                    f'Promedio semanal: {avg_weekly/1000000:.1f}M',
                    xy=(0.02, 0.98), xycoords='axes fraction',
                    fontsize=11, color='white', ha='left', va='top',
                    bbox=dict(boxstyle='round', facecolor='#2a2a2a', alpha=0.8))
        
        plt.tight_layout()
        
        # Convertir a base64
        buffer = io.BytesIO()
        plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight', 
                   facecolor='#1a1a1a', edgecolor='none')
        buffer.seek(0)
        image_base64 = base64.b64encode(buffer.getvalue()).decode()
        plt.close()
        
        return jsonify({'image': f'data:image/png;base64,{image_base64}'})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/charts/radar', methods=['GET'])
def radar_chart():
    """Gráfico radar - Comparación por actividad mejorado"""
    try:
        df = data_processor.get_dataframe()
        
        # Métricas por tipo de actividad
        activity_metrics = df.groupby('activity_type').agg({
            'cells_produced': 'mean',
            'duration_minutes': 'mean',
            'intensity': 'mean',
            'calories_burned': 'mean',
            'heart_rate_avg': 'mean',
            'age': 'mean'
        }).reset_index()
        
        # Calcular eficiencia (células por minuto)
        activity_metrics['efficiency'] = activity_metrics['cells_produced'] / activity_metrics['duration_minutes']
        
        # Normalizar métricas para el radar (0-1)
        metrics_normalized = activity_metrics.copy()
        metrics_to_normalize = ['cells_produced', 'duration_minutes', 'intensity', 
                               'calories_burned', 'heart_rate_avg', 'efficiency']
        
        for col in metrics_to_normalize:
            min_val = activity_metrics[col].min()
            max_val = activity_metrics[col].max()
            if max_val > min_val:
                metrics_normalized[col] = (activity_metrics[col] - min_val) / (max_val - min_val)
            else:
                metrics_normalized[col] = 0.5
        
        # Crear gráfico radar interactivo con Plotly
        categories = ['Producción Celular', 'Duración', 'Intensidad', 
                     'Calorías', 'Ritmo Cardíaco', 'Eficiencia']
        
        fig = go.Figure()
        
        for i, activity in enumerate(activity_metrics['activity_type']):
            metrics = metrics_normalized[metrics_normalized['activity_type'] == activity].iloc[0]
            
            fig.add_trace(go.Scatterpolar(
                r=[
                    metrics['cells_produced'],
                    metrics['duration_minutes'], 
                    metrics['intensity'],
                    metrics['calories_burned'],
                    metrics['heart_rate_avg'],
                    metrics['efficiency']
                ],
                theta=categories,
                fill='toself',
                name=activity,
                line=dict(color=COLOR_PALETTE[i % len(COLOR_PALETTE)], width=2.5),
                opacity=0.8,
                hovertemplate=(
                    f"<b>{activity}</b><br>" +
                    "Producción: %{r[0]:.2f}<br>" +
                    "Duración: %{r[1]:.2f}<br>" +
                    "Intensidad: %{r[2]:.2f}<br>" +
                    "Calorías: %{r[3]:.2f}<br>" +
                    "Ritmo Cardíaco: %{r[4]:.2f}<br>" +
                    "Eficiencia: %{r[5]:.2f}<extra></extra>"
                )
            ))
        
        fig.update_layout(
            polar=dict(
                radialaxis=dict(
                    visible=True, 
                    range=[0, 1],
                    tickfont=dict(color='white', size=10),
                    gridcolor='rgba(255,255,255,0.3)',
                    linecolor='rgba(255,255,255,0.5)'
                ),
                angularaxis=dict(
                    tickfont=dict(color='white', size=11),
                    gridcolor='rgba(255,255,255,0.3)',
                    linecolor='rgba(255,255,255,0.5)',
                    rotation=90
                ),
                bgcolor='rgba(0,0,0,0)'
            ),
            showlegend=True,
            title=dict(
                text='Análisis Comparativo Multidimensional por Actividad',
                font=dict(size=22, color='white', family='Arial', weight='bold'),
                x=0.5,
                y=0.95
            ),
            font=dict(size=12, color='white'),
            paper_bgcolor='rgba(0,0,0,0)',
            plot_bgcolor='rgba(0,0,0,0)',
            legend=dict(
                font=dict(color='white', size=11),
                bgcolor='rgba(0,0,0,0.7)',
                bordercolor='rgba(255,255,255,0.3)',
                borderwidth=1,
                orientation='v',
                yanchor='top',
                y=0.99,
                xanchor='left',
                x=1.05
            ),
            height=700,
            margin=dict(l=80, r=200, t=100, b=80)
        )
        
        # Agregar anotaciones con valores reales
        annotations = []
        for i, activity in enumerate(activity_metrics['activity_type']):
            original_metrics = activity_metrics[activity_metrics['activity_type'] == activity].iloc[0]
            annotations.append(dict(
                x=1.15,
                y=0.9 - (i * 0.12),
                xref='paper',
                yref='paper',
                text=f"<b>{activity}</b><br>" +
                     f"Células: {original_metrics['cells_produced']:,.0f}<br>" +
                     f"Eficiencia: {original_metrics['efficiency']:,.0f}/min",
                showarrow=False,
                font=dict(color=COLOR_PALETTE[i % len(COLOR_PALETTE)], size=10),
                bgcolor='rgba(0,0,0,0.7)',
                bordercolor='rgba(255,255,255,0.3)',
                borderwidth=1,
                borderpad=4
            ))
        
        fig.update_layout(annotations=annotations)
        
        return jsonify(fig.to_dict())
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/charts/radar_individual/<activity>', methods=['GET'])
def radar_individual_chart(activity):
    """Gráfico radar individual para cada actividad - MEJORADO"""
    try:
        df = data_processor.get_dataframe()
        
        # Filtrar por actividad específica
        activity_data = df[df['activity_type'] == activity]
        
        if activity_data.empty:
            return jsonify({'error': 'Actividad no encontrada'}), 404
        
        # Calcular métricas para esta actividad
        metrics = {
            'cells_produced': activity_data['cells_produced'].mean(),
            'duration_minutes': activity_data['duration_minutes'].mean(),
            'intensity': activity_data['intensity'].mean(),
            'calories_burned': activity_data['calories_burned'].mean(),
            'heart_rate_avg': activity_data['heart_rate_avg'].mean(),
            'efficiency': (activity_data['cells_produced'] / activity_data['duration_minutes']).mean()
        }
        
        # Obtener promedios generales para comparación
        overall_metrics = {
            'cells_produced': df['cells_produced'].mean(),
            'duration_minutes': df['duration_minutes'].mean(),
            'intensity': df['intensity'].mean(),
            'calories_burned': df['calories_burned'].mean(),
            'heart_rate_avg': df['heart_rate_avg'].mean(),
            'efficiency': (df['cells_produced'] / df['duration_minutes']).mean()
        }
        
        # Normalizar para radar (0-1)
        categories = ['Producción Celular', 'Duración', 'Intensidad', 'Calorías', 'Ritmo Cardíaco', 'Eficiencia']
        max_vals = {
            'cells_produced': max(df['cells_produced'].max(), metrics['cells_produced']),
            'duration_minutes': max(df['duration_minutes'].max(), metrics['duration_minutes']),
            'intensity': 1.0,
            'calories_burned': max(df['calories_burned'].max(), metrics['calories_burned']),
            'heart_rate_avg': max(df['heart_rate_avg'].max(), metrics['heart_rate_avg']),
            'efficiency': max((df['cells_produced'] / df['duration_minutes']).max(), metrics['efficiency'])
        }
        
        activity_normalized = [metrics[key] / max_vals[key] for key in ['cells_produced', 'duration_minutes', 'intensity', 'calories_burned', 'heart_rate_avg', 'efficiency']]
        overall_normalized = [overall_metrics[key] / max_vals[key] for key in ['cells_produced', 'duration_minutes', 'intensity', 'calories_burned', 'heart_rate_avg', 'efficiency']]
        
        # Crear gráfico radar individual MEJORADO
        fig = go.Figure()
        
        fig.add_trace(go.Scatterpolar(
            r=activity_normalized,
            theta=categories,
            fill='toself',
            name=activity,
            line=dict(color=COLOR_PALETTE[0], width=3),
            opacity=0.8,
            fillcolor='rgba(255, 107, 107, 0.4)'
        ))
        
        fig.add_trace(go.Scatterpolar(
            r=overall_normalized,
            theta=categories,
            fill='toself',
            name='Promedio General',
            line=dict(color=COLOR_PALETTE[1], width=2),
            opacity=0.6,
            fillcolor='rgba(78, 205, 196, 0.3)'
        ))
        
        fig.update_layout(
            polar=dict(
                radialaxis=dict(
                    visible=True, 
                    range=[0, 1],
                    tickfont=dict(color='white', size=12),
                    gridcolor='rgba(255,255,255,0.3)',
                    linecolor='rgba(255,255,255,0.5)',
                    tickvals=[0, 0.2, 0.4, 0.6, 0.8, 1.0],
                    ticktext=['0', '0.2', '0.4', '0.6', '0.8', '1.0']
                ),
                angularaxis=dict(
                    tickfont=dict(color='white', size=13),
                    gridcolor='rgba(255,255,255,0.3)',
                    linecolor='rgba(255,255,255,0.5)',
                    rotation=90
                ),
                bgcolor='rgba(0,0,0,0)'
            ),
            showlegend=True,
            title=dict(
                text=f'Análisis Detallado: {activity}',
                font=dict(size=24, color='white', family='Arial', weight='bold'),
                x=0.5,
                y=0.95
            ),
            font=dict(size=13, color='white'),
            paper_bgcolor='rgba(0,0,0,0)',
            plot_bgcolor='rgba(0,0,0,0)',
            legend=dict(
                font=dict(color='white', size=12),
                bgcolor='rgba(0,0,0,0.8)',
                bordercolor='rgba(255,255,255,0.3)',
                borderwidth=1,
                orientation='v',
                yanchor='top',
                y=0.98,
                xanchor='left',
                x=1.05
            ),
            height=800,  # Aumentado de 600 a 800
            width=1000,   # Ancho aumentado
            margin=dict(l=100, r=200, t=120, b=100)  # Márgenes ajustados
        )
        
        # Agregar anotaciones con valores reales MEJORADAS
        annotations = []
        for i, (key, value) in enumerate(metrics.items()):
            annotations.append(dict(
                x=1.15,
                y=0.9 - (i * 0.12),
                xref='paper',
                yref='paper',
                text=f"<b>{categories[i]}</b><br>" +
                     f"{activity}: {format_metric_value(key, value)}<br>" +
                     f"Promedio: {format_metric_value(key, overall_metrics[key])}",
                showarrow=False,
                font=dict(color='white', size=11),
                bgcolor='rgba(0,0,0,0.8)',
                bordercolor='rgba(255,255,255,0.3)',
                borderwidth=1,
                borderpad=8
            ))
        
        fig.update_layout(annotations=annotations)
        
        return jsonify(fig.to_dict())
    except Exception as e:
        return jsonify({'error': str(e)}), 500

def format_metric_value(key, value):
    """Formatear valores de métricas para mejor visualización"""
    if key == 'cells_produced':
        return f"{value/1000:.0f}K"
    elif key == 'duration_minutes':
        return f"{value:.0f} min"
    elif key == 'intensity':
        return f"{value:.2f}"
    elif key == 'calories_burned':
        return f"{value:.0f}"
    elif key == 'heart_rate_avg':
        return f"{value:.0f} BPM"
    elif key == 'efficiency':
        return f"{value:.0f}/min"
    else:
        return f"{value:.2f}"

@app.route('/api/charts/stacked_bar', methods=['GET'])
def stacked_bar_chart():
    """Gráfico de barras apiladas - Producción por actividad y género"""
    try:
        df = data_processor.get_dataframe()
        
        production_by_activity_gender = df.groupby(['activity_type', 'gender'])['cells_produced'].sum().unstack(fill_value=0)
        
        plt.figure(figsize=(14, 9))
        ax = production_by_activity_gender.plot(
            kind='bar', 
            stacked=True, 
            color=[COLOR_PALETTE[0], COLOR_PALETTE[1]], 
            figsize=(14, 9),
            width=0.8,
            edgecolor='white',
            linewidth=0.5
        )
        
        plt.title('Producción de Células por Actividad y Género', 
                 fontsize=20, fontweight='bold', pad=25, color='white')
        plt.xlabel('Tipo de Actividad', fontsize=14, color='white', labelpad=15)
        plt.ylabel('Células Producidas', fontsize=14, color='white', labelpad=15)
        plt.xticks(rotation=45, ha='right', color='white', fontsize=11)
        plt.yticks(color='white', fontsize=11)
        
        # Configurar tema oscuro
        plt.gca().set_facecolor('#1a1a1a')
        plt.gcf().set_facecolor('#1a1a1a')
        ax.grid(axis='y', alpha=0.2, color='white')
        
        # Formatear eje Y en millones
        ax.yaxis.set_major_formatter(plt.FuncFormatter(lambda x, p: f'{x/1000000:.1f}M'))
        
        # Configurar leyenda
        legend = plt.legend(title='Género', title_fontsize=12, fontsize=11, 
                           framealpha=0.9, loc='upper right')
        legend.get_title().set_color('white')
        for text in legend.get_texts():
            text.set_color('white')
        legend.get_frame().set_facecolor('#2a2a2a')
        legend.get_frame().set_edgecolor('white')
        
        # Agregar valores en las barras
        for container in ax.containers:
            ax.bar_label(container, label_type='center', fmt='%.1fM', 
                        color='white', fontsize=9, fontweight='bold',
                        padding=3)
        
        # Configurar bordes
        for spine in ax.spines.values():
            spine.set_color('white')
            spine.set_alpha(0.5)
        
        plt.tight_layout()
        
        buffer = io.BytesIO()
        plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight', 
                   facecolor='#1a1a1a', edgecolor='none')
        buffer.seek(0)
        image_base64 = base64.b64encode(buffer.getvalue()).decode()
        plt.close()
        
        return jsonify({'image': f'data:image/png;base64,{image_base64}'})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/charts/stacked_bar_percentage', methods=['GET'])
def stacked_bar_percentage_chart():
    """Gráfico de barras apiladas con porcentajes"""
    try:
        df = data_processor.get_dataframe()
        
        production_by_activity_gender = df.groupby(['activity_type', 'gender'])['cells_produced'].sum().unstack(fill_value=0)
        
        # Calcular porcentajes
        total_by_activity = production_by_activity_gender.sum(axis=1)
        percentage_data = production_by_activity_gender.div(total_by_activity, axis=0) * 100
        
        plt.figure(figsize=(14, 9))
        ax = percentage_data.plot(
            kind='bar', 
            stacked=True, 
            color=[COLOR_PALETTE[0], COLOR_PALETTE[1]], 
            figsize=(14, 9),
            width=0.8,
            edgecolor='white',
            linewidth=0.5
        )
        
        plt.title('Distribución Porcentual de Producción por Actividad y Género', 
                 fontsize=20, fontweight='bold', pad=25, color='white')
        plt.xlabel('Tipo de Actividad', fontsize=14, color='white', labelpad=15)
        plt.ylabel('Porcentaje de Producción (%)', fontsize=14, color='white', labelpad=15)
        plt.xticks(rotation=45, ha='right', color='white', fontsize=11)
        plt.yticks(color='white', fontsize=11)
        
        # Configurar tema oscuro
        plt.gca().set_facecolor('#1a1a1a')
        plt.gcf().set_facecolor('#1a1a1a')
        ax.grid(axis='y', alpha=0.2, color='white')
        
        # Agregar porcentajes en las barras
        for container in ax.containers:
            ax.bar_label(container, label_type='center', fmt='%.1f%%', 
                        color='white', fontsize=10, fontweight='bold',
                        padding=3)
        
        # Configurar leyenda
        legend = plt.legend(title='Género', title_fontsize=12, fontsize=11, 
                           framealpha=0.9, loc='upper right')
        legend.get_title().set_color('white')
        for text in legend.get_texts():
            text.set_color('white')
        legend.get_frame().set_facecolor('#2a2a2a')
        legend.get_frame().set_edgecolor('white')
        
        # Configurar bordes
        for spine in ax.spines.values():
            spine.set_color('white')
            spine.set_alpha(0.5)
        
        plt.tight_layout()
        
        buffer = io.BytesIO()
        plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight', 
                   facecolor='#1a1a1a', edgecolor='none')
        buffer.seek(0)
        image_base64 = base64.b64encode(buffer.getvalue()).decode()
        plt.close()
        
        # Preparar datos para la leyenda
        legend_data = []
        for activity in production_by_activity_gender.index:
            male_cells = production_by_activity_gender.loc[activity, 'Male'] if 'Male' in production_by_activity_gender.columns else 0
            female_cells = production_by_activity_gender.loc[activity, 'Female'] if 'Female' in production_by_activity_gender.columns else 0
            total_cells = male_cells + female_cells
            legend_data.append({
                'activity': activity,
                'male_cells': f"{male_cells/1000000:.1f}M",
                'female_cells': f"{female_cells/1000000:.1f}M", 
                'total_cells': f"{total_cells/1000000:.1f}M",
                'male_percentage': f"{(male_cells/total_cells*100):.1f}%" if total_cells > 0 else "0%",
                'female_percentage': f"{(female_cells/total_cells*100):.1f}%" if total_cells > 0 else "0%"
            })
        
        return jsonify({
            'image': f'data:image/png;base64,{image_base64}',
            'legend_data': legend_data
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/charts/pie', methods=['GET'])
def pie_chart():
    """Gráfico de pastel - Distribución por actividad"""
    try:
        df = data_processor.get_dataframe()
        
        activity_distribution = df.groupby('activity_type')['cells_produced'].sum()
        
        # Calcular porcentajes
        total = activity_distribution.sum()
        percentages = (activity_distribution / total * 100).round(1)
        
        plt.figure(figsize=(14, 10))
        wedges, texts, autotexts = plt.pie(
            activity_distribution.values, 
            labels=activity_distribution.index,
            colors=COLOR_PALETTE,
            autopct=lambda p: f'{p:.1f}%' if p > 3 else '',
            startangle=90,
            textprops={'fontsize': 12, 'color': 'white', 'fontweight': 'bold'},
            wedgeprops={'edgecolor': 'white', 'linewidth': 2, 'alpha': 0.9},
            explode=[0.05 if i == activity_distribution.argmax() else 0 for i in range(len(activity_distribution))]
        )
        
        plt.title('Distribución de Producción Celular por Tipo de Actividad', 
                 fontsize=20, fontweight='bold', pad=30, color='white')
        
        # Mejorar estética de los porcentajes
        for autotext in autotexts:
            autotext.set_color('white')
            autotext.set_fontweight('bold')
            autotext.set_fontsize(11)
            autotext.set_bbox(dict(boxstyle='round,pad=0.3', facecolor='#2a2a2a', 
                                 edgecolor='white', alpha=0.8))
        
        # Leyenda mejorada con valores absolutos
        legend_labels = [f'{label}\n({value/1000000:.1f}M células, {percentages[label]:.1f}%)' 
                        for label, value in activity_distribution.items()]
        legend = plt.legend(wedges, legend_labels, title="Actividades", 
                           loc="center left", bbox_to_anchor=(1, 0, 0.5, 1),
                           fontsize=11, framealpha=0.9)
        legend.get_title().set_color('white')
        legend.get_title().set_fontweight('bold')
        for text in legend.get_texts():
            text.set_color('white')
        legend.get_frame().set_facecolor('#2a2a2a')
        legend.get_frame().set_edgecolor('white')
        
        # Configurar fondo oscuro
        plt.gcf().set_facecolor('#1a1a1a')
        
        plt.tight_layout()
        
        buffer = io.BytesIO()
        plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight', 
                   facecolor='#1a1a1a', edgecolor='none')
        buffer.seek(0)
        image_base64 = base64.b64encode(buffer.getvalue()).decode()
        plt.close()
        
        return jsonify({'image': f'data:image/png;base64,{image_base64}'})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/charts/correlation', methods=['GET'])
def correlation_heatmap():
    """Mapa de calor de correlaciones"""
    try:
        df = data_processor.get_dataframe()
        
        # Seleccionar columnas numéricas
        numeric_cols = ['duration_minutes', 'intensity', 'age', 'cells_produced', 
                       'heart_rate_avg', 'calories_burned', 'sleep_hours', 'hydration_liters']
        
        correlation_matrix = df[numeric_cols].corr()
        
        plt.figure(figsize=(14, 10))
        sns.heatmap(
            correlation_matrix, 
            annot=True, 
            cmap='RdBu_r', 
            center=0,
            square=True, 
            linewidths=1, 
            cbar_kws={"shrink": .8, "label": "Coeficiente de Correlación"},
            annot_kws={"size": 11, "color": "black", "weight": "bold"},
            fmt='.2f',
            vmin=-1, vmax=1
        )
        
        plt.title('Mapa de Calor - Correlación entre Variables del Dataset', 
                 fontsize=18, fontweight='bold', pad=25, color='white')
        plt.xticks(rotation=45, ha='right', color='white', fontsize=11)
        plt.yticks(rotation=0, color='white', fontsize=11)
        
        # Configurar colorbar
        cbar = plt.gcf().axes[-1]
        cbar.tick_params(colors='white')
        cbar.yaxis.label.set_color('white')
        cbar.yaxis.label.set_fontsize(12)
        
        # Configurar fondo oscuro
        plt.gca().set_facecolor('#1a1a1a')
        plt.gcf().set_facecolor('#1a1a1a')
        
        plt.tight_layout()
        
        buffer = io.BytesIO()
        plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight', 
                   facecolor='#1a1a1a', edgecolor='none')
        buffer.seek(0)
        image_base64 = base64.b64encode(buffer.getvalue()).decode()
        plt.close()
        
        return jsonify({'image': f'data:image/png;base64,{image_base64}'})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/charts/trends', methods=['GET'])
def trends_chart():
    """Gráfico de tendencias y proyecciones"""
    try:
        df = data_processor.get_dataframe()
        df['date'] = pd.to_datetime(df['date'])
        
        # Tendencias por actividad (mensual)
        trends_data = df.groupby([df['date'].dt.to_period('M'), 'activity_type'])['cells_produced'].mean().reset_index()
        trends_data['date'] = trends_data['date'].dt.to_timestamp()
        
        # Proyección (simulada basada en tendencias)
        last_date = trends_data['date'].max()
        future_dates = [last_date + pd.DateOffset(months=i) for i in range(1, 7)]
        
        plt.figure(figsize=(16, 10))
        
        # Gráfico de tendencias
        for i, activity in enumerate(trends_data['activity_type'].unique()):
            activity_data = trends_data[trends_data['activity_type'] == activity]
            
            # Línea de tendencia histórica
            plt.plot(activity_data['date'], activity_data['cells_produced'], 
                    marker='o', linewidth=3, markersize=6, label=activity, 
                    color=COLOR_PALETTE[i], alpha=0.9)
            
            # Proyección (línea punteada)
            last_value = activity_data['cells_produced'].iloc[-1]
            growth_rate = 0.15  # 15% de crecimiento proyectado
            
            plt.plot([last_date, future_dates[-1]], [last_value, last_value * (1 + growth_rate)], 
                    '--', color=COLOR_PALETTE[i], alpha=0.6, linewidth=2,
                    label=f'{activity} (Proyección)')
        
        plt.title('Tendencias Históricas y Proyecciones de Producción Celular', 
                 fontsize=20, fontweight='bold', pad=30, color='white')
        plt.xlabel('Fecha', fontsize=14, color='white', labelpad=15)
        plt.ylabel('Células Producidas (Promedio Mensual)', fontsize=14, color='white', labelpad=15)
        
        # Leyenda mejorada
        legend = plt.legend(title='Actividades y Proyecciones', title_fontsize=12, 
                           fontsize=11, framealpha=0.9, loc='upper left')
        legend.get_title().set_color('white')
        for text in legend.get_texts():
            text.set_color('white')
        legend.get_frame().set_facecolor('#2a2a2a')
        legend.get_frame().set_edgecolor('white')
        
        plt.grid(True, alpha=0.3, color='white')
        plt.xticks(rotation=45, color='white', fontsize=11)
        plt.yticks(color='white', fontsize=11)
        
        # Formatear eje Y
        plt.gca().yaxis.set_major_formatter(plt.FuncFormatter(lambda x, p: f'{x/1000:.0f}K'))
        
        # Línea vertical indicando inicio de proyección
        plt.axvline(x=last_date, color='white', linestyle=':', alpha=0.7, linewidth=1)
        plt.annotate('Inicio Proyección', xy=(last_date, plt.ylim()[1] * 0.9), 
                    xytext=(10, 0), textcoords='offset points',
                    color='white', fontsize=10, ha='left')
        
        # Configurar tema oscuro
        plt.gca().set_facecolor('#1a1a1a')
        plt.gcf().set_facecolor('#1a1a1a')
        
        plt.tight_layout()
        
        buffer = io.BytesIO()
        plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight', 
                   facecolor='#1a1a1a', edgecolor='none')
        buffer.seek(0)
        image_base64 = base64.b64encode(buffer.getvalue()).decode()
        plt.close()
        
        return jsonify({'image': f'data:image/png;base64,{image_base64}'})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/charts/performance_metrics', methods=['GET'])
def performance_metrics():
    """Métricas de performance detalladas"""
    try:
        df = data_processor.get_dataframe()
        
        # Calcular métricas avanzadas
        efficiency_metrics = df.groupby('activity_type').agg({
            'cells_produced': ['mean', 'std', 'max'],
            'duration_minutes': 'mean',
            'intensity': 'mean',
            'calories_burned': 'mean'
        }).round(2)
        
        # Calcular eficiencia (células por minuto)
        df['efficiency'] = df['cells_produced'] / df['duration_minutes']
        efficiency_by_activity = df.groupby('activity_type')['efficiency'].mean().sort_values(ascending=False)
        
        fig, ((ax1, ax2), (ax3, ax4)) = plt.subplots(2, 2, figsize=(20, 16))
        
        # Gráfico 1: Eficiencia por actividad
        bars1 = efficiency_by_activity.plot(kind='bar', ax=ax1, color=COLOR_PALETTE, 
                                          edgecolor='white', linewidth=0.5)
        ax1.set_title('Eficiencia: Células Producidas por Minuto', 
                     fontsize=16, fontweight='bold', color='white', pad=20)
        ax1.set_ylabel('Células por Minuto', color='white', fontsize=12)
        ax1.tick_params(axis='x', rotation=45, colors='white', labelsize=11)
        ax1.tick_params(axis='y', colors='white', labelsize=11)
        
        # Agregar valores en las barras
        for i, v in enumerate(efficiency_by_activity):
            ax1.text(i, v + 1000, f'{v:,.0f}', ha='center', va='bottom', 
                    color='white', fontweight='bold', fontsize=10)
        
        # Gráfico 2: Producción máxima por actividad
        max_production = df.groupby('activity_type')['cells_produced'].max()
        bars2 = max_production.plot(kind='bar', ax=ax2, color=COLOR_PALETTE[1:], 
                                  edgecolor='white', linewidth=0.5)
        ax2.set_title('Producción Máxima por Actividad', 
                     fontsize=16, fontweight='bold', color='white', pad=20)
        ax2.set_ylabel('Células Producidas', color='white', fontsize=12)
        ax2.tick_params(axis='x', rotation=45, colors='white', labelsize=11)
        ax2.tick_params(axis='y', colors='white', labelsize=11)
        ax2.yaxis.set_major_formatter(plt.FuncFormatter(lambda x, p: f'{x/1000000:.1f}M'))
        
        # Gráfico 3: Intensidad vs Producción
        scatter1 = ax3.scatter(df['intensity'], df['cells_produced'], 
                              alpha=0.7, color=COLOR_PALETTE[2], s=50)
        ax3.set_title('Relación: Intensidad vs Producción Celular', 
                     fontsize=16, fontweight='bold', color='white', pad=20)
        ax3.set_xlabel('Intensidad', color='white', fontsize=12)
        ax3.set_ylabel('Células Producidas', color='white', fontsize=12)
        ax3.tick_params(axis='both', colors='white', labelsize=11)
        ax3.yaxis.set_major_formatter(plt.FuncFormatter(lambda x, p: f'{x/1000:.0f}K'))
        
        # Línea de tendencia
        z = np.polyfit(df['intensity'], df['cells_produced'], 1)
        p = np.poly1d(z)
        ax3.plot(df['intensity'], p(df['intensity']), "r--", alpha=0.8, linewidth=2)
        
        # Gráfico 4: Duración vs Eficiencia
        scatter2 = ax4.scatter(df['duration_minutes'], df['efficiency'], 
                              alpha=0.7, color=COLOR_PALETTE[3], s=50)
        ax4.set_title('Relación: Duración vs Eficiencia', 
                     fontsize=16, fontweight='bold', color='white', pad=20)
        ax4.set_xlabel('Duración (minutos)', color='white', fontsize=12)
        ax4.set_ylabel('Eficiencia (células/minuto)', color='white', fontsize=12)
        ax4.tick_params(axis='both', colors='white', labelsize=11)
        
        # Configurar tema oscuro para todos los subplots
        for ax in [ax1, ax2, ax3, ax4]:
            ax.set_facecolor('#1a1a1a')
            for spine in ax.spines.values():
                spine.set_color('white')
                spine.set_alpha(0.5)
        
        fig.suptitle('Métricas Avanzadas de Performance - Análisis Completo', 
                    fontsize=22, fontweight='bold', color='white', y=0.95)
        fig.patch.set_facecolor('#1a1a1a')
        plt.tight_layout()
        
        buffer = io.BytesIO()
        plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight', 
                   facecolor='#1a1a1a', edgecolor='none')
        buffer.seek(0)
        image_base64 = base64.b64encode(buffer.getvalue()).decode()
        plt.close()
        
        return jsonify({'image': f'data:image/png;base64,{image_base64}'})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/charts/efficiency_analysis', methods=['GET'])
def efficiency_analysis_chart():
    """Gráfico específico de análisis de eficiencia"""
    try:
        df = data_processor.get_dataframe()
        
        # Calcular eficiencia
        df['efficiency'] = df['cells_produced'] / df['duration_minutes']
        
        # Análisis de eficiencia por actividad
        efficiency_by_activity = df.groupby('activity_type')['efficiency'].mean().sort_values(ascending=False)
        
        plt.figure(figsize=(14, 8))
        bars = efficiency_by_activity.plot(kind='bar', color=COLOR_PALETTE, edgecolor='white', linewidth=0.5)
        
        plt.title('Análisis de Eficiencia por Tipo de Actividad', 
                 fontsize=18, fontweight='bold', pad=25, color='white')
        plt.xlabel('Tipo de Actividad', fontsize=14, color='white', labelpad=15)
        plt.ylabel('Células por Minuto', fontsize=14, color='white', labelpad=15)
        plt.xticks(rotation=45, ha='right', color='white', fontsize=11)
        plt.yticks(color='white', fontsize=11)
        
        # Agregar valores en las barras
        for i, v in enumerate(efficiency_by_activity):
            plt.text(i, v + 500, f'{v:,.0f}', ha='center', va='bottom', 
                    color='white', fontweight='bold', fontsize=10)
        
        # Configurar tema oscuro
        plt.gca().set_facecolor('#1a1a1a')
        plt.gcf().set_facecolor('#1a1a1a')
        plt.grid(axis='y', alpha=0.2, color='white')
        
        # Configurar bordes
        for spine in plt.gca().spines.values():
            spine.set_color('white')
            spine.set_alpha(0.5)
        
        plt.tight_layout()
        
        buffer = io.BytesIO()
        plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight', 
                   facecolor='#1a1a1a', edgecolor='none')
        buffer.seek(0)
        image_base64 = base64.b64encode(buffer.getvalue()).decode()
        plt.close()
        
        return jsonify({'image': f'data:image/png;base64,{image_base64}'})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/charts/production_analysis', methods=['GET'])
def production_analysis_chart():
    """Gráfico específico de análisis de producción"""
    try:
        df = data_processor.get_dataframe()
        
        # Análisis de producción por actividad
        production_by_activity = df.groupby('activity_type')['cells_produced'].mean().sort_values(ascending=False)
        
        plt.figure(figsize=(14, 8))
        bars = production_by_activity.plot(kind='bar', color=COLOR_PALETTE[1:], edgecolor='white', linewidth=0.5)
        
        plt.title('Producción Promedio por Tipo de Actividad', 
                 fontsize=18, fontweight='bold', pad=25, color='white')
        plt.xlabel('Tipo de Actividad', fontsize=14, color='white', labelpad=15)
        plt.ylabel('Células Producidas', fontsize=14, color='white', labelpad=15)
        plt.xticks(rotation=45, ha='right', color='white', fontsize=11)
        plt.yticks(color='white', fontsize=11)
        
        # Formatear eje Y
        plt.gca().yaxis.set_major_formatter(plt.FuncFormatter(lambda x, p: f'{x/1000:.0f}K'))
        
        # Agregar valores en las barras
        for i, v in enumerate(production_by_activity):
            plt.text(i, v + 5000, f'{v/1000:.0f}K', ha='center', va='bottom', 
                    color='white', fontweight='bold', fontsize=10)
        
        # Configurar tema oscuro
        plt.gca().set_facecolor('#1a1a1a')
        plt.gcf().set_facecolor('#1a1a1a')
        plt.grid(axis='y', alpha=0.2, color='white')
        
        # Configurar bordes
        for spine in plt.gca().spines.values():
            spine.set_color('white')
            spine.set_alpha(0.5)
        
        plt.tight_layout()
        
        buffer = io.BytesIO()
        plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight', 
                   facecolor='#1a1a1a', edgecolor='none')
        buffer.seek(0)
        image_base64 = base64.b64encode(buffer.getvalue()).decode()
        plt.close()
        
        return jsonify({'image': f'data:image/png;base64,{image_base64}'})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/charts/intensity_analysis', methods=['GET'])
def intensity_analysis_chart():
    """Gráfico específico de análisis de intensidad"""
    try:
        df = data_processor.get_dataframe()
        
        plt.figure(figsize=(14, 8))
        
        # Scatter plot de intensidad vs producción
        scatter = plt.scatter(df['intensity'], df['cells_produced'], 
                             alpha=0.6, color=COLOR_PALETTE[2], s=60)
        
        # Línea de tendencia
        z = np.polyfit(df['intensity'], df['cells_produced'], 1)
        p = np.poly1d(z)
        plt.plot(df['intensity'], p(df['intensity']), color=COLOR_PALETTE[0], 
                linewidth=3, alpha=0.8, label='Tendencia')
        
        plt.title('Relación entre Intensidad y Producción Celular', 
                 fontsize=18, fontweight='bold', pad=25, color='white')
        plt.xlabel('Intensidad del Ejercicio', fontsize=14, color='white', labelpad=15)
        plt.ylabel('Células Producidas', fontsize=14, color='white', labelpad=15)
        plt.xticks(color='white', fontsize=11)
        plt.yticks(color='white', fontsize=11)
        
        # Formatear eje Y
        plt.gca().yaxis.set_major_formatter(plt.FuncFormatter(lambda x, p: f'{x/1000:.0f}K'))
        
        # Leyenda
        legend = plt.legend(fontsize=12, framealpha=0.9)
        legend.get_frame().set_facecolor('#2a2a2a')
        for text in legend.get_texts():
            text.set_color('white')
        
        # Configurar tema oscuro
        plt.gca().set_facecolor('#1a1a1a')
        plt.gcf().set_facecolor('#1a1a1a')
        plt.grid(True, alpha=0.2, color='white')
        
        # Configurar bordes
        for spine in plt.gca().spines.values():
            spine.set_color('white')
            spine.set_alpha(0.5)
        
        plt.tight_layout()
        
        buffer = io.BytesIO()
        plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight', 
                   facecolor='#1a1a1a', edgecolor='none')
        buffer.seek(0)
        image_base64 = base64.b64encode(buffer.getvalue()).decode()
        plt.close()
        
        return jsonify({'image': f'data:image/png;base64,{image_base64}'})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/charts/duration_analysis', methods=['GET'])
def duration_analysis_chart():
    """Gráfico específico de análisis de duración"""
    try:
        df = data_processor.get_dataframe()
        
        # Calcular eficiencia
        df['efficiency'] = df['cells_produced'] / df['duration_minutes']
        
        plt.figure(figsize=(14, 8))
        
        # Scatter plot de duración vs eficiencia
        scatter = plt.scatter(df['duration_minutes'], df['efficiency'], 
                             alpha=0.6, color=COLOR_PALETTE[3], s=60)
        
        # Línea de tendencia
        z = np.polyfit(df['duration_minutes'], df['efficiency'], 1)
        p = np.poly1d(z)
        plt.plot(df['duration_minutes'], p(df['duration_minutes']), color=COLOR_PALETTE[1], 
                linewidth=3, alpha=0.8, label='Tendencia')
        
        plt.title('Relación entre Duración y Eficiencia', 
                 fontsize=18, fontweight='bold', pad=25, color='white')
        plt.xlabel('Duración (minutos)', fontsize=14, color='white', labelpad=15)
        plt.ylabel('Eficiencia (células/minuto)', fontsize=14, color='white', labelpad=15)
        plt.xticks(color='white', fontsize=11)
        plt.yticks(color='white', fontsize=11)
        
        # Leyenda
        legend = plt.legend(fontsize=12, framealpha=0.9)
        legend.get_frame().set_facecolor('#2a2a2a')
        for text in legend.get_texts():
            text.set_color('white')
        
        # Configurar tema oscuro
        plt.gca().set_facecolor('#1a1a1a')
        plt.gcf().set_facecolor('#1a1a1a')
        plt.grid(True, alpha=0.2, color='white')
        
        # Configurar bordes
        for spine in plt.gca().spines.values():
            spine.set_color('white')
            spine.set_alpha(0.5)
        
        plt.tight_layout()
        
        buffer = io.BytesIO()
        plt.savefig(buffer, format='png', dpi=300, bbox_inches='tight', 
                   facecolor='#1a1a1a', edgecolor='none')
        buffer.seek(0)
        image_base64 = base64.b64encode(buffer.getvalue()).decode()
        plt.close()
        
        return jsonify({'image': f'data:image/png;base64,{image_base64}'})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/analytics/insights', methods=['GET'])
def get_analytical_insights():
    """Insights analíticos basados en los datos"""
    try:
        df = data_processor.get_dataframe()
        
        # Calcular métricas avanzadas
        df['efficiency'] = df['cells_produced'] / df['duration_minutes']
        
        # Análisis por actividad
        activity_analysis = df.groupby('activity_type').agg({
            'cells_produced': ['mean', 'std', 'count'],
            'efficiency': 'mean',
            'duration_minutes': 'mean',
            'intensity': 'mean'
        }).round(2)
        
        # Encontrar patrones
        most_efficient = df.loc[df.groupby('activity_type')['efficiency'].idxmax()]
        least_efficient = df.loc[df.groupby('activity_type')['efficiency'].idxmin()]
        
        # Correlaciones significativas
        correlation_with_cells = df[['duration_minutes', 'intensity', 'age', 'heart_rate_avg', 
                                   'calories_burned', 'sleep_hours', 'hydration_liters']].corrwith(df['cells_produced'])
        
        insights = {
            'performance_analysis': {
                'most_efficient_activity': df.groupby('activity_type')['efficiency'].mean().idxmax(),
                'least_efficient_activity': df.groupby('activity_type')['efficiency'].mean().idxmin(),
                'highest_production_activity': df.groupby('activity_type')['cells_produced'].mean().idxmax(),
                'most_consistent_activity': df.groupby('activity_type')['cells_produced'].std().idxmin()
            },
            'optimal_parameters': {
                'best_intensity_range': {
                    'min': df.groupby(pd.cut(df['intensity'], bins=5))['efficiency'].mean().idxmax().left,
                    'max': df.groupby(pd.cut(df['intensity'], bins=5))['efficiency'].mean().idxmax().right
                },
                'best_duration_range': {
                    'min': df.groupby(pd.cut(df['duration_minutes'], bins=5))['efficiency'].mean().idxmax().left,
                    'max': df.groupby(pd.cut(df['duration_minutes'], bins=5))['efficiency'].mean().idxmax().right
                }
            },
            'demographic_insights': {
                'gender_efficiency': df.groupby('gender')['efficiency'].mean().to_dict(),
                'age_optimal_range': f"{df.groupby(pd.cut(df['age'], bins=5))['efficiency'].mean().idxmax().left:.0f}-{df.groupby(pd.cut(df['age'], bins=5))['efficiency'].mean().idxmax().right:.0f} años"
            },
            'key_correlations': {k: round(float(v), 3) for k, v in correlation_with_cells.abs().sort_values(ascending=False).head(3).to_dict().items()},
            'recommendations': [
                f"Priorizar {df.groupby('activity_type')['efficiency'].mean().idxmax()} para máxima eficiencia",
                f"Mantener intensidad entre {df.groupby(pd.cut(df['intensity'], bins=5))['efficiency'].mean().idxmax().left:.1f}-{df.groupby(pd.cut(df['intensity'], bins=5))['efficiency'].mean().idxmax().right:.1f}",
                f"Optimizar duración a {df.groupby(pd.cut(df['duration_minutes'], bins=5))['efficiency'].mean().idxmax().left:.0f}-{df.groupby(pd.cut(df['duration_minutes'], bins=5))['efficiency'].mean().idxmax().right:.0f} minutos"
            ]
        }
        
        return jsonify(insights)
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/stats/summary', methods=['GET'])
def get_stats_summary():
    """Obtener estadísticas resumidas del dataset"""
    try:
        df = data_processor.get_dataframe()
        
        summary = {
            'total_records': len(df),
            'total_cells_produced': int(df['cells_produced'].sum()),
            'average_cells_per_activity': df.groupby('activity_type')['cells_produced'].mean().to_dict(),
            'activity_distribution': df['activity_type'].value_counts().to_dict(),
            'gender_distribution': df['gender'].value_counts().to_dict(),
            'average_metrics': {
                'duration_minutes': round(df['duration_minutes'].mean(), 2),
                'intensity': round(df['intensity'].mean(), 3),
                'age': round(df['age'].mean(), 1),
                'heart_rate_avg': round(df['heart_rate_avg'].mean(), 1),
                'sleep_hours': round(df['sleep_hours'].mean(), 2),
                'hydration_liters': round(df['hydration_liters'].mean(), 2)
            }
        }
        
        return jsonify(summary)
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# Manejo de errores global
@app.errorhandler(404)
def not_found(error):
    return jsonify({'error': 'Endpoint no encontrado'}), 404

@app.errorhandler(500)
def internal_error(error):
    return jsonify({'error': 'Error interno del servidor'}), 500

@app.errorhandler(405)
def method_not_allowed(error):
    return jsonify({'error': 'Método no permitido'}), 405

if __name__ == '__main__':
    print("🚀 Iniciando servidor de análisis de producción celular...")
    print("📍 Endpoints disponibles:")
    print("   📊 GET  /api/data - Obtener todos los datos")
    print("   📝 POST /api/data - Agregar nuevo registro")
    print("   🤖 GET  /api/ml/train - Entrenar modelo ML")
    print("   📈 GET  /api/charts/area - Gráfico de área")
    print("   📊 GET  /api/charts/radar - Gráfico radar")
    print("   📊 GET  /api/charts/radar_individual/<activity> - Radar individual")
    print("   📊 GET  /api/charts/stacked_bar - Barras apiladas")
    print("   📊 GET  /api/charts/stacked_bar_percentage - Barras con porcentajes")
    print("   🥧 GET  /api/charts/pie - Gráfico de pastel")
    print("   🔥 GET  /api/charts/correlation - Mapa de calor")
    print("   📈 GET  /api/charts/trends - Tendencias y proyecciones")
    print("   📊 GET  /api/charts/performance_metrics - Métricas avanzadas")
    print("   📊 GET  /api/charts/efficiency_analysis - Análisis de eficiencia")
    print("   📊 GET  /api/charts/production_analysis - Análisis de producción")
    print("   📊 GET  /api/charts/intensity_analysis - Análisis de intensidad")
    print("   📊 GET  /api/charts/duration_analysis - Análisis de duración")
    print("   📊 GET  /api/analytics/insights - Insights analíticos")
    print("   📋 GET  /api/stats/summary - Estadísticas resumidas")
    print("   ❤️  GET  /api/health - Verificación de salud")
    print("\n🌐 Servidor ejecutándose en: http://localhost:5000")
    print("   Frontend PHP debe apuntar a: http://localhost:8000")
    
    app.run(debug=True, port=5000, host='0.0.0.0')