<?php
function esAdministrador($conn) {
    if (!isset($_SESSION['dni'])) return false;
    
    try {
        // Consultar la nueva columna "es_admin"
        $stmt = $conn->prepare("SELECT es_admin FROM empleados WHERE dni = :dni");
        $stmt->execute([':dni' => $_SESSION['dni']]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si existe el empleado y si es administrador
        return ($resultado && $resultado['es_admin'] == 1);

    } catch (PDOException $e) {
        error_log("Error verificando admin: " . $e->getMessage());
        return false;
    }
}
?>