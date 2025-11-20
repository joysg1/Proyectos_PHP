import json
import pandas as pd
import numpy as np
import base64
import io
import matplotlib
# Configurar matplotlib para usar backend no interactivo
matplotlib.use('Agg')
from matplotlib import pyplot as plt
import seaborn as sns
from datetime import datetime
import logging

# CONFIGURACIÓN DEFINITIVA PARA EVITAR WARNINGS DE FUENTES
import matplotlib.font_manager as fm
plt.rcParams['font.family'] = 'DejaVu Sans'
plt.rcParams['font.sans-serif'] = ['DejaVu Sans', 'Arial', 'Liberation Sans']
plt.rcParams['axes.unicode_minus'] = False

# Configuración global para mejor contraste y tamaño
plt.rcParams['figure.dpi'] = 100
plt.rcParams['savefig.dpi'] = 150
plt.rcParams['figure.figsize'] = [12, 8]

logger = logging.getLogger(__name__)

class DataProcessor:
    def __init__(self, data_file):
        self.data_file = data_file
        # Configurar estilo seaborn más elegante con mejor contraste
        plt.style.use('dark_background')
        sns.set_theme(style="whitegrid", context="talk", palette="husl")
        self.load_data()
        
    def load_data(self):
        """Cargar datos desde el archivo JSON"""
        try:
            with open(self.data_file, 'r', encoding='utf-8') as f:
                self.data = json.load(f)
            logger.info(f"✅ Datos cargados correctamente: {len(self.data.get('years', []))} años de datos")
        except Exception as e:
            logger.error(f"Error cargando datos: {e}")
            self.data = {}
    
    def get_gas_data(self):
        """Obtener datos de gases por año"""
        return {
            'years': self.data['years'],
            'gases': self.data['gases']
        }
    
    def get_sector_data(self):
        """Obtener datos por sector"""
        return {
            'years': self.data['years'],
            'sectors': self.data['sectors']
        }
    
    def get_region_data(self):
        """Obtener datos por región"""
        return {
            'years': self.data['years'],
            'regions': self.data['regions']
        }
    
    def plot_to_base64(self, plt_figure):
        """Convertir gráfico matplotlib a base64 con mejor calidad"""
        try:
            img = io.BytesIO()
            # Configuración mejorada para alta calidad y contraste
            plt_figure.savefig(img, format='png', dpi=200, bbox_inches='tight', 
                              facecolor='#0a0f1c', edgecolor='none',
                              transparent=False, pad_inches=0.5)
            img.seek(0)
            return base64.b64encode(img.getvalue()).decode()
        except Exception as e:
            logger.error(f"Error convirtiendo gráfico a base64: {e}")
            return None
    
    def create_area_chart(self):
        """Crear gráfico de área de escenarios futuros - MEJORADO TAMAÑO Y CONTRASTE"""
        try:
            # Configuración de estilo seaborn con mejor contraste
            sns.set_palette("viridis")
            
            # Tamaño más grande para mejor visualización
            fig, ax = plt.subplots(figsize=(16, 10))
            
            years = self.data['years']
            scenarios = self.data['scenarios']
            
            # Filtrar años desde 2024 para mejor visualización
            display_years = [year for year in years if year >= 2024]
            if not display_years:
                display_years = years[-8:]
            
            # Preparar datos
            display_indices = [years.index(year) for year in display_years]
            
            # Crear DataFrame para seaborn
            scenario_data = []
            for scenario_name, scenario_values in scenarios.items():
                for i, year in enumerate(display_years):
                    scenario_data.append({
                        'Año': year,
                        'Emisiones': scenario_values[display_indices[i]],
                        'Escenario': scenario_name
                    })
            
            df = pd.DataFrame(scenario_data)
            
            # Crear gráfico de área con seaborn
            pivot_df = df.pivot(index='Año', columns='Escenario', values='Emisiones')
            
            # Paleta de colores mejorada con mejor contraste
            colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFE66D']
            
            # Gráfico de área apilado
            ax = pivot_df.plot.area(
                ax=ax,
                alpha=0.85,
                color=colors[:len(pivot_df.columns)],
                linewidth=2,
                stacked=False
            )
            
            # MEJORAR ESTÉTICA - FUENTES MÁS GRANDES Y MEJOR CONTRASTE
            ax.set_xlabel('Año', fontsize=16, fontweight='bold', labelpad=15, color='white')
            ax.set_ylabel('Emisiones Totales (MtCO2eq)', fontsize=16, fontweight='bold', labelpad=15, color='white')
            ax.set_title('Escenarios de Emisiones de GEI 2024-2100\nAnálisis de Trayectorias Climáticas', 
                        fontsize=20, fontweight='bold', pad=30, color='white')
            
            # Personalizar ejes - MEJOR CONTRASTE
            ax.tick_params(axis='both', which='major', labelsize=14, colors='white')
            ax.tick_params(axis='both', which='minor', labelsize=12, colors='white')
            ax.grid(True, alpha=0.3, linestyle='-', linewidth=0.5, color='white')
            
            # Fondo mejorado
            ax.set_facecolor('#0a0f1c')
            fig.patch.set_facecolor('#0a0f1c')
            
            # Formatear eje Y
            max_value = pivot_df.max().max()
            if max_value > 100000:
                ax.yaxis.set_major_formatter(plt.FuncFormatter(lambda x, p: f'{x/1000:.0f}K'))
                ax.set_ylabel('Emisiones (KtCO2eq)', fontsize=16, fontweight='bold', color='white')
            
            # Leyenda mejorada - MÁS GRANDE Y MEJOR CONTRASTE
            legend = ax.legend(
                fontsize=14,
                framealpha=0.95,
                loc='upper left',
                bbox_to_anchor=(0, 1),
                ncol=1,
                fancybox=True,
                shadow=True,
                facecolor='#1a2238',
                edgecolor='white',
                labelcolor='white'
            )
            
            # Añadir anotaciones con mejor contraste
            current_year = 2024
            if current_year in years:
                current_idx = years.index(current_year)
                current_emissions = sum([gas_data[current_idx] for gas_data in self.data['gases'].values()])
                
                ax.annotate(
                    f'Emisiones Actuales ({current_year}):\n{current_emissions/1000:.0f}K MtCO2eq',
                    xy=(current_year, current_emissions),
                    xytext=(current_year + 5, current_emissions * 0.7),
                    arrowprops=dict(
                        arrowstyle='->',
                        color='white',
                        lw=2,
                        alpha=0.8,
                        connectionstyle="arc3,rad=0.1"
                    ),
                    fontsize=12,
                    color='white',
                    ha='left',
                    va='center',
                    bbox=dict(boxstyle="round,pad=0.5", facecolor='#2a2a2a', alpha=0.9, edgecolor='white')
                )
            
            plt.tight_layout()
            chart_base64 = self.plot_to_base64(fig)
            plt.close(fig)
            
            return chart_base64
        except Exception as e:
            logger.error(f"Error creando area chart: {e}")
            return None
    
    def create_radar_chart(self):
        """Crear gráfico radar del Potencial de Calentamiento Global - MEJORADO"""
        try:
            # Configurar estilo con mejor contraste
            sns.set_palette("rocket")
            
            fig = plt.figure(figsize=(16, 12))  # Más grande
            ax = fig.add_subplot(111, polar=True)
            
            # Datos de GWP
            gases = ['CO2', 'CH4', 'N2O', 'HFC-134a', 'PFC-14', 'SF6']
            gwp_values = [1, 28, 265, 1300, 6630, 23500]
            lifetimes = [100, 12, 114, 14, 50000, 3200]
            
            # Normalizar para escala logarítmica
            normalized_gwp = np.log10(gwp_values)
            
            # Ángulos
            angles = np.linspace(0, 2*np.pi, len(gases), endpoint=False).tolist()
            angles += angles[:1]
            normalized_gwp = list(normalized_gwp) + [normalized_gwp[0]]
            
            # Plot con estilo mejorado - MÁS GRUESO Y MEJOR CONTRASTE
            ax.plot(angles, normalized_gwp, 'o-', linewidth=5, markersize=15, 
                   color='#4ECDC4', label='Potencial de Calentamiento (GWP)', alpha=0.9)
            ax.fill(angles, normalized_gwp, alpha=0.4, color='#4ECDC4')
            
            # Configurar ejes - MEJOR CONTRASTE
            ax.set_xticks(angles[:-1])
            ax.set_xticklabels(gases, fontsize=16, fontweight='bold', color='white')
            
            # Eje Y mejorado
            y_ticks = [0, 1, 2, 3, 4]
            y_labels = ['1 (CO2)', '10', '100', '1,000', '10,000+']
            ax.set_yticks(y_ticks)
            ax.set_yticklabels(y_labels, fontsize=14, fontweight='bold', color='white')
            ax.set_ylim(0, 4.5)
            
            # Configuración polar
            ax.set_theta_offset(np.pi / 2)
            ax.set_theta_direction(-1)
            ax.grid(True, alpha=0.4, linestyle='-', linewidth=1, color='white')
            ax.set_facecolor('#0a0f1c')
            fig.patch.set_facecolor('#0a0f1c')
            
            ax.set_title('Potencial de Calentamiento Global (GWP - 100 años)\nComparativa de Impacto Climático Relativo', 
                        fontsize=18, fontweight='bold', pad=40, color='white')
            
            # Añadir valores con mejor contraste
            colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFE66D', '#FF9FF3']
            for angle, gwp, gas, lifetime, color in zip(angles[:-1], gwp_values, gases, lifetimes, colors):
                y_pos = normalized_gwp[angles.index(angle)]
                ax.text(angle, y_pos + 0.3, 
                       f'GWP: {gwp:,}\nVida: {lifetime}a', 
                       ha='center', va='center', fontsize=12, color='white', 
                       bbox=dict(boxstyle="round,pad=0.4", facecolor=color, alpha=0.9, edgecolor='white'),
                       fontweight='bold')
                
                ax.scatter(angle, y_pos, color=color, s=150, zorder=5, 
                          edgecolors='white', linewidth=3)
            
            plt.tight_layout()
            chart_base64 = self.plot_to_base64(fig)
            plt.close(fig)
            
            return chart_base64
        except Exception as e:
            logger.error(f"Error creando radar chart: {e}")
            return None
    
    def create_stacked_bar_chart(self):
        """Crear gráfico de barras apiladas por sector - MEJORADO"""
        try:
            # Configurar estilo seaborn con mejor contraste
            sns.set_palette("Set2")
            
            fig, ax = plt.subplots(figsize=(18, 12))  # Más grande
            
            years = self.data['years']
            sectors_data = self.data['sectors']
            
            # Seleccionar años estratégicos
            if len(years) > 12:
                key_years = [years[0], 2024, years[-1]]
                year_range = years[-1] - years[0]
                step = max(10, year_range // 8)
                
                for year in range(years[0] + step, years[-1], step):
                    if year in years and year not in key_years:
                        key_years.append(year)
                
                display_years = sorted(set(key_years))
            else:
                display_years = years
            
            if len(display_years) > 8:
                display_years = [display_years[0]] + display_years[-7:]
            
            # Preparar datos para seaborn
            display_indices = [years.index(year) for year in display_years]
            
            # Crear DataFrame largo para seaborn
            plot_data = []
            for sector, values in sectors_data.items():
                for i, year in enumerate(display_years):
                    plot_data.append({
                        'Año': year,
                        'Emisiones': values[display_indices[i]],
                        'Sector': sector
                    })
            
            df = pd.DataFrame(plot_data)
            
            # Gráfico de barras apiladas con seaborn
            pivot_df = df.pivot(index='Año', columns='Sector', values='Emisiones')
            
            # Colores personalizados con mejor contraste
            colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFE66D', '#FF9FF3']
            
            # Gráfico de barras - BARRAS MÁS ANCHAS
            bars = pivot_df.plot.bar(
                ax=ax,
                stacked=True,
                color=colors,
                alpha=0.9,
                edgecolor='white',
                linewidth=1.5,
                width=0.8  # Barras más anchas
            )
            
            # MEJORAR CONTRASTE EN TEXTOS
            ax.set_xlabel('Año', fontsize=16, fontweight='bold', labelpad=15, color='white')
            ax.set_ylabel('Emisiones (MtCO2eq)', fontsize=16, fontweight='bold', labelpad=15, color='white')
            ax.set_title('Evolución de Emisiones por Sector Económico\nAnálisis de Contribuciones Sectoriales', 
                        fontsize=20, fontweight='bold', pad=30, color='white')
            
            # Fondo mejorado
            ax.set_facecolor('#0a0f1c')
            fig.patch.set_facecolor('#0a0f1c')
            
            # Mejorar leyenda
            legend = ax.legend(
                fontsize=14,
                framealpha=0.95,
                loc='upper left',
                bbox_to_anchor=(1.02, 1),
                ncol=1,
                fancybox=True,
                shadow=True,
                title='Sectores',
                title_fontsize=14,
                facecolor='#1a2238',
                edgecolor='white',
                labelcolor='white'
            )
            legend.get_title().set_color('white')
            
            # Configurar ejes - MEJOR CONTRASTE
            ax.tick_params(axis='both', which='major', labelsize=14, colors='white')
            ax.tick_params(axis='x', rotation=45)
            ax.grid(True, alpha=0.3, axis='y', color='white')
            
            # Formatear eje Y
            total_values = pivot_df.sum(axis=1)
            y_max = max(total_values) * 1.15
            ax.set_ylim(0, y_max)
            
            if y_max > 50000:
                ax.yaxis.set_major_formatter(plt.FuncFormatter(lambda x, p: f'{x/1000:.0f}K'))
                ax.set_ylabel('Emisiones (KtCO2eq)', fontsize=16, fontweight='bold', color='white')
            
            # Añadir valores en las barras para mejor legibilidad
            for container in ax.containers:
                ax.bar_label(container, labels=[f'{v/1000:.0f}K' if v > 5000 else '' for v in container.datavalues],
                           label_type='center', fontsize=10, color='white', fontweight='bold')
            
            plt.tight_layout()
            chart_base64 = self.plot_to_base64(fig)
            plt.close(fig)
            
            return chart_base64
        except Exception as e:
            logger.error(f"Error creando stacked bar chart: {e}")
            return None
    
    def create_pie_chart(self):
        """Crear gráfico de pastel de distribución regional - MEJORADO"""
        try:
            # Configurar estilo con mejor contraste
            sns.set_palette("pastel")
            
            fig, ax = plt.subplots(figsize=(16, 12))  # Más grande
            
            regions_data = self.data['regions']
            
            # Usar año más reciente
            if 2024 in self.data['years']:
                target_year = 2024
                year_index = self.data['years'].index(2024)
            else:
                target_year = self.data['years'][-1]
                year_index = -1
            
            current_data = [region_data[year_index] for region_data in regions_data.values()]
            regions = list(regions_data.keys())
            
            # Nombres legibles
            region_names = {
                'Asia': 'Asia',
                'America_Norte': 'América Norte',
                'Europa': 'Europa',
                'America_Sur': 'América Sur',
                'Africa': 'África',
                'Oceania': 'Oceanía'
            }
            display_regions = [region_names.get(r, r) for r in regions]
            
            # Calcular porcentajes
            total_emissions = sum(current_data)
            percentages = [data/total_emissions * 100 for data in current_data]
            
            # Ordenar
            sorted_indices = np.argsort(percentages)[::-1]
            sorted_data = [current_data[i] for i in sorted_indices]
            sorted_regions = [display_regions[i] for i in sorted_indices]
            sorted_percentages = [percentages[i] for i in sorted_indices]
            
            # Colores seaborn con mejor contraste
            colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFE66D', '#96CEB4', '#FF9FF3']
            
            # Crear donut chart - MÁS GRANDE Y MEJOR CONTRASTE
            wedges, texts, autotexts = ax.pie(
                sorted_data, 
                labels=sorted_regions, 
                colors=colors,
                autopct='%1.1f%%', 
                startangle=90,
                shadow=True,
                textprops={'fontsize': 14, 'fontweight': 'bold', 'color': 'white'},
                pctdistance=0.85,
                wedgeprops={'edgecolor': 'white', 'linewidth': 3}
            )
            
            # MEJORAR CONTRASTE EN PORCENTAJES
            for autotext in autotexts:
                autotext.set_color('white')
                autotext.set_fontweight('bold')
                autotext.set_fontsize(12)
            
            # Centro blanco más grande
            centre_circle = plt.Circle((0,0),0.70,fc='#0a0f1c')
            fig.gca().add_artist(centre_circle)
            
            ax.set_title(f'Distribución Regional de Emisiones Globales ({target_year})\nAnálisis de Contribuciones Geográficas', 
                        fontsize=20, fontweight='bold', pad=40, color='white')
            
            # Fondo
            fig.patch.set_facecolor('#0a0f1c')
            
            # Texto central más grande
            ax.text(0, 0, f'Total\n{total_emissions/1000:.0f}K\nMtCO2eq', 
                   ha='center', va='center', fontsize=16, fontweight='bold', color='white',
                   bbox=dict(boxstyle="round,pad=1", facecolor='#2a2a2a', alpha=0.9, edgecolor='white'))
            
            plt.tight_layout()
            chart_base64 = self.plot_to_base64(fig)
            plt.close(fig)
            
            return chart_base64
        except Exception as e:
            logger.error(f"Error creando pie chart: {e}")
            return None
    
    def create_trend_comparison(self):
        """Crear gráfico de comparación de tendencias de gases - MEJORADO"""
        try:
            # Configurar estilo con mejor contraste
            sns.set_palette("tab10")
            
            fig, ax = plt.subplots(figsize=(18, 12))  # Más grande
            
            years = self.data['years']
            gases_data = self.data['gases']
            
            # Filtrar años desde 2000
            if 2000 in years:
                start_year = 2000
            else:
                start_year = max(years[0], years[-20] if len(years) > 20 else years[0])
            
            display_years = [year for year in years if year >= start_year]
            display_indices = [years.index(year) for year in display_years]
            
            # Normalizar datos
            base_year = display_years[0]
            base_index = display_indices[0]
            
            # Crear DataFrame para seaborn
            trend_data = []
            for gas, values in gases_data.items():
                base_value = values[base_index]
                for i, year in enumerate(display_years):
                    normalized_value = (values[display_indices[i]] / base_value) * 100
                    trend_data.append({
                        'Año': year,
                        'Índice': normalized_value,
                        'Gas': gas
                    })
            
            df = pd.DataFrame(trend_data)
            
            # Gráfico de líneas con seaborn - LÍNEAS MÁS GRUESAS
            sns.lineplot(
                data=df,
                x='Año',
                y='Índice',
                hue='Gas',
                ax=ax,
                linewidth=4,
                markers=True,
                markersize=10,
                dashes=False
            )
            
            # MEJORAR CONTRASTE EN TODO EL GRÁFICO
            ax.set_xlabel('Año', fontsize=16, fontweight='bold', labelpad=15, color='white')
            ax.set_ylabel(f'Índice ({base_year} = 100)', fontsize=16, fontweight='bold', labelpad=15, color='white')
            ax.set_title('Tendencias Relativas de Gases de Efecto Invernadero\nEvolución Comparativa desde Año Base', 
                        fontsize=20, fontweight='bold', pad=30, color='white')
            
            # Fondo mejorado
            ax.set_facecolor('#0a0f1c')
            fig.patch.set_facecolor('#0a0f1c')
            
            # Leyenda mejorada - MÁS GRANDE Y MEJOR CONTRASTE
            legend = ax.legend(
                fontsize=14,
                framealpha=0.95,
                loc='upper left',
                bbox_to_anchor=(0, 1),
                ncol=2,
                fancybox=True,
                shadow=True,
                title='Gases',
                title_fontsize=14,
                facecolor='#1a2238',
                edgecolor='white',
                labelcolor='white'
            )
            legend.get_title().set_color('white')
            
            ax.grid(True, alpha=0.3, color='white')
            ax.tick_params(axis='both', which='major', labelsize=14, colors='white')
            
            # Línea de referencia más visible
            ax.axhline(y=100, color='white', linestyle='--', alpha=0.8, linewidth=3)
            ax.text(display_years[-1], 102, f'Nivel {base_year}', 
                   ha='right', va='bottom', fontsize=14, color='white', fontweight='bold',
                   bbox=dict(boxstyle="round,pad=0.5", facecolor='#2a2a2a', alpha=0.9))
            
            plt.tight_layout()
            chart_base64 = self.plot_to_base64(fig)
            plt.close(fig)
            
            return chart_base64
        except Exception as e:
            logger.error(f"Error creando trend comparison: {e}")
            return None

# Para testing directo del archivo
if __name__ == "__main__":
    print("🧪 Testing DataProcessor - Gráficos mejorados...")
    
    try:
        processor = DataProcessor('greenhouse_gas_data.json')
        print(f"✅ DataProcessor creado exitosamente!")
        
        # Probar generación de gráficos
        print("\n🔄 Generando gráficos mejorados...")
        
        charts = {
            'area': processor.create_area_chart(),
            'radar': processor.create_radar_chart(),
            'stacked_bar': processor.create_stacked_bar_chart(),
            'pie': processor.create_pie_chart(),
            'trend': processor.create_trend_comparison()
        }
        
        successful_charts = {name: "✅" if chart else "❌" for name, chart in charts.items()}
        print("📈 Resultados de generación de gráficos:")
        for chart_name, status in successful_charts.items():
            print(f"   {chart_name}: {status}")
            
    except Exception as e:
        print(f"❌ Error en testing: {e}")
        import traceback
        traceback.print_exc()