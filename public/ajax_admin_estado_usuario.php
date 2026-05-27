<?php

/**
 * AJAX: cambiar estado de un usuario
 * ---------------------------------------------------------
 * Punto de entrada público para activar o bloquear un usuario
 * desde el panel de administración.
 *
 * Este archivo no contiene lógica de negocio ni SQL.
 *
 * Flujo:
 * public/ajax_cambiar_estado_usuario.php
 * → AdminUsuarioController::cambiarEstadoUsuarioAjax()
 * → Usuario::cambiarEstadoUsuario()
 *
 * Entrada esperada por POST:
 * - usuario_id
 * - activo
 *
 * Valores de activo:
 * - 1 → usuario activo.
 * - 0 → usuario bloqueado.
 *
 * Respuesta:
 * - JSON
 */

require_once __DIR__ . '/../app/controladores/admin_usuario_controller.php';

/**
 * Instanciamos el controlador de usuarios del panel admin.
 */
$controller = new AdminUsuarioController();

/**
 * Ejecutamos la acción AJAX correspondiente.
 */
$controller->cambiarEstadoUsuarioAjax();