#!/usr/bin/env python3
import json
import os

def create_data_file():
    data = {
        "years": [1990, 1995, 2000, 2005, 2010, 2015, 2020, 2022],
        "gases": {
            "CO2": [22000, 24500, 28000, 32000, 36000, 40000, 45000, 48000],
            "CH4": [320, 350, 380, 410, 440, 470, 500, 520],
            "N2O": [270, 290, 310, 330, 350, 370, 390, 400],
            "HFC": [50, 80, 120, 180, 250, 350, 480, 550],
            "PFC": [40, 45, 50, 55, 60, 65, 70, 72],
            "SF6": [35, 42, 50, 58, 65, 72, 80, 85]
        },
        "sectors": {
            "Energia": [15000, 17000, 19500, 22500, 25500, 28500, 32000, 34000],
            "Industria": [4000, 4500, 5200, 5900, 6500, 7200, 8000, 8500],
            "Agricultura": [2500, 2700, 3000, 3300, 3600, 3900, 4200, 4400],
            "Residuos": [800, 900, 1100, 1300, 1500, 1700, 1900, 2000],
            "Uso_suelo": [-1000, -800, -600, -400, -200, 0, 200, 300]
        },
        "regions": {
            "America_Norte": [6000, 6500, 7000, 7500, 8000, 8500, 9000, 9200],
            "Europa": [5500, 5800, 6000, 6200, 6300, 6400, 6500, 6550],
            "Asia": [4000, 5000, 6500, 8500, 11000, 14000, 18000, 20000],
            "America_Sur": [1500, 1600, 1800, 2000, 2200, 2400, 2600, 2700],
            "Africa": [1000, 1100, 1300, 1500, 1700, 1900, 2100, 2200],
            "Oceania": [500, 550, 600, 650, 700, 750, 800, 820]
        }
    }
    
    filename = 'greenhouse_gas_data.json'
    
    try:
        with open(filename, 'w', encoding='utf-8') as f:
            json.dump(data, f, indent=4, ensure_ascii=False)
        print(f"✅ Archivo {filename} creado exitosamente")
        print(f"📁 Ubicación: {os.path.abspath(filename)}")
        
        # Verificar que se puede leer
        with open(filename, 'r', encoding='utf-8') as f:
            loaded_data = json.load(f)
        print("✅ Archivo verificado - se puede leer correctamente")
        
    except Exception as e:
        print(f"❌ Error creando archivo: {e}")

if __name__ == '__main__':
    create_data_file()
