<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST['servicios'])) {
            throw new Exception("Debes seleccionar al menos un servicio.");
        }

	$id_cliente = $_POST['id_cliente'];
	$es_empleado = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'empleado';

        $conn->beginTransaction();
        
        // Insertar cita directamente
        $stmt = $conn->prepare("INSERT INTO citas 
            (id_cliente, fecha_cita, hora_inicio, estado, duracion_total, precio_final)
            VALUES (?, ?, ?, 'reservada', 0, 0)");
        
        $stmt->execute([
            $_POST['id_cliente'],
            $_POST['fecha'],
            $_POST['hora']
        ]);
        
        $id_cita = $conn->lastInsertId();
        
        // Insertar servicios
        foreach ($_POST['servicios'] as $id_servicio) {
            $stmt = $conn->prepare("INSERT INTO citas_servicios 
                (id_cita, id_servicio, id_empleado)
                VALUES (?, ?, (SELECT dni FROM empleados WHERE id_rol IN 
                    (SELECT id_rol FROM roles_servicios WHERE id_servicio = ?) LIMIT 1))");
            
            $stmt->execute([$id_cita, $id_servicio, $id_servicio]);
        }
        
        $conn->commit();

	// Obtener correo del cliente
	$stmt = $conn->prepare("SELECT email FROM clientes WHERE id_cliente = ?");
	$stmt->execute([$id_cliente]);
	$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($cliente && !empty($cliente['email'])) {
    		require_once 'enviar_correo.php';
    		enviarCorreoCita($cliente['email'], $_POST['fecha'], $_POST['hora']);
	}

	
	if ($es_empleado) {
	        header("Location: /TFGPeluqueria/paginas/citas.php?success=1");
	} else { 
		header("Location: /TFGPeluqueria/paginas/panel_cliente.php?success=1");
	}
    } catch (PDOException $e) {
        $conn->rollBack();

        // Extraer código de error MySQL
        $errorCode = $e->errorInfo[1];
        $errorMessage = $e->getMessage();

        // Mensajes personalizados
        $userMessage = "Lo sentimos, no se ha podido reservar su cita para el dia y hora especificados. Por favor, pruebe otros horarios/dias.";

        // Analizar el tipo de error
        if (strpos($e->getMessage(), 'No hay empleados disponibles') !== false) {
            $userMessage = "No hay profesionales disponibles para el servicio seleccionado. Pruebe otra hora/fecha.";
        } 
        elseif (strpos($e->getMessage(), 'Horario no válido') !== false) {
            $userMessage = "El horario debe ser entre 9:00-14:00 o 16:00-19:00 horas.";
        }
        elseif (strpos($e->getMessage(), 'precio final no coincide') !== false) {
            $userMessage = "Error en el cálculo del precio. Verifique los servicios seleccionados.";
        }
        elseif ($e->errorInfo[1] == 1062) { // Código para duplicados
            $userMessage = "Ya existe una cita reservada en ese horario.";
        }
        elseif (strpos($e->getMessage(), 'Debes seleccionar al menos un servicio') !== false) {
            $userMessage = $e->getMessage(); // Mensaje directo de la validación inicial
        }

        $_SESSION['error_cita'] = $userMessage;

        // Redirigir manteniendo el id_cliente si es empleado
        if ($es_empleado) {
            header("Location: /TFGPeluqueria/paginas/coger_citas.php?id_cliente=" . $id_cliente); // Añade el parámetro
        } else { 
            header("Location: /TFGPeluqueria/paginas/coger_citas.php");
        }

        exit();
    }
} else {
    header("Location: /TFGPeluqueria/index.php");
}
