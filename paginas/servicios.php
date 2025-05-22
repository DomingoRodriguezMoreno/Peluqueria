<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de servicios</title>
    <link rel="icon" type="image/png" href="/TFGPeluqueria/imagenes/Logo2.png">
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
    <?php
        session_start();
        // Verificar autenticación y tipo de usuario
        if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'empleado') {
            header('Location: /TFGPeluqueria/index.php');
            exit();
        }

        require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php'; // Incluir la conexión a la base de datos
        require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php'; // Verificar si el usuario es administrador
        include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/plantillas/navbar.php'; // Incluir la barra de navegación

        // Verificar si es admin
        $esAdmin = esAdministrador($conn);

        // Obtener lista completa de servicios
        $mostrar = $_GET['mostrar'] ?? 'activos';
        $condicion = ($mostrar === 'inactivos') ? 's.activo = 0' : 's.activo = 1';
        $servicios = [];

        try {
            $sql = "SELECT tt.nombre_tipo AS tipo, s.* 
                    FROM servicios s 
                    JOIN servicios_tipos st ON s.id_servicio = st.id_servicio 
                    JOIN tipos_tratamiento tt ON st.id_tipo = tt.id_tipo
                    WHERE $condicion";
                    
            $stmt = $conn->query($sql);
            $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener servicios: " . $e->getMessage());
        }
    ?>

    <div class="contenedor-principal">
        <h1>Listado de servicios <?= $mostrar === 'activos' ? 'activos' : 'inactivos' ?></h1>
        <br>

	<div class="contenedor-busqueda">
    		<input type="text" id="buscador-general" class="buscador-general input-busqueda"  placeholder="Buscar..." data-tabla=".tabla-scroll .tabla-citas">
	</div>

        <div class="tabla-scroll">
            <table class="tabla-citas">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Duración</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($servicios as $servicio): ?>
                        <tr <?= $esAdmin ? 'onclick="window.location=\'editar_servicio.php?id_servicio=' . htmlspecialchars($servicio['id_servicio']) . '\'"' : '' ?> 
                        class="<?= $esAdmin ? 'clickable-row' : '' ?>">
                            <td><?= htmlspecialchars($servicio['nombre_servicio']) ?></td>
                            <td><?= htmlspecialchars($servicio['tipo']) ?></td>
                            <td><?= htmlspecialchars($servicio['descripcion']) ?></td>
                            <td><?= htmlspecialchars($servicio['duracion']) ?> min</td>
                            <td><?= number_format($servicio['precio'], 2) ?>€</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="contenedor-botones">
            <?php if ($esAdmin): ?>
                <a href="/TFGPeluqueria/paginas/registro_servicio.php" class="boton-alta">Alta servicio</a>
                <a href="servicios.php?mostrar=<?= $mostrar === 'activos' ? 'inactivos' : 'activos' ?>" class="boton-baja">
                    <?= $mostrar === 'activos' ? 'Ver inactivos' : 'Ver activos' ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

