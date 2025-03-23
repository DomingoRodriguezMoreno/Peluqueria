<?php
// Incluir la conexión a la base de datos
include 'conexion.php';

// Obtener datos del formulario
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$contraseña = $_POST['contraseña'];

// Validar que los campos no estén vacíos
if (empty($nombre) || empty($apellidos) || empty($telefono) || empty($email) || empty($contraseña)) {
    die("Todos los campos son obligatorios.");
}

// Validar que el teléfono y el email no estén registrados previamente
$query_telefono = "SELECT id_cliente FROM clientes WHERE telefono = :telefono";
$stmt_telefono = $conn->prepare($query_telefono);
$stmt_telefono->bindParam(':telefono', $telefono);
$stmt_telefono->execute();

if ($stmt_telefono->rowCount() > 0) {
    die("El teléfono ya está registrado.");
}

$query_email = "SELECT id_cliente FROM clientes WHERE email = :email";
$stmt_email = $conn->prepare($query_email);
$stmt_email->bindParam(':email', $email);
$stmt_email->execute();

if ($stmt_email->rowCount() > 0) {
    die("El email ya está registrado.");
}

// Encriptar la contraseña
$hash = password_hash($contraseña, PASSWORD_DEFAULT);

/* Depuración: Verificar los valores
echo "Nombre: $nombre<br>";
echo "Apellidos: $apellidos<br>";
echo "Teléfono: $telefono<br>";
echo "Email: $email<br>";
echo "Hash: $hash<br>";
*/
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
    echo "Registro exitoso. ¡Bienvenido, $nombre!";
    // Redirigir al login después de 3 segundos
    header("Refresh: 3; url=/TFGPeluqueria/index.php");
} else {
    echo "Error al registrar el cliente.";
}
?>