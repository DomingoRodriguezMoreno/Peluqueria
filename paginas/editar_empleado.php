<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empleado</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
    <?php
        session_start();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php';
        include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/plantillas/navbar.php';

        if (!esAdministrador($conn)) {
            header('Location: /TFGPeluqueria/index.php');
            exit();
        }

        $dni = $_GET['dni'] ?? ($_SESSION['form_data_edicion_empleado']['dni'] ?? null);

        // Obtener datos del empleado
        $empleado = [];
        $roles = [];
        try {
            // Datos del empleado
            $stmt = $conn->prepare("SELECT * FROM empleados WHERE dni = ?");
            $stmt->execute([$dni]);
            $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$empleado) {
                die("Empleado no encontrado");
            }

            // Lista de roles
            $stmt = $conn->query("SELECT * FROM roles");
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Guardar datos originales para recuperación en errores
            $_SESSION['original_data_empleado'] = [
                'telefono' => $empleado['telefono'],
                'email' => $empleado['email'],
                'dni' => $empleado['dni'],
                'es_admin' => $empleado['es_admin'],
                'activo' => $empleado['activo']
            ];
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }

        $empleado = array_merge(
            $_SESSION['original_data_empleado'] ?? [],
            $_SESSION['form_data_edicion_empleado'] ?? $empleado
        );

        unset($_SESSION['form_data_edicion_empleado']);
    ?>
    
    <div class="registros-container">
        <h1>Editar Empleado</h1>

        <?php if (isset($_SESSION['error_edicion_empleado'])): ?>
            <div class="error-mensaje">
                <?= htmlspecialchars($_SESSION['error_edicion_empleado']) ?>
            </div>
            <?php unset($_SESSION['error_edicion_empleado']); ?>
        <?php endif; ?>

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
