<?php
require_once '../funcionalidades/conexion.php';
require_once '../funcionalidades/verificar_admin.php';

try {
    // Obtener roles y tipos de tratamiento
    $sql_roles = "SELECT id_rol, nombre_rol FROM roles";
    $sql_tipos = "SELECT id_tipo, nombre_tipo FROM tipos_tratamiento";
    
    $roles = $conn->query($sql_roles)->fetchAll(PDO::FETCH_ASSOC);
    $tipos = $conn->query($sql_tipos)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Tratamiento</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
    <?php include '../plantillas/navbar.php'; ?>

    <div class="contenedor-principal registros-container">
        <h1>Registrar Nuevo Tratamiento</h1>
        <form action="../funcionalidades/procesar_servicio.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre del Tratamiento:</label>
                <input type="text" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" required></textarea>
            </div>

            <div class="form-group">
                <label for="duracion">Duración (minutos):</label>
                <input type="number" name="duracion" required>
            </div>

            <div class="form-group">
                <label for="precio">Precio (€):</label>
                <input type="number" name="precio" step="0.01" required>
            </div>

            <!-- Desplegable para roles -->
            <div class="form-group">
                <label for="rol">Rol Asociado:</label>
                <select name="rol" required>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= $rol['id_rol'] ?>">
                            <?= $rol['nombre_rol'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Nuevo desplegable para tipos de tratamiento -->
            <div class="form-group">
                <label for="tipo">Tipo de Tratamiento:</label>
                <select name="tipo" required>
                    <?php foreach ($tipos as $tipo): ?>
                        <option value="<?= $tipo['id_tipo'] ?>">
                            <?= $tipo['nombre_tipo'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit">Crear Tratamiento</button>
        </form>
    </div>
</body>
</html>
