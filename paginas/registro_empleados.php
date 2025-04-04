<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Empleado</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
    <?php 
    session_start();
    include '../plantillas/navbar.php'; 
    require_once '../funcionalidades/verificar_admin.php'; // Verificar si el usuario es administrador

    ?>
    
    <div class="registros-container">
        <h1>Registro de Empleado</h1>
        <form action="/TFGPeluqueria/funcionalidades/procesar_registro_empleados.php" method="POST">
            <input type="hidden" name="tipo_usuario" value="empleado"> <!-- Campo oculto para diferenciar -->
            
            <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" required>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="id_rol">Rol:</label>
                <select id="id_rol" name="id_rol" required>
                    <option value="1">Peluquero</option>
                    <option value="2">Esteticien</option>
                </select>
            </div>

            <div class="form-group">
                <label for="contraseña">Contraseña:</label>
                <input type="password" id="contraseña" name="contraseña" required>
            </div>

            <button type="submit">Registrar Empleado</button>
        </form>
    </div>
</body>
</html>