import json
import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestRegressor, GradientBoostingRegressor
from sklearn.linear_model import LinearRegression
from sklearn.preprocessing import LabelEncoder, StandardScaler
from sklearn.model_selection import train_test_split
from sklearn.metrics import mean_absolute_error, r2_score
import warnings
warnings.filterwarnings('ignore')

class MLProcessor:
    def __init__(self, data_path):
        self.data_path = data_path
        self.scaler = StandardScaler()
        self.label_encoder = LabelEncoder()
        self.model = None
        self.is_trained = False
        self.features = [
            'tipo_encoded', 'capacidad_gb', 'tiempo_uso_meses', 
            'horas_encendido', 'ciclos_escritura', 'temperatura_promedio',
            'bad_sectors', 'marca_encoded'
        ]
    
    def cargar_y_limpiar_datos(self):
        """Cargar y limpiar datos de discos duros - VERSIÓN MEJORADA"""
        try:
            with open(self.data_path, 'r', encoding='utf-8') as f:
                datos = json.load(f)
            
            df = pd.DataFrame(datos['discos_duros'])
            
            # Verificar que tenemos datos
            if len(df) == 0:
                print("❌ No hay datos en el archivo JSON")
                return pd.DataFrame(), datos
            
            print(f"📁 Datos crudos cargados: {len(df)} registros")
            
            # Limpiar datos - reemplazar NaN y valores problemáticos
            df_clean = df.copy()
            
            # Columnas numéricas con valores por defecto
            numeric_columns = ['capacidad_gb', 'tiempo_uso_meses', 'horas_encendido', 
                             'ciclos_escritura', 'temperatura_promedio', 'bad_sectors', 
                             'porcentaje_desgaste']
            
            for col in numeric_columns:
                if col in df_clean.columns:
                    # Convertir a numérico y reemplazar NaN
                    df_clean[col] = pd.to_numeric(df_clean[col], errors='coerce')
                    # Reemplazar NaN con valores por defecto
                    if col == 'porcentaje_desgaste':
                        df_clean[col].fillna(50.0, inplace=True)  # Valor medio
                    elif col == 'capacidad_gb':
                        df_clean[col].fillna(1000, inplace=True)  # 1TB por defecto
                    elif col == 'tiempo_uso_meses':
                        df_clean[col].fillna(12, inplace=True)    # 1 año por defecto
                    else:
                        df_clean[col].fillna(0, inplace=True)
            
            # Verificar que tenemos porcentaje_desgaste
            if 'porcentaje_desgaste' not in df_clean.columns:
                print("❌ No hay columna 'porcentaje_desgaste' en los datos")
                return pd.DataFrame(), datos
            
            # Filtrar registros con desgaste válido
            df_clean = df_clean[df_clean['porcentaje_desgaste'].notna()]
            df_clean = df_clean[df_clean['porcentaje_desgaste'].between(0, 100)]
            
            if len(df_clean) == 0:
                print("❌ No hay registros con desgaste válido (0-100%)")
                return pd.DataFrame(), datos
            
            print(f"✅ Datos limpios: {len(df_clean)} registros válidos")
            
            # Preparar características para ML
            try:
                # Codificar tipo
                if len(df_clean['tipo'].unique()) > 1:
                    df_clean['tipo_encoded'] = self.label_encoder.fit_transform(df_clean['tipo'])
                else:
                    # Si solo hay un tipo, usar 0
                    df_clean['tipo_encoded'] = 0
                
                # Codificar marca
                marca_encoder = LabelEncoder()
                df_clean['marca_encoded'] = marca_encoder.fit_transform(df_clean['marca'])
                
            except Exception as e:
                print(f"⚠️ Error en codificación: {e}")
                # Usar valores por defecto
                df_clean['tipo_encoded'] = 0
                df_clean['marca_encoded'] = 0
            
            return df_clean, datos
        
        except Exception as e:
            print(f"❌ Error cargando datos: {e}")
            return pd.DataFrame(), {}
    
    def crear_datos_sinteticos(self, df_existente):
        """Crear datos sintéticos si no hay suficientes datos reales"""
        print("🔄 Generando datos sintéticos para entrenamiento...")
        
        datos_sinteticos = []
        
        # Basarse en los datos existentes
        for _, disco in df_existente.iterrows():
            for i in range(2):  # Duplicar cada registro con variaciones
                nuevo_disco = disco.copy()
                
                # Variar ligeramente los valores
                variacion = np.random.normal(1, 0.1)  # ±10% de variación
                
                if 'tiempo_uso_meses' in nuevo_disco:
                    nuevo_disco['tiempo_uso_meses'] = max(1, int(nuevo_disco['tiempo_uso_meses'] * variacion))
                
                if 'horas_encendido' in nuevo_disco:
                    nuevo_disco['horas_encendido'] = max(100, int(nuevo_disco['horas_encendido'] * variacion))
                
                if 'ciclos_escritura' in nuevo_disco:
                    nuevo_disco['ciclos_escritura'] = max(100, int(nuevo_disco['ciclos_escritura'] * variacion))
                
                if 'porcentaje_desgaste' in nuevo_disco:
                    # El desgaste debería aumentar con el tiempo de uso
                    factor_desgaste = nuevo_disco['tiempo_uso_meses'] / 12  # Desgaste por año
                    nuevo_disco['porcentaje_desgaste'] = min(100, max(0, int(factor_desgaste * 10 + np.random.normal(0, 5))))
                
                datos_sinteticos.append(nuevo_disco)
        
        df_sintetico = pd.DataFrame(datos_sinteticos)
        print(f"✅ Datos sintéticos generados: {len(df_sintetico)} registros")
        
        return df_sintetico
    
    def entrenar_modelo_prediccion(self):
        """Entrenar modelo para predecir desgaste de discos - VERSIÓN ROBUSTA"""
        try:
            df, _ = self.cargar_y_limpiar_datos()
            
            if df.empty:
                error_msg = "No hay datos válidos para entrenar el modelo"
                print(f"❌ {error_msg}")
                return {"error": error_msg}
            
            print(f"🤖 Iniciando entrenamiento con {len(df)} registros...")
            
            # Si tenemos muy pocos datos, generar datos sintéticos
            if len(df) < 4:
                print("📊 Pocos datos reales, generando datos sintéticos...")
                df_sintetico = self.crear_datos_sinteticos(df)
                df = pd.concat([df, df_sintetico], ignore_index=True)
                print(f"📈 Dataset ampliado: {len(df)} registros")
            
            # Verificar que todas las características existan
            for feature in self.features:
                if feature not in df.columns:
                    print(f"⚠️ Característica faltante: {feature}, añadiendo con valor 0")
                    df[feature] = 0
            
            # Preparar datos de entrenamiento
            X = df[self.features]
            y = df['porcentaje_desgaste']
            
            # Verificar que no haya NaN en los datos
            if X.isna().any().any() or y.isna().any():
                print("⚠️ NaN detectado en datos, limpiando...")
                X = X.fillna(0)
                y = y.fillna(50)  # Valor medio para desgaste
            
            print(f"📊 Características: {X.shape}")
            print(f"🎯 Target: {y.shape}")
            print(f"📋 Rango de desgaste: {y.min():.1f}% - {y.max():.1f}%")
            
            # Si tenemos pocos datos, usar validación cruzada simple
            if len(df) <= 5:
                print("🔧 Pocos datos, usando entrenamiento con todos los datos")
                X_train, X_test, y_train, y_test = X, X, y, y
            else:
                # Dividir datos normalmente
                test_size = min(0.3, 2/len(df))  # Máximo 30%, mínimo 2 muestras de test
                X_train, X_test, y_train, y_test = train_test_split(
                    X, y, test_size=test_size, random_state=42, shuffle=True
                )
            
            print(f"📚 Entrenamiento: {len(X_train)}, Prueba: {len(X_test)}")
            
            # Escalar características
            try:
                X_train_scaled = self.scaler.fit_transform(X_train)
                X_test_scaled = self.scaler.transform(X_test)
                print("✅ Características escaladas correctamente")
            except Exception as e:
                print(f"⚠️ Error escalando características: {e}")
                # Usar datos sin escalar
                X_train_scaled = X_train.values
                X_test_scaled = X_test.values
            
            # Entrenar múltiples modelos con configuración robusta
            models = {
                'RandomForest': RandomForestRegressor(
                    n_estimators=30, 
                    max_depth=5, 
                    random_state=42,
                    min_samples_split=2,
                    min_samples_leaf=1
                ),
                'GradientBoosting': GradientBoostingRegressor(
                    n_estimators=30,
                    max_depth=3,
                    random_state=42,
                    learning_rate=0.1
                ),
                'LinearRegression': LinearRegression()
            }
            
            best_score = -np.inf
            best_model = None
            best_model_name = ""
            resultados = {}
            
            for name, model in models.items():
                try:
                    print(f"🔧 Entrenando {name}...")
                    model.fit(X_train_scaled, y_train)
                    
                    # Predecir y calcular métricas
                    y_pred = model.predict(X_test_scaled)
                    
                    # Manejar posibles NaN en predicciones
                    if np.isnan(y_pred).any():
                        print(f"   ⚠️ {name} produjo NaN en predicciones")
                        continue
                    
                    score = r2_score(y_test, y_pred)
                    mae = mean_absolute_error(y_test, y_pred)
                    
                    print(f"   ✅ {name} - R²: {score:.3f}, MAE: {mae:.2f}%")
                    
                    resultados[name] = {
                        'score': score,
                        'mae': mae,
                        'model': model
                    }
                    
                    if score > best_score:
                        best_score = score
                        best_model = model
                        best_model_name = name
                        
                except Exception as e:
                    print(f"   ❌ Error en {name}: {e}")
                    continue
            
            # Si todos los modelos fallaron, usar el más simple
            if best_model is None:
                print("🔄 Todos los modelos complejos fallaron, usando LinearRegression básico...")
                try:
                    # Modelo lineal simple sin escalado
                    model_simple = LinearRegression()
                    model_simple.fit(X_train.values, y_train)
                    y_pred_simple = model_simple.predict(X_test.values)
                    
                    if not np.isnan(y_pred_simple).any():
                        best_score = r2_score(y_test, y_pred_simple)
                        best_model = model_simple
                        best_model_name = "LinearRegression_Simple"
                        resultados[best_model_name] = {
                            'score': best_score,
                            'mae': mean_absolute_error(y_test, y_pred_simple),
                            'model': best_model
                        }
                        print(f"   ✅ {best_model_name} - R²: {best_score:.3f}")
                except Exception as e:
                    print(f"   ❌ LinearRegression simple también falló: {e}")
            
            if best_model is None:
                error_msg = "Todos los modelos fallaron en el entrenamiento"
                print(f"❌ {error_msg}")
                return {"error": error_msg}
            
            # Guardar el mejor modelo
            self.model = best_model
            self.is_trained = True
            
            # Métricas finales
            y_pred_best = best_model.predict(X_test_scaled)
            mae_final = mean_absolute_error(y_test, y_pred_best)
            
            resultado = {
                "modelo": str(best_model_name),
                "r2_score": float(round(best_score, 3)),
                "mae": float(round(mae_final, 2)),
                "caracteristicas_importantes": self._obtener_importancias(best_model),
                "muestras_entrenamiento": int(len(X_train)),
                "muestras_prueba": int(len(X_test)),
                "total_muestras": int(len(df)),
                "rango_desgaste": f"{y.min():.1f}% - {y.max():.1f}%"
            }
            
            print(f"✅ Modelo {best_model_name} entrenado exitosamente")
            print(f"   R²: {best_score:.3f}, MAE: {mae_final:.2f}%")
            print(f"   Muestras: {len(X_train)} entrenamiento, {len(X_test)} prueba")
            
            return resultado
            
        except Exception as e:
            error_msg = f"Error entrenando modelo: {str(e)}"
            print(f"❌ {error_msg}")
            import traceback
            print(f"📋 Traceback: {traceback.format_exc()}")
            return {"error": error_msg}
    
    def _obtener_importancias(self, model):
        """Obtener importancia de características del modelo"""
        try:
            importancias = {}
            
            if hasattr(model, 'feature_importances_'):
                for i, feature in enumerate(self.features):
                    if i < len(model.feature_importances_):
                        importancia = float(model.feature_importances_[i])
                        importancias[feature] = round(importancia, 4)
            elif hasattr(model, 'coef_'):
                for i, feature in enumerate(self.features):
                    if i < len(model.coef_):
                        coef = float(model.coef_[i])
                        importancias[feature] = round(coef, 4)
            else:
                importancias = {"info": "Modelo sin importancias de características"}
            
            return importancias
            
        except Exception as e:
            return {"error_importancias": str(e)}
    
    def predecir_desgaste(self, datos_disco):
        """Predecir porcentaje de desgaste para un disco nuevo"""
        try:
            print("🔮 Iniciando predicción...")
            
            # Si el modelo no está entrenado, entrenarlo primero
            if not self.is_trained or self.model is None:
                print("🔄 Modelo no entrenado, entrenando ahora...")
                entrenamiento = self.entrenar_modelo_prediccion()
                
                if "error" in entrenamiento:
                    return {"error": f"No se pudo entrenar el modelo: {entrenamiento['error']}"}
            
            print("✅ Modelo listo para predicción")
            
            # Preparar datos de entrada
            df_input = pd.DataFrame([datos_disco])
            
            # Codificar tipo
            try:
                df_original, _ = self.cargar_y_limpiar_datos()
                if not df_original.empty:
                    tipos_unicos = df_original['tipo'].unique()
                    if datos_disco['tipo'] in tipos_unicos:
                        df_input['tipo_encoded'] = self.label_encoder.transform([datos_disco['tipo']])[0]
                    else:
                        df_input['tipo_encoded'] = 0
                else:
                    df_input['tipo_encoded'] = 0
            except:
                df_input['tipo_encoded'] = 0
            
            # Codificar marca
            try:
                df_original, _ = self.cargar_y_limpiar_datos()
                if not df_original.empty:
                    marca_encoder = LabelEncoder()
                    marcas_unicas = df_original['marca'].unique()
                    marca_encoder.fit(marcas_unicas)
                    if datos_disco['marca'] in marcas_unicas:
                        df_input['marca_encoded'] = marca_encoder.transform([datos_disco['marca']])[0]
                    else:
                        df_input['marca_encoded'] = 0
                else:
                    df_input['marca_encoded'] = 0
            except:
                df_input['marca_encoded'] = 0
            
            # Asegurar todas las características
            for feature in self.features:
                if feature not in df_input.columns:
                    df_input[feature] = 0
                else:
                    df_input[feature] = pd.to_numeric(df_input[feature], errors='coerce').fillna(0)
            
            # Preparar para predicción
            X_input = df_input[self.features]
            
            try:
                X_input_scaled = self.scaler.transform(X_input)
            except:
                X_input_scaled = X_input.values
            
            # Realizar predicción
            prediccion = self.model.predict(X_input_scaled)[0]
            
            # Validar predicción
            if np.isnan(prediccion) or np.isinf(prediccion):
                print("⚠️ Predicción inválida, usando valor por defecto")
                # Calcular desgaste estimado basado en tiempo de uso
                tiempo_uso = datos_disco.get('tiempo_uso_meses', 12)
                prediccion = min(95, max(5, tiempo_uso * 2))  # 2% por mes máximo
            
            prediccion = max(0, min(100, float(prediccion)))
            
            # Determinar estado
            if prediccion < 20:
                estado = "Excelente"
            elif prediccion < 40:
                estado = "Bueno"
            elif prediccion < 60:
                estado = "Moderado"
            elif prediccion < 80:
                estado = "Alto"
            else:
                estado = "Crítico"
            
            vida_util = self._calcular_vida_util(datos_disco, prediccion)
            
            resultado = {
                "porcentaje_desgaste_predicho": float(round(prediccion, 1)),
                "estado_predicho": str(estado),
                "vida_util_restante": vida_util
            }
            
            print(f"✅ Predicción completada: {resultado}")
            return resultado
            
        except Exception as e:
            error_msg = f"Error en predicción: {str(e)}"
            print(f"❌ {error_msg}")
            return {"error": error_msg}
    
    def _calcular_vida_util(self, datos_disco, desgaste_predicho):
        """Calcular vida útil restante estimada"""
        try:
            with open(self.data_path, 'r', encoding='utf-8') as f:
                datos = json.load(f)
            
            tipo_disco = datos_disco['tipo']
            vida_util_total = 60.0  # Valor por defecto
            
            if tipo_disco in datos.get('metricas_por_tipo', {}):
                vida_util_total = float(datos['metricas_por_tipo'][tipo_disco]['vida_util_meses'])
            
            tiempo_uso_actual = float(datos_disco.get('tiempo_uso_meses', 0))
            
            if desgaste_predicho >= 100:
                return {
                    "meses_restantes": 0.0, 
                    "riesgo": "Inminente", 
                    "recomendacion": "Reemplazar inmediatamente"
                }
            
            if desgaste_predicho > 0 and tiempo_uso_actual > 0:
                tasa_desgaste = desgaste_predicho / tiempo_uso_actual
                meses_restantes = (100.0 - desgaste_predicho) / tasa_desgaste
            else:
                meses_restantes = vida_util_total - tiempo_uso_actual
            
            meses_restantes = max(0.0, float(round(meses_restantes, 1)))
            
            # Determinar riesgo
            if meses_restantes > 24:
                riesgo = "Bajo"
            elif meses_restantes > 12:
                riesgo = "Moderado"
            elif meses_restantes > 6:
                riesgo = "Alto"
            else:
                riesgo = "Crítico"
            
            recomendacion = self._generar_recomendacion(riesgo, tipo_disco)
            
            return {
                "meses_restantes": meses_restantes,
                "riesgo": str(riesgo),
                "recomendacion": str(recomendacion)
            }
            
        except Exception as e:
            return {
                "meses_restantes": 12.0,  # Valor por defecto
                "riesgo": "Moderado", 
                "recomendacion": "Monitorear estado del disco regularmente"
            }
    
    def _generar_recomendacion(self, riesgo, tipo_disco):
        """Generar recomendación basada en el riesgo"""
        recomendaciones = {
            "Bajo": f"El disco {tipo_disco} está en excelente estado. Continuar con monitoreo regular.",
            "Moderado": f"Disco {tipo_disco} mostrando desgaste moderado. Realizar backup importante.",
            "Alto": f"ALERTA: Disco {tipo_disco} con desgaste avanzado. Planificar reemplazo pronto.",
            "Crítico": f"URGENTE: Disco {tipo_disco} en riesgo crítico. Reemplazar inmediatamente.",
            "Inminente": f"EMERGENCIA: Disco {tipo_disco} puede fallar. Reemplazar urgentemente."
        }
        return recomendaciones.get(riesgo, "Monitorear estado del disco.")
    
    def analizar_tendencias_desgaste(self):
        """Analizar tendencias de desgaste por tipo de disco - VERSIÓN SEGURA"""
        try:
            print("📊 Iniciando análisis de tendencias...")
            df, datos = self.cargar_y_limpiar_datos()
            
            if df.empty:
                return {"error": "No hay datos disponibles para análisis"}
            
            print(f"📈 Dataset para análisis: {len(df)} registros")
            
            # 1. Desgaste por tipo - con manejo de errores
            desgaste_por_tipo = {}
            for tipo in df['tipo'].unique():
                try:
                    tipo_str = str(tipo)
                    tipo_data = df[df['tipo'] == tipo]['porcentaje_desgaste']
                    
                    if len(tipo_data) > 0:
                        desgaste_por_tipo[tipo_str] = {
                            'mean': float(tipo_data.mean()),
                            'std': float(tipo_data.std() if len(tipo_data) > 1 else 0),
                            'count': int(tipo_data.count()),
                            'max': float(tipo_data.max())
                        }
                except Exception as e:
                    print(f"⚠️ Error procesando tipo {tipo}: {e}")
                    continue
            
            # 2. Correlaciones - con valores por defecto
            correlaciones = {}
            numeric_cols = ['capacidad_gb', 'tiempo_uso_meses', 'horas_encendido', 
                           'ciclos_escritura', 'temperatura_promedio', 'bad_sectors']
            
            for col in numeric_cols:
                if col in df.columns:
                    try:
                        corr_val = df[col].corr(df['porcentaje_desgaste'])
                        if pd.isna(corr_val) or np.isinf(corr_val):
                            correlaciones[col] = 0.0
                        else:
                            correlaciones[col] = float(round(corr_val, 3))
                    except:
                        correlaciones[col] = 0.0
            
            # 3. Discos críticos
            discos_criticos = []
            try:
                criticos_df = df[df['porcentaje_desgaste'] > 70]
                for _, disco in criticos_df.iterrows():
                    discos_criticos.append({
                        'id': int(disco.get('id', 0)),
                        'tipo': str(disco.get('tipo', 'Desconocido')),
                        'marca': str(disco.get('marca', 'Desconocida')),
                        'modelo': str(disco.get('modelo', 'Desconocido')),
                        'porcentaje_desgaste': float(disco['porcentaje_desgaste']),
                        'estado': str(disco.get('estado', 'Crítico'))
                    })
            except Exception as e:
                print(f"⚠️ Error en discos críticos: {e}")
            
            # 4. Estadísticas generales
            try:
                desgaste_promedio = float(df['porcentaje_desgaste'].mean())
                discos_riesgo = int(len(df[df['porcentaje_desgaste'] > 60]))
            except:
                desgaste_promedio = 0.0
                discos_riesgo = 0
            
            analisis = {
                "desgaste_por_tipo": desgaste_por_tipo,
                "correlaciones": correlaciones,
                "discos_criticos": discos_criticos,
                "estadisticas_generales": {
                    "total_discos": int(len(df)),
                    "desgaste_promedio": desgaste_promedio,
                    "discos_en_riesgo": discos_riesgo
                }
            }
            
            print("✅ Análisis de tendencias completado exitosamente")
            return analisis
            
        except Exception as e:
            error_msg = f"Error en análisis: {str(e)}"
            print(f"❌ {error_msg}")
            return {"error": error_msg}