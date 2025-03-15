<?php
// Configuración de la base de datos
$host = 'localhost'; // Servidor de la base de datos
$dbname = 'TFGPeluqueria'; // Nombre de la base de datos
$user = 'root'; // Usuario de MySQL (por defecto en XAMPP es 'root')
$password = ''; // Contraseña de MySQL (por defecto en XAMPP está vacía)

// Conectar a la base de datos
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Opcional: Para verificar que la conexión funciona
    echo "Conexión exitosa <br>"; 
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>