<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Soporte.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

$ticket_id = $_POST['ticket_id'] ?? null;
$mensaje = trim($_POST['mensaje'] ?? '');

if (!$ticket_id || $mensaje === '') {
    echo json_encode(['ok' => false, 'error' => 'Datos incompletos']);
    exit;
}

$model = new Soporte();
$model->enviarMensaje($ticket_id, 'usuario', $mensaje, $_SESSION['nombre_usuario'] ?? 'Usuario');
$ok = $model->enviarMensaje($ticket_id, 'usuario', $mensaje,  $_SESSION['nombre_usuario'] ?? 'Usuario');

if (!$ok) {
    echo json_encode([
        'ok' => false,
        'error' => 'Esta consulta está cerrada y no admite nuevos mensajes.'
    ]);
    exit;
}

echo json_encode(['ok' => true, 'mensaje' => 'Respuesta enviada correctamente.']);
exit;