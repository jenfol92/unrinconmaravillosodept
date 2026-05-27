<?php

/**
 * AJAX: alerta de seguridad
 * ---------------------------------------------------------
 * Punto de entrada público para la acción:
 * "No reconozco este acceso".
 *
 * Este archivo ya no contiene SQL.
 *
 * Su única responsabilidad es:
 * - cargar el controlador;
 * - ejecutar el método correspondiente.
 *
 * La lógica queda separada así:
 *
 * public/ajax_alerta_seguridad.php
 * → recibe la petición.
 *
 * SeguridadController::alertaSeguridadAjax()
 * → valida sesión, coordina la acción y devuelve JSON.
 *
 * Seguridad::registrarAlertaAccesoNoReconocido()
 * → ejecuta las operaciones SQL.
 */

require_once __DIR__ . '/../app/controladores/seguridad_controller.php';

/**
 * Instanciamos el controlador de seguridad.
 */
$controller = new SeguridadController();

/**
 * Ejecutamos la acción AJAX de alerta de seguridad.
 */
$controller->alertaSeguridadAjax();