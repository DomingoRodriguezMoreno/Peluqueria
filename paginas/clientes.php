<?php
session_start();
// Verificar autenticación y tipo de usuario
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'empleado') {
    header('Location: /TFGPeluqueria/index.html');
    exit();
}

require_once '../funcionalidades/conexion.php'; // Incluir la conexión a la base de datos
require_once '../funcionalidades/verificar_admin.php'; // Verificar si el usuario es administrador
include '../plantillas/navbar.php'; // Incluir la barra de navegación

// Verificar si es admin
$esAdmin = esAdministrador($conn);

// Obtener lista completa de empleados
$empleados = [];
try {
    $sql = "SELECT c.nombre, c.apellidos, c.telefono, c.email 
            FROM clientes c";
    $stmt = $conn->query($sql);
    $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al obtener clientes: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Empleados</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
    <div class="contenedor-empleado">
        <h1>Listado de Clientes</h1>
        
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
                <?php foreach ($empleados as $empleado): ?>
                    <tr>
                        <td><?= htmlspecialchars($empleado['nombre']) ?></td>
                        <td><?= htmlspecialchars($empleado['apellidos']) ?></td>
                        <td><?= htmlspecialchars($empleado['telefono']) ?></td>
                        <td><?= htmlspecialchars($empleado['email']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="contenedor-botones">
            <?php if ($esAdmin): ?>
                <a href="registro_cliente.php" class="boton-alta">Registrar Cliente</a>
                <a href="EliminarCliente.php" class="boton-baja">Eliminar Cliente</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>