<?php
session_start(); //Inicia sesion para poder acceder a las variables de sesion

unset($_SESSION); //Elimina las variables de sesion

session_destroy(); // Destruye la sesión
header('Location: /TFGPeluqueria/index.php'); // Redirige al inicio
exit();
?>
