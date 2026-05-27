<?php

/**
 * AJAX: guardar mensaje de contacto
 * ---------------------------------------------------------
 * Punto de entrada público para guardar mensajes enviados
 * desde el formulario de contacto.
 *
 * Este archivo ya no contiene SQL.
 *
 * La lógica queda separada así:
 *
 * public/ajax_contacto.php
 * → recibe la petición.
 *
 * ContactoController::guardarMensajeAjax()
 * → valida datos y devuelve JSON.
 *
 * Contacto::guardarMensaje()
 * → ejecuta el INSERT en la base de datos.
 */

require_once __DIR__ . '/../app/controladores/contacto_controller.php';

/**
 * Instanciamos el controlador de contacto.
 */
$controller = new ContactoController();

/**
 * Ejecutamos la acción AJAX de guardado de mensaje.
 */
$controller->guardarMensajeAjax();