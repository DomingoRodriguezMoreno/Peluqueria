<?php
// Hash almacenado en la base de datos
$hash_almacenado = '$2y$10$fG3kO.EVOmIOnlGAgO8MveRrtgDTCupLWodluTZtWoL06CWsUzJLG';

// Contraseña en texto plano proporcionada por el usuario
$password = 'cliente789'; // Contraseña a verificar

if (password_verify($password, $hash_almacenado)) {
    echo "¡Coincide!";
} else {
    echo "NO coincide. Regenera el hash.";
}
?>