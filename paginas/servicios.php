<?php
include '../funcionalidades/conexion.php'; // Incluye la conexión

// Obtener todos los tipos de tratamiento ordenados por ID
$query_tipos = "SELECT * FROM tipos_tratamiento ORDER BY id_tipo ASC";
$stmt_tipos = $conn->query($query_tipos);
$tipos = $stmt_tipos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios de Peluquería</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
    <?php include '../plantillas/navbar.php'; ?>

    <div class="servicios-container">
        <h1>Nuestros Servicios</h1>

        <?php foreach ($tipos as $tipo): ?>
            <details class="desplegable-tipo">
                <summary><?= htmlspecialchars($tipo['nombre_tipo']) ?></summary>
                
                <?php
                // Obtener servicios de este tipo
                $query_servicios = "SELECT s.* 
                                FROM servicios s
                                JOIN servicios_tipos st ON s.id_servicio = st.id_servicio
                                WHERE st.id_tipo = :id_tipo";
                $stmt_serv = $conn->prepare($query_servicios);
                $stmt_serv->execute([':id_tipo' => $tipo['id_tipo']]);
                $servicios = $stmt_serv->fetchAll(PDO::FETCH_ASSOC);
                ?>
                
                <?php if (!empty($servicios)): ?>
                    <table class="tabla-servicios">
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Duración</th>
                            <th>Precio</th>
                        </tr>
                        <?php foreach ($servicios as $servicio): ?>
                            <tr>
                                <td><?= htmlspecialchars($servicio['nombre_servicio']) ?></td>
                                <td><?= htmlspecialchars($servicio['descripcion']) ?></td>
                                <td><?= $servicio['duracion'] ?> min</td>
                                <td><?= number_format($servicio['precio'], 2) ?>€</td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <div class="sin-servicios">No hay servicios disponibles</div>
                <?php endif; ?>
            </details>
        <?php endforeach; ?>
    </div>
</body>
</html>