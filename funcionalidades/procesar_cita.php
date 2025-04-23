<?php
session_start();
include '../funcionalidades/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();
        
        // Insertar cita
        $stmt = $conn->prepare("INSERT INTO citas 
            (id_cliente, fecha_cita, hora_inicio, estado, duracion_total, precio_final)
            VALUES (?, ?, ?, 'reservada', 0, 0)");
        
        $stmt->execute([
            $_SESSION['id_cliente'],
            $_POST['fecha'],
            $_POST['hora']
        ]);
        
        $id_cita = $conn->lastInsertId();
        
        // Insertar servicios
        foreach ($_POST['servicios'] as $id_servicio) {
            $stmt = $conn->prepare("INSERT INTO citas_servicios 
                (id_cita, id_servicio, id_empleado)
                VALUES (?, ?, (SELECT dni FROM empleados WHERE id_rol IN 
                    (SELECT id_rol FROM roles_servicios WHERE id_servicio = ?) LIMIT 1))");
            
            $stmt->execute([$id_cita, $id_servicio, $id_servicio]);
        }
        
        $conn->commit();
        header("Location: /TFGPeluqueria/paginas/citas.php?success=1");
    } catch (PDOException $e) {
        $conn->rollBack();
        header("Location: /TFGPeluqueria/paginas/citas.php?error=1");
    }
} else {
    header("Location: /TFGPeluqueria/index.php");
}