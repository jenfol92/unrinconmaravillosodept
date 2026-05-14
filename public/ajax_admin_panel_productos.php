<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Producto.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], [1, 2])) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

$model = new Producto();

$pagina = (int)($_GET['pagina'] ?? 1);
$limite = 10;
$offset = ($pagina - 1) * $limite;

$busqueda = trim($_GET['busqueda'] ?? '');
$estado = trim($_GET['estado'] ?? '');
$categoria = $_GET['categoria'] ?? '';

$categorias = $categoria !== '' ? [$categoria] : [];
$niveles = [];

$resultado = $model->obtenerProductosAdmin(
    $categorias,
    $niveles,
    $busqueda,
    $estado,
    $limite,
    $offset
);

echo json_encode([
    'ok' => true,
    'productos' => $resultado['productos'],
    'total_paginas' => $resultado['total_paginas'],
    'pagina_actual' => $pagina
]);

exit;