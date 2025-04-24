<?php
function esAdministrador($conn) {
    if (!isset($_SESSION['dni']) || $_SESSION['tipo_usuario'] !== 'empleado') {
        return false;
    }
    
    try {
        $stmt = $conn->prepare("SELECT es_admin FROM empleados WHERE dni = :dni AND activo = 1");
        $stmt->execute([':dni' => $_SESSION['dni']]);
        return ($stmt->fetch(PDO::FETCH_COLUMN) == 1);
    } catch (PDOException $e) {
        error_log("Error verificando admin: " . $e->getMessage());
        return false;
    }
}