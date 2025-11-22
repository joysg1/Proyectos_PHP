import numpy as np
import pandas as pd
from sklearn.linear_model import LinearRegression
from sklearn.preprocessing import StandardScaler
from sklearn.cluster import KMeans
import json

class MLProcessor:
    def __init__(self, database):
        self.db = database
        self.scaler = StandardScaler()
    
    def prepare_data(self):
        """Prepara los datos para el análisis de ML"""
        countries = self.db.get_all_countries()
        
        if not countries:
            return None
        
        # Crear DataFrame
        data = []
        for country in countries:
            row = {
                'pais': country['nombre'],
                'tasa_crecimiento': country['tasa_crecimiento'],
                'densidad_poblacional': country['densidad_poblacional'],
                'expectativa_vida': country['expectativa_vida'],
                'tasa_natalidad': country['tasa_natalidad'],
                'tasa_mortalidad': country['tasa_mortalidad'],
                'migracion_neta': country['migracion_neta'],
                'poblacion_urbana': country['poblacion_urbana'],
                'poblacion_actual': country['poblacion_2023']
            }
            data.append(row)
        
        return pd.DataFrame(data)
    
    def predict_growth(self, country_name, years=5):
        """Predice el crecimiento poblacional para un país"""
        country = self.db.get_country_by_name(country_name)
        if not country:
            return None
        
        # Datos históricos
        years_hist = [2020, 2021, 2022, 2023]
        population_hist = [
            country['poblacion_2020'],
            country['poblacion_2021'],
            country['poblacion_2022'],
            country['poblacion_2023']
        ]
        
        # Modelo de regresión lineal
        X = np.array(years_hist).reshape(-1, 1)
        y = np.array(population_hist)
        
        model = LinearRegression()
        model.fit(X, y)
        
        # Predicciones
        future_years = np.array([2024, 2025, 2026, 2027, 2028]).reshape(-1, 1)
        predictions = model.predict(future_years)
        
        result = {
            'pais': country_name,
            'predicciones': {
                str(2024 + i): int(predictions[i]) for i in range(years)
            },
            'tasa_crecimiento_estimada': model.coef_[0] / country['poblacion_2023'] * 100
        }
        
        return result
    
    def cluster_countries(self, n_clusters=3):
        """Agrupa países por similitud en indicadores poblacionales"""
        df = self.prepare_data()
        if df is None:
            return None
        
        # Seleccionar características para clustering
        features = ['tasa_crecimiento', 'densidad_poblacional', 'expectativa_vida', 
                   'tasa_natalidad', 'poblacion_urbana']
        
        X = df[features]
        X_scaled = self.scaler.fit_transform(X)
        
        # Aplicar K-means
        kmeans = KMeans(n_clusters=n_clusters, random_state=42)
        clusters = kmeans.fit_predict(X_scaled)
        
        df['cluster'] = clusters
        
        # Analizar clusters
        cluster_analysis = []
        for i in range(n_clusters):
            cluster_data = df[df['cluster'] == i]
            analysis = {
                'cluster': i,
                'paises': cluster_data['pais'].tolist(),
                'caracteristicas_promedio': cluster_data[features].mean().to_dict(),
                'tamaño': len(cluster_data)
            }
            cluster_analysis.append(analysis)
        
        return cluster_analysis
    
    def get_country_comparison(self, country1, country2):
        """Compara dos países en base a sus indicadores"""
        c1 = self.db.get_country_by_name(country1)
        c2 = self.db.get_country_by_name(country2)
        
        if not c1 or not c2:
            return None
        
        comparison = {
            'paises': [country1, country2],
            'poblacion_2023': [c1['poblacion_2023'], c2['poblacion_2023']],
            'tasa_crecimiento': [c1['tasa_crecimiento'], c2['tasa_crecimiento']],
            'densidad_poblacional': [c1['densidad_poblacional'], c2['densidad_poblacional']],
            'expectativa_vida': [c1['expectativa_vida'], c2['expectativa_vida']],
            'tasa_natalidad': [c1['tasa_natalidad'], c2['tasa_natalidad']],
            'poblacion_urbana': [c1['poblacion_urbana'], c2['poblacion_urbana']]
        }
        
        return comparison
    
    def get_regional_analysis(self):
        """Análisis regional de América Latina"""
        countries = self.db.get_all_countries()
        
        if not countries:
            return None
        
        total_population = sum([c['poblacion_2023'] for c in countries])
        avg_growth = np.mean([c['tasa_crecimiento'] for c in countries])
        avg_life_expectancy = np.mean([c['expectativa_vida'] for c in countries])
        avg_urban_population = np.mean([c['poblacion_urbana'] for c in countries])
        
        analysis = {
            'poblacion_total': total_population,
            'tasa_crecimiento_promedio': avg_growth,
            'expectativa_vida_promedio': avg_life_expectancy,
            'poblacion_urbana_promedio': avg_urban_population,
            'numero_paises': len(countries),
            'pais_mas_poblado': max(countries, key=lambda x: x['poblacion_2023'])['nombre'],
            'pais_menos_poblado': min(countries, key=lambda x: x['poblacion_2023'])['nombre'],
            'mayor_crecimiento': max(countries, key=lambda x: x['tasa_crecimiento'])['nombre']
        }
        
        return analysis