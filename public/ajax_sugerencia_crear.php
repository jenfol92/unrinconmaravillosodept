<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Soporte.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'ok' => false,
        'error' => 'Debes iniciar sesión para enviar sugerencias.'
    ]);
    exit;
}

$mensaje = trim($_POST['mensaje'] ?? '');

if ($mensaje === '') {
    echo json_encode([
        'ok' => false,
        'error' => 'Debes escribir una sugerencia.'
    ]);
    exit;
}

$model = new Soporte();

$model->crearSugerencia($_SESSION['usuario_id'], $mensaje);

echo json_encode([
    'ok' => true,
    'mensaje' => 'Tu sugerencia se ha enviado correctamente.'
]);

exit;
