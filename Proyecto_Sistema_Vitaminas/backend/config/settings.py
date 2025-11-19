import os
from pathlib import Path

# Configuración de paths
BASE_DIR = Path(__file__).resolve().parent.parent.parent
DATA_DIR = BASE_DIR / "data"
ASSETS_DIR = BASE_DIR / "assets"
BACKEND_DIR = BASE_DIR / "backend"

# Configuración de la API
API_HOST = "localhost"
API_PORT = 5000
DEBUG = True

# Configuración de archivos
DATA_FILE = DATA_DIR / "vitamin_data.json"
ML_MODEL_FILE = DATA_DIR / "vitamin_model.joblib"

# Asegurar que los directorios existen
DATA_DIR.mkdir(exist_ok=True)
ASSETS_DIR.mkdir(exist_ok=True)
