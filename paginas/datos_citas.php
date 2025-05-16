<?php
session_start();
// Verificar que sea un empleado
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'empleado') {
    header('Location: /TFGPeluqueria/index.php');
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php';
include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/plantillas/navbar.php';

$esAdmin = esAdministrador($conn);
$id_cita = $_GET['id_cita'] ?? null;

// Obtener datos completos de la cita
$cita = [];
$empleados_asignados = [];
try {
    // Datos básicos
    $stmt = $conn->prepare("
        SELECT c.*, 
               CONCAT(cli.nombre, ' ', cli.apellidos) AS cliente,
               cli.telefono,
               SEC_TO_TIME(SUM(s.duracion)*60) AS hora_fin
        FROM citas c
        JOIN clientes cli ON c.id_cliente = cli.id_cliente
        JOIN citas_servicios cs ON c.id_cita = cs.id_cita
        JOIN servicios s ON cs.id_servicio = s.id_servicio
        WHERE c.id_cita = ?
    ");
    $stmt->execute([$id_cita]);
    $cita = $stmt->fetch(PDO::FETCH_ASSOC);

    // Empleados asignados
    $stmt = $conn->prepare("
        SELECT CONCAT(e.nombre, ' ', e.apellidos) AS nombre_empleado 
        FROM empleados e
        JOIN citas_servicios cs ON e.id_empleado = cs.id_empleado
        WHERE cs.id_cita = ?
    ");
    $stmt->execute([$id_cita]);
    $empleados_asignados = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles de la Cita</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
    <div class="contenedor-principal">
        <h1>Detalles de la Cita</h1>
        
        <?php if (!empty($cita)): ?>
            <div class="detalles-cita">
                <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?></p>
                <p><strong>Hora inicio:</strong> <?= date('H:i', strtotime($cita['hora_inicio'])) ?></p>
                <p><strong>Hora fin estimada:</strong> <?= $cita['hora_fin'] ?? '--' ?></p>
                <p><strong>Cliente:</strong> <?= $cita['cliente'] ?></p>
                <p><strong>Teléfono:</strong> <?= $cita['telefono'] ?></p>
                <p><strong>Empleados:</strong> <?= implode(', ', $empleados_asignados) ?></p>
                <p><strong>Estado:</strong> <span class="estado-cita estado-<?= $cita['estado'] ?>"><?= ucfirst($cita['estado']) ?></span></p>
                
                <?php if ($esAdmin): ?>
                    <?php
                    $fechaCita = new DateTime($cita['fecha_cita'] . ' ' . $cita['hora_inicio']);
                    $puedeCancelar = ($fechaCita > new DateTime()) && ($cita['estado'] === 'reservada');
                    ?>
                    <form action="/TFGPeluqueria/funcionalidades/cancelar_cita.php" method="POST">
                        <input type="hidden" name="id_cita" value="<?= $id_cita ?>">
                        <button type="submit" class="cancelar-btn" <?= !$puedeCancelar ? 'disabled' : '' ?>>
                            Cancelar Cita
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>Cita no encontrada.</p>
        <?php endif; ?>

        <a href="citas.php" class="boton-volver">Volver a Citas</a>
    </div>
</body>
</html>