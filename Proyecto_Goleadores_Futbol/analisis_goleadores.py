import json
import matplotlib.pyplot as plt
import seaborn as sns
import pandas as pd
import numpy as np
from datetime import datetime
import sys
import os

# Configuración para servidor web
plt.switch_backend('Agg')
sns.set_theme(style="whitegrid")

# Configurar paleta de colores
DARK_PALETTE = ["#3b82f6", "#ef4444", "#10b981", "#f59e0b", "#8b5cf6"]
sns.set_palette(DARK_PALETTE)

# Configuración de estilo
plt.rcParams['figure.figsize'] = (12, 8)
plt.rcParams['font.family'] = 'DejaVu Sans'

def cargar_datos():
    """Cargar datos desde el archivo JSON"""
    try:
        # Verificar si el archivo existe
        if not os.path.exists('goleadores.json'):
            print("ERROR: El archivo 'goleadores.json' no existe")
            return None
            
        with open('goleadores.json', 'r', encoding='utf-8') as file:
            data = json.load(file)
        return data
    except Exception as e:
        print(f"ERROR: No se pudo cargar el archivo JSON: {e}")
        return None

def generar_graficos_individuales(df):
    """Generar gráficos individuales"""
    try:
        # GRÁFICO 1: Top 10 goleadores
        plt.figure(figsize=(14, 8))
        top_10 = df.nlargest(10, 'goles_totales')
        
        ax = sns.barplot(data=top_10, y='nombre', x='goles_totales')
        plt.title('TOP 10 MEJORES GOLEADORES', fontsize=16, fontweight='bold', pad=20)
        plt.xlabel('Goles Totales', fontsize=12)
        plt.ylabel('')
        
        # Añadir valores en las barras
        for i, value in enumerate(top_10['goles_totales']):
            ax.text(value + 5, i, f'{int(value)}', va='center', fontweight='bold', fontsize=11)
        
        plt.tight_layout()
        plt.savefig('grafico_top10.png', dpi=100, bbox_inches='tight')
        plt.close()

        # GRÁFICO 2: Distribución de tipos de goles
        plt.figure(figsize=(10, 8))
        tipos_goles_totales = df[['pie_derecho', 'pie_izquierdo', 'cabeza', 'penal', 'tiro_libre']].sum()
        labels = ['Pie Derecho', 'Pie Izquierdo', 'Cabeza', 'Penal', 'Tiro Libre']
        colors = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6']
        
        plt.pie(tipos_goles_totales, labels=labels, autopct='%1.1f%%', colors=colors, startangle=90)
        plt.title('DISTRIBUCIÓN DE TIPOS DE GOLES', fontsize=16, fontweight='bold', pad=20)
        
        plt.tight_layout()
        plt.savefig('grafico_distribucion.png', dpi=100, bbox_inches='tight')
        plt.close()

        # GRÁFICO 3: Goles por país
        plt.figure(figsize=(12, 8))
        goles_por_pais = df.groupby('pais')['goles_totales'].sum().sort_values(ascending=True)
        
        ax = sns.barplot(x=goles_por_pais.values, y=goles_por_pais.index, palette="viridis")
        plt.title('GOLES TOTALES POR PAÍS', fontsize=16, fontweight='bold', pad=20)
        plt.xlabel('Goles Totales', fontsize=12)
        plt.ylabel('País', fontsize=12)
        
        # Añadir valores en las barras
        for i, value in enumerate(goles_por_pais.values):
            ax.text(value + 10, i, f'{int(value)}', va='center', fontweight='bold', fontsize=10)
        
        plt.tight_layout()
        plt.savefig('grafico_paises.png', dpi=100, bbox_inches='tight')
        plt.close()

        # GRÁFICO 4: Eficiencia vs Goles Totales
        plt.figure(figsize=(12, 8))
        scatter = sns.scatterplot(data=df, x='goles_totales', y='promedio_goles', 
                                 size='goles_totales', hue='goles_totales', 
                                 sizes=(50, 300), palette='coolwarm', alpha=0.7)
        
        # Añadir etiquetas a los puntos más importantes
        top_players = df.nlargest(5, 'goles_totales')
        for idx, row in top_players.iterrows():
            plt.annotate(row['nombre'].split()[-1], 
                        (row['goles_totales'], row['promedio_goles']),
                        xytext=(5, 5), textcoords='offset points',
                        fontsize=9, fontweight='bold')
        
        plt.title('EFICIENCIA: GOLES TOTALES vs PROMEDIO', fontsize=16, fontweight='bold', pad=20)
        plt.xlabel('Goles Totales', fontsize=12)
        plt.ylabel('Promedio de Goles por Partido', fontsize=12)
        plt.legend(title='Goles Totales', bbox_to_anchor=(1.05, 1), loc='upper left')
        
        plt.tight_layout()
        plt.savefig('grafico_eficiencia.png', dpi=100, bbox_inches='tight')
        plt.close()
        
        # GRÁFICO 5: Distribución detallada por jugador (CORREGIDO)
        plt.figure(figsize=(14, 8))
        top_8 = df.nlargest(8, 'goles_totales')
        
        tipos = ['pie_derecho', 'pie_izquierdo', 'cabeza', 'penal', 'tiro_libre']
        nombres_tipos = ['Pie Derecho', 'Pie Izquierdo', 'Cabeza', 'Penal', 'Tiro Libre']
        colores_tipos = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6']
        
        bottom = np.zeros(len(top_8))
        for i, tipo in enumerate(tipos):
            # CORRECCIÓN: Acceder directamente a las columnas del DataFrame
            valores = top_8[tipo].values
            plt.bar(top_8['nombre'], valores, bottom=bottom, label=nombres_tipos[i], 
                   color=colores_tipos[i], alpha=0.8)
            bottom += valores
        
        plt.title('DISTRIBUCIÓN POR JUGADOR (TOP 8)', fontsize=16, fontweight='bold', pad=20)
        plt.ylabel('Cantidad de Goles', fontsize=12)
        plt.xticks(rotation=45, ha='right')
        plt.legend(bbox_to_anchor=(1.05, 1), loc='upper left')
        
        plt.tight_layout()
        plt.savefig('grafico_detalle_jugadores.png', dpi=100, bbox_inches='tight')
        plt.close()
        
        # GRÁFICO 6: Heatmap de correlación
        plt.figure(figsize=(10, 8))
        tipos_df = df[['pie_derecho', 'pie_izquierdo', 'cabeza', 'penal', 'tiro_libre', 'goles_totales']]
        correlation_matrix = tipos_df.corr()
        
        sns.heatmap(correlation_matrix, annot=True, cmap='coolwarm', center=0,
                   square=True, linewidths=0.5, fmt='.2f')
        plt.title('CORRELACIÓN ENTRE TIPOS DE GOLES', fontsize=16, fontweight='bold', pad=20)
        
        plt.tight_layout()
        plt.savefig('grafico_correlacion.png', dpi=100, bbox_inches='tight')
        plt.close()
        
        print("✓ Todos los gráficos generados exitosamente")
        return True
        
    except Exception as e:
        print(f"✗ ERROR al generar gráficos: {str(e)}")
        import traceback
        traceback.print_exc()
        return False

