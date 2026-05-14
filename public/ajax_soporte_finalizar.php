<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Soporte.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

$ticket_id = $_POST['ticket_id'] ?? null;

if (!$ticket_id) {
    echo json_encode(['ok' => false, 'error' => 'Ticket no válido']);
    exit;
}

$model = new Soporte();
$model->finalizarTicket($ticket_id, $_SESSION['usuario_id']);

echo json_encode(['ok' => true, 'mensaje' => 'Consulta finalizada correctamente.']);
exit;