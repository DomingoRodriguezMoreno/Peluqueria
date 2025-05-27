<?php
session_start();
// Incluir la conexión a la base de datos
include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';

$_SESSION['form_data_cliente'] = $_POST; // Guardar datos en sesión
unset($_SESSION['form_data_cliente']['contraseña']); // No guardar la contraseña en la sesión

// Obtener datos del formulario
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$contraseña = $_POST['contraseña'];

// Validar que los campos no estén vacíos
if (empty($nombre) || empty($apellidos) || empty($telefono) || empty($email) || empty($contraseña)) {
    $_SESSION['error_cliente'] = "Todos los campos son obligatorios.";
    header('Location: /TFGPeluqueria/paginas/registro_cliente.php');
    exit();}

// Validar que el teléfono y el email no estén registrados previamente
$query_telefono = "SELECT id_cliente FROM clientes WHERE telefono = :telefono";
$stmt_telefono = $conn->prepare($query_telefono);
$stmt_telefono->bindParam(':telefono', $telefono);
$stmt_telefono->execute();

if ($stmt_telefono->rowCount() > 0) {
    $_SESSION['error_cliente'] = "El teléfono ya está registrado.";
    header('Location: /TFGPeluqueria/paginas/registro_cliente.php');
    exit();
}

$query_email = "SELECT id_cliente FROM clientes WHERE email = :email";
$stmt_email = $conn->prepare($query_email);
$stmt_email->bindParam(':email', $email);
$stmt_email->execute();

if ($stmt_email->rowCount() > 0) {
    $_SESSION['error_cliente'] = "El email ya está registrado.";
    header('Location: /TFGPeluqueria/paginas/registro_cliente.php');
    exit();
}

// Encriptar la contraseña
$hash = password_hash($contraseña, PASSWORD_DEFAULT);

// Insertar el nuevo cliente en la base de datos
$query_insert = "INSERT INTO clientes (nombre, apellidos, telefono, email, contraseña) 
                 VALUES (:nombre, :apellidos, :telefono, :email, :contrasena)";
$stmt_insert = $conn->prepare($query_insert);
$stmt_insert->bindParam(':nombre', $nombre);
$stmt_insert->bindParam(':apellidos', $apellidos);
$stmt_insert->bindParam(':telefono', $telefono);
$stmt_insert->bindParam(':email', $email);
$stmt_insert->bindParam(':contrasena', $hash);

if ($stmt_insert->execute()) {
    unset($_SESSION['form_data_cliente']); // Limpiar datos de sesión
    $_SESSION['exito_registro'] = "Registro exitoso.";
    header("Location: /TFGPeluqueria/index.php");
    exit();
} else {
    echo "Error al registrar el cliente.";
}
?>
