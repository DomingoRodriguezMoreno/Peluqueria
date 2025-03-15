<?php
$nuevo_hash = password_hash('cliente789', PASSWORD_DEFAULT);
echo "Nuevo hash: " . $nuevo_hash;
?>