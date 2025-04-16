<?php
session_start();
include '../funcionalidades/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Cita - Millán Vega</title>
    <link rel="stylesheet" href="/TFGPeluqueria/css/styles.css">
</head>
<body>
    <?php include '../plantillas/navbar.php'; ?>
    
    <section class="contenedor-principal citas">
        <!-- Resumen flotante -->
        <div class="resumen-cita">
            <h3>Resumen de Cita</h3>
            <p>Tiempo total: <span id="tiempo-total">0</span> min</p>
            <p>Coste total: <span id="coste-total">0.00</span> €</p>
            <button onclick="mostrarCalendario()" id="btn-continuar" disabled>Continuar</button>
        </div>

        <!-- Listado de servicios -->
        <div class="lista-servicios">
            <h2>Selecciona tus servicios</h2>
            <br>
            
            <?php
            // Obtener tipos de tratamiento
            $sql = "SELECT * FROM tipos_tratamiento ORDER BY nombre_tipo";
            $result = $conn->query($sql);
            
            while($tipo = $result->fetch(PDO::FETCH_ASSOC)) {
                echo '<details class="desplegable-tipo">';
                echo '<summary>'.$tipo['nombre_tipo'].'</summary>';
                
                // Obtener servicios de este tipo
                $sql_servicios = "SELECT s.* 
                                 FROM servicios s
                                 JOIN servicios_tipos st ON s.id_servicio = st.id_servicio
                                 WHERE st.id_tipo = :id_tipo";
                $stmt = $conn->prepare($sql_servicios);
                $stmt->execute(['id_tipo' => $tipo['id_tipo']]);
                
                echo '<table class="tabla-servicios">';
                while($servicio = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td><input type="checkbox" 
                                    class="servicio-checkbox" 
                                    value="'.$servicio['id_servicio'].'"
                                    data-duracion="'.$servicio['duracion'].'"
                                    data-precio="'.$servicio['precio'].'"></td>';
                    echo '<td>'.$servicio['nombre_servicio'].'</td>';
                    echo '<td>'.$servicio['duracion'].' min</td>';
                    echo '<td>'.$servicio['precio'].' €</td>';
                    echo '</tr>';
                }
                echo '</table></details>';
            }
            ?>
        </div>

        <!-- Formulario de fecha/hora -->
        <div id="formulario-cita" style="display:none;">
            <h2>Selecciona fecha y hora</h2>
            <form action="../funcionalidades/procesar_cita.php" method="POST" id="form-cita">
                <input type="date" name="fecha" id="fecha-cita" required>
                <input type="time" name="hora" id="hora-cita" required>
                <div id="servicios-seleccionados"></div>
                <button type="submit">Confirmar Cita</button>
            </form>
        </div>
    </section>

    <script src="/TFGPeluqueria/js/script.js"></script>
</body>
</html>