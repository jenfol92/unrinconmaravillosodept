<?php

/**
 * AJAX: crear categoría de producto
 * ---------------------------------------------------------
 * Punto de entrada público para crear una nueva categoría de
 * productos desde el panel de administración.
 *
 * Este archivo NO contiene lógica de negocio ni SQL.
 *
 * Su única responsabilidad es:
 * - cargar el controlador correspondiente;
 * - instanciarlo;
 * - ejecutar la acción AJAX.
 *
 * Flujo:
 * public/ajax_crear_categoria.php
 * → AdminProductoController::crearCategoriaProductoAjax()
 * → Producto::crearCategoria()
 *
 * Entrada esperada por POST:
 * - nombre: nombre de la nueva categoría.
 *
 * Respuesta:
 * - JSON con ok y categoria, o error.
 */

require_once __DIR__ . '/../app/controladores/admin_producto_controller.php';

/**
 * Instanciamos el controlador de productos del panel admin.
 */
$controller = new AdminProductoController();

/**
 * Ejecutamos la acción AJAX que crea la categoría de producto.
 */
$controller->crearCategoriaProductoAjax();