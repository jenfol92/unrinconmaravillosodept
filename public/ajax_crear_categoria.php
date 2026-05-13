<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Producto.php';

header('Content-Type: application/json');

// Solo admin o profesor
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], [1, 2])) {
    echo json_encode([
        'ok' => false,
        'error' => 'No autorizado'
    ]);
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');

if ($nombre === '') {
    echo json_encode([
        'ok' => false,
        'error' => 'El nombre de la categoría es obligatorio'
    ]);
    exit;
}

$model = new Producto();

$categoria = $model->crearCategoria($nombre);

echo json_encode([
    'ok' => true,
    'categoria' => $categoria
]);
exit;