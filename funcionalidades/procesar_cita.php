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
        $userMessage = "Error al procesar la cita. Por favor, inténtalo de nuevo.";

        $_SESSION['error_cita'] = $userMessage;
        header("Location: /TFGPeluqueria/paginas/coger_citas.php");
        exit();
    }
} else {
    header("Location: /TFGPeluqueria/index.php");
}
