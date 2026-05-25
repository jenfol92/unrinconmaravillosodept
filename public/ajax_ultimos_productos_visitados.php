<?php

/**
 * AJAX - Productos recientes
 * ---------------------------------------------------------
 * Este endpoint lee la cookie productos_recientes y devuelve
 * los datos reales de esos productos desde la base de datos.
 *
 * La cookie solo guarda IDs.
 * Los datos visibles, como título, imagen o precio, se obtienen
 * siempre desde MySQL.
 */

require_once __DIR__ . '/../app/modelos/Producto.php';

header('Content-Type: application/json; charset=utf-8');

/**
 * Leemos la cookie.
 * Si no existe, usamos un array JSON vacío.
 */
$cookie = $_COOKIE['productos_recientes'] ?? '[]';

/**
 * Convertimos el JSON de la cookie a array PHP.
 */
$ids = json_decode($cookie, true);

if (!is_array($ids)) {
    $ids = [];
}

/**
 * Seguridad:
 * - Convertimos todo a enteros.
 * - Eliminamos IDs inválidos.
 * - Eliminamos duplicados.
 * - Limitamos a 5 productos.
 */
$ids = array_map('intval', $ids);

$ids = array_values(array_filter($ids, function ($id) {
    return $id > 0;
}));

$ids = array_values(array_unique($ids));

$ids = array_slice($ids, 0, 5);

/**
 * Si no hay productos recientes, devolvemos array vacío.
 */
if (empty($ids)) {
    echo json_encode([
        'ok' => true,
        'productos' => []
    ]);
    exit;
}

try {
    $productoModel = new Producto();

    /**
     * Reutilizamos la función existente del modelo Producto.
     * Como le pasamos un array, devuelve varios productos.
     */
    $productos = $productoModel->obtenerProductosID($ids);

    /**
     * Reordenamos los productos según el orden de la cookie.
     * Así el último visitado aparece primero.
     */
    $productosPorId = [];

    foreach ($productos as $producto) {
        $productosPorId[(int)$producto['id']] = $producto;
    }

    $productosOrdenados = [];

    foreach ($ids as $id) {
        if (isset($productosPorId[$id])) {
            $productosOrdenados[] = $productosPorId[$id];
        }
    }

    echo json_encode([
        'ok' => true,
        'productos' => $productosOrdenados
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'ok' => false,
        'error' => 'Error cargando productos recientes.'
    ]);
    exit;
}