def generar_reporte_detallado(df):
    """Generar reporte detallado"""
    try:
        with open('reporte_goleadores.txt', 'w', encoding='utf-8') as file:
            file.write("REPORTE DETALLADO DE GOLEADORES\n")
            file.write("=" * 50 + "\n")
            file.write(f"Fecha: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n\n")
            
            file.write("RESUMEN ESTADÍSTICO:\n")
            file.write("-" * 30 + "\n")
            file.write(f"Total jugadores: {len(df)}\n")
            file.write(f"Total goles: {df['goles_totales'].sum():,}\n")
            file.write(f"Promedio: {df['goles_totales'].mean():.2f} goles/jugador\n\n")
            
            file.write("TOP 5 GOLEADORES:\n")
            file.write("-" * 30 + "\n")
            top_5 = df.nlargest(5, 'goles_totales')
            for i, (_, row) in enumerate(top_5.iterrows(), 1):
                file.write(f"{i}. {row['nombre']} ({row['pais']}): {row['goles_totales']:,} goles\n")
        
        print("✓ Reporte generado exitosamente")
        return True
        
    except Exception as e:
        print(f"✗ ERROR al generar reporte: {str(e)}")
        return False

def main():
    """Función principal"""
    print("Iniciando análisis de goleadores...")
    
    # Cargar datos
    data = cargar_datos()
    if data is None:
        sys.exit(1)
    
    # Convertir a DataFrame
    df = pd.DataFrame(data)
    
    # Expandir tipos de goles
    tipos_goles = pd.json_normalize(df['tipos_goles'])
    df = pd.concat([df, tipos_goles], axis=1)
    
    print(f"✓ Datos cargados: {len(df)} jugadores")
    
    # Generar gráficos
    print("Generando gráficos...")
    graficos_ok = generar_graficos_individuales(df)
    
    # Generar reporte
    print("Generando reporte...")
    reporte_ok = generar_reporte_detallado(df)
    
    if graficos_ok and reporte_ok:
        print("\n¡Análisis completado exitosamente!")
        sys.exit(0)
    else:
        print("\n¡Análisis completado con errores!")
        sys.exit(1)

if __name__ == "__main__":
    main()