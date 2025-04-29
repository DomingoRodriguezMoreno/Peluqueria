<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php';

if (!esAdministrador($conn)) {
    die("Acceso no autorizado");
}

$id_cliente = $_POST['id_cliente'];
$campos = [
    'nombre' => $_POST['nombre'],
    'apellidos' => $_POST['apellidos'],
    'email' => $_POST['email'],
    'telefono' => $_POST['telefono']
];

// Actualizar contraseña si se proporciona
if (!empty($_POST['nueva_contraseña'])) {
    $hashed_password = password_hash($_POST['nueva_contraseña'], PASSWORD_DEFAULT);
    $campos['contraseña'] = $hashed_password;
}

try {
    $query = "UPDATE clientes SET 
                nombre = :nombre,
                apellidos = :apellidos,
                email = :email,
                telefono = :telefono"
                . (!empty($_POST['nueva_contraseña']) ? ", contraseña = :contraseña" : "") .
              " WHERE id_cliente = :id_cliente";
    
    $stmt = $conn->prepare($query);
    $stmt->execute(array_merge([':id_cliente' => $id_cliente], $campos));
    
    header("Location: /TFGPeluqueria/paginas/clientes.php");
} catch (PDOException $e) {
    die("Error al actualizar: " . $e->getMessage());
}