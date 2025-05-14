<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/enviar_correo.php';

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
    $stmt = $conn->prepare("SELECT estado, id_cliente, fecha_cita, hora_inicio FROM citas WHERE id_cita = ?");
    $stmt->execute([$id_cita]);
    $cita_actual = $stmt->fetch(PDO::FETCH_ASSOC);

    // Actualizar solo los datos básicos de la cita
    $query = "UPDATE citas SET 
                id_cliente = :id_cliente,
                fecha_cita = :fecha_cita,
                hora_inicio = :hora_inicio,
                estado = :estado
              WHERE id_cita = :id_cita";
    
    $stmt = $conn->prepare($query);
    $stmt->execute(array_merge([':id_cita' => $id_cita], $campos));

    if ($cita_actual['estado'] !== 'cancelada' && $campos['estado'] === 'cancelada') {
        // Obtener email del cliente
        $stmt = $conn->prepare("SELECT email FROM clientes WHERE id_cliente = ?");
        $stmt->execute([$cita_actual['id_cliente']]);
        $email_cliente = $stmt->fetchColumn();
        
        if ($email_cliente) {
            enviarCorreoCancelacion(
                $email_cliente,
                $cita_actual['fecha_cita'],
                $cita_actual['hora_inicio']
            );
        }
    }

    header("Location: /TFGPeluqueria/paginas/citas.php");
} catch (PDOException $e) {
    die("Error al actualizar: " . $e->getMessage());
}
