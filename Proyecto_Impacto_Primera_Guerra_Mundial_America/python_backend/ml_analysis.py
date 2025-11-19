import numpy as np
from sklearn.linear_model import LinearRegression
from sklearn.metrics import roc_curve, auc
import json

def analyze_impact(data, analysis_type):
    """Realizar análisis de impacto usando machine learning"""
    countries = data['countries']
    
    if analysis_type == 'economic':
        return _economic_analysis(countries)
    elif analysis_type == 'social':
        return _social_analysis(countries)
    else:
        return {'error': 'Tipo de análisis no válido'}

def _economic_analysis(countries):
    """Análisis económico usando regresión lineal"""
    try:
        X = []
        y = []
        country_names = []
        
        for country in countries:
            economic = country['economic_data']
            X.append([economic['pre_war_gdp'], economic['exports_change']])
            y.append(economic['post_war_gdp'])
            country_names.append(country['name'])
        
        X = np.array(X)
        y = np.array(y)
        
        # Entrenar modelo de regresión lineal
        model = LinearRegression()
        model.fit(X, y)
        predictions = model.predict(X)
        
        return {
            'success': True,
            'country_names': country_names,
            'actual': y.tolist(),
            'predicted': predictions.tolist(),
            'r_squared': float(model.score(X, y)),
            'coefficients': model.coef_.tolist(),
            'intercept': float(model.intercept_)
        }
    except Exception as e:
        return {'success': False, 'error': str(e)}

def _social_analysis(countries):
    """Análisis de impacto social"""
    try:
        urbanization = []
        migration = []
        participation = []
        participation_binary = []
        country_names = []
        
        for country in countries:
            social = country['social_data']
            urbanization.append(social['urbanization_rate'])
            migration.append(social['migration'] / 10000)
            participation.append(social['war_participation'])
            participation_binary.append(1 if social['war_participation'] > 0 else 0)
            country_names.append(country['name'])
        
        # Calcular curva ROC
        if len(set(participation_binary)) > 1:
            fpr, tpr, thresholds = roc_curve(participation_binary, migration)
            roc_auc = auc(fpr, tpr)
        else:
            fpr, tpr, thresholds = [0, 1], [0, 1], [0.5]
            roc_auc = 0.5
        
        return {
            'success': True,
            'country_names': country_names,
            'urbanization': urbanization,
            'migration': migration,
            'participation': participation,
            'participation_binary': participation_binary,
            'roc_curve': {
                'fpr': fpr.tolist(),
                'tpr': tpr.tolist(),
                'auc': float(roc_auc)
            }
        }
    except Exception as e:
        return {'success': False, 'error': str(e)}

def generate_predictions(data):
    """Generar predicciones de tendencias post-guerra"""
    try:
        countries_data = data['countries']
        predictions = {}
        
        for country in countries_data:
            name = country['name']
            economic = country['economic_data']
            
            # Predicciones basadas en datos históricos
            base_growth = 0.08
            export_effect = economic['exports_change'] / 1000 * 0.01
            industrial_effect = (economic['industrial_production'] - 100) / 100 * 0.02
            
            predicted_growth = base_growth + export_effect + industrial_effect
            confidence = 0.85 + (economic['exports_change'] / 1000) * 0.1
            
            # Limitar valores
            predicted_growth = max(0.02, min(0.15, predicted_growth))
            confidence = max(0.7, min(0.98, confidence))
            
            predictions[name] = {
                'predicted_growth': float(predicted_growth),
                'confidence': float(confidence),
                'expected_gdp_1925': float(economic['post_war_gdp'] * (1 + predicted_growth) ** 5),
                'factors': [
                    f"Crecimiento base: {base_growth*100:.1f}%",
                    f"Efecto exportaciones: {export_effect*100:+.1f}%",
                    f"Efecto industrial: {industrial_effect*100:+.1f}%"
                ]
            }
        
        return {'success': True, 'predictions': predictions}
    except Exception as e:
        return {'success': False, 'error': str(e)}

def get_economic_trends(data):
    """Obtener datos para tendencias económicas"""
    countries = data['countries']
    
    trends_data = {
        'countries': [country['name'] for country in countries],
        'growth_rates': [
            ((country['economic_data']['post_war_gdp'] - country['economic_data']['pre_war_gdp']) / 
             country['economic_data']['pre_war_gdp'] * 100) 
            for country in countries
        ],
        'industrial_growth': [
            country['economic_data']['industrial_production'] - 100 
            for country in countries
        ]
    }
    
    return trends_data

def get_trade_categories(data):
    """Obtener datos para categorías de comercio"""
    trade_data = data['trade_data']
    categories = {}
    
    for item in trade_data:
        if item['category'] not in categories:
            categories[item['category']] = 0
        categories[item['category']] += item['value']
    
    return {
        'categories': list(categories.keys()),
        'values': list(categories.values())
    }
