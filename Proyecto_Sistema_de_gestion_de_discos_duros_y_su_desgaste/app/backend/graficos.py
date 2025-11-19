import os
import json
import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
import numpy as np
from io import BytesIO
from math import pi

class GeneradorGraficos:
    def __init__(self, data_path):
        self.data_path = data_path
        self.configurar_estilos()
    
    def configurar_estilos(self):
        """Configurar estilos de seaborn para gráficos atractivos"""
        sns.set_theme(style="darkgrid")
        plt.style.use('dark_background')
        
        # Configuración de colores para modo oscuro
        self.colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD', '#98D8C8', '#F7DC6F']
        
        # Colores específicos para cada tipo de disco
        self.colors_ssd = '#4ECDC4'    # Verde azulado
        self.colors_hdd = '#FF6B6B'    # Rojo
        self.colors_nvme = '#45B7D1'   # Azul
        self.colors_otros = '#FFEAA7'  # Amarillo
        
        sns.set_palette(sns.color_palette(self.colors))
    
    def cargar_datos(self):
        """Cargar datos desde el archivo JSON"""
        try:
            with open(self.data_path, 'r', encoding='utf-8') as f:
                return json.load(f)
        except Exception as e:
            print(f"Error cargando datos: {e}")
            return self._generar_datos_ejemplo()
    
    def _generar_datos_ejemplo(self):
        """Generar datos de ejemplo si no existe el archivo"""
        datos_ejemplo = {
            "discos_duros": [
                {
                    "id": 1, "tipo": "SSD", "marca": "Samsung", "modelo": "870 EVO",
                    "capacidad_gb": 1000, "tiempo_uso_meses": 6, "porcentaje_desgaste": 5,
                    "horas_encendido": 4320, "ciclos_escritura": 1500, "temperatura_promedio": 45,
                    "bad_sectors": 0, "estado": "Excelente"
                },
                {
                    "id": 2, "tipo": "HDD", "marca": "Western Digital", "modelo": "Blue", 
                    "capacidad_gb": 2000, "tiempo_uso_meses": 24, "porcentaje_desgaste": 35,
                    "horas_encendido": 17280, "ciclos_escritura": 8500, "temperatura_promedio": 38,
                    "bad_sectors": 12, "estado": "Moderado"
                },
                {
                    "id": 3, "tipo": "NVMe", "marca": "WD Black", "modelo": "SN850",
                    "capacidad_gb": 2000, "tiempo_uso_meses": 12, "porcentaje_desgaste": 15,
                    "horas_encendido": 8640, "ciclos_escritura": 8000, "temperatura_promedio": 52,
                    "bad_sectors": 0, "estado": "Bueno"
                }
            ],
            "metricas_por_tipo": {
                "SSD": {"vida_util_meses": 60},
                "HDD": {"vida_util_meses": 48},
                "NVMe": {"vida_util_meses": 72}
            }
        }
        
        # Guardar datos de ejemplo
        os.makedirs(os.path.dirname(self.data_path), exist_ok=True)
        with open(self.data_path, 'w', encoding='utf-8') as f:
            json.dump(datos_ejemplo, f, indent=2)
        
        return datos_ejemplo
    
    def _obtener_colores_por_tipo(self, tipos):
        """Obtener colores específicos para cada tipo de disco"""
        colores = []
        for tipo in tipos:
            if tipo == 'SSD':
                colores.append(self.colors_ssd)
            elif tipo == 'HDD':
                colores.append(self.colors_hdd)
            elif tipo == 'NVMe':
                colores.append(self.colors_nvme)
            else:
                colores.append(self.colors_otros)
        return colores
    
    def grafico_area(self):
        """Generar gráfico de área de desgaste por tipo de disco - CORREGIDO"""
        datos = self.cargar_datos()
        df = pd.DataFrame(datos['discos_duros'])
        
        # Verificar que hay datos
        if df.empty:
            print("❌ No hay datos para generar el gráfico de área")
            return self._generar_grafico_vacio("No hay datos disponibles")
        
        print(f"📊 Tipos de disco en datos: {df['tipo'].unique()}")
        
        fig, ax = plt.subplots(figsize=(12, 6))
        
        try:
            # Agrupar por tipo y tiempo de uso
            df_grouped = df.groupby(['tipo', 'tiempo_uso_meses'])['porcentaje_desgaste'].mean().unstack('tipo')
            
            # Rellenar valores faltantes para una línea continua
            df_grouped = df_grouped.fillna(method='ffill').fillna(method='bfill').fillna(0)
            
            # Ordenar por tiempo de uso
            df_grouped = df_grouped.sort_index()
            
            print(f"📈 Datos agrupados: {df_grouped.columns.tolist()}")
            
            # Obtener colores específicos para cada tipo
            tipos_presentes = df_grouped.columns
            colores = self._obtener_colores_por_tipo(tipos_presentes)
            
            # Gráfico de área
            for i, tipo in enumerate(tipos_presentes):
                valores = df_grouped[tipo].values
                if len(valores) > 0:
                    ax.fill_between(df_grouped.index, valores, alpha=0.4, 
                                  label=f'{tipo}', color=colores[i])
                    ax.plot(df_grouped.index, valores, linewidth=2.5, 
                           color=colores[i], marker='o', markersize=6)
            
            ax.set_title('Evolución del Desgaste por Tipo de Disco Duro', 
                        fontsize=16, fontweight='bold', pad=20)
            ax.set_xlabel('Tiempo de Uso (meses)', fontsize=12)
            ax.set_ylabel('Porcentaje de Desgaste (%)', fontsize=12)
            ax.legend(loc='upper left')
            ax.grid(True, alpha=0.3)
            ax.set_ylim(0, 100)
            
            # Añadir anotaciones para cada punto
            for tipo in tipos_presentes:
                for idx, valor in df_grouped[tipo].items():
                    if not np.isnan(valor):
                        ax.annotate(f'{valor:.1f}%', 
                                  (idx, valor), 
                                  textcoords="offset points", 
                                  xytext=(0,10), 
                                  ha='center', 
                                  fontsize=8,
                                  alpha=0.8)
            
        except Exception as e:
            print(f"❌ Error generando gráfico de área: {e}")
            ax.text(0.5, 0.5, f'Error: {str(e)}', 
                   horizontalalignment='center', verticalalignment='center',
                   transform=ax.transAxes, fontsize=12, color='red')
        
        plt.tight_layout()
        return self._fig_a_buffer(fig)
    
    def grafico_radar(self):
        """Generar gráfico radar de métricas de salud del disco - CORREGIDO"""
        datos = self.cargar_datos()
        df = pd.DataFrame(datos['discos_duros'])
        
        if df.empty:
            print("❌ No hay datos para generar el gráfico radar")
            return self._generar_grafico_vacio("No hay datos disponibles")
        
        print(f"📊 Tipos de disco para radar: {df['tipo'].unique()}")
        
        fig = plt.figure(figsize=(10, 10))
        ax = fig.add_subplot(111, polar=True)
        
        try:
            # Agrupar por tipo y calcular promedios
            metricas_por_tipo = df.groupby('tipo').agg({
                'porcentaje_desgaste': 'mean',
                'temperatura_promedio': 'mean',
                'ciclos_escritura': lambda x: x.mean() / 1000,  # Normalizar
                'bad_sectors': 'mean',
                'horas_encendido': lambda x: x.mean() / 1000   # Normalizar
            }).reset_index()
            
            print(f"📈 Métricas por tipo: {metricas_por_tipo['tipo'].tolist()}")
            
            # Normalizar valores para el radar (0-100)
            for col in metricas_por_tipo.columns[1:]:
                if metricas_por_tipo[col].max() > 0:
                    # Evitar división por cero
                    max_val = metricas_por_tipo[col].max()
                    if max_val > 0:
                        metricas_por_tipo[col] = (metricas_por_tipo[col] / max_val) * 100
                else:
                    metricas_por_tipo[col] = 50  # Valor medio si no hay datos
            
            # Categorías para el radar
            categorias = ['Desgaste', 'Temperatura', 'Ciclos Escritura', 'Sectores Dañados', 'Horas Uso']
            N = len(categorias)
            
            # Ángulos para cada categoría
            angles = [n / float(N) * 2 * pi for n in range(N)]
            angles += angles[:1]  # Completar el círculo
            
            # Obtener tipos y colores
            tipos_presentes = metricas_por_tipo['tipo'].unique()
            colores = self._obtener_colores_por_tipo(tipos_presentes)
            
            # Dibujar cada tipo
            for idx, tipo in enumerate(tipos_presentes):
                fila = metricas_por_tipo[metricas_por_tipo['tipo'] == tipo].iloc[0]
                valores = fila[1:].tolist()
                valores += valores[:1]  # Completar el círculo
                
                color = colores[idx]
                
                ax.plot(angles, valores, 'o-', linewidth=2, label=tipo, color=color)
                ax.fill(angles, valores, alpha=0.25, color=color)
            
            # Configurar el gráfico radar
            ax.set_theta_offset(pi / 2)
            ax.set_theta_direction(-1)
            ax.set_xticks(angles[:-1])
            ax.set_xticklabels(categorias)
            ax.set_ylim(0, 100)
            ax.set_yticks([20, 40, 60, 80, 100])
            ax.set_yticklabels(['20', '40', '60', '80', '100'])
            ax.grid(True, alpha=0.3)
            ax.legend(loc='upper right', bbox_to_anchor=(1.3, 1.1))
            ax.set_title('Métricas de Salud por Tipo de Disco - Radar', 
                        size=16, fontweight='bold', pad=20)
            
        except Exception as e:
            print(f"❌ Error generando gráfico radar: {e}")
            ax.text(0.5, 0.5, f'Error: {str(e)}', 
                   horizontalalignment='center', verticalalignment='center',
                   transform=ax.transAxes, fontsize=12, color='red')
        
        return self._fig_a_buffer(fig)
    
    def grafico_barras_apiladas(self):
        """Generar gráfico de barras apiladas de estado de discos por tipo - CORREGIDO"""
        datos = self.cargar_datos()
        df = pd.DataFrame(datos['discos_duros'])
        
        if df.empty:
            print("❌ No hay datos para generar gráfico de barras")
            return self._generar_grafico_vacio("No hay datos disponibles")
        
        print(f"📊 Tipos de disco para barras: {df['tipo'].unique()}")
        
        fig, ax = plt.subplots(figsize=(12, 6))
        
        try:
            # Clasificar discos por estado
            def clasificar_estado(desgaste):
                if desgaste < 20: return 'Excelente'
                elif desgaste < 40: return 'Bueno'
                elif desgaste < 60: return 'Moderado'
                elif desgaste < 80: return 'Alto'
                else: return 'Crítico'
            
            df['estado_calculado'] = df['porcentaje_desgaste'].apply(clasificar_estado)
            
            # Crear tabla pivote
            pivot_table = pd.crosstab(df['tipo'], df['estado_calculado'])
            
            # Ordenar columnas por nivel de desgaste
            orden_estados = ['Excelente', 'Bueno', 'Moderado', 'Alto', 'Crítico']
            pivot_table = pivot_table.reindex(columns=orden_estados, fill_value=0)
            
            print(f"📈 Tabla pivote:\n{pivot_table}")
            
            # Colores para cada estado
            colors_estados = ['#4ECDC4', '#45B7D1', '#FFEAA7', '#FFB347', '#FF6B6B']
            
            bottom = np.zeros(len(pivot_table))
            for i, estado in enumerate(pivot_table.columns):
                valores = pivot_table[estado]
                ax.bar(pivot_table.index, valores, bottom=bottom, label=estado,
                      color=colors_estados[i], alpha=0.8, edgecolor='white', linewidth=0.5)
                bottom += valores
            
            ax.set_xlabel('Tipo de Disco', fontsize=12)
            ax.set_ylabel('Cantidad de Discos', fontsize=12)
            ax.set_title('Estado de Discos por Tipo - Barras Apiladas', 
                        fontsize=16, fontweight='bold', pad=20)
            ax.legend(title='Estado', loc='upper right')
            ax.grid(True, alpha=0.3, axis='y')
            
            # Añadir valores en las barras
            for i, (tipo, total) in enumerate(zip(pivot_table.index, bottom)):
                if total > 0:
                    ax.text(i, total + 0.1, f'{int(total)}', 
                           ha='center', va='bottom', fontweight='bold', fontsize=10)
            
            # Añadir porcentajes dentro de las barras
            for i, tipo in enumerate(pivot_table.index):
                total_tipo = bottom[i]
                if total_tipo > 0:
                    current_bottom = 0
                    for j, estado in enumerate(pivot_table.columns):
                        valor = pivot_table.loc[tipo, estado]
                        if valor > 0:
                            altura = valor / 2 + current_bottom
                            porcentaje = (valor / total_tipo) * 100
                            ax.text(i, altura, f'{porcentaje:.0f}%', 
                                   ha='center', va='center', fontweight='bold', 
                                   fontsize=8, color='white' if j > 2 else 'black')
                            current_bottom += valor
            
        except Exception as e:
            print(f"❌ Error generando gráfico de barras: {e}")
            ax.text(0.5, 0.5, f'Error: {str(e)}', 
                   horizontalalignment='center', verticalalignment='center',
                   transform=ax.transAxes, fontsize=12, color='red')
        
        plt.tight_layout()
        return self._fig_a_buffer(fig)
    
    def grafico_pastel(self):
        """Generar gráfico de pastel de distribución por tipo y estado - CORREGIDO"""
        datos = self.cargar_datos()
        df = pd.DataFrame(datos['discos_duros'])
        
        if df.empty:
            print("❌ No hay datos para generar gráfico de pastel")
            return self._generar_grafico_vacio("No hay datos disponibles")
        
        print(f"📊 Tipos de disco para pastel: {df['tipo'].unique()}")
        
        fig, (ax1, ax2) = plt.subplots(1, 2, figsize=(15, 7))
        
        try:
            # Gráfico 1: Distribución por tipo
            tipo_counts = df['tipo'].value_counts()
            colores_tipo = self._obtener_colores_por_tipo(tipo_counts.index)
            
            wedges1, texts1, autotexts1 = ax1.pie(
                tipo_counts.values, 
                labels=tipo_counts.index,
                autopct='%1.1f%%',
                colors=colores_tipo,
                startangle=90,
                explode=[0.05] * len(tipo_counts),
                shadow=True,
                textprops={'fontsize': 10}
            )
            
            for autotext in autotexts1:
                autotext.set_color('white')
                autotext.set_fontweight('bold')
                autotext.set_fontsize(9)
            
            ax1.set_title('Distribución por Tipo de Disco', 
                         fontsize=14, fontweight='bold', pad=20)
            
            # Gráfico 2: Distribución por estado
            def clasificar_estado(desgaste):
                if desgaste < 20: return 'Excelente'
                elif desgaste < 40: return 'Bueno'
                elif desgaste < 60: return 'Moderado'
                elif desgaste < 80: return 'Alto'
                else: return 'Crítico'
            
            df['estado_calculado'] = df['porcentaje_desgaste'].apply(clasificar_estado)
            estado_counts = df['estado_calculado'].value_counts()
            
            colors_estado = ['#4ECDC4', '#45B7D1', '#FFEAA7', '#FFB347', '#FF6B6B']
            
            wedges2, texts2, autotexts2 = ax2.pie(
                estado_counts.values,
                labels=estado_counts.index,
                autopct='%1.1f%%',
                colors=colors_estado[:len(estado_counts)],
                startangle=90,
                explode=[0.05] * len(estado_counts),
                shadow=True,
                textprops={'fontsize': 10}
            )
            
            for autotext in autotexts2:
                autotext.set_color('white')
                autotext.set_fontweight('bold')
                autotext.set_fontsize(9)
            
            ax2.set_title('Distribución por Estado de Desgaste', 
                         fontsize=14, fontweight='bold', pad=20)
            
        except Exception as e:
            print(f"❌ Error generando gráfico de pastel: {e}")
            for ax in [ax1, ax2]:
                ax.clear()
                ax.text(0.5, 0.5, f'Error: {str(e)}', 
                       horizontalalignment='center', verticalalignment='center',
                       transform=ax.transAxes, fontsize=12, color='red')
        
        plt.tight_layout()
        return self._fig_a_buffer(fig)
    
    def grafico_prediccion_desgaste(self, datos_prediccion):
        """Generar gráfico de predicción de desgaste futuro"""
        fig, ax = plt.subplots(figsize=(12, 6))
        
        try:
            # Datos actuales
            meses_actual = datos_prediccion['tiempo_uso_actual']
            desgaste_actual = datos_prediccion['desgaste_actual']
            
            # Predicciones
            meses_pred = datos_prediccion['meses_prediccion']
            desgaste_pred = datos_prediccion['desgaste_prediccion']
            
            # Gráfico
            ax.plot(meses_actual, desgaste_actual, 'o-', linewidth=3, 
                   color=self.colors_ssd, label='Desgaste Actual', markersize=8)
            ax.plot(meses_pred, desgaste_pred, '--', linewidth=2, 
                   color=self.colors_hdd, label='Predicción')
            
            # Área de confianza
            if 'desgaste_min' in datos_prediccion and 'desgaste_max' in datos_prediccion:
                ax.fill_between(meses_pred, datos_prediccion['desgaste_min'], 
                               datos_prediccion['desgaste_max'], alpha=0.2, 
                               color=self.colors_hdd, label='Rango Probable')
            
            # Líneas de referencia
            ax.axhline(y=80, color='red', linestyle=':', alpha=0.7, label='Límite Crítico (80%)')
            ax.axhline(y=60, color='orange', linestyle=':', alpha=0.7, label='Límite Alto (60%)')
            
            ax.set_xlabel('Tiempo de Uso (meses)', fontsize=12)
            ax.set_ylabel('Porcentaje de Desgaste (%)', fontsize=12)
            ax.set_title('Predicción de Desgaste Futuro', fontsize=16, fontweight='bold', pad=20)
            ax.legend()
            ax.grid(True, alpha=0.3)
            ax.set_ylim(0, 100)
            
        except Exception as e:
            print(f"❌ Error generando gráfico de predicción: {e}")
            ax.text(0.5, 0.5, f'Error: {str(e)}', 
                   horizontalalignment='center', verticalalignment='center',
                   transform=ax.transAxes, fontsize=12, color='red')
        
        plt.tight_layout()
        return self._fig_a_buffer(fig)
    
    def _generar_grafico_vacio(self, mensaje):
        """Generar un gráfico vacío con mensaje de error"""
        fig, ax = plt.subplots(figsize=(10, 6))
        ax.text(0.5, 0.5, mensaje, 
               horizontalalignment='center', verticalalignment='center',
               transform=ax.transAxes, fontsize=14, color='white',
               bbox=dict(boxstyle="round,pad=0.3", facecolor='#2E2E2E', edgecolor='red'))
        ax.set_facecolor('#1A1A1A')
        ax.set_xticks([])
        ax.set_yticks([])
        ax.spines['top'].set_visible(False)
        ax.spines['right'].set_visible(False)
        ax.spines['bottom'].set_visible(False)
        ax.spines['left'].set_visible(False)
        return self._fig_a_buffer(fig)
    
    def _fig_a_buffer(self, fig):
        """Convertir figura matplotlib a buffer de bytes"""
        buffer = BytesIO()
        fig.savefig(buffer, format='png', dpi=100, bbox_inches='tight', 
                   facecolor='#1a1a1a', edgecolor='none')
        buffer.seek(0)
        plt.close(fig)
        return buffer