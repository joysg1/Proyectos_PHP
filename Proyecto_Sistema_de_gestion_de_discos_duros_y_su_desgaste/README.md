# Sistema de Predicción de Desgaste de Discos Duros

## Descripción General

Sistema inteligente para monitoreo y predicción del desgaste de discos duros utilizando Machine Learning. Combina PHP como frontend y Python como backend para análisis predictivo y visualización de datos.

## Características Principales

### 🎯 Funcionalidades Específicas
- **Predicción de Desgaste**: ML para predecir porcentaje de desgaste basado en tipo y uso
- **Análisis por Tecnología**: SSD, HDD y NVMe con métricas específicas
- **Vida Útil Estimada**: Cálculo de meses restantes antes de fallo
- **Alertas Tempranas**: Detección proactiva de discos en riesgo
- **Visualizaciones Avanzadas**: Gráficos especializados para análisis de desgaste

### 🛠 Tecnologías Implementadas

#### Backend (Python)
- **Flask**: API RESTful para predicciones
- **Scikit-learn**: Random Forest, Gradient Boosting, Regresión Lineal
- **Seaborn/Matplotlib**: Gráficos profesionales de desgaste
- **Pandas**: Procesamiento de datos de discos

#### Frontend (PHP/JavaScript)
- **PHP**: Interfaz de usuario especializada
- **JavaScript**: Interactividad y llamadas a la API de predicción
- **CSS3**: Diseño oscuro moderno con variables específicas

## Estructura del Proyecto
- Para ejecutar el api de python ir al directorio backend y ejecutar:
--- python api.py

- Para ejecutar el servidor php ir al directorio frontend y ejecutar:
--- php -S localhost:8000
--- http://localhost:8000
