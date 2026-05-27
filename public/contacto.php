<?php

/**
 * Página pública de contacto.
 * ---------------------------------------------------------
 * Este archivo actúa como punto de entrada público.
 *
 * Su única responsabilidad es:
 * - Cargar el ContactoController.
 * - Ejecutar el método index().
 *
 * La lógica real está dentro del controlador.
 */

require_once __DIR__ . '/../app/controladores/contacto_controller.php';

$controller = new ContactoController();
$controller->index();