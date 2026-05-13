<?php

// Cargamos la sesión
require_once __DIR__ . "/../includes/session.php";

// Muy importante: devolver JSON
header('Content-Type: application/json');

// Inicializamos carrito
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Recogemos datos
$id = $_POST['id'] ?? null;
$accion = $_POST['accion'] ?? null;

// Validamos
if (!$id || !$accion) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Datos incompletos',
        'contador_carrito' => array_sum($_SESSION['carrito']),
        'usuario_logueado' => isset($_SESSION['usuario_id'])
    ]);
    exit;
}

// Mensaje por defecto
$mensaje = 'Operación realizada correctamente';

// Procesamos acción
switch ($accion) {

    case 'add_carrito':
        $_SESSION['carrito'][$id] = ($_SESSION['carrito'][$id] ?? 0) + 1;
        $mensaje = 'Producto añadido al carrito';
        break;

    case 'restar_carrito':
        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]--;

            if ($_SESSION['carrito'][$id] <= 0) {
                unset($_SESSION['carrito'][$id]);
            }
        }
        $mensaje = 'Producto actualizado';
        break;

    case 'eliminar_carrito':
        unset($_SESSION['carrito'][$id]);
        $mensaje = 'Producto eliminado del carrito';
        break;

    default:
        echo json_encode([
            'status' => 'error',
            'message' => 'Acción no válida',
            'contador_carrito' => array_sum($_SESSION['carrito']),
            'usuario_logueado' => isset($_SESSION['usuario_id'])
        ]);
        exit;
}

// Devolvemos respuesta JSON limpia
echo json_encode([
    'status' => 'success',
    'message' => $mensaje,
    'contador_carrito' => array_sum($_SESSION['carrito']),
    'usuario_logueado' => isset($_SESSION['usuario_id'])
]);
exit;