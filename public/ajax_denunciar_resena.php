<?php

/**
 * AJAX: denunciar reseña y bloquear usuario
 * ---------------------------------------------------------
 * Punto de entrada público para la acción de moderación.
 *
 * Este archivo no contiene lógica de negocio.
 * Solo carga el controlador y ejecuta el método correspondiente.
 */

require_once __DIR__ . '/../app/controladores/resena_controller.php';

$controller = new ResenaController();
$controller->denunciarResenaAjax();