<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/TFGPeluqueria/funcionalidades/conexion.php';

$duracion = $_GET['duracion'] ?? 0;
$fecha = $_GET['fecha'] ?? null;

header('Content-Type: application/json');

try {
    if ($fecha) {
        // Obtener horarios libres para fecha específica
        $stmt = $conn->prepare("SELECT hora_inicio, hora_fin FROM citas WHERE fecha_cita = ?");
        $stmt->execute([$fecha]);
        $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $horariosLibres = calcularHorariosLibres($citas, $duracion);
        echo json_encode(['horarios' => $horariosLibres]);
    } else {
        // Obtener días disponibles para el mes actual
        $dias = generarCalendario($conn, $duracion);
        echo json_encode($dias);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

function generarCalendario($conn, $duracion) {
    $mesActual = date('Y-m');
    $dias = [];
    
    // Obtener todos los días del mes
    $fechaInicio = new DateTime("first day of $mesActual");
    $fechaFin = new DateTime("last day of $mesActual");
    
    while ($fechaInicio <= $fechaFin) {
        $fechaActual = $fechaInicio->format('Y-m-d');
        $dias[$fechaActual] = esDiaDisponible($conn, $fechaActual, $duracion);
        $fechaInicio->modify('+1 day');
    }
    
    return $dias;
}

function esDiaDisponible($conn, $fecha, $duracion) {
    // Verificar si es día pasado
    if (strtotime($fecha) < strtotime(date('Y-m-d'))) return false;
    
    // Verificar horario comercial
    $horarioManana = generarIntervalos('09:00', '14:00', $duracion);
    $horarioTarde = generarIntervalos('16:00', '19:00', $duracion);
    $tramosPosibles = array_merge($horarioManana, $horarioTarde);
    
    // Verificar citas existentes
    $stmt = $conn->prepare("SELECT hora_inicio, hora_fin FROM citas WHERE fecha_cita = ?");
    $stmt->execute([$fecha]);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($tramosPosibles as $tramo) {
        if (estaTramoLibre($tramo['inicio'], $tramo['fin'], $citas)) {
            return true;
        }
    }
    return false;
}

function calcularHorariosLibres($citas, $duracion) {
    $horarioManana = generarIntervalos('09:00', '14:00', $duracion);
    $horarioTarde = generarIntervalos('16:00', '19:00', $duracion);
    $tramosPosibles = array_merge($horarioManana, $horarioTarde);
    
    $tramosLibres = [];
    foreach ($tramosPosibles as $tramo) {
        if (estaTramoLibre($tramo['inicio'], $tramo['fin'], $citas)) {
            $tramosLibres[] = [
                'inicio' => $tramo['inicio'],
                'fin' => $tramo['fin']
            ];
        }
    }
    return $tramosLibres;
}

function generarIntervalos($horaInicio, $horaFin, $duracion) {
    $intervalos = [];
    $horaActual = new DateTime($horaInicio);
    $horaFinal = new DateTime($horaFin);
    
    while ($horaActual <= $horaFinal) {
        $horaFinIntervalo = clone $horaActual;
        $horaFinIntervalo->modify("+$duracion minutes");
        
        if ($horaFinIntervalo > $horaFinal) break;
        
        $intervalos[] = [
            'inicio' => $horaActual->format('H:i'),
            'fin' => $horaFinIntervalo->format('H:i')
        ];
        
        $horaActual->modify('+15 minutes'); // Intervalos cada 15min
    }
    return $intervalos;
}

function estaTramoLibre($inicio, $fin, $citas) {
    foreach ($citas as $cita) {
        $citaInicio = strtotime($cita['hora_inicio']);
        $citaFin = strtotime($cita['hora_fin']);
        $tramoInicio = strtotime($inicio);
        $tramoFin = strtotime($fin);
        
        if ($tramoInicio < $citaFin && $tramoFin > $citaInicio) {
            return false;
        }
    }
    return true;
}