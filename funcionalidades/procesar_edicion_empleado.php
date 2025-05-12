<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php';

if (!esAdministrador($conn)) {
    die("Acceso no autorizado");
}

$dni_empleado = $_POST['dni'];
$nuevo_telefono = $_POST['telefono'];

// Verificar si el teléfono ya existe en otro cliente
$stmt_telefono = $conn->prepare("
    SELECT dni 
    FROM empleados 
    WHERE telefono = :telefono 
    AND dni != :dni
");
$stmt_telefono->bindParam(':telefono', $nuevo_telefono);
$stmt_telefono->bindParam(':dni', $dni_empleado);
$stmt_telefono->execute();

if ($stmt_telefono->rowCount() > 0) {
    $_SESSION['error_edicion_empleado'] = "El teléfono ya está registrado en otro empleado.";
    
    // Restaurar teléfono original y mantener otros campos
    $_SESSION['form_data_edicion_empleado'] = array_merge(
        $_POST,
        ['telefono' => $_SESSION['original_data_empleado']['telefono']]
    );
    
    header("Location: /TFGPeluqueria/paginas/editar_empleado.php?dni=$dni_empleado");
    exit();
}

// Verificar si el email ya existe en otro cliente
$nuevo_email = $_POST['email'];
$stmt_email = $conn->prepare("
    SELECT dni 
    FROM empleados 
    WHERE email = :email 
    AND dni != :dni
");
$stmt_email->bindParam(':email', $nuevo_email);
$stmt_email->bindParam(':dni', $dni_empleado);
$stmt_email->execute();

if ($stmt_email->rowCount() > 0) {
    $_SESSION['error_edicion_empleado'] = "El email ya está registrado en otro empleado.";
    
    // Restaurar teléfono original y mantener otros campos
    $_SESSION['form_data_edicion_empleado'] = array_merge(
        $_POST,
        ['email' => $_SESSION['original_data_empleado']['email']]
    );
    
    header("Location: /TFGPeluqueria/paginas/editar_empleado.php?id_empleado=$dni_empleado");
    exit();
}

$dni = $_POST['dni'];
$campos = [
    'nombre' => $_POST['nombre'],
    'apellidos' => $_POST['apellidos'],
    'telefono' => $_POST['telefono'],
    'email' => $_POST['email'],
    'id_rol' => $_POST['id_rol'],
    'es_admin' => isset($_POST['es_admin']) ? 1 : 0,
    'activo' => isset($_POST['activo']) ? 1 : 0
];

try {
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

    unset($_SESSION['original_data_empleado']);
    header("Location: /TFGPeluqueria/paginas/empleados.php");
} catch (PDOException $e) {
    die("Error al actualizar: " . $e->getMessage());
}
