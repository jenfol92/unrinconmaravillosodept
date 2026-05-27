<?php

/**
 * Endpoint AJAX para filtrar productos/recursos en la tienda pública.
 * ---------------------------------------------------------
 * Este archivo es llamado desde JavaScript, probablemente desde tienda.js.
 *
 * Su única responsabilidad es:
 * - Cargar el ProductoController.
 * - Ejecutar el método que devuelve los productos filtrados en JSON.
 */

require_once __DIR__ . '/../app/controladores/ProductoController.php';

$controller = new ProductoController();
$controller->filtrarRecursosAjax();