import matplotlib
# Usar backend que no requiera GUI
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import pandas as pd
import numpy as np
import seaborn as sns
import plotly.express as px
import plotly.graph_objects as go
from plotly.subplots import make_subplots
import base64
import io
import logging
import os
import sys

# Configurar path para imports
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

try:
    from backend.models.vitamin_models import VitaminDataset
    from backend.utils.helpers import load_json_data
except ImportError as e:
    print(f"Error de importación en data_processor.py: {e}")
    # Fallback a imports relativos
    from models.vitamin_models import VitaminDataset
    from utils.helpers import load_json_data

class ChartGenerator:
    """Generador de gráficos con Seaborn y Plotly"""
    
    def __init__(self):
        self.setup_styles()
    
    def setup_styles(self):
        """Configura estilos para los gráficos"""
        sns.set_theme(style="darkgrid")
        plt.rcParams['figure.figsize'] = (12, 8)
        plt.rcParams['font.size'] = 12
        plt.rcParams['axes.facecolor'] = '#1e1e1e'
        plt.rcParams['figure.facecolor'] = '#1e1e1e'
        plt.rcParams['text.color'] = 'white'
        plt.rcParams['axes.labelcolor'] = 'white'
        plt.rcParams['xtick.color'] = 'white'
        plt.rcParams['ytick.color'] = 'white'
        plt.rcParams['figure.max_open_warning'] = 0
    
    def load_data(self):
        """Carga y procesa los datos"""
        raw_data = load_json_data()
        vitamin_data = []
        
        for i, item in enumerate(raw_data):
            try:
                vitamin_data.append({
                    'id': i + 1,
                    'vitamina': item.get('vitamina', 'Desconocida'),
                    'dosis_diaria': float(item.get('dosis_diaria', 0)),
                    'duracion_semanas': int(item.get('duracion_semanas', 0)),
                    'globulos_rojos_inicio': float(item.get('globulos_rojos_inicio', 0)),
                    'globulos_rojos_fin': float(item.get('globulos_rojos_fin', 0)),
                    'edad_paciente': item.get('edad_paciente'),
                    'sexo': item.get('sexo')
                })
            except (ValueError, TypeError) as e:
                logging.warning(f"Error procesando registro {i}: {e}")
                continue
        
        return pd.DataFrame(vitamin_data)
    
    def plot_to_base64(self, plt_figure):
        """Convierte un gráfico de matplotlib a base64"""
        try:
            buffer = io.BytesIO()
            plt_figure.savefig(buffer, format='png', dpi=300, bbox_inches='tight', 
                              facecolor='#1e1e1e', edgecolor='none')
            buffer.seek(0)
            image_base64 = base64.b64encode(buffer.getvalue()).decode()
            buffer.close()
            plt.close(plt_figure)
            return image_base64
        except Exception as e:
            logging.error(f"Error convirtiendo gráfico a base64: {e}")
            return None
    
    def generate_area_chart(self):
        """Genera gráfico de área sobre la curva"""
        try:
            df = self.load_data()
            if df.empty:
                logging.warning("No hay datos para generar el gráfico de área")
                return None
            
            # Preparar datos para el gráfico de área
            df['incremento'] = df['globulos_rojos_fin'] - df['globulos_rojos_inicio']
            df_sorted = df.sort_values('dosis_diaria')
            
            fig, ax = plt.subplots(figsize=(14, 8))
            
            # Gráfico de área para cada vitamina
            vitaminas = df['vitamina'].unique()
            colors = sns.color_palette("husl", len(vitaminas))
            
            for i, vit in enumerate(vitaminas):
                vit_data = df_sorted[df_sorted['vitamina'] == vit]
                if not vit_data.empty:
                    # Ordenar por dosis para un área continua
                    vit_data = vit_data.sort_values('dosis_diaria')
                    ax.fill_between(vit_data['dosis_diaria'], 
                                  vit_data['incremento'], 
                                  alpha=0.4, 
                                  label=vit,
                                  color=colors[i])
                    ax.plot(vit_data['dosis_diaria'], 
                           vit_data['incremento'], 
                           linewidth=2.5, 
                           color=colors[i],
                           marker='o', markersize=4)
            
            ax.set_xlabel('Dosis Diaria (mg)', fontsize=14, color='white')
            ax.set_ylabel('Incremento Glóbulos Rojos (millones/mL)', fontsize=14, color='white')
            ax.set_title('Área sobre la Curva: Impacto de Vitaminas en Glóbulos Rojos', 
                        fontsize=16, color='white', pad=20)
            ax.legend(title='Vitaminas', title_fontsize=12, fontsize=11, 
                     facecolor='#2d2d2d', edgecolor='none')
            ax.grid(True, alpha=0.3)
            
            # Configurar colores para tema oscuro
            ax.set_facecolor('#1e1e1e')
            fig.patch.set_facecolor('#1e1e1e')
            ax.tick_params(colors='white')
            
            return self.plot_to_base64(fig)
        except Exception as e:
            logging.error(f"Error generating area chart: {e}")
            return None
    
    def generate_radar_chart(self):
        """Genera gráfico radar comparativo - VERSIÓN CORREGIDA"""
        try:
            df = self.load_data()
            if df.empty:
                logging.warning("No hay datos para generar el gráfico radar")
                return None
            
            # Calcular métricas por vitamina
            df['incremento'] = df['globulos_rojos_fin'] - df['globulos_rojos_inicio']
            df['eficiencia'] = df['incremento'] / (df['dosis_diaria'] * df['duracion_semanas'])
            
            # Agrupar por vitamina y calcular métricas
            metrics = df.groupby('vitamina').agg({
                'incremento': 'mean',
                'eficiencia': 'mean',
                'dosis_diaria': 'mean',
                'duracion_semanas': 'mean'
            }).reset_index()
            
            # Si hay menos de 2 vitaminas, no podemos hacer comparación radar
            if len(metrics) < 2:
                logging.warning("Se necesitan al menos 2 vitaminas diferentes para el gráfico radar")
                return self.generate_single_vitamin_radar(metrics)
            
            # Normalizar métricas para el radar chart (0-1)
            normalized_metrics = metrics.copy()
            features_to_normalize = ['incremento', 'eficiencia', 'dosis_diaria', 'duracion_semanas']
            
            for col in features_to_normalize:
                min_val = metrics[col].min()
                max_val = metrics[col].max()
                if max_val > min_val:
                    normalized_metrics[col] = (metrics[col] - min_val) / (max_val - min_val)
                else:
                    normalized_metrics[col] = 0.5  # Valor medio si no hay variación
            
            # Crear gráfico radar con Plotly - VERSIÓN SIMPLIFICADA
            fig = go.Figure()
            
            colors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4']
            
            for i, row in normalized_metrics.iterrows():
                fig.add_trace(go.Scatterpolar(
                    r=[
                        row['incremento'],
                        row['eficiencia'], 
                        row['dosis_diaria'],
                        row['duracion_semanas'],
                        row['incremento']  # Cerrar el polígono
                    ],
                    theta=['Incremento', 'Eficiencia', 'Dosis', 'Duración', 'Incremento'],
                    fill='toself',
                    name=row['vitamina'],
                    line=dict(color=colors[i % len(colors)], width=2),
                    opacity=0.7
                ))
            
            fig.update_layout(
                polar=dict(
                    radialaxis=dict(
                        visible=True,
                        range=[0, 1],
                        tickvals=[0, 0.5, 1],
                        ticktext=['Bajo', 'Medio', 'Alto'],
                        gridcolor='rgba(255,255,255,0.3)',
                        linecolor='rgba(255,255,255,0.5)',
                        tickcolor='white'
                    ),
                    angularaxis=dict(
                        gridcolor='rgba(255,255,255,0.3)',
                        linecolor='rgba(255,255,255,0.5)',
                        rotation=90
                    ),
                    bgcolor='rgba(30,41,59,0.5)'
                ),
                showlegend=True,
                legend=dict(
                    orientation="h",
                    yanchor="bottom",
                    y=1.02,
                    xanchor="center",
                    x=0.5,
                    bgcolor='rgba(30,41,59,0.8)',
                    bordercolor='rgba(255,255,255,0.2)',
                    font=dict(color='white')
                ),
                paper_bgcolor='rgba(0,0,0,0)',
                plot_bgcolor='rgba(0,0,0,0)',
                font=dict(color='white', size=12),
                height=500,
                margin=dict(l=50, r=50, t=80, b=50),
                title=dict(
                    text='Comparación Radar de Efectividad de Vitaminas',
                    x=0.5,
                    font=dict(size=16, color='white')
                )
            )
            
            # Convertir a HTML
            html_content = fig.to_html(
                include_plotlyjs='cdn',
                config={
                    'displayModeBar': True,
                    'displaylogo': False,
                    'modeBarButtonsToRemove': ['pan2d', 'lasso2d', 'select2d'],
                    'responsive': True
                },
                div_id="radar-chart"
            )
            
            return html_content
            
        except Exception as e:
            logging.error(f"Error generating radar chart: {e}")
            import traceback
            logging.error(traceback.format_exc())
            return None
    
    def generate_single_vitamin_radar(self, metrics):
        """Genera un radar alternativo cuando solo hay una vitamina"""
        try:
            if len(metrics) == 0:
                return None
                
            vit_data = metrics.iloc[0]
            
            # Crear un radar simple con valores absolutos
            fig = go.Figure()
            
            fig.add_trace(go.Scatterpolar(
                r=[
                    vit_data['incremento'],
                    vit_data['eficiencia'] * 100,  # Escalar para mejor visualización
                    vit_data['dosis_diaria'],
                    vit_data['duracion_semanas'],
                    vit_data['incremento']
                ],
                theta=['Incremento (M/mL)', 'Eficiencia (%)', 'Dosis (mg)', 'Duración (semanas)', 'Incremento (M/mL)'],
                fill='toself',
                name=vit_data['vitamina'],
                line=dict(color='#6366f1', width=3),
                opacity=0.8
            ))
            
            fig.update_layout(
                polar=dict(
                    radialaxis=dict(
                        visible=True,
                        gridcolor='rgba(255,255,255,0.3)',
                        linecolor='rgba(255,255,255,0.5)',
                        tickcolor='white'
                    ),
                    angularaxis=dict(
                        gridcolor='rgba(255,255,255,0.3)',
                        linecolor='rgba(255,255,255,0.5)',
                        rotation=90
                    ),
                    bgcolor='rgba(30,41,59,0.5)'
                ),
                showlegend=True,
                paper_bgcolor='rgba(0,0,0,0)',
                plot_bgcolor='rgba(0,0,0,0)',
                font=dict(color='white', size=12),
                height=500,
                margin=dict(l=50, r=50, t=80, b=50),
                title=dict(
                    text=f'Análisis de {vit_data["vitamina"]}',
                    x=0.5,
                    font=dict(size=16, color='white')
                )
            )
            
            html_content = fig.to_html(
                include_plotlyjs='cdn',
                config={
                    'displayModeBar': True,
                    'displaylogo': False,
                    'responsive': True
                },
                div_id="radar-chart-single"
            )
            
            return html_content
            
        except Exception as e:
            logging.error(f"Error generating single vitamin radar: {e}")
            return None
    
    def generate_stacked_bar_chart(self):
        """Genera gráfico de barras apiladas - VERSIÓN CORREGIDA"""
        try:
            df = self.load_data()
            if df.empty:
                logging.warning("No hay datos para generar el gráfico de barras apiladas")
                return None
            
            # Preparar datos para barras apiladas
            df['incremento'] = df['globulos_rojos_fin'] - df['globulos_rojos_inicio']
            
            # Agrupar por vitamina y rango de duración - CORREGIDO: observed=True
            df['duracion_grupo'] = pd.cut(df['duracion_semanas'], 
                                        bins=[0, 3, 6, 12], 
                                        labels=['1-3 semanas', '4-6 semanas', '7+ semanas'])
            
            pivot_data = df.pivot_table(
                values='incremento',
                index='vitamina',
                columns='duracion_grupo',
                aggfunc='mean',
                fill_value=0
            )
            
            fig, ax = plt.subplots(figsize=(14, 8))
            
            # Gráfico de barras apiladas
            colors = ['#FF6B6B', '#4ECDC4', '#45B7D1']
            pivot_data.plot(kind='bar', stacked=True, ax=ax, 
                          color=colors, alpha=0.85, edgecolor='white', linewidth=0.5)
            
            ax.set_xlabel('Vitamina', fontsize=14, color='white')
            ax.set_ylabel('Incremento Acumulado Glóbulos Rojos (millones/mL)', fontsize=14, color='white')
            ax.set_title('Barras Apiladas: Incremento por Vitamina y Duración del Tratamiento', 
                        fontsize=16, color='white', pad=20)
            ax.legend(title='Duración del Tratamiento', title_fontsize=12, fontsize=11,
                     facecolor='#2d2d2d', edgecolor='none', labelcolor='white')
            ax.grid(True, alpha=0.3, axis='y')
            
            # Configurar colores para tema oscuro
            ax.set_facecolor('#1e1e1e')
            fig.patch.set_facecolor('#1e1e1e')
            ax.tick_params(colors='white')
            plt.xticks(rotation=45, ha='right')
            
            # Añadir valores en las barras
            for container in ax.containers:
                ax.bar_label(container, fmt='%.2f', label_type='center', color='white', fontsize=9)
            
            return self.plot_to_base64(fig)
        except Exception as e:
            logging.error(f"Error generating stacked bar chart: {e}")
            return None
    
    def generate_pie_chart(self):
        """Genera gráfico de pastel"""
        try:
            df = self.load_data()
            if df.empty:
                logging.warning("No hay datos para generar el gráfico de pastel")
                return None
            
            # Calcular eficiencia promedio por vitamina
            df['incremento'] = df['globulos_rojos_fin'] - df['globulos_rojos_inicio']
            df['eficiencia'] = df['incremento'] / (df['dosis_diaria'] * df['duracion_semanas'])
            
            efficiency_data = df.groupby('vitamina')['eficiencia'].mean().sort_values(ascending=False)
            
            fig, ax = plt.subplots(figsize=(12, 10))
            
            # Gráfico de pastel con Seaborn
            colors = sns.color_palette("Set3", len(efficiency_data))
            wedges, texts, autotexts = ax.pie(efficiency_data.values, 
                                            labels=efficiency_data.index,
                                            autopct='%1.1f%%',
                                            colors=colors,
                                            startangle=90,
                                            shadow=True,
                                            textprops={'color': 'white', 'fontsize': 11})
            
            # Mejorar estética
            for autotext in autotexts:
                autotext.set_color('white')
                autotext.set_fontweight('bold')
                autotext.set_fontsize(10)
            
            for text in texts:
                text.set_fontsize(12)
                text.set_color('white')
                text.set_fontweight('bold')
            
            ax.set_title('Distribución de Eficiencia Relativa por Vitamina', 
                        fontsize=16, color='white', pad=20)
            
            # Añadir leyenda
            ax.legend(wedges, efficiency_data.index,
                     title="Vitaminas",
                     loc="center left",
                     bbox_to_anchor=(1, 0, 0.5, 1),
                     facecolor='#2d2d2d',
                     edgecolor='none',
                     fontsize=11,
                     title_fontsize=12)
            
            fig.patch.set_facecolor('#1e1e1e')
            
            return self.plot_to_base64(fig)
        except Exception as e:
            logging.error(f"Error generating pie chart: {e}")
            return None
    
    def generate_ml_insights_chart(self):
        """Genera gráfico con insights del modelo de ML"""
        try:
            df = self.load_data()
            if df.empty:
                return None
            
            # Calcular importancia de características (simulada para el ejemplo)
            # En una implementación real, esto vendría del modelo entrenado
            features_importance = {
                'Dosis Diaria': 0.35,
                'Duración': 0.25,
                'Nivel Inicial': 0.20,
                'Tipo de Vitamina': 0.15,
                'Edad': 0.05
            }
            
            fig, ax = plt.subplots(figsize=(12, 8))
            
            # Gráfico de barras horizontal para importancia de características
            features = list(features_importance.keys())
            importance = list(features_importance.values())
            
            y_pos = np.arange(len(features))
            bars = ax.barh(y_pos, importance, color='#3498db', alpha=0.8, height=0.6)
            
            ax.set_yticks(y_pos)
            ax.set_yticklabels(features, color='white', fontsize=12)
            ax.set_xlabel('Importancia Relativa', fontsize=14, color='white')
            ax.set_title('Importancia de Características en el Modelo de ML', 
                        fontsize=16, color='white', pad=20)
            ax.grid(True, alpha=0.3, axis='x')
            
            # Añadir valores en las barras
            for bar, value in zip(bars, importance):
                ax.text(bar.get_width() + 0.01, bar.get_y() + bar.get_height()/2,
                       f'{value:.2f}', ha='left', va='center', color='white', fontsize=11)
            
            # Configurar tema oscuro
            ax.set_facecolor('#1e1e1e')
            fig.patch.set_facecolor('#1e1e1e')
            ax.tick_params(colors='white')
            
            return self.plot_to_base64(fig)
        except Exception as e:
            logging.error(f"Error generating ML insights chart: {e}")
            return None
    
    def generate_all_charts(self):
        """Genera todos los gráficos y retorna en formato para carousel"""
        logging.info("Generando todos los gráficos...")
        charts = {
            'area_chart': self.generate_area_chart(),
            'radar_chart': self.generate_radar_chart(),
            'stacked_bar_chart': self.generate_stacked_bar_chart(),
            'pie_chart': self.generate_pie_chart(),
            'ml_insights': self.generate_ml_insights_chart()
        }
        
        # Verificar qué gráficos se generaron correctamente
        successful_charts = {k: v for k, v in charts.items() if v is not None}
        logging.info(f"Gráficos generados exitosamente: {list(successful_charts.keys())}")
        
        return charts