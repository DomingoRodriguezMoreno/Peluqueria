<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/plantillas/navbar.php';

if (!isset($_SESSION['tipo_usuario'])) {
    header("Location: /TFGPeluqueria/index.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar contraseña</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
    <div class="registros-container">
        <h2>Cambiar contraseña</h2>
        <br>

        <?php if (isset($_SESSION['error_cambio_contrasena'])): ?>
            <div class="error-mensaje">
                <?= htmlspecialchars($_SESSION['error_cambio_contrasena']) ?>
            </div>
            <?php unset($_SESSION['error_cambio_contrasena']); ?>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class='mensaje-error'><?= htmlspecialchars($error) ?></p>
        <?php elseif ($exito): ?>
            <p class='mensaje-exito'><?= htmlspecialchars($exito) ?></p>
        <?php endif; ?>
        <form action="/TFGPeluqueria/funcionalidades/procesar_cambio_contrasena.php" method="POST">
            <input type="password" name="contrasena_actual" placeholder="Contraseña actual" required>
            <input type="password" name="nueva_contrasena" placeholder="Nueva contraseña" required>
            <input type="password" name="confirmar_contrasena" placeholder="Confirmar nueva contraseña" required>
            <button type="submit">Actualizar</button>
            <a href="/TFGPeluqueria/paginas/panel_<?= $_SESSION['tipo_usuario'] ?>.php" class="cancelar-btn">Cancelar</a>
        </form>
    </div>
    <script src="/TFGPeluqueria/js/scripts.js"></script>
</body>
</html>
