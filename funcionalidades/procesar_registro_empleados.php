<?php
// Incluir la conexión a la base de datos
include 'conexion.php';

// Obtener datos del formulario
$dni = $_POST['dni'];
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$id_rol = $_POST['id_rol'];
$contraseña = $_POST['contraseña'];

// Validar campos obligatorios
if (empty($dni) || empty($nombre) || empty($apellidos) || empty($telefono) || empty($email) || empty($id_rol) || empty($contraseña)) {
    die("Todos los campos son obligatorios.");
}

// Validar formato de DNI (ejemplo básico)
if (!preg_match('/^[0-9]{8}[A-Za-z]$/', $dni)) {
    die("Formato de DNI inválido.");
}

// Validar rol permitido (1 o 2)
if (!in_array($id_rol, [1, 2])) {
    die("Rol seleccionado no válido.");
}

// Verificar unicidad de DNI, teléfono y email
$verificaciones = [
    'dni' => "SELECT dni FROM empleados WHERE dni = :valor",
    'telefono' => "SELECT telefono FROM empleados WHERE telefono = :valor",
    'email' => "SELECT email FROM empleados WHERE email = :valor"
];

foreach ($verificaciones as $campo => $query) {
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':valor', ${$campo});
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        die("El $campo ya está registrado.");
    }
}

// Encriptar contraseña
$hash = password_hash($contraseña, PASSWORD_DEFAULT);

// Insertar empleado
try {
    $query = "INSERT INTO empleados (dni, nombre, apellidos, telefono, email, id_rol, contraseña) 
              VALUES (:dni, :nombre, :apellidos, :telefono, :email, :id_rol, :contrasena)";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':dni', $dni);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellidos', $apellidos);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
    $stmt->bindParam(':contrasena', $hash);

    if ($stmt->execute()) {
        echo "Empleado registrado exitosamente.";
        // Redirigir después de 3 segundos
        header("Refresh: 3; url=/TFGPeluqueria/paginas/empleados.php");
    }
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        die("Error: Datos duplicados (DNI, teléfono o email ya existen).");
    }
    die("Error al registrar empleado: " . $e->getMessage());
}
?>