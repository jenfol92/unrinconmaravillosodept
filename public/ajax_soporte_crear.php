<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Soporte.php';

header('Content-Type: application/json');

// Comprobamos sesión
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'ok' => false,
        'error' => 'No autorizado'
    ]);
    exit;
}

// Recogemos datos
$asunto = trim($_POST['asunto'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

// Validamos campos
if ($asunto === '' || $mensaje === '') {
    echo json_encode([
        'ok' => false,
        'error' => 'Completa asunto y mensaje'
    ]);
    exit;
}

// Creamos ticket
$soporte = new Soporte();

$ticket_id = $soporte->crearTicket(
    $_SESSION['usuario_id'],
    $asunto,
    $mensaje
);

// Devolvemos respuesta
echo json_encode([
    'ok' => true,
    'ticket_id' => $ticket_id,
    'mensaje' => 'Tu consulta se ha enviado correctamente.'
]);

exit;