<?php
require_once __DIR__ . "/../includes/session.php";
require_once __DIR__ . "/../app/controladores/ProductoController.php";


$tienda = new ProductoController();
$tienda->tienda();
