<?php
// Verificar sesión activa como empleado primero
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'empleado') {
    header('Location: /TFGPeluqueria/index.html');
    exit();
}

// Verificar si es administrador
require_once 'conexion.php'; // Asegura la conexión a la base de datos

if (!isset($_SESSION['dni'])) {
    header('Location: panel_empleado.php');
    exit();
}

try {
    $stmt = $conn->prepare("SELECT id_rol FROM empleados WHERE dni = :dni");
    $stmt->execute([':dni' => $_SESSION['dni']]);
    $rol = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($rol['id_rol'] != 3) { // 3 = Administrador en tu base de datos
        $_SESSION['error_permisos'] = "Acceso restringido: se requieren privilegios de administrador.";
        header('Location: panel_empleado.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("Error al verificar rol: " . $e->getMessage());
    header('Location: panel_empleado.php');
    exit();
}
?>