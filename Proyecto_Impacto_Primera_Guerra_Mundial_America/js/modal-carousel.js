// Sistema básico de modal (simplificado para esta versión)
class ModalSystem {
    constructor() {
        this.modals = new Map();
    }

    registerModal(id, content) {
        this.modals.set(id, content);
    }

    openModal(id) {
        console.log(`Abriendo modal: ${id}`);
        // Implementación básica - puedes expandir esto
    }
}

const modalSystem = new ModalSystem();

// Función para crear modales de países
function createCountryModal(countryData) {
    return `
        <div class="modal-content">
            <button class="modal-close">&times;</button>
            <div class="modal-header">
                <h3>${countryData.name}</h3>
                <span class="region">${countryData.region}</span>
            </div>
            <div class="modal-body">
                <p>Análisis detallado de ${countryData.name}</p>
                <!-- Contenido del modal -->
            </div>
        </div>
    `;
}
