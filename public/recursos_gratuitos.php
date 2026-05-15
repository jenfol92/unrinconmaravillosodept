<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/recursosGratuitos.php';

$model = new RecursoGratuito();

$categoriasGratuitas = $model->obtenerCategoriasGratuitas();
$recursosGratuitos = $model->obtenerRecursosGratuitos();

require_once __DIR__ . '/../app/vistas/recursos_gratuitos_view.php';