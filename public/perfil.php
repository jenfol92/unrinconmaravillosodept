<?php
// Iniciamos sesión
require_once __DIR__ . '/../includes/session.php';

// Cargamos controlador
require_once __DIR__ . '/../app/controladores/usuario_controller.php';

// Ejecutamos el método perfil
$controller = new UsuarioController();
$controller->perfil();