<?php

// Iniciamos sesión y comprobamos usuario
require_once __DIR__ . "/../includes/session.php";

// Cargamos el controlador del administrador
require_once __DIR__ . "/../app/controladores/admin_controller.php";

// Creamos el controlador
$admin = new AdminController();

// Mostramos el panel admin
$admin->dashboard();