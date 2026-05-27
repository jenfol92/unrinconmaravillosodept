<?php

/**
 * AJAX: cargar productos del panel de administración
 * ---------------------------------------------------------
 * Punto de entrada público para devolver, en formato JSON,
 * el listado de productos del panel de administración.
 *
 * Este archivo NO contiene lógica de negocio ni consultas.
 *
 * Su única responsabilidad es:
 * - cargar el controlador correspondiente;
 * - instanciarlo;
 * - ejecutar la acción AJAX.
 *
 * Flujo:
 * public/archivo_actual.php
 * → AdminProductoController::cargarProductosAdminAjax()
 * → Producto::obtenerProductosAdmin()
 *
 * Entrada esperada por GET:
 * - pagina
 * - busqueda
 * - estado
 * - categoria
 * - periodo_datos
 * - datos_desde
 * - datos_hasta
 *
 * Respuesta:
 * - JSON con productos, paginación y periodo aplicado.
 */

require_once __DIR__ . '/../app/controladores/admin_producto_controller.php';

/**
 * Instanciamos el controlador de productos del panel admin.
 */
$controller = new AdminProductoController();

/**
 * Ejecutamos la acción AJAX que carga los productos filtrados.
 */
$controller->cargarProductosAdminAjax();