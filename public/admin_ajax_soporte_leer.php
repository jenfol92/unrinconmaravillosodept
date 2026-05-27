<?php

/**
 * AJAX: obtener mensajes de un ticket de soporte
 * ---------------------------------------------------------
 * Punto de entrada público para cargar los mensajes de un ticket
 * desde el panel de administración.
 *
 * Este archivo no contiene lógica de negocio ni SQL.
 *
 * Flujo:
 * public/admin_ajax_soporte_leer.php
 * → SoporteController::leerTicketAdminAjax()
 * → Soporte::obtenerMensajesTicket()
 *
 * Entrada esperada por GET:
 * - ticket_id
 *
 * Respuesta:
 * - JSON con ok y mensajes, o error.
 */

require_once __DIR__ . '/../app/controladores/soporte_controller.php';

/**
 * Instanciamos el controlador de soporte.
 */
$controller = new SoporteController();

/**
 * Ejecutamos la acción AJAX correspondiente.
 */
$controller->leerTicketAdminAjax();