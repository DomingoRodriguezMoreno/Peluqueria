<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php';

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

    $empleado_mostrar = isset($_SESSION['original_data']) ? $_SESSION['original_data'] : $empleado;


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
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/plantillas/navbar.php'; ?>
    
    <div class="registros-container">
        <h1>Editar Empleado</h1>

        <?php if (isset($_SESSION['error_edicion_empleado'])): ?>
            <div class="error-mensaje">
                <?= htmlspecialchars($_SESSION['error_edicion_empleado']) ?>
            </div>
            <?php unset($_SESSION['error_edicion_empleado']); ?>
        <?php endif; ?>

        <form action="/TFGPeluqueria/funcionalidades/procesar_edicion_empleado.php" method="POST">
            <input type="hidden" name="original_dni" value="<?= htmlspecialchars($empleado['dni']) ?>">
            
            <!-- Campos editables -->
            <div class="form-group">
                <label>DNI: 
        		<input type="text" name="dni" value="<?= htmlspecialchars($empleado_mostrar['dni']) ?>" required>
                </label>
            </div>

            <div class="form-group">
                <label>Nombre: 
                    <input type="text" name="nombre" value="<?= htmlspecialchars($_SESSION['form_data']['nombre'] ?? $empleado['nombre']) ?>" required>
                </label>
            </div>

            <div class="form-group">
                <label>Apellidos: 
                    <input type="text" name="apellidos" value="<?= htmlspecialchars($_SESSION['form_data']['apellidos'] ?? $empleado['apellidos']) ?>" required>
                </label>
            </div>

            <div class="form-group">
                <label>Teléfono: 
                    <input type="text" name="telefono" value="<?= htmlspecialchars($_SESSION['form_data']['telefono'] ?? $empleado['telefono']) ?>" required>
                </label>
            </div>

            <div class="form-group">
                <label>Email: 
                    <input type="text" name="email" value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? $empleado['email']) ?>" required>
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
