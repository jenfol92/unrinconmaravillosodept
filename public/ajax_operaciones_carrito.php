<?php

/**
 * Endpoint AJAX para operaciones del carrito.
 * ---------------------------------------------------------
 * Este archivo recibe peticiones desde JavaScript para:
 *
 * - Añadir productos al carrito.
 * - Restar productos del carrito.
 * - Eliminar productos del carrito.
 *
 * La lógica real está en CarritoController.
 */

require_once __DIR__ . '/../app/controladores/carrito_controller.php';

$controller = new CarritoController();
$controller->operacionesAjax();