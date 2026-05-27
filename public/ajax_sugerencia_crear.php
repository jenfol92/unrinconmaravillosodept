<?php

/**
 * Endpoint AJAX para enviar sugerencias de usuario.
 * ---------------------------------------------------------
 * Este archivo se llama desde JavaScript cuando un usuario
 * logueado envía una sugerencia desde la web.
 *
 * Su única responsabilidad es:
 * - Cargar el SoporteController.
 * - Ejecutar el método guardarSugerenciaAjax().
 *
 * La lógica real está dentro del controlador.
 */

require_once __DIR__ . '/../app/controladores/soporte_controller.php';

$controller = new SoporteController();
$controller->guardarSugerenciaAjax();