// frontend/js/api.js - Funciones auxiliares para API

class APIHelper {
    constructor(baseURL = 'http://localhost:5000/api') {
        this.baseURL = baseURL;
        this.cache = new Map();
    }

    async get(endpoint, useCache = true) {
        const cacheKey = `GET_${endpoint}`;
        
        if (useCache && this.cache.has(cacheKey)) {
            return this.cache.get(cacheKey);
        }

        try {
            const response = await fetch(`${this.baseURL}/${endpoint}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (useCache) {
                this.cache.set(cacheKey, data);
            }
            
            return data;
        } catch (error) {
            console.error(`API Error (GET ${endpoint}):`, error);
            throw error;
        }
    }

    async post(endpoint, data) {
        try {
            const response = await fetch(`${this.baseURL}/${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error(`API Error (POST ${endpoint}):`, error);
            throw error;
        }
    }

    async put(endpoint, data) {
        try {
            const response = await fetch(`${this.baseURL}/${endpoint}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error(`API Error (PUT ${endpoint}):`, error);
            throw error;
        }
    }

    async delete(endpoint) {
        try {
            const response = await fetch(`${this.baseURL}/${endpoint}`, {
                method: 'DELETE'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error(`API Error (DELETE ${endpoint}):`, error);
            throw error;
        }
    }

    clearCache() {
        this.cache.clear();
    }

    // Métodos específicos para el proyecto
    async getCountries() {
        return await this.get('paises');
    }

    async getCountry(id) {
        return await this.get(`paises/${id}`);
    }

    async getRegionalAnalysis() {
        return await this.get('analisis/regional');
    }

    async getPredictions(countryName) {
        return await this.get(`prediccion/${encodeURIComponent(countryName)}`, false);
    }

    async getClusters(nClusters = 3) {
        return await this.get(`clusters?n_clusters=${nClusters}`, false);
    }

    async getComparison(country1, country2) {
        return await this.get(`comparacion?pais1=${encodeURIComponent(country1)}&pais2=${encodeURIComponent(country2)}`, false);
    }

    // Métodos para gráficos
    async getAreaChart() {
        return await this.get('graficos/area', false);
    }

    async getRadarChart() {
        return await this.get('graficos/radar', false);
    }

    async getBarChart() {
        return await this.get('graficos/barras-apiladas', false);
    }

    async getPieChart() {
        return await this.get('graficos/pastel', false);
    }

    async getComparisonChart(country1, country2) {
        return await this.get(`graficos/comparacion-radar?pais1=${encodeURIComponent(country1)}&pais2=${encodeURIComponent(country2)}`, false);
    }
}

// Instancia global del helper de API
const api = new APIHelper();

// Funciones de utilidad para formatos
function formatNumber(num) {
    return new Intl.NumberFormat('es-ES').format(num);
}

function formatPercentage(num, decimals = 2) {
    return new Intl.NumberFormat('es-ES', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(num) + '%';
}

function formatLargeNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return formatNumber(num);
}

// Función para verificar el estado del servidor
async function checkServerStatus() {
    try {
        const response = await fetch('http://localhost:5000/');
        return response.ok;
    } catch (error) {
        return false;
    }
}

// Función para mostrar estado de conexión
async function showConnectionStatus() {
    const isOnline = await checkServerStatus();
    const statusElement = document.getElementById('connectionStatus') || createStatusElement();
    
    if (isOnline) {
        statusElement.textContent = '✅ Conectado al servidor';
        statusElement.style.color = '#27ae60';
    } else {
        statusElement.textContent = '❌ Servidor no disponible';
        statusElement.style.color = '#e74c3c';
    }
}

function createStatusElement() {
    const statusElement = document.createElement('div');
    statusElement.id = 'connectionStatus';
    statusElement.style.cssText = `
        position: fixed;
        bottom: 10px;
        right: 10px;
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 12px;
        z-index: 1000;
    `;
    document.body.appendChild(statusElement);
    return statusElement;
}

// Verificar conexión periódicamente
setInterval(showConnectionStatus, 30000);

// Verificar al cargar la página
document.addEventListener('DOMContentLoaded', showConnectionStatus);