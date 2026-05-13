<?php
require_once __DIR__ . "/../includes/session.php";
require_once __DIR__ . '/../app/controladores/ProductoController.php';


$controller = new ProductoController();
$controller->home();
?>