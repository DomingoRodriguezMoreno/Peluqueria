<?php
session_start();
session_destroy(); // Destruye la sesión
header('Location: /TFGPeluqueria/index.php'); // Redirige al inicio
exit();
?>