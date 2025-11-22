import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestRegressor
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder, StandardScaler
from sklearn.metrics import r2_score, mean_absolute_error
import json

class MLProcessor:
    def __init__(self, data_processor):
        self.data_processor = data_processor
        self.model = None
        self.scaler = StandardScaler()
        self.label_encoders = {}
        
    def convert_numpy_types(self, obj):
        """Convertir tipos NumPy a tipos nativos de Python"""
        if isinstance(obj, (np.integer, np.int64, np.int32)):
            return int(obj)
        elif isinstance(obj, (np.floating, np.float64, np.float32)):
            return float(obj)
        elif isinstance(obj, np.ndarray):
            return obj.tolist()
        elif isinstance(obj, dict):
            return {key: self.convert_numpy_types(value) for key, value in obj.items()}
        elif isinstance(obj, list):
            return [self.convert_numpy_types(item) for item in obj]
        else:
            return obj
        
    def prepare_features(self, df):
        """Preparar características para el modelo"""
        # Codificar variables categóricas
        categorical_cols = ['activity_type', 'gender']
        for col in categorical_cols:
            self.label_encoders[col] = LabelEncoder()
            df[col + '_encoded'] = self.label_encoders[col].fit_transform(df[col])
        
        # Seleccionar características
        features = ['duration_minutes', 'intensity', 'age', 'heart_rate_avg', 
                   'calories_burned', 'sleep_hours', 'hydration_liters',
                   'activity_type_encoded', 'gender_encoded']
        
        X = df[features]
        y = df['cells_produced']
        
        return X, y
    
    def train_model(self):
        """Entrenar modelo de machine learning"""
        try:
            df = self.data_processor.get_dataframe()
            X, y = self.prepare_features(df)
            
            # Escalar características
            X_scaled = self.scaler.fit_transform(X)
            
            # Dividir datos
            X_train, X_test, y_train, y_test = train_test_split(
                X_scaled, y, test_size=0.2, random_state=42
            )
            
            # Entrenar modelo
            self.model = RandomForestRegressor(
                n_estimators=100,
                max_depth=10,
                random_state=42
            )
            
            self.model.fit(X_train, y_train)
            
            # Evaluar modelo
            y_pred = self.model.predict(X_test)
            r2 = r2_score(y_test, y_pred)
            mae = mean_absolute_error(y_test, y_pred)
            
            # Convertir importancia de características a tipos nativos
            feature_importance = dict(zip(X.columns, self.model.feature_importances_))
            feature_importance = self.convert_numpy_types(feature_importance)
            
            return {
                'r2_score': float(r2),
                'mae': float(mae),
                'feature_importance': feature_importance
            }
        except Exception as e:
            print(f"❌ Error entrenando modelo: {e}")
            raise
    
    def predict_cells(self, input_data):
        """Predecir producción de células"""
        if self.model is None:
            self.train_model()
        
        try:
            # Preparar datos de entrada
            df_input = pd.DataFrame([input_data])
            
            # Codificar variables categóricas
            for col in ['activity_type', 'gender']:
                if col in input_data:
                    if col in self.label_encoders:
                        df_input[col + '_encoded'] = self.label_encoders[col].transform([input_data[col]])
            
            # Seleccionar características
            features = ['duration_minutes', 'intensity', 'age', 'heart_rate_avg', 
                       'calories_burned', 'sleep_hours', 'hydration_liters',
                       'activity_type_encoded', 'gender_encoded']
            
            X_input = df_input[features]
            X_scaled = self.scaler.transform(X_input)
            
            prediction = self.model.predict(X_scaled)[0]
            
            return int(prediction)
        except Exception as e:
            print(f"❌ Error en predicción: {e}")
            raise
    
    def get_activity_recommendations(self, user_profile):
        """Generar recomendaciones de actividad optimizadas"""
        try:
            activities = ['Running', 'Swimming', 'Cycling', 'Weight Training', 'Yoga', 'HIIT']
            recommendations = []
            
            for activity in activities:
                test_input = user_profile.copy()
                test_input['activity_type'] = activity
                
                predicted_cells = self.predict_cells(test_input)
                
                recommendations.append({
                    'activity': activity,
                    'predicted_cells': int(predicted_cells),
                    'efficiency_score': float(predicted_cells / test_input['duration_minutes'])
                })
            
            # Ordenar por eficiencia
            recommendations.sort(key=lambda x: x['efficiency_score'], reverse=True)
            
            return recommendations
        except Exception as e:
            print(f"❌ Error generando recomendaciones: {e}")
            raise