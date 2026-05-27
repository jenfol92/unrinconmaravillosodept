<?php

/**
 * Acción: guardar producto
 * ---------------------------------------------------------
 * Este archivo actúa como punto de entrada para guardar un
 * producto desde el panel de administración.
 *
 * Uso principal:
 * - Panel de administración.
 * - Creación de nuevos productos.
 * - Edición o actualización de productos existentes.
 *
 * Flujo general:
 * 1. Se carga el controlador de administración.
 * 2. Se instancia AdminController.
 * 3. Se llama al método guardarProducto().
 *
 * La lógica real de:
 * - comprobación de permisos;
 * - validación de datos del formulario;
 * - subida o tratamiento de imágenes;
 * - creación o actualización del producto;
 * - guardado en base de datos;
 * - redirección o respuesta posterior;
 *
 * debe estar dentro del método:
 * AdminController::guardarProducto()
 */

require_once __DIR__ . '/../app/controladores/admin_controller.php';

/**
 * Instanciamos el controlador de administración.
 * ---------------------------------------------------------
 * Este controlador centraliza las acciones relacionadas con
 * el panel admin, entre ellas la creación y edición de productos.
 */
$controller = new AdminController();

/**
 * Ejecutamos la acción de guardado de producto.
 * ---------------------------------------------------------
 * Este método debe encargarse de procesar los datos recibidos
 * desde el formulario del panel de administración y guardar
 * el producto en la base de datos.
 */
$controller->guardarProducto();