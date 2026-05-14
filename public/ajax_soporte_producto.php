<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Soporte.php';
require_once __DIR__ . '/../app/modelos/Producto.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'ok' => false,
        'error' => 'Para que la autora pueda responderte, debes iniciar sesión.'
    ]);
    exit;
}

$producto_id = $_POST['producto_id'] ?? null;
$mensaje = trim($_POST['mensaje'] ?? '');

if (!$producto_id) {
    echo json_encode([
        'ok' => false,
        'error' => 'Producto no encontrado.'
    ]);
    exit;
}

if ($mensaje === '') {
    echo json_encode([
        'ok' => false,
        'error' => 'Debes escribir un mensaje.'
    ]);
    exit;
}

$productoModel = new Producto();
$producto = $productoModel->obtenerProductosID($producto_id);

if (!$producto) {
    echo json_encode([
        'ok' => false,
        'error' => 'Producto no encontrado en la base de datos.'
    ]);
    exit;
}

$asunto = 'Consulta sobre: ' . $producto['titulo'];

$soporteModel = new Soporte();

$soporteModel->crearTicket(
    $_SESSION['usuario_id'],
    $asunto,
    $mensaje
);

echo json_encode([
    'ok' => true,
    'mensaje' => 'Tu consulta se ha enviado correctamente. Podrás ver la respuesta en tu panel.'
]);

exit;