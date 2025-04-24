<?php
session_start();
// Verificar autenticación y tipo de usuario
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'empleado') {
    header('Location: /TFGPeluqueria/index.php');
    exit();
}

require_once '../funcionalidades/conexion.php'; // Incluir la conexión a la base de datos
require_once '../funcionalidades/verificar_admin.php'; // Verificar si el usuario es administrador
include '../plantillas/navbar.php'; // Incluir la barra de navegación

// Verificar si es admin
$esAdmin = esAdministrador($conn);

// Obtener lista completa de empleados
$mostrar = $_GET['mostrar'] ?? 'activos';
$condicion = ($mostrar === 'inactivos') ? 'e.activo = 0' : 'e.activo = 1';
$empleados = [];

try {
    $sql = "SELECT e.dni, e.nombre, e.apellidos, e.telefono, e.email, r.nombre_rol 
            FROM empleados e
            INNER JOIN roles r ON e.id_rol = r.id_rol
            WHERE $condicion";
    $stmt = $conn->query($sql);
    $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al obtener empleados: " . $e->getMessage());
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
    <div class="contenedor-principal">
        <h1>Listado de Empleados</h1>
        
        <table class="tabla-citas">
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Rol</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empleados as $empleado): ?>
                    <tr <?= $esAdmin ? 'onclick="window.location=\'editar_empleado.php?dni=' . htmlspecialchars($empleado['dni']) . '\'"' : '' ?> 
                    class="<?= $esAdmin ? 'clickable-row' : '' ?>">
                        <td><?= htmlspecialchars($empleado['dni']) ?></td>
                        <td><?= htmlspecialchars($empleado['nombre']) ?></td>
                        <td><?= htmlspecialchars($empleado['apellidos']) ?></td>
                        <td><?= htmlspecialchars($empleado['telefono']) ?></td>
                        <td><?= htmlspecialchars($empleado['email']) ?></td>
                        <td><?= htmlspecialchars($empleado['nombre_rol']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="contenedor-botones">
            <?php if ($esAdmin): ?>
                <a href="/TFGPELUQUERIA/paginas/registro_empleados.php" class="boton-alta">Alta empleado</a>
                <a href="empleados.php?mostrar=<?= $mostrar === 'activos' ? 'inactivos' : 'activos' ?>" class="boton-baja">
                    <?= $mostrar === 'activos' ? 'Ver inactivos' : 'Ver activos' ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>