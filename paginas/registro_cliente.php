<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cliente</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css"> <!-- Enlaza tu archivo CSS -->
</head>
<body>
    <?php 
    session_start();

    include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/plantillas/navbar.php';

    if (isset($_SESSION['error_cliente'])) {
        echo "<script>alert('".$_SESSION['error_cliente']."');</script>";
        unset($_SESSION['error_cliente']);
    }
    ?>
    
    <div class="registros-container">
        <h1>Registro de Cliente</h1>
        <form action="/TFGPeluqueria/funcionalidades/procesar_registro_cliente.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($_SESSION['form_data_cliente']['nombre'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" value="<?= htmlspecialchars($_SESSION['form_data_cliente']['apellidos'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($_SESSION['form_data_cliente']['telefono'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['form_data_cliente']['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="contraseña">Contraseña:</label>
                <input type="password" id="contraseña" name="contraseña" required>
            </div>

            <button type="submit">Registrarse</button>
        </form>
    </div>
</body>
</html>