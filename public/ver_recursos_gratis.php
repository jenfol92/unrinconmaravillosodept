<?php

require_once __DIR__ . '/../app/modelos/recursosGratuitos.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: /UNRINCONDEPT/public/recursos_gratuitos.php');
    exit;
}

$model = new RecursoGratuito();

$recurso = $model->obtenerRecursoPorId($id);

if (!$recurso) {
    header('Location: /UNRINCONDEPT/public/recursos_gratuitos.php');
    exit;
}

$model->incrementarClicks($id);

header('Location: ' . $recurso['url_drive']);
exit;