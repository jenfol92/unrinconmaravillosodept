<?php

require_once __DIR__ . '/../app/modelos/recursosGratuitos.php';

header('Content-Type: application/json');

$model = new RecursoGratuito();

$categorias = $_GET['categorias'] ?? '';
$busqueda = trim($_GET['busqueda'] ?? '');

$categorias = $categorias ? json_decode($categorias, true) : [];

if (!is_array($categorias)) {
    $categorias = [];
}

$recursos = $model->obtenerRecursosGratuitos($categorias, $busqueda);

echo json_encode([
    'ok' => true,
    'recursos' => $recursos,
    'total' => count($recursos)
]);

exit;