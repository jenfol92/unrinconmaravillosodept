<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Soporte.php';

header('Content-Type: application/json');

// Comprobar admin/profesor
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], [1, 2])) {
    echo json_encode([
        'ok' => false,
        'error' => 'No autorizado'
    ]);
    exit;
}

// Recoger datos
$ticket_id = $_POST['ticket_id'] ?? null;
$mensaje = trim($_POST['mensaje'] ?? '');

if (!$ticket_id || $mensaje === '') {
    echo json_encode([
        'ok' => false,
        'error' => 'Datos incompletos'
    ]);
    exit;
}

// Guardar mensaje
$model = new Soporte();

$remitenteNombre = $_SESSION['nombre_usuario'] ?? 'Administrador';

$model->enviarMensaje($ticket_id, 'admin', $mensaje, $remitenteNombre);

echo json_encode([
    'ok' => true,
    'mensaje' => 'Respuesta enviada correctamente.'
]);

exit;