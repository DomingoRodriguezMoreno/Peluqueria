<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peluquería Millan Vega</title>
    <link rel="icon" type="image/png" href="/TFGPeluqueria/imagenes/Logo2.png">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <?php
        session_start();

        include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/plantillas/navbar.php'; // Incluir la barra de navegación
    ?>

    <?php if (isset($_SESSION['exito_registro'])): ?>
        <div class="mensaje-exito">
            <?php
            echo $_SESSION['exito_registro'];
            unset($_SESSION['exito_registro']);
            ?>
        </div>
    <?php elseif (isset($_SESSION['exito_login'])): ?>
        <div class="mensaje-exito">
            <?php
            echo $_SESSION['exito_login'];
            unset($_SESSION['exito_login']);
            ?>
        </div>
    <?php endif; ?>

    <!-- Sección contenedor-principal con Introducción -->
    <section class="contenedor-principal">
        <div class="intro-texto">
            <img src="imagenes/Logo2.png" alt="Logo Peluquería">
            <h1>¡Bienvenidos a Millán & Vega!</h1>
            <p>
              Donde el estilo cobra vida y la confianza se renueva con cada corte. 
              En nuestro espacio, no solo transformamos cabellos, sino que creamos 
              experiencias únicas que reflejan tu esencia. Con un equipo de expertos 
              apasionados por la tendencia y la tradición, en Millán Vega nos dedicamos
              a realzar tu belleza natural, ofreciéndote un servicio personalizado y de alta calidad.
            </p>
        </div>
        
        <!-- Redes Sociales -->
        <div class="redes-sociales">
            <a href="https://www.facebook.com/peluqueriavillamarta/" target="_blank"><img src="/TFGPeluqueria/imagenes/facebook.png" alt="Facebook"></a>
        </div>
    </section>


</body>
</html>
