<?php
session_start();
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header('Location: /TFGPeluqueria/index.html'); // Redirigir si no está logueado como cliente
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Clientes</title>
</head>
<body>
    <h1>Bienvenido, <?php echo $_SESSION['nombre']; ?></h1>
    <p>Aquí puedes ver tus citas, reservar servicios, etc.</p>
    <a href="/TFGPeluqueria/funcionalidades/logout.php">Cerrar sesión</a>
</body>
</html>