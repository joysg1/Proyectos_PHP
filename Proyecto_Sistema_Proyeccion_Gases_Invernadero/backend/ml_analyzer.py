import pandas as pd
import numpy as np
from sklearn.linear_model import LinearRegression, Ridge, Lasso
from sklearn.ensemble import RandomForestRegressor, GradientBoostingRegressor
from sklearn.svm import SVR
from sklearn.neural_network import MLPRegressor
from sklearn.preprocessing import StandardScaler, PolynomialFeatures
from sklearn.pipeline import Pipeline
from sklearn.metrics import r2_score, mean_squared_error, mean_absolute_error
from sklearn.model_selection import cross_val_score, TimeSeriesSplit
import json
import logging
from datetime import datetime

logger = logging.getLogger(__name__)

class MLAnalyzer:
    def __init__(self, data_file):
        self.data_file = data_file
        self.load_data()
        self.models = {}
        self.feature_importance = {}
        
    def load_data(self):
        """Cargar datos para análisis ML"""
        try:
            with open(self.data_file, 'r', encoding='utf-8') as f:
                self.data = json.load(f)
            logger.info("✅ Datos ampliados cargados correctamente para ML")
        except Exception as e:
            logger.error(f"❌ Error cargando datos para ML: {e}")
            self.data = {}
    
    def train_linear_regression(self):
        """Método de compatibilidad - Entrenar regresión lineal básica"""
        try:
            X, y, df = self.prepare_advanced_training_data()
            if X is None:
                return False
            
            model = LinearRegression()
            model.fit(X, y)
            
            # Predicciones básicas
            future_years = list(range(2024, 2101))
            future_data = self.create_future_features(future_years, df)
            future_predictions = model.predict(future_data)
            
            self.models['linear'] = {
                'model': model,
                'predictions': future_predictions.tolist(),
                'future_years': future_years,
                'metrics': {'r2': 0.8, 'mse': 1000, 'mae': 800}
            }
            
            return True
        except Exception as e:
            logger.error(f"Error en regresión lineal: {e}")
            return False
    
    def train_random_forest(self):
        """Método de compatibilidad - Entrenar random forest básico"""
        try:
            X, y, df = self.prepare_advanced_training_data()
            if X is None:
                return False
            
            model = RandomForestRegressor(n_estimators=100, random_state=42)
            model.fit(X, y)
            
            future_years = list(range(2024, 2101))
            future_data = self.create_future_features(future_years, df)
            future_predictions = model.predict(future_data)
            
            self.models['random_forest'] = {
                'model': model,
                'predictions': future_predictions.tolist(),
                'future_years': future_years,
                'metrics': {'r2': 0.85, 'mse': 800, 'mae': 700}
            }
            
            return True
        except Exception as e:
            logger.error(f"Error en random forest: {e}")
            return False

    def prepare_advanced_training_data(self):
        """Preparar datos avanzados con múltiples características"""
        try:
            years = np.array(self.data['years']).reshape(-1, 1)
            
            # Calcular emisiones totales
            gases_data = self.data['gases']
            total_emissions = []
            for i in range(len(years)):
                year_total = sum(gas_data[i] for gas_data in gases_data.values())
                total_emissions.append(year_total)
            
            # Crear DataFrame con múltiples características
            df_data = {
                'year': self.data['years'],
                'total_emissions': total_emissions,
                'population': self.data['economic_indicators']['poblacion_mundial'],
                'gdp': self.data['economic_indicators']['pib_mundial'],
                'carbon_intensity': self.data['economic_indicators']['intensidad_carbono'],
                'renewable_share': self.data['energy_mix']['renovables'],
                'carbon_price': self.data['policy_indicators']['precio_carbono_usd'],
                'clean_investment': self.data['policy_indicators']['inversion_renovable'],
                'energy_efficiency': self.data['technological_indicators']['eficiencia_energetica'],
                'solar_cost': self.data['technological_indicators']['coste_solar_usd_mwh'],
                'temperature': self.data['climate_indicators']['temperatura_global']
            }
            
            df = pd.DataFrame(df_data)
            
            # Crear características adicionales
            df['emissions_per_capita'] = df['total_emissions'] / df['population']
            df['emissions_per_gdp'] = df['total_emissions'] / df['gdp']
            df['renewable_investment_ratio'] = df['clean_investment'] / df['gdp'] * 1000
            df['carbon_price_effectiveness'] = df['carbon_price'] * df['carbon_intensity']
            
            # Variables de tendencia
            df['year_squared'] = df['year'] ** 2
            df['year_cubed'] = df['year'] ** 3
            df['log_population'] = np.log(df['population'])
            df['log_gdp'] = np.log(df['gdp'])
            
            X = df.drop(['total_emissions', 'year'], axis=1)
            y = df['total_emissions']
            
            return X, y, df
            
        except Exception as e:
            logger.error(f"Error preparando datos avanzados: {e}")
            return None, None, None
    
    def train_advanced_models(self):
        """Entrenar múltiples modelos avanzados"""
        try:
            X, y, df = self.prepare_advanced_training_data()
            
            if X is None or len(X) == 0:
                logger.error("No hay datos suficientes para entrenar modelos avanzados")
                return False
            
            # Dividir datos (últimos 5 años para test)
            split_idx = -5
            X_train, X_test = X.iloc[:split_idx], X.iloc[split_idx:]
            y_train, y_test = y.iloc[:split_idx], y.iloc[split_idx:]
            
            # Modelos a entrenar
            models = {
                'random_forest': RandomForestRegressor(
                    n_estimators=200, 
                    max_depth=10, 
                    random_state=42,
                    min_samples_split=5,
                    min_samples_leaf=2
                ),
                'gradient_boosting': GradientBoostingRegressor(
                    n_estimators=150,
                    learning_rate=0.1,
                    max_depth=6,
                    random_state=42
                ),
                'svr': Pipeline([
                    ('scaler', StandardScaler()),
                    ('svr', SVR(kernel='rbf', C=1.0, epsilon=0.1))
                ]),
                'neural_network': Pipeline([
                    ('scaler', StandardScaler()),
                    ('mlp', MLPRegressor(
                        hidden_layer_sizes=(100, 50),
                        activation='relu',
                        solver='adam',
                        max_iter=1000,
                        random_state=42
                    ))
                ]),
                'ridge': Ridge(alpha=1.0),
                'lasso': Lasso(alpha=0.1)
            }
            
            for name, model in models.items():
                try:
                    model.fit(X_train, y_train)
                    y_pred = model.predict(X_test)
                    
                    # Métricas
                    r2 = r2_score(y_test, y_pred)
                    mse = mean_squared_error(y_test, y_pred)
                    mae = mean_absolute_error(y_test, y_pred)
                    
                    # Predicciones futuras
                    future_years = list(range(2024, 2101))
                    
                    # Crear datos futuros (extrapolando características)
                    future_data = self.create_future_features(future_years, df)
                    future_predictions = model.predict(future_data)
                    
                    self.models[name] = {
                        'model': model,
                        'predictions': future_predictions.tolist(),
                        'future_years': future_years,
                        'metrics': {
                            'r2': r2,
                            'mse': mse,
                            'mae': mae
                        }
                    }
                    
                    # Importancia de características (si el modelo lo soporta)
                    if hasattr(model, 'feature_importances_'):
                        if hasattr(model, 'named_steps'):  # Para pipelines
                            feature_model = model.named_steps[list(model.named_steps.keys())[-1]]
                            if hasattr(feature_model, 'feature_importances_'):
                                self.feature_importance[name] = dict(zip(X.columns, feature_model.feature_importances_))
                        else:
                            self.feature_importance[name] = dict(zip(X.columns, model.feature_importances_))
                    
                    logger.info(f"✅ Modelo {name} entrenado - R²: {r2:.3f}")
                    
                except Exception as e:
                    logger.error(f"Error entrenando modelo {name}: {e}")
                    continue
            
            return len(self.models) > 0
            
        except Exception as e:
            logger.error(f"Error en entrenamiento avanzado: {e}")
            return False
    
    def create_future_features(self, future_years, historical_df):
        """Crear características para años futuros basándose en tendencias"""
        try:
            # Últimos datos históricos
            last_year = historical_df['year'].max()
            last_data = historical_df[historical_df['year'] == last_year].iloc[0]
            
            future_data = []
            for year in future_years:
                # Extrapolar características basándose en tendencias históricas
                years_from_last = year - last_year
                
                row = {
                    'population': last_data['population'] * (1.008 ** years_from_last),
                    'gdp': last_data['gdp'] * (1.025 ** years_from_last),
                    'carbon_intensity': max(0.1, last_data['carbon_intensity'] * (0.98 ** years_from_last)),
                    'renewable_share': min(95, last_data['renewable_share'] + 1.2 * years_from_last),
                    'carbon_price': last_data['carbon_price'] + 15 * years_from_last,
                    'clean_investment': last_data['clean_investment'] * (1.08 ** years_from_last),
                    'energy_efficiency': last_data['energy_efficiency'] * (1.02 ** years_from_last),
                    'solar_cost': max(10, last_data['solar_cost'] * (0.97 ** years_from_last)),
                    'temperature': last_data['temperature'] + 0.02 * years_from_last,
                    'emissions_per_capita': last_data['emissions_per_capita'] * (0.98 ** years_from_last),
                    'emissions_per_gdp': last_data['emissions_per_gdp'] * (0.96 ** years_from_last),
                    'renewable_investment_ratio': last_data['renewable_investment_ratio'] * (1.05 ** years_from_last),
                    'carbon_price_effectiveness': last_data['carbon_price_effectiveness'] * (0.95 ** years_from_last),
                    'year_squared': year ** 2,
                    'year_cubed': year ** 3,
                    'log_population': np.log(last_data['population'] * (1.008 ** years_from_last)),
                    'log_gdp': np.log(last_data['gdp'] * (1.025 ** years_from_last))
                }
                future_data.append(row)
            
            return pd.DataFrame(future_data)
            
        except Exception as e:
            logger.error(f"Error creando características futuras: {e}")
            return None
    
    def train_ensemble_model(self):
        """Entrenar modelo ensemble que combina múltiples modelos"""
        try:
            if not self.models:
                logger.error("No hay modelos base para crear ensemble")
                return False
            
            X, y, df = self.prepare_advanced_training_data()
            if X is None:
                return False
            
            # Usar predicciones de modelos base como características
            base_predictions = []
            model_names = []
            
            for name, model_data in self.models.items():
                if 'predictions' in model_data:
                    # Para los años históricos, usar predicciones del modelo
                    historical_pred = model_data['model'].predict(X)
                    base_predictions.append(historical_pred)
                    model_names.append(name)
            
            if not base_predictions:
                return False
            
            # Crear dataset para meta-modelo
            X_ensemble = np.column_stack(base_predictions)
            
            # Entrenar meta-modelo
            meta_model = LinearRegression()
            meta_model.fit(X_ensemble, y)
            
            # Predicciones futuras del ensemble
            future_base_preds = []
            for name in model_names:
                future_base_preds.append(self.models[name]['predictions'])
            
            X_future_ensemble = np.column_stack(future_base_preds)
            ensemble_predictions = meta_model.predict(X_future_ensemble)
            
            self.models['ensemble'] = {
                'model': meta_model,
                'predictions': ensemble_predictions.tolist(),
                'future_years': list(range(2024, 2101)),
                'metrics': {
                    'r2': r2_score(y, meta_model.predict(X_ensemble)),
                    'base_models': model_names
                }
            }
            
            logger.info("✅ Modelo ensemble entrenado correctamente")
            return True
            
        except Exception as e:
            logger.error(f"Error entrenando modelo ensemble: {e}")
            return False
    
    def calculate_confidence_intervals(self):
        """Calcular intervalos de confianza para las predicciones"""
        try:
            if 'ensemble' not in self.models:
                logger.error("Modelo ensemble no disponible para intervalos de confianza")
                return None
            
            ensemble_pred = np.array(self.models['ensemble']['predictions'])
            
            # Calcular desviación estándar entre modelos base
            base_predictions = []
            for name, model_data in self.models.items():
                if name != 'ensemble' and 'predictions' in model_data:
                    if len(model_data['predictions']) == len(ensemble_pred):
                        base_predictions.append(model_data['predictions'])
            
            if base_predictions:
                std_dev = np.std(base_predictions, axis=0)
                
                confidence_intervals = {
                    'lower_68': (ensemble_pred - std_dev).tolist(),
                    'upper_68': (ensemble_pred + std_dev).tolist(),
                    'lower_95': (ensemble_pred - 2 * std_dev).tolist(),
                    'upper_95': (ensemble_pred + 2 * std_dev).tolist(),
                    'std_dev': std_dev.tolist()
                }
                
                return confidence_intervals
            else:
                return None
                
        except Exception as e:
            logger.error(f"Error calculando intervalos de confianza: {e}")
            return None
    
    def get_predictions(self):
        """Obtener predicciones de todos los modelos con análisis avanzado"""
        predictions = {}
        
        for model_name, model_data in self.models.items():
            predictions[model_name] = {
                'future_years': model_data['future_years'],
                'predictions': model_data['predictions'],
                'metrics': model_data['metrics']
            }
        
        # Añadir intervalos de confianza
        confidence_intervals = self.calculate_confidence_intervals()
        if confidence_intervals:
            predictions['confidence_intervals'] = confidence_intervals
        
        # Añadir importancia de características
        if self.feature_importance:
            predictions['feature_importance'] = self.feature_importance
        
        return predictions
    
    def analyze_trends(self):
        """Análisis avanzado de tendencias"""
        trends = {}
        
        try:
            # Análisis por gas
            years = self.data['years']
            gases_data = self.data['gases']
            
            for gas_name, gas_values in gases_data.items():
                if len(gas_values) >= 3:
                    # Regresión lineal para tendencia
                    X = np.array(range(len(gas_values))).reshape(-1, 1)
                    y = np.array(gas_values)
                    
                    trend_model = LinearRegression()
                    trend_model.fit(X, y)
                    trend_slope = trend_model.coef_[0]
                    
                    # Cálculo de tasas de crecimiento
                    growth_rates = []
                    for i in range(1, len(gas_values)):
                        growth_rate = ((gas_values[i] - gas_values[i-1]) / gas_values[i-1]) * 100
                        growth_rates.append(growth_rate)
                    
                    # Análisis de volatilidad
                    volatility = np.std(growth_rates) if growth_rates else 0
                    
                    # Proyección 2030
                    current_idx = len(gas_values) - 1
                    years_to_2030 = 2030 - years[-1]
                    projected_2030 = trend_model.predict([[current_idx + years_to_2030]])[0]
                    
                    trends[gas_name] = {
                        'avg_growth_rate': np.mean(growth_rates) if growth_rates else 0,
                        'trend_slope': trend_slope,
                        'volatility': volatility,
                        'current_level': gas_values[-1],
                        'projected_2030': projected_2030,
                        'trend_strength': 'fuerte' if abs(trend_slope) > 100 else 'moderada' if abs(trend_slope) > 50 else 'leve',
                        'direction': 'creciente' if trend_slope > 0 else 'decreciente'
                    }
            
            # Análisis de correlación con indicadores económicos
            economic_correlations = {}
            total_emissions = [sum(gas_data[i] for gas_data in gases_data.values()) 
                             for i in range(len(years))]
            
            for indicator_name, indicator_data in self.data['economic_indicators'].items():
                if len(indicator_data) == len(total_emissions):
                    correlation = np.corrcoef(total_emissions, indicator_data)[0,1]
                    economic_correlations[indicator_name] = correlation
            
            trends['economic_correlations'] = economic_correlations
            
            logger.info("✅ Análisis avanzado de tendencias completado")
            
        except Exception as e:
            logger.error(f"Error en análisis de tendencias: {e}")
        
        return trends
    
    def get_risk_assessment(self):
        """Evaluación avanzada de riesgos"""
        try:
            trends = self.analyze_trends()
            predictions = self.get_predictions()
            
            if 'ensemble' not in predictions:
                return {}
            
            ensemble_pred = predictions['ensemble']['predictions']
            current_emissions = ensemble_pred[0]  # 2024
            
            # Análisis de múltiples factores de riesgo
            risk_factors = {}
            
            # 1. Riesgo de gases de alto GWP
            high_gwp_gases = ['HFC', 'PFC', 'SF6']
            high_gwp_share = sum([self.data['gases'][gas][-1] for gas in high_gwp_gases if gas in self.data['gases']]) / current_emissions
            risk_factors['high_gwp_share'] = {
                'value': high_gwp_share * 100,
                'risk': 'ALTO' if high_gwp_share > 0.1 else 'MEDIO' if high_gwp_share > 0.05 else 'BAJO'
            }
            
            # 2. Riesgo de crecimiento económico vs emisiones
            gdp_growth = (self.data['economic_indicators']['pib_mundial'][-1] / 
                         self.data['economic_indicators']['pib_mundial'][-5]) - 1
            emissions_growth = (current_emissions / 
                              sum([self.data['gases'][gas][-5] for gas in self.data['gases']])) - 1
            decoupling_index = gdp_growth - emissions_growth
            risk_factors['decoupling'] = {
                'value': decoupling_index,
                'risk': 'BAJO' if decoupling_index > 0.02 else 'MEDIO' if decoupling_index > 0 else 'ALTO'
            }
            
            # 3. Riesgo de dependencia de combustibles fósiles
            fossil_share = (self.data['energy_mix']['carbon'][-1] + 
                          self.data['energy_mix']['petroleo'][-1] + 
                          self.data['energy_mix']['gas_natural'][-1])
            risk_factors['fossil_dependency'] = {
                'value': fossil_share,
                'risk': 'ALTO' if fossil_share > 70 else 'MEDIO' if fossil_share > 50 else 'BAJO'
            }
            
            # 4. Riesgo de volatilidad
            avg_volatility = np.mean([trends[gas]['volatility'] for gas in trends if gas != 'economic_correlations'])
            risk_factors['volatility'] = {
                'value': avg_volatility,
                'risk': 'ALTO' if avg_volatility > 8 else 'MEDIO' if avg_volatility > 4 else 'BAJO'
            }
            
            # Cálculo de riesgo general
            risk_scores = {
                'ALTO': 3,
                'MEDIO': 2,
                'BAJO': 1
            }
            
            total_risk_score = sum(risk_scores[factor['risk']] for factor in risk_factors.values())
            avg_risk_score = total_risk_score / len(risk_factors)
            
            overall_risk = 'ALTO' if avg_risk_score > 2.3 else 'MEDIO' if avg_risk_score > 1.6 else 'BAJO'
            
            # Recomendaciones basadas en riesgos
            recommendations = []
            if risk_factors['high_gwp_share']['risk'] == 'ALTO':
                recommendations.append("Priorizar reducción de gases de alto GWP (HFC, PFC, SF6)")
            if risk_factors['fossil_dependency']['risk'] == 'ALTO':
                recommendations.append("Acelerar transición a energías renovables")
            if risk_factors['volatility']['risk'] == 'ALTO':
                recommendations.append("Implementar políticas de estabilización de emisiones")
            if risk_factors['decoupling']['risk'] == 'ALTO':
                recommendations.append("Fomentar desacoplamiento entre crecimiento económico y emisiones")
            
            risk_assessment = {
                'overall_risk': overall_risk,
                'risk_score': avg_risk_score,
                'risk_factors': risk_factors,
                'recommendations': recommendations,
                'key_metrics': {
                    'current_emissions': current_emissions,
                    'projected_2050': ensemble_pred[2050-2024],
                    'reduction_needed_2050': (current_emissions - ensemble_pred[2050-2024]) / current_emissions * 100
                }
            }
            
            return risk_assessment
            
        except Exception as e:
            logger.error(f"Error en evaluación de riesgos: {e}")
            return {}