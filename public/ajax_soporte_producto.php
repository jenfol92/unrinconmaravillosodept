<?php

/**
 * AJAX: crear consulta de soporte sobre producto
 * ---------------------------------------------------------
 * Punto de entrada público para enviar una consulta relacionada
 * con un producto concreto.
 *
 * Este archivo no contiene lógica de negocio ni SQL.
 *
 * Flujo:
 * public/ajax_soporte_producto.php
 * → SoporteController::crearTicketProductoAjax()
 * → Producto::obtenerProductosID()
 * → Soporte::crearTicket()
 *
 * Entrada esperada por POST:
 * - producto_id
 * - mensaje
 *
 * Respuesta:
 * - JSON con ok y mensaje, o error.
 */

require_once __DIR__ . '/../app/controladores/soporte_controller.php';

/**
 * Instanciamos el controlador de soporte.
 */
$controller = new SoporteController();

/**
 * Ejecutamos la acción AJAX correspondiente.
 */
$controller->crearTicketProductoAjax();