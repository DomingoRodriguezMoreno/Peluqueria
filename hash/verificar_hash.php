<?php
// Hash almacenado en la base de datos
$hash_almacenado = '$2y$10$j7zMPVbssrj4.wdaDDDBa.Z.u1l9VecAccwRWOAjlaRVP3ZG4ckYy';

// Contraseña en texto plano proporcionada por el usuario
$password = 'doromo96'; // Contraseña a verificar

if (password_verify($password, $hash_almacenado)) {
    echo "¡Coincide!";
} else {
    echo "NO coincide. Regenera el hash.";
}
?>