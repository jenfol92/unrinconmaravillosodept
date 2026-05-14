<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Producto.php';

header('Content-Type: application/json');

// Comprobar login
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'ok' => false,
        'error' => 'Debes iniciar sesión para publicar una reseña.'
    ]);
    exit;
}

// Recoger datos
$producto_id = $_POST['producto_id'] ?? null;
$puntuacion = $_POST['puntuacion'] ?? null;
$comentario = trim($_POST['comentario'] ?? '');

// Validar producto
if (!$producto_id) {
    echo json_encode([
        'ok' => false,
        'error' => 'Producto no válido.'
    ]);
    exit;
}

// Validar puntuación
if (!$puntuacion || $puntuacion < 1 || $puntuacion > 5) {
    echo json_encode([
        'ok' => false,
        'error' => 'La puntuación debe estar entre 1 y 5.'
    ]);
    exit;
}

// Validar comentario
if ($comentario === '') {
    echo json_encode([
        'ok' => false,
        'error' => 'El comentario no puede estar vacío.'
    ]);
    exit;
}

$model = new Producto();

// Comprobar si el usuario puede reseñar
if (!$model->usuarioPuedeResenar($_SESSION['usuario_id'])) {
    echo json_encode([
        'ok' => false,
        'error' => 'No tienes permitido publicar nuevas reseñas.'
    ]);
    exit;
}

// Guardar reseña
$model->guardarResena(
    $_SESSION['usuario_id'],
    $producto_id,
    $puntuacion,
    $comentario
);

echo json_encode([
    'ok' => true,
    'mensaje' => 'Reseña guardada correctamente.'
]);

exit;