        </div> <!-- Cierre del container principal -->
        
        <!-- Footer Moderno -->
        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div style="margin-bottom: 1.5rem;">
                        <div class="logo" style="font-size: 1.5rem; justify-content: center;">
                            <i class="fas fa-chart-line"></i>
                            VitaminaAnalytics
                        </div>
                        <p style="color: var(--text-muted); margin-top: 0.5rem;">
                            Sistema avanzado de análisis con Machine Learning
                        </p>
                    </div>
                    
                    <div class="footer-links">
                        <a href="#"><i class="fas fa-shield-alt"></i> Privacidad</a>
                        <a href="#"><i class="fas fa-file-contract"></i> Términos</a>
                        <a href="#"><i class="fas fa-envelope"></i> Contacto</a>
                        <a href="#"><i class="fas fa-code-branch"></i> v1.0.0</a>
                    </div>
                    
                    <p style="color: var(--text-muted); margin-top: 2rem; font-size: 0.9rem;">
                        &copy; 2024 VitaminaAnalytics. Desarrollado con Python, Flask y Machine Learning.
                    </p>
                </div>
            </div>
        </footer>

        <!-- Scripts -->
        <script src="js/carousel.js"></script>
        <script src="js/charts.js"></script>
        <script src="js/modern-ui.js"></script>
        
        <script>
            // Funciones globales mejoradas
            function openModal(content) {
                const modal = document.getElementById('chartModal');
                const modalContent = document.getElementById('modalContent');
                
                if (content.startsWith('data:image')) {
                    modalContent.innerHTML = `
                        <div style="text-align: center;">
                            <img src="${content}" style="max-width: 100%; height: auto; border-radius: 12px; box-shadow: var(--shadow-lg);">
                        </div>
                    `;
                } else {
                    modalContent.innerHTML = content;
                }
                
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                const modal = document.getElementById('chartModal');
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }

            function showSystemInfo() {
                fetch('http://localhost:5000/api/system/status')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const info = data.system_status;
                            const modalContent = `
                                <h2 style="margin-bottom: 1.5rem; color: var(--primary);">
                                    <i class="fas fa-info-circle"></i>
                                    Estado del Sistema
                                </h2>
                                
                                <div style="display: grid; gap: 1rem;">
                                    <div style="display: flex; justify-content: space-between; padding: 1rem; background: var(--bg-secondary); border-radius: 8px;">
                                        <span>Datos:</span>
                                        <span class="badge ${info.data.estado === 'ok' ? 'badge-success' : 'badge-warning'}">
                                            ${info.data.registros_validos} registros
                                        </span>
                                    </div>
                                    
                                    <div style="display: flex; justify-content: space-between; padding: 1rem; background: var(--bg-secondary); border-radius: 8px;">
                                        <span>Modelo ML:</span>
                                        <span class="badge ${info.model.entrenado ? 'badge-success' : 'badge-warning'}">
                                            ${info.model.estado}
                                        </span>
                                    </div>
                                    
                                    <div style="display: flex; justify-content: space-between; padding: 1rem; background: var(--bg-secondary); border-radius: 8px;">
                                        <span>Gráficos:</span>
                                        <span class="badge ${info.charts.estado === 'ok' ? 'badge-success' : 'badge-warning'}">
                                            ${info.charts.estado}
                                        </span>
                                    </div>
                                    
                                    <div style="display: flex; justify-content: space-between; padding: 1rem; background: var(--bg-secondary); border-radius: 8px;">
                                        <span>API:</span>
                                        <span class="badge badge-success">${info.api.estado}</span>
                                    </div>
                                </div>
                                
                                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border);">
                                    <h4 style="margin-bottom: 1rem;">Endpoints Disponibles:</h4>
                                    <div style="display: grid; gap: 0.5rem; font-family: monospace; font-size: 0.9rem;">
                                        ${info.api.endpoints_disponibles.map(endpoint => 
                                            `<div style="padding: 0.5rem; background: var(--bg-primary); border-radius: 4px;">${endpoint}</div>`
                                        ).join('')}
                                    </div>
                                </div>
                            `;
                            openModal(modalContent);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading system info:', error);
                    });
            }

            // Cerrar modal con ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });

            // Cerrar modal al hacer clic fuera
            const modal = document.getElementById('chartModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeModal();
                    }
                });
            }
        </script>
    </body>
</html>