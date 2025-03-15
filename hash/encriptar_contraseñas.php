<?php
// Incluir la conexión a la base de datos
include 'conexion.php';

// Encriptar contraseñas de empleados
$query_empleados = "SELECT dni, contraseña FROM empleados";
$stmt_empleados = $conn->query($query_empleados);
while ($empleado = $stmt_empleados->fetch(PDO::FETCH_ASSOC)) {
    $dni = $empleado['dni'];
    $password = $empleado['contraseña'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Actualizar la contraseña encriptada
    $query_update = "UPDATE empleados SET contraseña = :hash WHERE dni = :dni";
    $stmt_update = $conn->prepare($query_update);
    $stmt_update->bindParam(':hash', $hash);
    $stmt_update->bindParam(':dni', $dni);
    $stmt_update->execute();
}

// Encriptar contraseñas de clientes
$query_clientes = "SELECT id_cliente, contraseña FROM clientes";
$stmt_clientes = $conn->query($query_clientes);
while ($cliente = $stmt_clientes->fetch(PDO::FETCH_ASSOC)) {
    $id_cliente = $cliente['id_cliente'];
    $password = $cliente['contraseña'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Actualizar la contraseña encriptada
    $query_update = "UPDATE clientes SET contraseña = :hash WHERE id_cliente = :id_cliente";
    $stmt_update = $conn->prepare($query_update);
    $stmt_update->bindParam(':hash', $hash);
    $stmt_update->bindParam(':id_cliente', $id_cliente);
    $stmt_update->execute();
}

echo "Contraseñas encriptadas correctamente.";
?>