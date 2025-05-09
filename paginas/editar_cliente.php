<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php';

if (!esAdministrador($conn)) {
    header('Location: /TFGPeluqueria/index.php');
    exit();
}

// Validación robusta del ID
$id_cliente = filter_input(INPUT_GET, 'id_cliente', FILTER_VALIDATE_INT);

if (!$id_cliente) {
    die("ID de cliente inválido");
}

// Obtener datos del cliente
try {
    $stmt = $conn->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        die("Cliente no encontrado en la base de datos");
    }

    // Guardar datos originales para recuperación en errores
    $_SESSION['original_data_cliente'] = [
        'telefono' => $cliente['telefono'],
        'email' => $cliente['email']
    ];

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}

// Combinar datos: originales + ediciones (si existen)
$cliente = array_merge(
    $_SESSION['original_data_cliente'] ?? [],
    $_SESSION['form_data_edicion_cliente'] ?? $cliente
);

// Limpiar datos temporales después de usarlos
unset($_SESSION['form_data_edicion_cliente']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cliente</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/plantillas/navbar.php'; ?>
    
    <div class="registros-container">
        <h1>Editar Cliente</h1>

        <?php if (isset($_SESSION['error_edicion_cliente'])): ?>
            <div class="error-mensaje">
                <?= htmlspecialchars($_SESSION['error_edicion_cliente']) ?>
            </div>
            <?php unset($_SESSION['error_edicion_cliente']); ?>
        <?php endif; ?>

        <form action="/TFGPeluqueria/funcionalidades/procesar_edicion_cliente.php" method="POST">
            <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($cliente['id_cliente']) ?>"> 

            <div class="form-group">
                <label>Nombre: 
                    <input type="text" name="nombre" value="<?= htmlspecialchars($cliente['nombre']) ?>" required>
                </label>
            </div>

            <div class="form-group">
                <label>Apellidos: 
                    <input type="text" name="apellidos" value="<?= htmlspecialchars($cliente['apellidos']) ?>" required>
                </label>
            </div>

            <div class="form-group">
                <label>Email: 
                    <input type="email" name="email" value="<?= htmlspecialchars($cliente['email']) ?>" required>
                </label>
            </div>

            <div class="form-group">
                <label>Teléfono: 
                    <input type="text" name="telefono" value="<?= htmlspecialchars($cliente['telefono']) ?>" required>
                </label>
            </div>

            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>
