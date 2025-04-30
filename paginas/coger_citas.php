<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';
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
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/plantillas/navbar.php'; ?>
    
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
            $sql = "SELECT * FROM tipos_tratamiento ORDER BY nombre_tipo";
            $result = $conn->query($sql);
            $tipos = $result->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($tipos as $tipo): ?>
                <details class="desplegable-tipo">
                    <summary><?= htmlspecialchars($tipo['nombre_tipo']) ?></summary>
                    
                    <?php
                    $sql_servicios = "SELECT s.* 
                                    FROM servicios s
                                    JOIN servicios_tipos st ON s.id_servicio = st.id_servicio
                                    WHERE st.id_tipo = :id_tipo";
                    $stmt = $conn->prepare($sql_servicios);
                    $stmt->execute(['id_tipo' => $tipo['id_tipo']]);
                    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
                    <table class="tabla-servicios">
                        <?php foreach ($servicios as $servicio): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" 
                                           class="servicio-checkbox"
                                           value="<?= htmlspecialchars($servicio['id_servicio']) ?>"
                                           data-duracion="<?= htmlspecialchars($servicio['duracion']) ?>"
                                           data-precio="<?= htmlspecialchars($servicio['precio']) ?>">
                                </td>
                                <td><?= htmlspecialchars($servicio['nombre_servicio']) ?></td>
                                <td><?= htmlspecialchars($servicio['duracion']) ?> min</td>
                                <td><?= number_format($servicio['precio'], 2) ?> €</td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </details>
            <?php endforeach; ?>
        </div>

        <!-- Formulario de fecha/hora -->
        <div id="citaModal" class="modal">
            <div class="modal-contenido">
                <span class="cerrar" onclick="cerrarCitaModal()">&times;</span>
                <h2>Selecciona fecha y hora</h2>
                
                <!-- Nuevos elementos añadidos -->
                <div class="calendario-container">
                    <h3>Selecciona una fecha</h3>
                    <div id="calendario"></div>
                </div>

                <div id="horarios-container" style="display:none; margin-top:20px;">
                    <h3>Horarios disponibles</h3>
                    <div id="horarios"></div>
                </div>

                <form action="/TFGPeluqueria/funcionalidades/procesar_cita.php" method="POST" id="form-cita">
                    <input type="hidden" name="fecha" id="fecha-seleccionada">
                    <input type="hidden" name="hora" id="hora-seleccionada">
                    <div id="servicios-seleccionados"></div>
                    <button type="submit" style="margin-top:20px;">Confirmar Cita</button>
                </form>
            </div>
        </div>
        
    </section>
    <script src="/TFGPeluqueria/js/script.js"></script>
</body>
</html>