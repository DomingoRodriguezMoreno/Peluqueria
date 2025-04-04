<?php
session_start();
// Verificar autenticación y permisos de administrador
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'empleado') {
    header('Location: /TFGPeluqueria/index.php');
    exit();
}

require_once '../funcionalidades/conexion.php';
require_once '../funcionalidades/verificar_admin.php'; // Verificar si el usuario es administrador
include '../plantillas/navbar.php';

// Obtener lista de empleados NO administradores
$empleados = [];
try {
    $sql = "SELECT e.dni, e.nombre, e.apellidos 
            FROM empleados e
            INNER JOIN roles r ON e.id_rol = r.id_rol
            WHERE r.nombre_rol != 'Administrador'"; // <- Filtro clave
    $stmt = $conn->query($sql);
    $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al obtener empleados: " . $e->getMessage());
}

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dni'])) {
    $dni = $_POST['dni'];
    try {
        // Verificar que no sea administrador
        $sqlCheck = "SELECT r.nombre_rol 
                    FROM empleados e
                    INNER JOIN roles r ON e.id_rol = r.id_rol
                    WHERE e.dni = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->execute([$dni]);
        $rol = $stmtCheck->fetchColumn();

        if ($rol === 'Administrador') {
            $error = "No se puede eliminar administradores.";
        } else {
            // Eliminar empleado
            $sqlDelete = "DELETE FROM empleados WHERE dni = ?";
            $stmtDelete = $conn->prepare($sqlDelete);
            $stmtDelete->execute([$dni]);
            
            if ($stmtDelete->rowCount() > 0) {
                $mensaje = "Empleado eliminado correctamente.";
            } else {
                $error = "Error al eliminar el empleado.";
            }
        }
    } catch (PDOException $e) {
        error_log("Error al eliminar empleado: " . $e->getMessage());
        $error = "Error en la base de datos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Empleado</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
    <div class="contenedor-empleado">
        <h1>Eliminar Empleado</h1>
        
        <?php if (isset($mensaje)): ?>
            <div class="mensaje-exito"><?= $mensaje ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="mensaje-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" id="formEliminar" onsubmit="return confirmarEliminacion()">
            <select name="dni" class="select-empleado" required>
                <option value="">Seleccione un empleado</option>
                <?php foreach ($empleados as $emp): ?>
                    <option value="<?= htmlspecialchars($emp['dni']) ?>">
                        <?= htmlspecialchars($emp['dni'] . " - " . $emp['nombre'] . " " . $emp['apellidos']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" class="boton-eliminar">Eliminar Empleado</button>
        </form>
    </div>

    <script>
        function confirmarEliminacion() {
            return confirm("¿Está seguro de eliminar este empleado? Esta acción no se puede deshacer.");
        }
    </script>
</body>
</html>