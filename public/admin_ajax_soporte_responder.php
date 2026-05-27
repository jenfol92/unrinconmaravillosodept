<?php

/**
 * AJAX: responder ticket de soporte desde administración
 * ---------------------------------------------------------
 * Punto de entrada público para responder a un ticket desde
 * el panel de administración.
 *
 * Este archivo no contiene lógica de negocio ni SQL.
 *
 * Flujo:
 * public/admin_ajax_soporte_responder.php
 * → SoporteController::responderTicketAdminAjax()
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
$controller->responderTicketAdminAjax();