<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/enviar_correo.php';

// Verificar autenticación y tipo de usuario
if (!isset($_SESSION['tipo_usuario']) || 
    ($_SESSION['tipo_usuario'] !== 'cliente' && $_SESSION['tipo_usuario'] !== 'empleado')) {
    header('Location: /TFGPeluqueria/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cita'])) {
    try {
        $conn->beginTransaction();

        // Obtener datos de la cita
        $stmt = $conn->prepare("
            SELECT id_cliente, fecha_cita, hora_inicio, estado 
            FROM citas 
            WHERE id_cita = :id_cita
        ");
        $stmt->execute([':id_cita' => $_POST['id_cita']]);
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cita) {
            throw new Exception("Cita no encontrada.");
        }

        // Validaciones para clientes
        if ($_SESSION['tipo_usuario'] === 'cliente') {
            // Verificar propiedad de la cita
            if ($cita['id_cliente'] != $_SESSION['id_cliente']) {
                throw new Exception("Acceso no autorizado.");
            }

            // Validar estado y fecha
            $fechaCita = new DateTime($cita['fecha_cita'] . ' ' . $cita['hora_inicio']);
            $ahora = new DateTime();
            
            // Solo se puede cancelar si está en estado 'reservada' y es futura
            if ($cita['estado'] !== 'reservada' || $fechaCita <= $ahora) {
                throw new Exception("No puedes cancelar esta cita.");
            }
        }

        // Empleados pueden cancelar cualquier cita 'reservada'
        if ($_SESSION['tipo_usuario'] === 'empleado' && $cita['estado'] !== 'reservada') {
            throw new Exception("Solo se pueden cancelar citas reservadas.");
        }

        // Actualizar estado
        $stmt = $conn->prepare("
            UPDATE citas 
            SET estado = 'cancelada' 
            WHERE id_cita = :id_cita
        ");
        $stmt->execute([':id_cita' => $_POST['id_cita']]);

        $conn->commit();

        $stmt = $conn->prepare("SELECT email FROM clientes WHERE id_cliente = ?");
        $stmt->execute([$cita['id_cliente']]);
        $email_cliente = $stmt->fetchColumn();

        if ($email_cliente) {
            enviarCorreoCancelacion($email_cliente, $cita['fecha_cita'], $cita['hora_inicio']);
        }

        // Redirección
        $pagina = ($_SESSION['tipo_usuario'] === 'cliente') ? 'panel_cliente.php' : 'citas.php';
        header("Location: /TFGPeluqueria/paginas/$pagina");

    } catch (Exception $e) {
        $conn->rollBack();
        $pagina = ($_SESSION['tipo_usuario'] === 'cliente') ? 'panel_cliente.php' : 'citas.php';
        header("Location: $pagina?error=" . urlencode($e->getMessage()));
    }
    exit();
}
