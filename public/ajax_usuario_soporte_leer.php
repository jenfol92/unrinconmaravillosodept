<?php

/**
 * AJAX: leer mensajes de soporte del usuario
 * ---------------------------------------------------------
 * Punto de entrada público para que un usuario logueado pueda
 * cargar los mensajes de uno de sus tickets de soporte.
 *
 * Este archivo no contiene lógica de negocio ni SQL.
 *
 * Flujo:
 * public/ajax_usuario_soporte_leer.php
 * → SoporteController::leerTicketUsuarioAjax()
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
$controller->leerTicketUsuarioAjax();