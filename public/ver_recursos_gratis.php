<?php

/**
 * Endpoint público para acceder a un recurso gratuito.
 * ---------------------------------------------------------
 * Este archivo no contiene lógica de negocio.
 * Solo carga el controlador y delega la gestión.
 */

require_once __DIR__ . '/../app/controladores/RecursoGratuitoController.php';

$controller = new RecursoGratuitoController();
$controller->redirigirRecursoGratuito();