<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Usuario.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], [1, 2])) {
    echo json_encode([
        'ok' => false,
        'error' => 'No autorizado'
    ]);
    exit;
}

$usuario_id = $_GET['usuario_id'] ?? null;

if (!$usuario_id) {
    echo json_encode([
        'ok' => false,
        'error' => 'Usuario no válido'
    ]);
    exit;
}

$model = new Usuario();

echo json_encode([
    'ok' => true,
    'resenas' => $model->obtenerResenasUsuario($usuario_id)
]);

exit;