<?php

/**
 * AJAX: obtener favoritos de un usuario
 * ---------------------------------------------------------
 * Punto de entrada público para consultar los productos favoritos
 * de un usuario desde el panel de administración.
 *
 * Este archivo no contiene lógica de negocio ni SQL.
 *
 * Flujo:
 * public/ajax_usuario_favoritos.php
 * → AdminUsuarioController::obtenerFavoritosUsuarioAjax()
 * → Producto::obtenerProductosFavoritos()
 *
 * Entrada esperada por GET:
 * - usuario_id
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
$controller->obtenerFavoritosUsuarioAjax();