<?php
// Incluir la conexión a la base de datos
include 'conexion.php';

// Regenerar hash para empleados
$query_empleados = "SELECT dni, contraseña FROM empleados";
$stmt_empleados = $conn->query($query_empleados);

while ($empleado = $stmt_empleados->fetch(PDO::FETCH_ASSOC)) {
    $dni = $empleado['dni'];
    $password = $empleado['contraseña']; // Contraseña en texto plano
    $hash = password_hash($password, PASSWORD_DEFAULT); // Generar nuevo hash

    // Actualizar el hash en la base de datos
    $query_update = "UPDATE empleados SET contraseña = :hash WHERE dni = :dni";
    $stmt_update = $conn->prepare($query_update);
    $stmt_update->bindParam(':hash', $hash);
    $stmt_update->bindParam(':dni', $dni);
    $stmt_update->execute();

    echo "Hash actualizado para el empleado con DNI: $dni<br>";
}

// Regenerar hash para clientes
$query_clientes = "SELECT id_cliente, contraseña FROM clientes";
$stmt_clientes = $conn->query($query_clientes);

while ($cliente = $stmt_clientes->fetch(PDO::FETCH_ASSOC)) {
    $id_cliente = $cliente['id_cliente'];
    $password = $cliente['contraseña']; // Contraseña en texto plano
    $hash = password_hash($password, PASSWORD_DEFAULT); // Generar nuevo hash

    // Actualizar el hash en la base de datos
    $query_update = "UPDATE clientes SET contraseña = :hash WHERE id_cliente = :id_cliente";
    $stmt_update = $conn->prepare($query_update);
    $stmt_update->bindParam(':hash', $hash);
    $stmt_update->bindParam(':id_cliente', $id_cliente);
    $stmt_update->execute();

    echo "Hash actualizado para el cliente con ID: $id_cliente<br>";
}

echo "¡Todos los hash han sido regenerados correctamente!";
?>