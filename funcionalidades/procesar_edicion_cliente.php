<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php';

if (!esAdministrador($conn)) {
    die("Acceso no autorizado");
}

$id_cliente = $_POST['id_cliente'];
$nuevo_telefono = $_POST['telefono'];

// Verificar si el teléfono ya existe en otro cliente
$stmt_telefono = $conn->prepare("
    SELECT id_cliente 
    FROM clientes 
    WHERE telefono = :telefono 
    AND id_cliente != :id_cliente
");
$stmt_telefono->bindParam(':telefono', $nuevo_telefono);
$stmt_telefono->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
$stmt_telefono->execute();

if ($stmt_telefono->rowCount() > 0) {
    $_SESSION['error_edicion_cliente'] = "El teléfono ya está registrado en otro cliente.";
    
    // Restaurar teléfono original y mantener otros campos
    $_SESSION['form_data_edicion_cliente'] = array_merge(
        $_POST,
        ['telefono' => $_SESSION['original_data_cliente']['telefono']]
    );
    
    header("Location: /TFGPeluqueria/paginas/editar_cliente.php?id_cliente=$id_cliente");
    exit();
}

$nuevo_email = $_POST['email'];

// Verificar si el teléfono ya existe en otro cliente
$stmt_email = $conn->prepare("
    SELECT id_cliente 
    FROM clientes 
    WHERE email = :email 
    AND id_cliente != :id_cliente
");
$stmt_email->bindParam(':email', $nuevo_email);
$stmt_email->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
$stmt_email->execute();

if ($stmt_email->rowCount() > 0) {
    $_SESSION['error_edicion_cliente'] = "El email ya está registrado en otro cliente.";
    
    // Restaurar teléfono original y mantener otros campos
    $_SESSION['form_data_edicion_cliente'] = array_merge(
        $_POST,
        ['email' => $_SESSION['original_data_cliente']['email']]
    );
    
    header("Location: /TFGPeluqueria/paginas/editar_cliente.php?id_cliente=$id_cliente");
    exit();
}

// Actualizar campos
$campos = [
    'nombre' => $_POST['nombre'],
    'apellidos' => $_POST['apellidos'],
    'email' => $_POST['email'],
    'telefono' => $nuevo_telefono
];

try {
    $query = "UPDATE clientes SET 
                nombre = :nombre,
                apellidos = :apellidos,
                email = :email,
                telefono = :telefono"
                . (!empty($_POST['nueva_contraseña']) ? ", contraseña = :contraseña" : "") . "
              WHERE id_cliente = :id_cliente";
    
    $stmt = $conn->prepare($query);
    $stmt->execute(array_merge([':id_cliente' => $id_cliente], $campos));
    
    // Limpiar sesión después de éxito
    unset($_SESSION['original_data_cliente']);
    header("Location: /TFGPeluqueria/paginas/clientes.php");

} catch (PDOException $e) {
    $_SESSION['error_edicion_cliente'] = "Error al actualizar: " . $e->getMessage();
    header("Location: /TFGPeluqueria/paginas/editar_cliente.php?id_cliente=$id_cliente");
}
