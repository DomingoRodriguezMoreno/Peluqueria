<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados</title>
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

        // Obtener lista completa de empleados
        $clientes = [];
        try {
            $sql = "SELECT c.id_cliente, c.nombre, c.apellidos, c.telefono, c.email 
                    FROM clientes c";
            $stmt = $conn->query($sql);
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener clientes: " . $e->getMessage());
        }
    ?>

    <div class="contenedor-principal">
        <h1>Listado de Clientes</h1>
        <br>

        <div class="contenedor-busqueda">
                <input type="text" id="buscador-general" class="buscador-general input-busqueda"  placeholder="Buscar..." data-tabla=".tabla-scroll .tabla-citas">
        </div>

        <div class="tabla-scroll">
            <table class="tabla-citas">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr <?= $esAdmin ? 'onclick="window.location=\'editar_cliente.php?id_cliente=' . htmlspecialchars($cliente['id_cliente']) . '\'"' : '' ?>>
                            <td><?= htmlspecialchars($cliente['nombre']) ?></td>
                            <td><?= htmlspecialchars($cliente['apellidos']) ?></td>
                            <td><?= htmlspecialchars($cliente['telefono']) ?></td>
                            <td><?= htmlspecialchars($cliente['email']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="contenedor-botones">
            <?php if ($esAdmin): ?>
                <a href="registro_cliente.php" class="boton-alta">Registrar Cliente</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
