<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

function enviarCorreoCita($destinatario, $fecha, $hora) {
    $config = require __DIR__ . '/../config_correo.php';
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($config['username'], 'Peluquería Millan Vega');
        $mail->addAddress($destinatario);

        $mail->isHTML(true);
        $mail->Subject = 'Confirmación de tu cita';
        $mail->Body = "
            Hola,<br><br>
            Tu cita ha sido registrada para el <strong>$fecha</strong> a las <strong>$hora</strong>.<br><br>
            ¡Gracias por elegirnos!
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
        return false;
    }
}

function enviarCorreoCancelacion($destinatario, $fecha, $hora) {
    $config = require __DIR__ . '/../config_correo.php';
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($config['username'], 'Peluquería Millan Vega');
        $mail->addAddress($destinatario);

        $mail->isHTML(true);
        $mail->Subject = 'Cancelación de cita';
        $mail->Body = "
            Hola,<br><br>
            Tu cita para el <strong>$fecha</strong> a las <strong>$hora</strong> ha sido cancelada.<br><br>
            Para reagendar o consultar detalles, contáctanos.<br>
            ¡Gracias!
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
        return false;
    }
}
