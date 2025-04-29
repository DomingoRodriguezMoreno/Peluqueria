<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php';

if (!esAdministrador($conn)) {
    header('Location: /TFGPeluqueria/index.php');
    exit();
}

$id_cita = $_GET['id_cita'] ?? null;

// Obtener datos de la cita
$cita = [];
try {
    // Datos de la cita
    $stmt = $conn->prepare("SELECT * FROM citas WHERE id_cita = ?");
    $stmt->execute([$id_cita]);
    $cita = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cita) {
        die("Cita no encontrada");
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cita</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/plantillas/navbar.php'; ?>
    
    <div class="registros-container">
        <h1>Editar Cita #<?= $id_cita ?></h1>
        <form action="/TFGPeluqueria/funcionalidades/procesar_edicion_cita.php" method="POST">
            <input type="hidden" name="id_cita" value="<?= htmlspecialchars($cita['id_cita']) ?>">
            
            <!-- Campos editables: Fecha, Hora y Estado -->
            <div class="form-group">
                <label>Fecha:
                    <input type="date" name="fecha_cita" value="<?= htmlspecialchars($cita['fecha_cita']) ?>" required>
                </label>
            </div>

            <div class="form-group">
                <label>Hora de inicio:
                    <input type="time" name="hora_inicio" value="<?= htmlspecialchars(substr($cita['hora_inicio'], 0, 5)) ?>" required>
                </label>
            </div>

            <div class="form-group">
                <label>Estado:
                    <select name="estado" required>
                        <option value="reservada" <?= $cita['estado'] == 'reservada' ? 'selected' : '' ?>>Reservada</option>
                        <option value="cancelada" <?= $cita['estado'] == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                    </select>
                </label>
            </div>

            <!-- Campos ocultos para mantener la integridad de los datos -->
            <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($cita['id_cliente']) ?>">
            
            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>