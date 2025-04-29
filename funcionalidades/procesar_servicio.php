<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/verificar_admin.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $conn->beginTransaction();

        // Insertar en servicios
        $sql_servicio = "INSERT INTO servicios 
            (nombre_servicio, descripcion, duracion, precio) 
            VALUES (:nombre, :descripcion, :duracion, :precio)";

        $stmt = $conn->prepare($sql_servicio);
        $stmt->execute([
            ':nombre' => $_POST['nombre'],
            ':descripcion' => $_POST['descripcion'],
            ':duracion' => $_POST['duracion'],
            ':precio' => $_POST['precio']
        ]);

        $id_servicio = $conn->lastInsertId();

        // Insertar en roles_servicios
        $sql_rol = "INSERT INTO roles_servicios (id_rol, id_servicio) 
                    VALUES (:id_rol, :id_servicio)";
        $stmt = $conn->prepare($sql_rol);
        $stmt->execute([
            ':id_rol' => $_POST['rol'],
            ':id_servicio' => $id_servicio
        ]);

        // Insertar en servicios_tipos
        $sql_tipo = "INSERT INTO servicios_tipos (id_servicio, id_tipo) 
                     VALUES (:id_servicio, :id_tipo)";
        $stmt = $conn->prepare($sql_tipo);
        $stmt->execute([
            ':id_servicio' => $id_servicio,
            ':id_tipo' => $_POST['tipo']
        ]);

        $conn->commit();
        header("Location: /TFGPeluqueria/paginas/servicios.php?exito=1");
        
    } catch (PDOException $e) {
        $conn->rollBack();
        die("Error: " . $e->getMessage());
    }
}