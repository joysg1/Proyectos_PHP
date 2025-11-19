import numpy as np
import pandas as pd
from sklearn.ensemble import RandomForestRegressor
from sklearn.preprocessing import LabelEncoder, StandardScaler
from sklearn.model_selection import train_test_split
from sklearn.metrics import r2_score, mean_squared_error, mean_absolute_error
import joblib
import logging
import os
import sys

# Configurar path para imports
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

try:
    from backend.config.settings import ML_MODEL_FILE, DATA_DIR
except ImportError as e:
    print(f"Error de importación en ml_models.py: {e}")
    # Fallback a imports relativos
    from config.settings import ML_MODEL_FILE, DATA_DIR

class VitaminPredictor:
    """Modelo de ML para predecir el impacto de vitaminas en glóbulos rojos"""
    
    def __init__(self):
        self.model = RandomForestRegressor(
            n_estimators=100,
            max_depth=10,
            min_samples_split=5,
            min_samples_leaf=2,
            random_state=42
        )
        self.scaler = StandardScaler()
        self.encoder = LabelEncoder()
        self.is_trained = False
        self.feature_names = []
        self.model_metrics = {}
        self.encoder_fitted = False  # Nueva bandera para controlar si el encoder está entrenado
        
        # Intentar cargar modelo existente al inicializar
        self.load_model()
    
    def prepare_features(self, df, training_mode=False):
        """Prepara las características para el modelo"""
        try:
            df_encoded = df.copy()
            
            # Codificar variable categórica (vitamina)
            if 'vitamina' in df_encoded.columns:
                if training_mode:
                    # Durante entrenamiento, usar fit_transform
                    df_encoded['vitamina_encoded'] = self.encoder.fit_transform(df_encoded['vitamina'])
                    self.encoder_fitted = True
                    logging.info(f"Encoder entrenado con clases: {list(self.encoder.classes_)}")
                else:
                    # Durante predicción, usar transform si el encoder está entrenado
                    if self.encoder_fitted and hasattr(self.encoder, 'classes_'):
                        # Para vitaminas no vistas durante el entrenamiento, asignar -1
                        mask = df_encoded['vitamina'].isin(self.encoder.classes_)
                        if mask.all():
                            df_encoded['vitamina_encoded'] = self.encoder.transform(df_encoded['vitamina'])
                        else:
                            # Manejar categorías no vistas
                            unseen_vitamins = df_encoded[~mask]['vitamina'].unique()
                            logging.warning(f"Vitaminas no vistas durante entrenamiento: {unseen_vitamins}")
                            df_encoded['vitamina_encoded'] = -1  # Valor por defecto para categorías no vistas
                    else:
                        logging.warning("Encoder no está entrenado, usando valor por defecto")
                        df_encoded['vitamina_encoded'] = -1
            
            # Seleccionar características base
            base_features = ['dosis_diaria', 'duracion_semanas', 'globulos_rojos_inicio', 'vitamina_encoded']
            available_features = []
            
            for feature in base_features:
                if feature in df_encoded.columns:
                    available_features.append(feature)
            
            # Agregar características adicionales si existen y tienen datos
            optional_features = ['edad_paciente']
            for feature in optional_features:
                if (feature in df_encoded.columns and 
                    not df_encoded[feature].isnull().all()):
                    # Llenar valores nulos con la media
                    if df_encoded[feature].isnull().any():
                        df_encoded[feature] = df_encoded[feature].fillna(df_encoded[feature].mean())
                    available_features.append(feature)
            
            self.feature_names = available_features
            logging.info(f"Características utilizadas: {available_features}")
            
            return df_encoded[available_features]
            
        except Exception as e:
            logging.error(f"Error preparando características: {e}")
            return None
    
    def train(self, df):
        """Entrena el modelo con los datos proporcionados"""
        try:
            if df.empty:
                logging.warning("No hay datos para entrenar el modelo")
                return False
            
            logging.info(f"Iniciando entrenamiento con {len(df)} registros")
            
            # Verificar que tenemos las columnas necesarias
            required_columns = ['vitamina', 'dosis_diaria', 'duracion_semanas', 
                              'globulos_rojos_inicio', 'globulos_rojos_fin']
            missing_columns = [col for col in required_columns if col not in df.columns]
            if missing_columns:
                logging.error(f"Columnas faltantes en los datos: {missing_columns}")
                return False
            
            X = self.prepare_features(df, training_mode=True)
            if X is None or X.empty:
                logging.error("No se pudieron preparar las características")
                return False
            
            # Variable objetivo: incremento en glóbulos rojos
            y = df['globulos_rojos_fin'] - df['globulos_rojos_inicio']
            
            # Verificar que tenemos suficientes datos
            if len(X) < 5:
                logging.warning("Datos insuficientes para entrenamiento (mínimo 5 registros)")
                return False
            
            # Dividir datos (80% entrenamiento, 20% prueba)
            X_train, X_test, y_train, y_test = train_test_split(
                X, y, test_size=0.2, random_state=42, shuffle=True
            )
            
            # Escalar características numéricas
            numeric_features = [col for col in X_train.columns if col != 'vitamina_encoded']
            if numeric_features:
                X_train_scaled = X_train.copy()
                X_test_scaled = X_test.copy()
                
                X_train_scaled[numeric_features] = self.scaler.fit_transform(X_train[numeric_features])
                X_test_scaled[numeric_features] = self.scaler.transform(X_test[numeric_features])
            else:
                X_train_scaled = X_train
                X_test_scaled = X_test
            
            # Entrenar modelo
            logging.info("Entrenando modelo Random Forest...")
            self.model.fit(X_train_scaled, y_train)
            
            # Evaluar modelo
            y_pred = self.model.predict(X_test_scaled)
            
            # Calcular métricas
            self.model_metrics = {
                'r2_score': r2_score(y_test, y_pred),
                'mse': mean_squared_error(y_test, y_pred),
                'mae': mean_absolute_error(y_test, y_pred),
                'rmse': np.sqrt(mean_squared_error(y_test, y_pred)),
                'n_samples': len(X),
                'n_features': len(self.feature_names)
            }
            
            logging.info(f"Modelo entrenado exitosamente - R²: {self.model_metrics['r2_score']:.4f}")
            logging.info(f"MSE: {self.model_metrics['mse']:.4f}, MAE: {self.model_metrics['mae']:.4f}")
            
            self.is_trained = True
            
            # Guardar modelo
            if self.save_model():
                logging.info("Modelo guardado exitosamente")
            else:
                logging.warning("No se pudo guardar el modelo")
            
            return True
            
        except Exception as e:
            logging.error(f"Error entrenando modelo: {e}")
            import traceback
            logging.error(traceback.format_exc())
            return False
    
    def predict(self, input_data):
        """Realiza predicciones con el modelo entrenado"""
        if not self.is_trained:
            logging.warning("Modelo no entrenado, no se puede realizar predicción")
            return None
        
        try:
            # Convertir input a DataFrame
            input_df = pd.DataFrame([input_data])
            
            # Preparar características (modo predicción)
            X = self.prepare_features(input_df, training_mode=False)
            if X is None:
                return None
            
            # Escalar características numéricas
            numeric_features = [col for col in X.columns if col != 'vitamina_encoded']
            if numeric_features and hasattr(self.scaler, 'mean_'):
                X_scaled = X.copy()
                X_scaled[numeric_features] = self.scaler.transform(X[numeric_features])
            else:
                X_scaled = X
            
            # Realizar predicción
            prediction = self.model.predict(X_scaled)[0]
            
            # Asegurar que la predicción sea razonable
            # Los glóbulos rojos normalmente están entre 4-6 millones/mL
            # Un incremento razonable sería 0-2 millones/mL
            prediction = max(0, min(prediction, 2.0))  # Limitar entre 0 y 2
            
            logging.info(f"Predicción realizada: {prediction:.4f}")
            return prediction
            
        except Exception as e:
            logging.error(f"Error realizando predicción: {e}")
            import traceback
            logging.error(traceback.format_exc())
            return None
    
    def get_feature_importance(self):
        """Obtiene la importancia de las características del modelo"""
        if not self.is_trained:
            return {}
        
        try:
            importance = self.model.feature_importances_
            feature_importance_dict = dict(zip(self.feature_names, importance))
            
            # Ordenar por importancia descendente
            sorted_importance = dict(sorted(
                feature_importance_dict.items(), 
                key=lambda x: x[1], 
                reverse=True
            ))
            
            return sorted_importance
        except Exception as e:
            logging.error(f"Error obteniendo importancia de características: {e}")
            return {}
    
    def get_model_info(self):
        """Obtiene información del modelo"""
        return {
            'is_trained': self.is_trained,
            'metrics': self.model_metrics if self.is_trained else {},
            'feature_importance': self.get_feature_importance() if self.is_trained else {},
            'feature_names': self.feature_names,
            'n_estimators': self.model.n_estimators if self.is_trained else 0,
            'encoder_fitted': self.encoder_fitted
        }
    
    def save_model(self):
        """Guarda el modelo entrenado"""
        try:
            if not self.is_trained:
                logging.warning("Modelo no entrenado, no se puede guardar")
                return False
            
            # Asegurar que el directorio existe
            DATA_DIR.mkdir(exist_ok=True)
            
            model_data = {
                'model': self.model,
                'scaler': self.scaler,
                'encoder': self.encoder,
                'feature_names': self.feature_names,
                'metrics': self.model_metrics,
                'is_trained': self.is_trained,
                'encoder_fitted': self.encoder_fitted
            }
            
            joblib.dump(model_data, ML_MODEL_FILE)
            logging.info(f"Modelo guardado en: {ML_MODEL_FILE}")
            return True
            
        except Exception as e:
            logging.error(f"Error guardando modelo: {e}")
            return False
    
    def load_model(self):
        """Carga un modelo previamente entrenado"""
        try:
            if ML_MODEL_FILE.exists():
                logging.info("Cargando modelo existente...")
                loaded_data = joblib.load(ML_MODEL_FILE)
                
                self.model = loaded_data['model']
                self.scaler = loaded_data['scaler']
                self.encoder = loaded_data['encoder']
                self.feature_names = loaded_data.get('feature_names', [])
                self.model_metrics = loaded_data.get('metrics', {})
                self.is_trained = loaded_data.get('is_trained', False)
                self.encoder_fitted = loaded_data.get('encoder_fitted', False)
                
                logging.info("Modelo cargado exitosamente")
                if self.is_trained:
                    logging.info(f"Métricas del modelo cargado - R²: {self.model_metrics.get('r2_score', 'N/A')}")
                    logging.info(f"Encoder clases: {list(self.encoder.classes_) if self.encoder_fitted else 'No entrenado'}")
                return True
            else:
                logging.info("No se encontró modelo pre-entrenado")
                return False
                
        except Exception as e:
            logging.error(f"Error cargando modelo: {e}")
            return False
    
    def validate_prediction_input(self, input_data):
        """Valida los datos de entrada para predicción"""
        required_fields = ['vitamina', 'dosis_diaria', 'duracion_semanas', 'globulos_rojos_inicio']
        
        for field in required_fields:
            if field not in input_data:
                return False, f"Campo requerido faltante: {field}"
        
        try:
            # Validar tipos de datos
            dosis = float(input_data['dosis_diaria'])
            duracion = int(input_data['duracion_semanas'])
            globulos_inicio = float(input_data['globulos_rojos_inicio'])
            
            # Validar rangos razonables
            if dosis <= 0:
                return False, "La dosis diaria debe ser mayor a 0"
            if dosis > 1000:  # Límite superior razonable
                return False, "La dosis diaria es demasiado alta"
                
            if duracion <= 0:
                return False, "La duración debe ser mayor a 0 semanas"
            if duracion > 52:  # Máximo 1 año
                return False, "La duración no puede ser mayor a 52 semanas"
                
            if globulos_inicio <= 0:
                return False, "El nivel inicial de glóbulos rojos debe ser mayor a 0"
            if globulos_inicio > 10:  # Límite superior razonable
                return False, "El nivel inicial de glóbulos rojos es demasiado alto"
                
            return True, "Datos válidos"
            
        except (ValueError, TypeError) as e:
            return False, f"Error en tipos de datos: {e}"

# Instancia global del predictor
vitamin_predictor = VitaminPredictor()

def get_predictor():
    """Retorna la instancia global del predictor"""
    return vitamin_predictor