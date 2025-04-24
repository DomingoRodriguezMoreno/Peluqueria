<?php
session_start();
require_once '../funcionalidades/conexion.php';
require_once '../funcionalidades/verificar_admin.php';

if (!esAdministrador($conn)) {
    header('Location: /TFGPeluqueria/index.php');
    exit();
}

$dni = $_GET['dni'] ?? null;

// Obtener datos del empleado
$empleado = [];
$roles = [];
try {
    // Datos del empleado
    $stmt = $conn->prepare("SELECT * FROM empleados WHERE dni = ?");
    $stmt->execute([$dni]);
    $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Lista de roles
    $stmt = $conn->query("SELECT * FROM roles");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Empleado</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
    <?php include '../plantillas/navbar.php'; ?>
    
    <div class="registros-container">
        <h1>Editar Empleado</h1>
        <form action="/TFGPeluqueria/funcionalidades/procesar_edicion_empleado.php" method="POST">
            <input type="hidden" name="dni" value="<?= htmlspecialchars($empleado['dni']) ?>">
            
            <!-- Campos editables -->
            <div class="form-group">
                <label>Nombre: 
                    <input type="text" name="nombre" value="<?= htmlspecialchars($empleado['nombre']) ?>" required>
                </label>
            </div>

            <div class="form-group">
                <label>Apellidos: 
                    <input type="text" name="apellidos" value="<?= htmlspecialchars($empleado['apellidos']) ?>" required>
                </label>
            </div>

            <div class="form-group">
                <label>Teléfono: 
                    <input type="text" name="telefono" value="<?= htmlspecialchars($empleado['telefono']) ?>" required>
                </label>
            </div>

            <div class="form-group">
                <label>Email: 
                    <input type="email" name="email" value="<?= htmlspecialchars($empleado['email']) ?>" required>
                </label>
            </div>
            
            <!-- Repetir para otros campos (apellidos, teléfono, email) -->
            <div class="form-group">
                <label>Rol:
                    <select name="id_rol" required>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= $rol['id_rol'] ?>" <?= $rol['id_rol'] == $empleado['id_rol'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($rol['nombre_rol']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <div class="form-group">
                <label>Administrador: 
                    <input type="checkbox" name="es_admin" value="1" <?= $empleado['es_admin'] ? 'checked' : '' ?>>
                </label>
            </div>

            <div class="form-group">
                <label>Activo: 
                    <input type="checkbox" name="activo" value="1" <?= $empleado['activo'] ? 'checked' : '' ?>>
                </label>
            </div>

            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>