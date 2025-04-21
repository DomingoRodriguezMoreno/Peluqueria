<?php
session_start();
// Verificar que sea un empleado
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'empleado') {
    header('Location: /TFGPeluqueria/index.html');
    exit();
}

require_once '../funcionalidades/conexion.php';
include '../plantillas/navbar.php'; // Navbar específico para empleados

// Obtener todas las citas con datos del cliente
$citas = [];
try {
    $stmt = $conn->prepare("
        SELECT c.id_cita, c.fecha_cita, c.hora_inicio, c.estado, 
               c.duracion_total, c.precio_final,
               GROUP_CONCAT(s.nombre_servicio SEPARATOR ', ') AS servicios,
               CONCAT(cli.nombre, ' ', cli.apellidos) AS cliente,
               cli.telefono
        FROM citas c
        JOIN clientes cli ON c.id_cliente = cli.id_cliente
        JOIN citas_servicios cs ON c.id_cita = cs.id_cita
        JOIN servicios s ON cs.id_servicio = s.id_servicio
        GROUP BY c.id_cita
        ORDER BY c.fecha_cita DESC, c.hora_inicio DESC
    ");
    $stmt->execute();
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener citas: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Empleado - Todas las Citas</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
    <div class="contenedor-principal">
        <h1>Citas Registradas</h1>
        
        <?php if (empty($citas)): ?>
            <p>No hay citas programadas.</p>
        <?php else: ?>
            <table class="tabla-citas">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Servicios</th>
                        <th>Duración</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($citas as $cita): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?></td>
                            <td><?= date('H:i', strtotime($cita['hora_inicio'])) ?></td>
                            <td><?= $cita['servicios'] ?></td>
                            <td><?= $cita['duracion_total'] ?> min</td>
                            <td><?= number_format($cita['precio_final'], 2) ?> €</td>
                            <td><span class="estado-cita estado-<?= $cita['estado'] ?>"><?= ucfirst($cita['estado']) ?></span></td>
                            <td><?= $cita['cliente'] ?></td>
                            <td><?= $cita['telefono'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>