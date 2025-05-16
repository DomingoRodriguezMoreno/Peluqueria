<?php
session_start();
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'empleado') {
    header('Location: /TFGPeluqueria/index.php');
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php'; // Asegúrate de incluir la conexión si es necesaria
include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/plantillas/navbar.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Empleados</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
    <div class="contenedor-principal">
        <h1>Bienvenido, <?php echo $_SESSION['nombre']; ?></h1>
        
        <div class="botones-panel">
            <a href="/TFGPeluqueria/paginas/empleados.php" class="boton-panel">Empleados</a>
            <a href="/TFGPeluqueria/paginas/citas.php" class="boton-panel">Citas</a>
            <a href="/TFGPeluqueria/paginas/clientes.php" class="boton-panel">Clientes</a>
            <a href="/TFGPeluqueria/paginas/servicios.php" class="boton-panel">Servicios</a>
        </div>

        <a href="/TFGPeluqueria/funcionalidades/logout.php" class="logout-btn">Cerrar sesión</a>
    </div>
</body>
</html>
