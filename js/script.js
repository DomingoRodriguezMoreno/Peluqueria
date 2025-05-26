// Función para mostrar el modal
function mostrarLogin() {
    document.getElementById('loginModal').style.display = 'block';
}

// Función para cerrar el modal
function cerrarLogin() {
    document.getElementById('loginModal').style.display = 'none';
}

// Cerrar el modal si se hace clic fuera de él
window.onclick = function(event) {
    const modal = document.getElementById('loginModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// ======================= FUNCIONALIDAD CITAS ======================= 
document.addEventListener('DOMContentLoaded', function() {
    // Solo ejecutar en la página de citas
    if (document.querySelector('.contenedor-principal.citas')) {
        const checkboxes = document.querySelectorAll('.servicio-checkbox');
        const fechaInput = document.getElementById('fecha-cita');
        
        // Actualizar resumen al cambiar checkboxes
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', actualizarResumen);
        });

        // Establecer fecha mínima (mañana)
        const hoy = new Date();
        fechaInput.min = new Date(hoy.setDate(hoy.getDate() + 1)).toISOString().split('T')[0];
    }

    crearBuscadorGeneral();

});

// Función de búsqueda genérica
function crearBuscadorGeneral() {
    document.querySelectorAll('.buscador-general').forEach(buscador => {
        const tablaSelector = buscador.dataset.tabla; // Obtener selector de data-attribute
        
        buscador.addEventListener('input', function() {
            const filtro = this.value.toLowerCase();
            const palabrasFiltro = filtro.split(/\s+/); // Divide por espacios
            const filas = document.querySelectorAll(`${tablaSelector} tbody tr`);
            
            filas.forEach(fila => {
                const textoFila = fila.textContent.toLowerCase();
                const contieneTodas = palabrasFiltro.every(palabra => textoFila.includes(palabra)); // Verifica cada palabra
                fila.style.display = contieneTodas ? '' : 'none';
            });
        });
    });
}

function actualizarResumen() {
    let totalTiempo = 0;
    let totalPrecio = 0;
    
    document.querySelectorAll('.servicio-checkbox:checked').forEach(servicio => {
        totalTiempo += parseInt(servicio.dataset.duracion);
        totalPrecio += parseFloat(servicio.dataset.precio);
    });
    
    document.getElementById('tiempo-total').textContent = totalTiempo;
    document.getElementById('coste-total').textContent = totalPrecio.toFixed(2);
    document.getElementById('btn-continuar').disabled = totalTiempo === 0;
}

function mostrarCalendario() {
    const modal = document.getElementById('citaModal');
    const serviciosSeleccionados = document.getElementById('servicios-seleccionados');
    
    modal.style.display = 'block';
    const servicios = Array.from(document.querySelectorAll('.servicio-checkbox:checked'))
                        .map(s => `<input type="hidden" name="servicios[]" value="${s.value}">`);
    
    serviciosSeleccionados.innerHTML = servicios.join('');
}

// Añadir funciones para manejar el modal
function cerrarCitaModal() {
    document.getElementById('citaModal').style.display = 'none';
}

// Actualizar el window.onclick existente
window.onclick = function(event) {
    const loginModal = document.getElementById('loginModal');
    const citaModal = document.getElementById('citaModal');
    
    if (event.target == loginModal) {
        loginModal.style.display = 'none';
    }
    if (event.target == citaModal) {
        citaModal.style.display = 'none';
    }
}
