<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Producto.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], [1, 2])) {
    echo json_encode([
        'ok' => false,
        'error' => 'No autorizado'
    ]);
    exit;
}

$producto_id = $_GET['producto_id'] ?? null;

if (!$producto_id) {
    echo json_encode([
        'ok' => false,
        'error' => 'Producto no válido'
    ]);
    exit;
}

$model = new Producto();

$resenas = $model->obtenerResenasAdminPorProducto($producto_id);

echo json_encode([
    'ok' => true,
    'resenas' => $resenas
]);

exit;