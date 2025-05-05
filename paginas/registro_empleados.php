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

    include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/plantillas/navbar.php'; 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php'; // Verificar si el usuario es administrador
    require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
    
    ?>
    
    <div class="registros-container">
        <h1>Registro de Empleado</h1>

        <?php if (isset($_SESSION['error_empleado'])): ?>
            <div class="error-mensaje">
                <?= htmlspecialchars($_SESSION['error_empleado']) ?>
            </div>
            <?php unset($_SESSION['error_empleado']); ?>
        <?php endif; ?>
        
        <form action="/TFGPeluqueria/funcionalidades/procesar_registro_empleados.php" method="POST">
            <input type="hidden" name="tipo_usuario" value="empleado"> <!-- Campo oculto para diferenciar -->
            
            <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" value="<?= $_SESSION['form_data']['dni'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" name="nombre" value="<?= htmlspecialchars($_SESSION['form_data']['nombre'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" name="apellidos" value="<?= htmlspecialchars($_SESSION['form_data']['apellidos'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($_SESSION['form_data']['telefono'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="id_rol">Rol:</label>
                <select id="id_rol" name="id_rol" required>
                    <?php
                    try {
                        // Consulta para obtener roles
                        $query = "SELECT id_rol, nombre_rol FROM roles"; 
                        $stmt = $conn->query($query);

                        if ($stmt->rowCount() > 0) {
                            while ($rol = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . htmlspecialchars($rol['id_rol']) . '">' 
                                    . htmlspecialchars($rol['nombre_rol']) . '</option>';
                            }
                        } else {
                            echo '<option value="">No hay roles registrados</option>';
                        }
                    } catch (PDOException $e) {
                        echo '<option value="">Error al cargar roles</option>';
                    }
                    ?>
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