from dataclasses import dataclass
from typing import List, Optional
import numpy as np

@dataclass
class VitaminData:
    """Modelo de datos para registros de vitaminas"""
    id: int
    vitamina: str
    dosis_diaria: float
    duracion_semanas: int
    globulos_rojos_inicio: float
    globulos_rojos_fin: float
    edad_paciente: Optional[int] = None
    sexo: Optional[str] = None
    
    def to_dict(self):
        return {
            'id': self.id,
            'vitamina': self.vitamina,
            'dosis_diaria': self.dosis_diaria,
            'duracion_semanas': self.duracion_semanas,
            'globulos_rojos_inicio': self.globulos_rojos_inicio,
            'globulos_rojos_fin': self.globulos_rojos_fin,
            'edad_paciente': self.edad_paciente,
            'sexo': self.sexo
        }
    
    @property
    def incremento_globulos(self):
        return self.globulos_rojos_fin - self.globulos_rojos_inicio
    
    @property
    def eficiencia(self):
        return self.incremento_globulos / (self.dosis_diaria * self.duracion_semanas)

class VitaminDataset:
    """Conjunto de datos de vitaminas"""
    def __init__(self, data_list: List[VitaminData]):
        self.data = data_list
    
    def get_vitaminas_unicas(self):
        return list(set([d.vitamina for d in self.data]))
    
    def filtrar_por_vitamina(self, vitamina: str):
        return VitaminDataset([d for d in self.data if d.vitamina == vitamina])
    
    def to_dataframe_format(self):
        """Convierte a formato compatible con pandas"""
        return [d.to_dict() for d in self.data]
