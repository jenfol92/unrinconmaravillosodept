<?php

/**
 * AJAX: eliminar recurso gratuito
 * ---------------------------------------------------------
 * Este archivo actúa como punto de entrada para eliminar un
 * recurso gratuito desde el panel de administración.
 *
 * Uso principal:
 * - Panel de administración.
 * - Gestión de recursos gratuitos.
 * - Eliminación de recursos gratuitos existentes.
 *
 * Flujo general:
 * 1. Se carga la sesión del usuario.
 * 2. Se carga el controlador de administración.
 * 3. Se instancia AdminController.
 * 4. Se llama al método eliminarRecursoGratuitoAjax().
 *
 * La lógica real de:
 * - comprobación de permisos;
 * - validación del ID del recurso;
 * - eliminación del recurso en base de datos;
 * - posible eliminación de imagen o archivo asociado;
 * - devolución de respuesta JSON;
 *
 * debe estar dentro del método:
 * AdminController::eliminarRecursoGratuitoAjax()
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/controladores/admin_controller.php';

/**
 * Instanciamos el controlador de administración.
 * ---------------------------------------------------------
 * Este controlador centraliza las acciones relacionadas con
 * el panel admin, entre ellas la gestión de recursos gratuitos.
 */
$controller = new AdminController();

/**
 * Ejecutamos la acción AJAX para eliminar un recurso gratuito.
 * ---------------------------------------------------------
 * Este método debe encargarse de procesar la petición recibida,
 * comprobar permisos, eliminar el recurso correspondiente y
 * devolver una respuesta JSON al navegador.
 */
$controller->eliminarRecursoGratuitoAjax();