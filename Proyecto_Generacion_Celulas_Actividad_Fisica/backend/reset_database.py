#!/usr/bin/env python3
"""
Script para resetear la base de datos
"""

import os
import sys

# Agregar el directorio actual al path
sys.path.append(os.path.dirname(__file__))

from data_processor import DataProcessor

def reset_database():
    """Regenerar completamente la base de datos"""
    data_file = '../data/fitness_data.json'
    
    # Eliminar archivo existente si existe
    if os.path.exists(data_file):
        os.remove(data_file)
        print("🗑️  Archivo de datos existente eliminado")
    
    try:
        # Crear nuevo procesador de datos (generará datos automáticamente)
        processor = DataProcessor(data_file)
        
        print(f"✅ Base de datos regenerada con {len(processor.data)} registros")
        print("📍 Ubicación:", os.path.abspath(data_file))
        
        # Mostrar algunos datos de ejemplo
        if len(processor.data) > 0:
            print("\n📊 Primeros 3 registros de ejemplo:")
            for i in range(min(3, len(processor.data))):
                record = processor.data[i]
                print(f"   {i+1}. {record['activity_type']} - {record['cells_produced']} células")
                
    except Exception as e:
        print(f"❌ Error regenerando base de datos: {e}")
        sys.exit(1)

if __name__ == '__main__':
    reset_database()