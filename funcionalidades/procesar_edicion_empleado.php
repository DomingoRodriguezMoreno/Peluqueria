<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php';

if (!esAdministrador($conn)) {
    die("Acceso no autorizado");
}
// Guardar datos del formulario en la sesión para repoblar
$_SESSION['form_data'] = $_POST;


$original_dni = $_POST['original_dni'];
$new_dni = $_POST['dni'];
$campos = [
    ':nombre' => $_POST['nombre'],
    ':apellidos' => $_POST['apellidos'],
    ':telefono' => $_POST['telefono'],
    ':email' => $_POST['email'],
    ':id_rol' => $_POST['id_rol'],
    ':es_admin' => isset($_POST['es_admin']) ? 1 : 0,
    ':activo' => isset($_POST['activo']) ? 1 : 0,
    ':dni' => $new_dni // Nuevo DNI
];

$_SESSION['original_data'] = $current_empleado; 

// Validar formato de DNI (ejemplo básico)
if (!preg_match('/^[0-9]{8}[A-Za-z]$/', $new_dni)) {
    $_SESSION['error_edicion_empleado'] = "Error en el formato del DNI.";
    header('Location: /TFGPeluqueria/paginas/editar_empleado.php'); // Redirigir al formulario
    exit();
}

try {
    // Verificar duplicados de DNI
    if ($new_dni !== $original_dni) {
        $stmt = $conn->prepare("SELECT dni FROM empleados WHERE dni = ?");
        $stmt->execute([$new_dni]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['error_edicion_empleado'] = "El dni ya está registrado en otro empleado.";
            header('Location: /TFGPeluqueria/paginas/editar_empleado.php'); // Redirigir al formulario
            exit();
        }
    }

    // Verificar duplicados de teléfono (si cambió)
    if ($_POST['telefono'] !== $current_empleado['telefono']) {
        $stmt = $conn->prepare("SELECT telefono FROM empleados WHERE telefono = ? AND dni != ?");
        $stmt->execute([$_POST['telefono'], $original_dni]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['error_edicion_empleado'] = "El telefono ya está registrado en otro empleado.";
            header('Location: /TFGPeluqueria/paginas/editar_empleado.php'); // Redirigir al formulario
            exit();
        }
    }

    // Verificar duplicados de email (si cambió)
    if ($_POST['email'] !== $current_empleado['email']) {
        $stmt = $conn->prepare("SELECT email FROM empleados WHERE email = ? AND dni != ?");
        $stmt->execute([$_POST['email'], $original_dni]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['error_edicion_empleado'] = "El email ya está registrado en otro empleado.";
            header('Location: /TFGPeluqueria/paginas/editar_empleado.php'); // Redirigir al formulario
            exit();
        }
    }


    $query = "UPDATE empleados SET 
                nombre = :nombre,
                apellidos = :apellidos,
                telefono = :telefono,
                email = :email,
                id_rol = :id_rol,
                es_admin = :es_admin,
                activo = :activo
              WHERE dni = :dni";
    
    $stmt = $conn->prepare($query);
    $stmt->execute(array_merge([':dni' => $dni], $campos));

    unset($_SESSION['form_data']); // Limpiar datos de sesión    
    header("Location: /TFGPeluqueria/paginas/empleados.php");
} catch (PDOException $e) {
    die("Error al actualizar: " . $e->getMessage());
}
