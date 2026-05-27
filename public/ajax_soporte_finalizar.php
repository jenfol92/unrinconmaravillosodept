<?php

/**
 * AJAX: finalizar ticket de soporte
 * ---------------------------------------------------------
 * Punto de entrada público para finalizar una consulta de soporte
 * desde el panel del usuario.
 *
 * Este archivo no contiene lógica de negocio ni SQL.
 *
 * Flujo:
 * public/ajax_soporte_finalizar.php
 * → SoporteController::finalizarTicketAjax()
 * → Soporte::finalizarTicket()
 *
 * Entrada esperada por POST:
 * - ticket_id
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
$controller->finalizarTicketAjax();