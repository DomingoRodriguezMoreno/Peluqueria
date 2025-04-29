<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php';

if (!esAdministrador($conn)) {
    die("Acceso no autorizado");
}

$id_cita = $_POST['id_cita'];
$campos = [
    'id_cliente' => $_POST['id_cliente'],
    'fecha_cita' => $_POST['fecha_cita'],
    'hora_inicio' => $_POST['hora_inicio'] . ':00', // Formato HH:MM:SS
    'estado' => $_POST['estado']
];

try {
    // Actualizar solo los datos básicos de la cita
    $query = "UPDATE citas SET 
                id_cliente = :id_cliente,
                fecha_cita = :fecha_cita,
                hora_inicio = :hora_inicio,
                estado = :estado
              WHERE id_cita = :id_cita";
    
    $stmt = $conn->prepare($query);
    $stmt->execute(array_merge([':id_cita' => $id_cita], $campos));

    header("Location: /TFGPeluqueria/paginas/citas.php");
} catch (PDOException $e) {
    die("Error al actualizar: " . $e->getMessage());
}