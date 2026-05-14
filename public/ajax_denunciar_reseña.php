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

$resena_id = $_POST['resena_id'] ?? null;
$usuario_id = $_POST['usuario_id'] ?? null;

if (!$resena_id || !$usuario_id) {
    echo json_encode([
        'ok' => false,
        'error' => 'Datos incompletos'
    ]);
    exit;
}

$model = new Producto();

$model->denunciarResenaYBloquearUsuario($resena_id, $usuario_id);

echo json_encode([
    'ok' => true,
    'mensaje' => 'Reseña denunciada y usuario bloqueado para nuevas reseñas.'
]);

exit;