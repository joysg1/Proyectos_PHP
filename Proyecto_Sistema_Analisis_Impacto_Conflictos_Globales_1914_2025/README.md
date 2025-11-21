# Sistema de Análisis del Impacto Económico de Conflictos Bélicos

## Descripción General

Sistema web integral para el análisis y visualización del impacto económico de conflictos bélicos en el período 1914-2025. Combina tecnologías modernas de backend (Python/Flask) y frontend (PHP) con algoritmos de machine learning para proporcionar insights profundos sobre las relaciones entre conflictos armados y variables económicas.

## Características Principales

### 🎯 Funcionalidades
- **Análisis Visual**: Gráficos interactivos (área, radar, barras apiladas, pastel)
- **Machine Learning**: Clustering de conflictos y análisis predictivo
- **Base de Datos Robustas**: Dataset histórico comprehensivo 1914-2025
- **Interfaz Moderna**: Diseño oscuro responsive y profesional
- **API RESTful**: Arquitectura separada backend-frontend

### 📊 Tipos de Gráficos
1. **Área sobre Curva**: Evolución temporal de GDP e inflación
2. **Radar**: Comparación multidimensional regional
3. **Barras Apiladas**: Indicadores económicos por década
4. **Pastel**: Distribución de conflictos por región

### 🤖 Algoritmos ML
- **Random Forest**: Predicción de impacto económico
- **K-means Clustering**: Agrupamiento de conflictos por patrones
- **Análisis de Tendencia**: Identificación de patrones temporales

## Requisitos del Sistema

### Backend (Python)
- Python 3.8+
- Flask 2.3.3
- Pandas, NumPy, Scikit-learn
- Seaborn, Matplotlib
- Flask-CORS

### Frontend (PHP)
- PHP 7.4+
- Servidor web (Apache/Nginx)
- JavaScript habilitado

++ Como ejecutar el sistema
--- Backend: cd backend ---> python app.py
--- Frontend: php -S localhost:8000 
--- Abrir navegador de preferencia: http://localhost:8000/index.php

