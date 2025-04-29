<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php';

if (!esAdministrador($conn)) {
    die("Acceso no autorizado");
}

$id_servicio = $_POST['id_servicio'];
$campos = [
    'nombre_servicio' => $_POST['nombre_servicio'],
    'descripcion' => $_POST['descripcion'],
    'duracion' => $_POST['duracion'],
    'precio' => $_POST['precio'],
    'activo' => isset($_POST['activo']) ? 1 : 0 // Nuevo campo
];

try {
    $query = "UPDATE servicios SET 
        nombre_servicio = :nombre_servicio,
        descripcion = :descripcion,
        duracion = :duracion,
        precio = :precio,
        activo = :activo
        WHERE id_servicio = :id_servicio";
    
    $stmt = $conn->prepare($query);
    $stmt->execute(array_merge([':id_servicio' => $id_servicio], $campos));
    
    header("Location: /TFGPeluqueria/paginas/servicios.php");
} catch (PDOException $e) {
    die("Error al actualizar: " . $e->getMessage());
}