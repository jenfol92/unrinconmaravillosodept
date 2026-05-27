<?php

/**
 * AJAX: obtener descargas de un usuario
 * ---------------------------------------------------------
 * Punto de entrada público para consultar las descargas de
 * un usuario desde el panel de administración.
 *
 * Este archivo no contiene lógica de negocio.
 *
 * Flujo:
 * public/ajax_usuario_descargas.php
 * → AdminUsuarioController::obtenerDescargasUsuarioAjax()
 * → Usuario::obtenerDescargasUsuario()
 */

require_once __DIR__ . '/../app/controladores/admin_usuario_controller.php';

$controller = new AdminUsuarioController();
$controller->obtenerDescargasUsuarioAjax();