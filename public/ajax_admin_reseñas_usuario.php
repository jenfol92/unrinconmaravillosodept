<?php

/**
 * AJAX: obtener reseñas de un usuario
 * ---------------------------------------------------------
 * Punto de entrada público para consultar, desde el panel de
 * administración, las reseñas realizadas por un usuario concreto.
 *
 * Este archivo NO contiene lógica de negocio ni SQL.
 *
 * Flujo:
 * public/ajax_admin_reseñas_usuario.php
 * → ResenaController::obtenerResenasUsuarioAjax()
 * → Usuario::obtenerResenasUsuario()
 *
 * Entrada esperada por GET:
 * - usuario_id: ID del usuario del que se quieren consultar reseñas.
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
$controller->obtenerResenasUsuarioAjax();