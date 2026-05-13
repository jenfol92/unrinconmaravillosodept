<?php

// Incluimos la sesión (usuario logueado)
require_once __DIR__ . "/../includes/session.php";

// Incluimos el modelo de Producto
require_once __DIR__ . '/../app/modelos/Producto.php';

// Indicamos que vamos a devolver JSON
header('Content-Type: application/json');

// Comprobamos que el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'ok' => false,
        'error' => 'No autorizado'
    ]);
    exit;
}

// Recogemos el ID del producto enviado por AJAX
$producto_id = $_POST['producto_id'] ?? null;

// Validamos que exista
if (!$producto_id) {
    echo json_encode([
        'ok' => false,
        'error' => 'Producto no válido'
    ]);
    exit;
}

// Creamos instancia del modelo
$model = new Producto();

// Alternamos favorito (añadir o quitar)
$resultado = $model->toggleFavorito($_SESSION['usuario_id'], $producto_id);

// Devolvemos respuesta al frontend
echo json_encode([
    'ok' => true,
    'estado' => $resultado // 'guardado' o 'eliminado'
]);

exit;