let duracionTotal = 0;
let fechaSeleccionada = null;
let mesVisible = (new Date()).getMonth(); // 0 = enero
let anioVisible = (new Date()).getFullYear();


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
document.addEventListener('DOMContentLoaded', function () {
    if (document.querySelector('.contenedor-principal.citas')) {
        const checkboxes = document.querySelectorAll('.servicio-checkbox');
        const fechaInput = document.getElementById('fecha-cita');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                actualizarResumen();
                duracionTotal = Array.from(document.querySelectorAll('.servicio-checkbox:checked'))
                    .reduce((sum, el) => sum + parseInt(el.dataset.duracion), 0);
                actualizarCalendario(); // Se actualiza el calendario al seleccionar servicios
            });
        });

        // Establecer fecha mínima (mañana)
        const hoy = new Date();
        fechaInput.min = new Date(hoy.setDate(hoy.getDate() + 1)).toISOString().split('T')[0];
    }
});

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
    actualizarCalendario(); // Generar calendario del mes actual

    const servicios = Array.from(document.querySelectorAll('.servicio-checkbox:checked'))
        .map(s => `<input type="hidden" name="servicios[]" value="${s.value}">`);
    serviciosSeleccionados.innerHTML = servicios.join('');
}

// ================ NUEVAS FUNCIONALIDADES CALENDARIO ================
function actualizarCalendario() {
    if (duracionTotal === 0) return;

    fetch(`/TFGPeluqueria/funcionalidades/obtener_disponibilidad.php?duracion=${duracionTotal}&mes=${mesVisible + 1}&anio=${anioVisible}`)
        .then(response => response.json())
        .then(dias => {
            const mesActual = new Date(anioVisible, mesVisible);
            const nombreMes = mesActual.toLocaleString('es-ES', { month: 'long' });

            let html = `
                <div class="navegacion-mes">
                    <button onclick="cambiarMes(-1)">←</button>
                    <h4>${nombreMes} de ${anioVisible}</h4>
                    <button onclick="cambiarMes(1)">→</button>
                </div>
                <table class="calendario"><tr>
            `;

            ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'].forEach(dia => {
                html += `<th>${dia}</th>`;
            });

            html += '</tr><tr>';

            Object.entries(dias).forEach(([fecha, disponible]) => {
                const fechaObj = new Date(fecha);
                if (fechaObj.getDay() === 1 && html.endsWith('</tr>')) {
                    html += '<tr>';
                }

                const clase = disponible ? 'dia-disponible' : 'dia-no-disponible';
                html += `<td class="${clase}" data-fecha="${fecha}">${fechaObj.getDate()}</td>`;

                if (fechaObj.getDay() === 0) {
                    html += '</tr>';
                }
            });

            html += '</table>';
            document.getElementById('calendario').innerHTML = html;

            document.querySelectorAll('.dia-disponible').forEach(dia => {
                dia.addEventListener('click', () => {
                    fechaSeleccionada = dia.dataset.fecha;
                    document.getElementById('fecha-seleccionada').value = fechaSeleccionada;
                    cargarHorarios(fechaSeleccionada);
                });
            });
        });
}

function cambiarMes(delta) {
    mesVisible += delta;

    if (mesVisible < 0) {
        mesVisible = 11;
        anioVisible--;
    } else if (mesVisible > 11) {
        mesVisible = 0;
        anioVisible++;
    }

    actualizarCalendario();
}

function cargarHorarios(fecha) {
    document.getElementById('hora-seleccionada').value = ''; // Reset hora seleccionada
    fetch(`/TFGPeluqueria/funcionalidades/obtener_disponibilidad.php?duracion=${duracionTotal}&fecha=${fecha}`)
        .then(response => response.json())
        .then(({horarios}) => {
            let html = '<div class="horario-container">';
            horarios.forEach(tramo => {
                html += `
                    <div class="tramo-horario tramo-libre" 
                         data-hora="${tramo.inicio}"
                         onclick="seleccionarHorario(this, '${tramo.inicio}')">
                        ${tramo.inicio} - ${tramo.fin}
                    </div>
                `;
            });
            html += '</div>';
            
            document.getElementById('horarios').innerHTML = html;
            document.getElementById('horarios-container').style.display = 'block';
        });
}

function seleccionarHorario(elemento, hora) {
    document.querySelectorAll('.tramo-seleccionado').forEach(el => {
        el.classList.remove('tramo-seleccionado');
    });

    elemento.classList.add('tramo-seleccionado');
    document.getElementById('hora-seleccionada').value = hora;
    document.getElementById('btn-continuar').disabled = false;

    document.querySelector('#form-cita button').scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });
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
