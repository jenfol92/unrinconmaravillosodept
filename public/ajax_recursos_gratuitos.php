<?php

/**
 * Endpoint AJAX para filtrar recursos gratuitos.
 * ---------------------------------------------------------
 * Este archivo es llamado desde JavaScript cuando el usuario
 * filtra recursos gratuitos en la parte pública de la web.
 *
 * Su única responsabilidad es:
 * - Cargar RecursoGratuitoController.
 * - Ejecutar el método filtrarRecursosGratuitosAjax().
 */

require_once __DIR__ . '/../app/controladores/RecursoGratuitoController.php';

$controller = new RecursoGratuitoController();
$controller->filtrarRecursosGratuitosAjax();