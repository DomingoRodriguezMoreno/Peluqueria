<?php
// Habilitar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';

if (!isset($_SESSION['tipo_usuario'])) {
    header("Location: /TFGPeluqueria/index.php");
    exit();
}

$usuario = ($_SESSION['tipo_usuario'] === 'empleado') ? $_SESSION['dni'] : $_SESSION['id_cliente'];
$tabla = ($_SESSION['tipo_usuario'] === 'empleado') ? 'empleados' : 'clientes';
$campoID = ($_SESSION['tipo_usuario'] === 'empleado') ? 'dni' : 'id_cliente';

try {
    // Validar campos vacíos
    if (empty($_POST['contrasena_actual']) || empty($_POST['nueva_contrasena']) || empty($_POST['confirmar_contrasena'])) {
        $_SESSION['error_cambio_contrasena'] = "Todos los campos son obligatorios.";
        header("Location: /TFGPeluqueria/paginas/cambiar_contrasena.php");
        exit();
    }

    // Obtener contraseña actual
    $stmt = $conn->prepare("SELECT `contraseña` FROM `$tabla` WHERE `$campoID` = :usuario");
    $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$resultado || !password_verify($_POST['contrasena_actual'], $resultado['contraseña'])) {
        $_SESSION['error_cambio_contrasena'] = "La contraseña actual es incorrecta.";
        header("Location: /TFGPeluqueria/paginas/cambiar_contrasena.php");
        exit();
    }

    // Validar coincidencia de contraseñas nuevas
    if ($_POST['nueva_contrasena'] !== $_POST['confirmar_contrasena']) {
        $_SESSION['error_cambio_contrasena'] = "Las nuevas contraseñas no coinciden.";
        header("Location: /TFGPeluqueria/paginas/cambiar_contrasena.php");
        exit();
    }

    // Actualizar contraseña
    $nuevoHash = password_hash($_POST['nueva_contrasena'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE `$tabla` SET `contraseña` = :contrasena WHERE `$campoID` = :usuario");
    $stmt->bindParam(':contrasena', $nuevoHash, PDO::PARAM_STR);
    $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $_SESSION['exito_cambio_contrasena'] = "Contraseña actualizada correctamente.";
    } else {
        $_SESSION['error_cambio_contrasena'] = "Error al actualizar la contraseña.";
    }

    header("Location: /TFGPeluqueria/paginas/cambiar_contrasena.php");
    exit();

} catch (PDOException $e) {
    // Registrar el error en logs o mostrar para depuración
    die("Error en la base de datos: " . $e->getMessage());
}
