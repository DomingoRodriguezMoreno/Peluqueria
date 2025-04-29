<?php
session_start();
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header('Location: ../index.php');
    exit();
}

require_once '../funcionalidades/conexion.php'; // Ajusta la ruta según tu estructura

include '../plantillas/navbar.php';
// Obtener citas del cliente
$citas = [];
try {
    $stmt = $conn->prepare("
        SELECT c.id_cita, c.fecha_cita, c.hora_inicio, c.estado, 
               c.duracion_total, c.precio_final,
               GROUP_CONCAT(s.nombre_servicio SEPARATOR ', ') AS servicios
        FROM citas c
        JOIN citas_servicios cs ON c.id_cita = cs.id_cita
        JOIN servicios s ON cs.id_servicio = s.id_servicio
        WHERE c.id_cliente = :id_cliente
        GROUP BY c.id_cita
        ORDER BY c.fecha_cita DESC, c.hora_inicio DESC
    ");
    $stmt->execute([':id_cliente' => $_SESSION['id_cliente']]);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener citas: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Clientes</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="contenedor-principal">
        <h1>Bienvenido, <?php echo $_SESSION['nombre']; ?></h1>

        <h2>Tus Citas</h2>
        <?php if (empty($citas)): ?>
            <p>No tienes citas programadas.</p>
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
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($citas as $cita): ?>
                        <?php 
                        $fechaActual = new DateTime();
                        $fechaCita = new DateTime($cita['fecha_cita'] . ' ' . $cita['hora_inicio']);
                        $puedeCancelar = ($fechaCita > $fechaActual) && ($cita['estado'] === 'reservada');
                        ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?></td>
                            <td><?= date('H:i', strtotime($cita['hora_inicio'])) ?></td>
                            <td><?= $cita['servicios'] ?></td>
                            <td><?= $cita['duracion_total'] ?> min</td>
                            <td><?= number_format($cita['precio_final'], 2) ?> €</td>
                            <td><span class="estado-cita estado-<?= $cita['estado'] ?>"><?= ucfirst($cita['estado']) ?></span></td>
                            <td>
                                <form action="../funcionalidades/cancelar_cita.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="id_cita" value="<?= $cita['id_cita'] ?>">
                                    <!-- En el botón, agregar un tooltip cuando esté deshabilitado -->
                                    <button 
                                        type="submit" 
                                        class="cancelar-btn" 
                                        <?= !$puedeCancelar ? 'disabled title="Solo puedes cancelar citas futuras en estado pendiente/confirmada."' : '' ?>>
                                        Cancelar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <a href="../funcionalidades/logout.php" class="logout-btn">Cerrar sesión</a>
    </div>
</body>
</html>