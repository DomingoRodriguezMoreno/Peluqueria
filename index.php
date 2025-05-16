<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peluquería Millan Vega</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <?php
        // Esto debe ser LO PRIMERO en el archivo (sin espacios ni saltos de línea antes)
        session_start();

        // Luego puedes incluir otros archivos
        include './plantillas/navbar.php';
    ?>

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
