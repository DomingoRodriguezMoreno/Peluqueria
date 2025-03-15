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
