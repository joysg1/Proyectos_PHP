import json
import logging
from pathlib import Path
from backend.config.settings import DATA_FILE

def setup_logging():
    """Configura el sistema de logging"""
    logging.basicConfig(
        level=logging.INFO,
        format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
    )

def load_json_data():
    """Carga datos desde el archivo JSON"""
    try:
        if DATA_FILE.exists():
            with open(DATA_FILE, 'r', encoding='utf-8') as f:
                return json.load(f)
        return []
    except Exception as e:
        logging.error(f"Error loading data: {e}")
        return []

def save_json_data(data):
    """Guarda datos en el archivo JSON"""
    try:
        with open(DATA_FILE, 'w', encoding='utf-8') as f:
            json.dump(data, f, indent=2, ensure_ascii=False)
        return True
    except Exception as e:
        logging.error(f"Error saving data: {e}")
        return False

def validate_vitamin_data(data):
    """Valida la estructura de los datos de vitaminas"""
    required_fields = ['vitamina', 'dosis_diaria', 'duracion_semanas', 'globulos_rojos_inicio', 'globulos_rojos_fin']
    
    for field in required_fields:
        if field not in data:
            return False
    
    # Validar tipos de datos
    try:
        float(data['dosis_diaria'])
        int(data['duracion_semanas'])
        float(data['globulos_rojos_inicio'])
        float(data['globulos_rojos_fin'])
    except (ValueError, TypeError):
        return False
    
    return True
