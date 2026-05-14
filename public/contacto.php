<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/controladores/ContactoController.php';

$controller = new ContactoController();
$controller->index();