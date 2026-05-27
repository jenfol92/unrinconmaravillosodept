<?php

/**
 * AJAX: guardar reseña de producto
 * ---------------------------------------------------------
 * Punto de entrada público para que un usuario logueado pueda
 * publicar una reseña sobre un producto.
 *
 * Este archivo NO contiene lógica de negocio ni SQL.
 *
 * Flujo:
 * public/ajax_guardar_reseña.php
 * → ResenaController::guardarResenaAjax()
 * → Producto::usuarioPuedeResenar()
 * → Producto::guardarResena()
 *
 * Entrada esperada por POST:
 * - producto_id: ID del producto reseñado.
 * - puntuacion: valoración entre 1 y 5.
 * - comentario: texto de la reseña.
 *
 * Respuesta:
 * - JSON con ok y mensaje, o error.
 */

require_once __DIR__ . '/../app/controladores/resena_controller.php';

/**
 * Instanciamos el controlador de reseñas.
 */
$controller = new ResenaController();

/**
 * Ejecutamos la acción AJAX correspondiente.
 */
$controller->guardarResenaAjax();