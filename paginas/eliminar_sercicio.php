<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php';

// Obtener todos los servicios para el desplegable
try {
    $sql = "SELECT s.id_servicio, s.nombre_servicio, t.nombre_tipo 
            FROM servicios s
            JOIN servicios_tipos st ON s.id_servicio = st.id_servicio
            JOIN tipos_tratamiento t ON st.id_tipo = t.id_tipo";
    $servicios = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar servicios: " . $e->getMessage());
}

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_servicio'])) {
    try {
        $conn->beginTransaction();
        $id_servicio = $_POST['id_servicio'];

        // Eliminar relaciones primero
        $conn->exec("DELETE FROM citas_servicios WHERE id_servicio = $id_servicio");
        $conn->exec("DELETE FROM roles_servicios WHERE id_servicio = $id_servicio");
        $conn->exec("DELETE FROM servicios_tipos WHERE id_servicio = $id_servicio");

        // Eliminar el servicio
        $stmt = $conn->prepare("DELETE FROM servicios WHERE id_servicio = ?");
        $stmt->execute([$id_servicio]);

        $conn->commit();
        header("Location: servicios.php?exito=eliminado");
        
    } catch (PDOException $e) {
        $conn->rollBack();
        header("Location: eliminar_tratamiento.php?error=1");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Tratamiento</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/plantillas/navbar.php'; ?>

    <div class="contenedor-principal">
        <h1>Eliminar Tratamiento</h1>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="mensaje-error">Error al eliminar el tratamiento</div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Seleccionar tratamiento:</label>
                <select name="id_servicio" required class="select-estilizado">
                    <?php foreach ($servicios as $servicio): ?>
                        <option value="<?= $servicio['id_servicio'] ?>">
                            <?= $servicio['nombre_servicio'] ?> (<?= $servicio['nombre_tipo'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="boton-eliminar" 
                    onclick="return confirm('¿Estás seguro de eliminar este tratamiento?')">
                Eliminar definitivamente
            </button>
        </form>
    </div>
</body>
</html>