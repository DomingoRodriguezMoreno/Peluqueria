<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php';

if (!esAdministrador($conn)) {
    header('Location: /TFGPeluqueria/index.php');
    exit();
}

$id_servicio = $_GET['id_servicio'] ?? null;

// Obtener datos del servicio
$servicio = [];
try {
    $stmt = $conn->prepare("SELECT * FROM servicios WHERE id_servicio = ?");
    $stmt->execute([$id_servicio]);
    $servicio = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Servicio</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/plantillas/navbar.php'; ?>
    
    <div class="registros-container">
        <h1>Editar Servicio</h1>
        <form action="/TFGPeluqueria/funcionalidades/procesar_edicion_servicio.php" method="POST">
            <input type="hidden" name="id_servicio" value="<?= htmlspecialchars($servicio['id_servicio']) ?>">
            
            <div class="form-group">
                <label>Nombre: 
                    <input type="text" name="nombre_servicio" value="<?= htmlspecialchars($servicio['nombre_servicio']) ?>" required>
                </label>
            </div>

            <div class="form-group">
                <label>Descripción: 
                    <textarea name="descripcion" required><?= htmlspecialchars($servicio['descripcion']) ?></textarea>
                </label>
            </div>

            <div class="form-group">
                <label>Duración (minutos): 
                    <input type="number" name="duracion" value="<?= htmlspecialchars($servicio['duracion']) ?>" required>
                </label>
            </div>

            <div class="form-group">
                <label>Precio (€): 
                    <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($servicio['precio']) ?>" required>
                </label>
            </div>

            <div class="form-group">
                <label>Activo: 
                    <input type="checkbox" name="activo" value="1" 
                        <?= $servicio['activo'] ? 'checked' : '' ?>>
                </label>
            </div>

            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>
