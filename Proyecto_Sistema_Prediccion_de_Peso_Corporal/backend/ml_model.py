import json
import pandas as pd
import numpy as np
from sklearn.linear_model import LinearRegression
from sklearn.ensemble import RandomForestRegressor
from sklearn.model_selection import train_test_split
from sklearn.metrics import mean_absolute_error, r2_score
from sklearn.preprocessing import StandardScaler
import joblib
import os

class PesoPredictor:
    def __init__(self):
        self.model = None
        self.scaler = StandardScaler()
        self.model_trained = False
        self.feature_names = ['calorias', 'edad', 'altura', 'actividad_num']
        self.data_file = os.path.join(os.path.dirname(__file__), 'data', 'datos_peso.json')
        print(f"Ruta del archivo de datos: {self.data_file}")
        
        # Intentar cargar modelo existente
        self.cargar_modelo_guardado()
        
    def cargar_datos(self):
        """Carga los datos desde el archivo JSON"""
        try:
            if not os.path.exists(self.data_file):
                print("Archivo no encontrado, creando datos de ejemplo...")
                return self.crear_datos_ejemplo()
            
            with open(self.data_file, 'r', encoding='utf-8') as f:
                datos = json.load(f)
                print(f"Datos cargados: {len(datos.get('registros', []))} registros")
                return datos
        except Exception as e:
            print(f"Error cargando datos: {e}")
            return self.crear_datos_ejemplo()
    
    def crear_datos_ejemplo(self):
        """Crea datos de ejemplo si el archivo no existe"""
        datos_ejemplo = {
            "registros": [
                {"id": 1, "fecha": "2024-01-01", "calorias": 2000, "peso": 70.0, "edad": 25, "altura": 170, "actividad": "moderada"},
                {"id": 2, "fecha": "2024-01-02", "calorias": 2200, "peso": 70.2, "edad": 25, "altura": 170, "actividad": "moderada"},
                {"id": 3, "fecha": "2024-01-03", "calorias": 2500, "peso": 70.5, "edad": 25, "altura": 170, "actividad": "alta"},
                {"id": 4, "fecha": "2024-01-04", "calorias": 1800, "peso": 70.3, "edad": 25, "altura": 170, "actividad": "baja"},
                {"id": 5, "fecha": "2024-01-05", "calorias": 3000, "peso": 70.8, "edad": 25, "altura": 170, "actividad": "alta"}
            ]
        }
        self.guardar_datos(datos_ejemplo)
        return datos_ejemplo
    
    def guardar_datos(self, datos):
        """Guarda los datos en el archivo JSON"""
        try:
            os.makedirs(os.path.dirname(self.data_file), exist_ok=True)
            with open(self.data_file, 'w', encoding='utf-8') as f:
                json.dump(datos, f, indent=2, ensure_ascii=False)
            print(f"Datos guardados: {len(datos.get('registros', []))} registros")
        except Exception as e:
            print(f"Error guardando datos: {e}")
    
    def preparar_datos(self):
        """Prepara los datos para el entrenamiento del modelo"""
        datos = self.cargar_datos()
        
        if not datos or 'registros' not in datos or len(datos['registros']) < 2:
            return None, None, None
        
        df = pd.DataFrame(datos['registros'])
        print(f"DataFrame preparado con {len(df)} registros")
        
        # Asegurar que las columnas numéricas sean del tipo correcto
        df['calorias'] = pd.to_numeric(df['calorias'], errors='coerce')
        df['peso'] = pd.to_numeric(df['peso'], errors='coerce')
        df['edad'] = pd.to_numeric(df['edad'], errors='coerce')
        df['altura'] = pd.to_numeric(df['altura'], errors='coerce')
        
        # Eliminar filas con valores NaN
        df = df.dropna()
        
        if len(df) < 2:
            return None, None, None
        
        # Convertir actividad a numérico
        actividad_map = {'baja': 1, 'moderada': 2, 'alta': 3}
        df['actividad_num'] = df['actividad'].map(actividad_map)
        
        # Calcular cambio de peso (diferencia entre registros consecutivos)
        df = df.sort_values('fecha')
        df['cambio_peso'] = df['peso'].diff()
        
        # Eliminar la primera fila (sin cambio de peso)
        df = df.iloc[1:].reset_index(drop=True)
        
        if len(df) == 0:
            return None, None, None
        
        # Características para el modelo con nombres explícitos
        X = df[self.feature_names]
        y = df['cambio_peso']
        
        return X, y, df
    
    def entrenar_modelo(self):
        """Entrena el modelo de machine learning"""
        try:
            X, y, df = self.preparar_datos()
            
            if X is None or len(X) < 2:
                return {"error": "No hay suficientes datos para entrenar el modelo (mínimo 2 registros)"}
            
            print(f"Entrenando modelo con {len(X)} muestras...")
            
            # Dividir datos
            X_train, X_test, y_train, y_test = train_test_split(
                X, y, test_size=0.2, random_state=42
            )
            
            # Escalar características con nombres
            X_train_scaled = self.scaler.fit_transform(X_train)
            X_test_scaled = self.scaler.transform(X_test)
            
            # Convertir de vuelta a DataFrame para mantener nombres
            X_train_scaled_df = pd.DataFrame(X_train_scaled, columns=self.feature_names)
            X_test_scaled_df = pd.DataFrame(X_test_scaled, columns=self.feature_names)
            
            # Entrenar modelo
            self.model = RandomForestRegressor(
                n_estimators=100, 
                random_state=42,
                max_depth=10,
                min_samples_split=5
            )
            self.model.fit(X_train_scaled_df, y_train)
            
            # Evaluar modelo
            y_pred = self.model.predict(X_test_scaled_df)
            mae = mean_absolute_error(y_test, y_pred)
            r2 = r2_score(y_test, y_pred)
            
            self.model_trained = True
            
            # Guardar modelo
            try:
                model_dir = os.path.dirname(self.data_file)
                model_path = os.path.join(model_dir, 'peso_model.joblib')
                joblib.dump({
                    'model': self.model,
                    'scaler': self.scaler,
                    'feature_names': self.feature_names
                }, model_path)
                print(f"Modelo guardado exitosamente en {model_path}")
            except Exception as e:
                print(f"Error guardando modelo: {e}")
            
            return {
                "mae": round(mae, 3),
                "r2": round(r2, 3),
                "muestras_entrenamiento": len(X_train),
                "muestras_prueba": len(X_test),
                "mensaje": "Modelo entrenado exitosamente"
            }
        except Exception as e:
            error_msg = f"Error entrenando modelo: {str(e)}"
            print(error_msg)
            return {"error": error_msg}
    
    def predecir_cambio_peso(self, calorias, edad, altura, actividad):
        """Predice el cambio de peso basado en las calorías ingeridas"""
        try:
            if not self.model_trained:
                print("Modelo no entrenado, entrenando...")
                resultado_entrenamiento = self.entrenar_modelo()
                if "error" in resultado_entrenamiento:
                    return {"error": f"No se pudo entrenar el modelo: {resultado_entrenamiento['error']}"}
            
            actividad_map = {'baja': 1, 'moderada': 2, 'alta': 3}
            actividad_num = actividad_map.get(actividad.lower(), 2)
            
            # Preparar datos de entrada como DataFrame con nombres
            X_nuevo = pd.DataFrame([[
                float(calorias), 
                float(edad), 
                float(altura), 
                float(actividad_num)
            ]], columns=self.feature_names)
            
            # Escalar datos
            X_nuevo_scaled = self.scaler.transform(X_nuevo)
            X_nuevo_scaled_df = pd.DataFrame(X_nuevo_scaled, columns=self.feature_names)
            
            # Predecir
            cambio_predicho = self.model.predict(X_nuevo_scaled_df)[0]
            
            # Generar recomendación más precisa
            recomendacion = self.generar_recomendacion_detallada(cambio_predicho, calorias, actividad)
            
            return {
                "calorias_ingeridas": calorias,
                "cambio_peso_predicho": round(cambio_predicho, 3),
                "recomendacion": recomendacion,
                "estado": "éxito"
            }
        except Exception as e:
            error_msg = f"Error en predicción: {str(e)}"
            print(error_msg)
            return {"error": error_msg}
    
    def generar_recomendacion_detallada(self, cambio_peso, calorias, actividad):
        """Genera recomendaciones detalladas basadas en la predicción"""
        if cambio_peso > 0.5:
            return f"⚠️ Alto aumento de peso previsto (+{cambio_peso:.2f} kg). Considere reducir {max(200, int(calorias * 0.1))} calorías diarias y aumentar actividad física."
        elif cambio_peso > 0.2:
            return f"📈 Aumento moderado de peso (+{cambio_peso:.2f} kg). Mantenga su régimen actual pero considere actividad {actividad} más intensa."
        elif cambio_peso > 0.05:
            return f"↗️ Leve aumento (+{cambio_peso:.2f} kg). Su peso se mantiene estable. Buen trabajo!"
        elif cambio_peso < -0.5:
            return f"⚠️ Pérdida de peso significativa ({cambio_peso:.2f} kg). Asegúrese de consumir nutrientes suficientes y consulte a un profesional."
        elif cambio_peso < -0.2:
            return f"📉 Pérdida moderada ({cambio_peso:.2f} kg). Si es intencional, buen progreso. Mantenga una dieta balanceada."
        elif cambio_peso < -0.05:
            return f"↘️ Leve pérdida ({cambio_peso:.2f} kg). Peso estable con tendencia a la baja. Excelente control."
        else:
            return f"✅ Peso estable ({cambio_peso:.2f} kg). Excelente mantenimiento de su régimen actual. ¡Siga así!"
    
    def agregar_registro(self, nuevo_registro):
        """Agrega un nuevo registro a la base de datos"""
        try:
            datos = self.cargar_datos()
            if not datos or 'registros' not in datos:
                datos = {'registros': []}
            
            nuevo_id = max([r['id'] for r in datos['registros']]) + 1 if datos['registros'] else 1
            nuevo_registro['id'] = nuevo_id
            datos['registros'].append(nuevo_registro)
            self.guardar_datos(datos)
            
            # Reentrenar modelo con nuevos datos si hay suficientes
            if len(datos['registros']) >= 3:
                print("Reentrenando modelo con nuevo registro...")
                self.entrenar_modelo()
            
            return {"mensaje": "Registro agregado exitosamente", "id": nuevo_id}
        except Exception as e:
            return {"error": f"Error agregando registro: {str(e)}"}
    
    def actualizar_registro(self, registro_actualizado):
        """Actualiza un registro existente"""
        try:
            datos = self.cargar_datos()
            if not datos or 'registros' not in datos:
                return {"error": "No hay registros existentes"}
            
            registro_id = registro_actualizado['id']
            for i, registro in enumerate(datos['registros']):
                if registro['id'] == registro_id:
                    datos['registros'][i] = registro_actualizado
                    self.guardar_datos(datos)
                    
                    # Reentrenar modelo con datos actualizados si hay suficientes
                    if len(datos['registros']) >= 3:
                        print("Reentrenando modelo con registro actualizado...")
                        self.entrenar_modelo()
                    
                    return {"mensaje": f"Registro {registro_id} actualizado exitosamente"}
            
            return {"error": f"Registro con ID {registro_id} no encontrado"}
        except Exception as e:
            return {"error": f"Error actualizando registro: {str(e)}"}
    
    def eliminar_registro(self, registro_id):
        """Elimina un registro"""
        try:
            datos = self.cargar_datos()
            if not datos or 'registros' not in datos:
                return {"error": "No hay registros existentes"}
            
            registros_originales = len(datos['registros'])
            datos['registros'] = [r for r in datos['registros'] if r['id'] != registro_id]
            
            if len(datos['registros']) == registros_originales:
                return {"error": f"Registro con ID {registro_id} no encontrado"}
            
            self.guardar_datos(datos)
            
            # Reentrenar modelo si hay suficientes datos
            if len(datos['registros']) >= 3:
                print("Reentrenando modelo después de eliminar registro...")
                self.entrenar_modelo()
            
            return {"mensaje": f"Registro {registro_id} eliminado exitosamente"}
        except Exception as e:
            return {"error": f"Error eliminando registro: {str(e)}"}
    
    def obtener_registro_por_id(self, registro_id):
        """Obtiene un registro específico por ID"""
        try:
            datos = self.cargar_datos()
            if not datos or 'registros' not in datos:
                return None
            
            for registro in datos['registros']:
                if registro['id'] == registro_id:
                    return registro
            
            return None
        except Exception as e:
            print(f"Error obteniendo registro: {e}")
            return None
    
    def obtener_estadisticas(self):
        """Calcula estadísticas de los datos"""
        try:
            datos = self.cargar_datos()
            if not datos or 'registros' not in datos or len(datos['registros']) == 0:
                return {"error": "No hay datos disponibles"}
            
            df = pd.DataFrame(datos['registros'])
            
            # Asegurar que los datos sean numéricos
            df['calorias'] = pd.to_numeric(df['calorias'], errors='coerce')
            df['peso'] = pd.to_numeric(df['peso'], errors='coerce')
            df = df.dropna()
            
            if len(df) == 0:
                return {"error": "No hay datos numéricos válidos"}
            
            stats = {
                "total_registros": len(df),
                "promedio_calorias": round(df['calorias'].mean(), 2),
                "promedio_peso": round(df['peso'].mean(), 2),
                "max_calorias": int(df['calorias'].max()),
                "min_calorias": int(df['calorias'].min()),
                "max_peso": round(df['peso'].max(), 2),
                "min_peso": round(df['peso'].min(), 2),
                "tendencia_peso": "Estable"
            }
            
            # Calcular tendencia
            if len(df) > 1:
                df = df.sort_values('fecha')
                primer_peso = df.iloc[0]['peso']
                ultimo_peso = df.iloc[-1]['peso']
                if ultimo_peso > primer_peso + 0.5:
                    stats["tendencia_peso"] = "En aumento"
                elif ultimo_peso < primer_peso - 0.5:
                    stats["tendencia_peso"] = "En disminución"
            
            return stats
        except Exception as e:
            return {"error": f"Error calculando estadísticas: {str(e)}"}
    
    def cargar_modelo_guardado(self):
        """Carga un modelo previamente guardado"""
        try:
            model_dir = os.path.dirname(self.data_file)
            model_path = os.path.join(model_dir, 'peso_model.joblib')
            
            if os.path.exists(model_path):
                modelo_data = joblib.load(model_path)
                self.model = modelo_data['model']
                self.scaler = modelo_data['scaler']
                self.feature_names = modelo_data.get('feature_names', ['calorias', 'edad', 'altura', 'actividad_num'])
                self.model_trained = True
                print("Modelo cargado desde archivo")
                return True
            return False
        except Exception as e:
            print(f"Error cargando modelo: {e}")
            return False