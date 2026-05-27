<?php

/**
 * AJAX: alternar favorito de producto
 * ---------------------------------------------------------
 * Punto de entrada público para añadir o quitar un producto
 * de los favoritos del usuario logueado.
 *
 * Este archivo NO contiene lógica de negocio ni SQL.
 *
 * Su única responsabilidad es:
 * - cargar el controlador correspondiente;
 * - instanciarlo;
 * - ejecutar la acción AJAX.
 *
 * Flujo:
 * public/ajax_toggle_favorito.php
 * → FavoritoController::toggleFavoritoAjax()
 * → Producto::toggleFavorito()
 *
 * Entrada esperada por POST:
 * - producto_id: ID del producto que se quiere guardar o quitar
 *   de favoritos.
 *
 * Respuesta:
 * - JSON con ok y estado.
 *
 * Posibles estados:
 * - guardado
 * - eliminado
 */

require_once __DIR__ . '/../app/controladores/favorito_controller.php';

/**
 * Instanciamos el controlador de favoritos.
 */
$controller = new FavoritoController();

/**
 * Ejecutamos la acción AJAX correspondiente.
 */
$controller->toggleFavoritoAjax();