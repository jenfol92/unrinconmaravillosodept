<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Producto.php';

header('Content-Type: application/json; charset=utf-8');

// Solo admin o rol autorizado
if (
    empty($_SESSION['usuario_id']) ||
    !in_array((int)($_SESSION['rol'] ?? 0), [1, 2], true)
) {
    echo json_encode([
        'ok' => false,
        'error' => 'No autorizado'
    ]);
    exit;
}

$usuarioId = isset($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : 0;

if ($usuarioId <= 0) {
    echo json_encode([
        'ok' => false,
        'error' => 'Usuario no válido'
    ]);
    exit;
}

try {
    $productoModel = new Producto();

    $favoritos = $productoModel->obtenerProductosFavoritos($usuarioId);

    echo json_encode([
        'ok' => true,
        'favoritos' => $favoritos
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'ok' => false,
        'error' => 'Error cargando favoritos: ' . $e->getMessage()
    ]);
    exit;
}