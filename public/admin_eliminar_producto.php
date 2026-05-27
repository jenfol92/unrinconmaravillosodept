<?php

/**
 * Acción: eliminar producto
 * ---------------------------------------------------------
 * Este archivo actúa como punto de entrada para eliminar un
 * producto desde el panel de administración.
 *
 * Uso principal:
 * - Panel de administración.
 * - Gestión de productos de la tienda.
 * - Eliminación de productos existentes.
 *
 * Flujo general:
 * 1. Se carga el controlador de administración.
 * 2. Se instancia AdminController.
 * 3. Se llama al método eliminarProducto().
 *
 * La lógica real de:
 * - comprobación de permisos;
 * - validación del ID del producto;
 * - eliminación en base de datos;
 * - posible eliminación de archivos asociados;
 * - redirección o respuesta posterior;
 *
 * debe estar dentro del método:
 * AdminController::eliminarProducto()
 */

require_once __DIR__ . '/../app/controladores/admin_controller.php';

/**
 * Instanciamos el controlador de administración.
 * ---------------------------------------------------------
 * Este controlador centraliza las acciones relacionadas con
 * el panel admin, entre ellas la gestión de productos.
 */
$controller = new AdminController();

/**
 * Ejecutamos la acción de eliminación de producto.
 * ---------------------------------------------------------
 * Este método debe encargarse de procesar la petición,
 * comprobar permisos, eliminar el producto correspondiente
 * y devolver la respuesta adecuada.
 */
$controller->eliminarProducto();