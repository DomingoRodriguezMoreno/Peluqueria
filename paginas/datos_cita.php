<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Cita</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
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
        try {
            // Datos principales de la cita
            $stmt = $conn->prepare("
                SELECT c.*, 
                    CONCAT(cli.nombre, ' ', cli.apellidos) AS cliente,
                    cli.telefono,
                    ADDTIME(c.hora_inicio, SEC_TO_TIME(SUM(s.duracion)*60)) AS hora_fin,
                    GROUP_CONCAT(DISTINCT s.nombre_servicio SEPARATOR ', ') AS servicios,
                    GROUP_CONCAT(DISTINCT CONCAT(e.nombre, ' ', e.apellidos) SEPARATOR ', ') AS empleados
                FROM citas c
                JOIN clientes cli ON c.id_cliente = cli.id_cliente
                JOIN citas_servicios cs ON c.id_cita = cs.id_cita
                JOIN servicios s ON cs.id_servicio = s.id_servicio
                JOIN empleados e ON cs.id_empleado = e.dni
                WHERE c.id_cita = ?
                GROUP BY c.id_cita
            ");
            $stmt->execute([$id_cita]);
            $cita = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    ?>

    <div class="contenedor-principal">
        <h1>Detalles de la Cita #<?= $cita['id_cita'] ?? 'N/A' ?></h1>
        
        <?php if (!empty($cita)): ?>
            <div class="detalles-cita">
                <!-- Columna 1: Información General -->
                <div class="seccion">
                    <h3>Información General</h3>
                    <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?></p>
                    <p><strong>Hora Inicio:</strong> <?= date('H:i', strtotime($cita['hora_inicio'])) ?></p>
                    <p><strong>Hora Fin:</strong> <?= $cita['hora_fin'] ?? '--' ?></p>
                    <p><strong>Estado:</strong> <span class="estado-cita estado-<?= $cita['estado'] ?>"><?= ucfirst($cita['estado']) ?></span></p>
                </div>

                <!-- Columna 2: Cliente y Contacto -->
                <div class="seccion">
                    <h3>Cliente</h3>
                    <p><strong>Nombre:</strong> <?= $cita['cliente'] ?></p>
                    <p><strong>Teléfono:</strong> <?= $cita['telefono'] ?></p>
                </div>

                <!-- Servicios Contratados -->
                <div class="seccion" style="grid-column: span 2;">
                    <h3>Servicios</h3>
                    <p><?= $cita['servicios'] ?? 'No hay servicios registrados' ?></p>
                </div>

                <!-- Empleados Asignados -->
                <div class="seccion" style="grid-column: span 2;">
                    <h3>Empleados Asignados</h3>
                    <p><?= $cita['empleados'] ?? 'No hay empleados asignados' ?></p>
                </div>
            </div>

            <!-- Botón de Cancelación (para admin) -->
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
        <?php else: ?>
            <p>Cita no encontrada.</p>
        <?php endif; ?>

        <a href="citas.php" class="boton-volver">← Volver a Citas</a>
    </div>
</body>
</html>
