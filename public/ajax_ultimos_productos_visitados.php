<?php

/**
 * AJAX - ÚLTIMOS PRODUCTOS VISITADOS
 * ---------------------------------------------------------
 * Endpoint público usado por tienda.js para cargar el widget
 * "Últimos vistos".
 *
 * Este archivo no contiene lógica de negocio.
 * Solo delega en ProductoController.
 */

require_once __DIR__ . '/../app/controladores/ProductoController.php';

$controller = new ProductoController();
$controller->ultimosVisitadosAjax();