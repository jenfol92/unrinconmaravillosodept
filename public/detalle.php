<?php
require_once __DIR__ . "/../includes/session.php";
require_once __DIR__ ."/../app/controladores/ProductoController.php";

$detalle=new ProductoController();
$detalle-> detalle();

?>