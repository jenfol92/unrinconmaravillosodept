<?php

/**
 * AJAX: responder ticket de soporte desde el usuario
 * ---------------------------------------------------------
 * Punto de entrada público para que un usuario logueado pueda
 * responder a una conversación de soporte desde su panel.
 *
 * Este archivo no contiene lógica de negocio ni SQL.
 *
 * Flujo:
 * public/ajax_usuario_soporte_responder.php
 * → SoporteController::responderTicketUsuarioAjax()
 * → Soporte::enviarMensaje()
 *
 * Entrada esperada por POST:
 * - ticket_id
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
$controller->responderTicketUsuarioAjax();