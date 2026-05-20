<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/controladores/admin_controller.php';

$controller = new AdminController();
$controller->eliminarRecursoGratuitoAjax();