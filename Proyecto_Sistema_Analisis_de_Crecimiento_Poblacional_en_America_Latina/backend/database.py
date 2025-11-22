import json
import os

class Database:
    def __init__(self, data_file='data/poblacion_data.json'):
        self.data_file = data_file
        self.data = self._load_data()
    
    def _load_data(self):
        """Carga los datos desde el archivo JSON"""
        try:
            with open(self.data_file, 'r', encoding='utf-8') as file:
                return json.load(file)
        except FileNotFoundError:
            print(f"Error: No se pudo encontrar el archivo {self.data_file}")
            return {"paises": [], "proyecciones": []}
        except json.JSONDecodeError:
            print(f"Error: El archivo {self.data_file} no es un JSON válido")
            return {"paises": [], "proyecciones": []}
    
    def _save_data(self):
        """Guarda los datos en el archivo JSON"""
        try:
            with open(self.data_file, 'w', encoding='utf-8') as file:
                json.dump(self.data, file, indent=2, ensure_ascii=False)
            return True
        except Exception as e:
            print(f"Error al guardar datos: {e}")
            return False
    
    def get_all_countries(self):
        """Obtiene todos los países"""
        return self.data.get('paises', [])
    
    def get_country_by_id(self, country_id):
        """Obtiene un país por ID"""
        countries = self.data.get('paises', [])
        for country in countries:
            if country['id'] == country_id:
                return country
        return None
    
    def get_country_by_name(self, country_name):
        """Obtiene un país por nombre"""
        countries = self.data.get('paises', [])
        for country in countries:
            if country['nombre'].lower() == country_name.lower():
                return country
        return None
    
    def add_country(self, country_data):
        """Agrega un nuevo país"""
        countries = self.data.get('paises', [])
        
        # Generar nuevo ID
        new_id = max([c['id'] for c in countries], default=0) + 1
        country_data['id'] = new_id
        
        countries.append(country_data)
        self.data['paises'] = countries
        return self._save_data()
    
    def update_country(self, country_id, update_data):
        """Actualiza un país existente"""
        countries = self.data.get('paises', [])
        for i, country in enumerate(countries):
            if country['id'] == country_id:
                # Mantener el ID original
                update_data['id'] = country_id
                countries[i] = update_data
                self.data['paises'] = countries
                return self._save_data()
        return False
    
    def delete_country(self, country_id):
        """Elimina un país"""
        countries = self.data.get('paises', [])
        countries = [c for c in countries if c['id'] != country_id]
        self.data['paises'] = countries
        return self._save_data()
    
    def get_projections(self):
        """Obtiene las proyecciones poblacionales"""
        return self.data.get('proyecciones', [])
    
    def get_country_projections(self, country_name):
        """Obtiene proyecciones para un país específico"""
        projections = self.data.get('proyecciones', [])
        for proj in projections:
            if proj['pais'].lower() == country_name.lower():
                return proj
        return None