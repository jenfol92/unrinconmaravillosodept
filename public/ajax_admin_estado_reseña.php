<?php

/**
 * AJAX: cambiar estado de una reseña
 * ---------------------------------------------------------
 * Punto de entrada público para actualizar el estado de una
 * reseña desde el panel de administración.
 *
 * Este archivo NO contiene lógica de negocio ni SQL.
 *
 * Flujo:
 * public/ajax_admin_estado_resena.php
 * → ResenaController::cambiarEstadoResenaAjax()
 * → Producto::cambiarEstadoResena()
 *
 * Entrada esperada por POST:
 * - resena_id: ID de la reseña.
 * - estado: nuevo estado de la reseña.
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
$controller->cambiarEstadoResenaAjax();