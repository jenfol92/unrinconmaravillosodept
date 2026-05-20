<?php
require_once __DIR__ ."/../app/controladores/ProductoController.php";
require_once __DIR__ . "/../includes/session.php";

$carrito= new ProductoController();
$carrito ->carrito();

?>

