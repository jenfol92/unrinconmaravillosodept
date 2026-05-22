<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/controladores/AuthController.php';

$controller = new AuthController();
$controller->restablecerPassword();