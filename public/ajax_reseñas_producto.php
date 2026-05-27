<?php

/**
 * AJAX: obtener reseñas de un producto
 * ---------------------------------------------------------
 * Punto de entrada público para consultar, desde el panel de
 * administración, las reseñas asociadas a un producto concreto.
 *
 * Este archivo NO contiene lógica de negocio ni SQL.
 *
 * Flujo:
 * public/ajax_reseñas_producto.php
 * → ResenaController::obtenerResenasProductoAdminAjax()
 * → Producto::obtenerResenasAdminPorProducto()
 *
 * Entrada esperada por GET:
 * - producto_id: ID del producto del que se quieren consultar reseñas.
 *
 * Respuesta:
 * - JSON con ok y resenas, o error.
 */

require_once __DIR__ . '/../app/controladores/resena_controller.php';

/**
 * Instanciamos el controlador de reseñas.
 */
$controller = new ResenaController();

/**
 * Ejecutamos la acción AJAX correspondiente.
 */
$controller->obtenerResenasProductoAdminAjax();