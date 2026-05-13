<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Soporte.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], [1, 2])) {
    echo json_encode([
        'ok' => false,
        'error' => 'No autorizado'
    ]);
    exit;
}

$ticket_id = $_GET['ticket_id'] ?? null;

if (!$ticket_id) {
    echo json_encode([
        'ok' => false,
        'error' => 'Ticket no válido'
    ]);
    exit;
}

$model = new Soporte();

$mensajes = $model->obtenerMensajesTicket($ticket_id);

echo json_encode([
    'ok' => true,
    'mensajes' => $mensajes
]);

exit;