import json
import pandas as pd
import numpy as np
from datetime import datetime, timedelta
import os

class DataProcessor:
    def __init__(self, data_file):
        self.data_file = data_file
        self.data = self.load_data()
        
    def load_data(self):
        """Cargar datos desde el archivo JSON con manejo de errores"""
        try:
            # Verificar si el archivo existe y no está vacío
            if not os.path.exists(self.data_file) or os.path.getsize(self.data_file) == 0:
                print("📁 Archivo de datos no existe o está vacío. Generando datos de ejemplo...")
                return self.generate_sample_data()
            
            with open(self.data_file, 'r', encoding='utf-8') as f:
                content = f.read().strip()
                if not content:
                    print("📁 Archivo de datos vacío. Generando datos de ejemplo...")
                    return self.generate_sample_data()
                
                data = json.loads(content)
                print(f"✅ Datos cargados exitosamente: {len(data)} registros")
                return data
                
        except (json.JSONDecodeError, Exception) as e:
            print(f"❌ Error cargando datos: {e}. Generando nuevos datos...")
            return self.generate_sample_data()
    
    def convert_numpy_types(self, obj):
        """Convertir tipos NumPy a tipos nativos de Python para JSON serialization"""
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
    
    def generate_sample_data(self):
        """Generar datos de ejemplo robustos"""
        print("🔄 Generando base de datos de ejemplo...")
        np.random.seed(42)
        activities = ['Running', 'Swimming', 'Cycling', 'Weight Training', 'Yoga', 'HIIT']
        ages = range(18, 65)
        genders = ['Male', 'Female']
        
        data = []
        base_date = datetime.now() - timedelta(days=365)
        
        for i in range(1000):
            activity = np.random.choice(activities)
            age = np.random.choice(ages)
            gender = np.random.choice(genders)
            duration = max(10, np.random.normal(60, 20))  # minutos, mínimo 10
            intensity = np.random.uniform(0.5, 1.0)
            
            # Cálculo de células producidas basado en actividad, duración e intensidad
            base_cells = {
                'Running': 1500000,
                'Swimming': 1800000,
                'Cycling': 1200000,
                'Weight Training': 800000,
                'Yoga': 500000,
                'HIIT': 2000000
            }
            
            cells_produced = int(base_cells[activity] * duration/60 * intensity * 
                               (1 + (30 - abs(age-30))/100))  # Efecto edad
            
            entry = {
                'id': i + 1,
                'date': (base_date + timedelta(days=i)).strftime('%Y-%m-%d'),
                'activity_type': activity,
                'duration_minutes': round(float(duration), 1),  # Convertir explícitamente a float
                'intensity': round(float(intensity), 2),        # Convertir explícitamente a float
                'age': int(age),                               # Convertir explícitamente a int
                'gender': gender,
                'cells_produced': int(cells_produced),         # Convertir explícitamente a int
                'heart_rate_avg': int(np.random.randint(120, 180)),
                'calories_burned': int(duration * intensity * 8),
                'sleep_hours': round(float(max(4, np.random.normal(7, 1))), 1),
                'hydration_liters': round(float(max(1, np.random.normal(2, 0.5))), 1)
            }
            data.append(entry)
        
        # Convertir todos los tipos NumPy a tipos nativos de Python
        data = self.convert_numpy_types(data)
        
        # Guardar datos generados
        os.makedirs(os.path.dirname(self.data_file), exist_ok=True)
        with open(self.data_file, 'w', encoding='utf-8') as f:
            json.dump(data, f, indent=2, ensure_ascii=False)
        
        print(f"✅ Base de datos generada: {len(data)} registros guardados en {self.data_file}")
        return data
    
    def get_dataframe(self):
        """Convertir datos a DataFrame de pandas"""
        return pd.DataFrame(self.data)
    
    def add_record(self, record):
        """Agregar nuevo registro"""
        # Convertir tipos NumPy antes de agregar
        record = self.convert_numpy_types(record)
        record['id'] = len(self.data) + 1
        self.data.append(record)
        self.save_data()
        return record
    
    def update_record(self, record_id, updated_data):
        """Actualizar registro existente"""
        # Convertir tipos NumPy antes de actualizar
        updated_data = self.convert_numpy_types(updated_data)
        for i, record in enumerate(self.data):
            if record['id'] == record_id:
                updated_data['id'] = record_id
                self.data[i] = updated_data
                self.save_data()
                return updated_data
        return None
    
    def delete_record(self, record_id):
        """Eliminar registro"""
        self.data = [record for record in self.data if record['id'] != record_id]
        self.save_data()
        return True
    
    def save_data(self):
        """Guardar datos al archivo JSON"""
        try:
            # Convertir tipos NumPy antes de guardar
            data_to_save = self.convert_numpy_types(self.data)
            with open(self.data_file, 'w', encoding='utf-8') as f:
                json.dump(data_to_save, f, indent=2, ensure_ascii=False)
            print(f"💾 Datos guardados: {len(self.data)} registros")
        except Exception as e:
            print(f"❌ Error guardando datos: {e}")