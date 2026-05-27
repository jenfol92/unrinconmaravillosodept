<?php

/**
 * AJAX: crear ticket de soporte
 * ---------------------------------------------------------
 * Punto de entrada público para crear una nueva consulta de soporte
 * desde el panel del usuario.
 *
 * Este archivo no contiene lógica de negocio ni SQL.
 *
 * Flujo:
 * public/ajax_soporte_crear.php
 * → SoporteController::crearTicketAjax()
 * → Soporte::crearTicket()
 *
 * Entrada esperada por POST:
 * - asunto
 * - mensaje
 *
 * Respuesta:
 * - JSON con ok, ticket_id y mensaje, o error.
 */

require_once __DIR__ . '/../app/controladores/soporte_controller.php';

/**
 * Instanciamos el controlador de soporte.
 */
$controller = new SoporteController();

/**
 * Ejecutamos la acción AJAX correspondiente.
 */
$controller->crearTicketAjax();