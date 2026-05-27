<?php

/**
 * Página pública del carrito.
 * ---------------------------------------------------------
 * Este archivo muestra el contenido del carrito.
 *
 * La lógica está en CarritoController::index().
 */

require_once __DIR__ . '/../app/controladores/carrito_controller.php';

$controller = new CarritoController();
$controller->index();