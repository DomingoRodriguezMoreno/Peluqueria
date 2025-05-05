<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php';

if (!esAdministrador($conn)) {
    die("Acceso no autorizado");
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
    
    header("Location: /TFGPeluqueria/paginas/empleados.php");
} catch (PDOException $e) {
    die("Error al actualizar: " . $e->getMessage());
}