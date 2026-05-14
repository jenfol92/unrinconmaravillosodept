<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Producto.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], [1, 2])) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

$resena_id = $_POST['resena_id'] ?? null;
$estado = $_POST['estado'] ?? null;

if (!$resena_id || !$estado) {
    echo json_encode(['ok' => false, 'error' => 'Datos incompletos']);
    exit;
}

$model = new Producto();

if (!$model->cambiarEstadoResena($resena_id, $estado)) {
    echo json_encode(['ok' => false, 'error' => 'Estado no válido']);
    exit;
}

echo json_encode([
    'ok' => true,
    'mensaje' => 'Estado de reseña actualizado.'
]);

exit;