<?php
session_start();
// Verificar que sea un empleado
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'empleado') {
    header('Location: /TFGPeluqueria/index.php');
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php';
include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/plantillas/navbar.php'; // Navbar específico para empleados

$esAdmin = esAdministrador($conn); 

$filtro = $_GET['filtro'] ?? 'reservadas';
$condicionEstado = ($filtro === 'reservadas') 
    ? "c.estado = 'reservada'" 
    : "c.estado IN ('finalizada', 'cancelada')";


// Obtener todas las citas con datos del cliente
$citas = [];
try {
    $stmt = $conn->prepare("
        SELECT c.id_cita, c.fecha_cita, c.hora_inicio, c.estado, 
               c.precio_final,
               GROUP_CONCAT(s.nombre_servicio SEPARATOR ', ') AS servicios,
               CONCAT(cli.nombre, ' ', cli.apellidos) AS cliente
        FROM citas c
        JOIN clientes cli ON c.id_cliente = cli.id_cliente
        JOIN citas_servicios cs ON c.id_cita = cs.id_cita
        JOIN servicios s ON cs.id_servicio = s.id_servicio
	WHERE $condicionEstado
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
        <h1>Citas <?= $filtro === 'reservadas' ? 'reservadas' : 'finalizadas o canceladas' ?></h1>
        <br>
        <?php if (empty($citas)): ?>
                <p>No hay citas <?= $filtro === 'reservadas' ? 'reservadas' : 'finalizadas o canceladas' ?>.</p>
        <?php else: ?>
	    <div class="contenedor-busqueda">
    		<input type="text" id="buscador-citas" placeholder="Buscar por cliente, servicios o fecha..." class="input-busqueda">
	    </div>

            <table class="tabla-citas">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Servicios</th>
                        <th>Precio</th>
                        <th>Cliente</th>
		   </tr>
                </thead>
                <tbody>
                    <?php foreach ($citas as $cita): ?>
                        <tr onclick="window.location='/TFGPeluqueria/paginas/datos_cita.php?id_cita=<?= $cita['id_cita'] ?>'" style="cursor: pointer;">
                            <td><?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?></td>
                            <td><?= date('H:i', strtotime($cita['hora_inicio'])) ?></td>
                            <td><?= $cita['servicios'] ?></td>
                            <td><?= number_format($cita['precio_final'], 2) ?> €</td>
                            <td><?= $cita['cliente'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="contenedor-botones">
            <?php if ($esAdmin): ?>
                <a href="/TFGPeluqueria/paginas/seleccionar_cliente.php" class="boton-alta">Nueva cita</a>
                
		<!-- Botón para alternar entre estados -->
                <a href="citas.php?filtro=<?= $filtro === 'reservadas' ? 'finalizadas' : 'reservadas' ?>" 
                class="boton-baja">
                    <?= $filtro === 'reservadas' ? 'Ver finalizadas/canceladas' : 'Ver reservadas' ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
