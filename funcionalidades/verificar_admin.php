<?php
function esAdministrador($conn) {
    if (!isset($_SESSION['dni'])) return false;
    
    try {
        $stmt = $conn->prepare("SELECT id_rol FROM empleados WHERE dni = :dni");
        $stmt->execute([':dni' => $_SESSION['dni']]);
        $rol = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($rol['id_rol'] == 3); // 3 = Admin
    } catch (PDOException $e) {
        error_log("Error verificando admin: " . $e->getMessage());
        return false;
    }
}
?>