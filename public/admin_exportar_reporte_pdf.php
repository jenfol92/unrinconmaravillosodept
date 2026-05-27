<?php

/**
 * Acción: exportar productos a PDF
 * ---------------------------------------------------------
 * Este archivo actúa como punto de entrada para generar una
 * exportación en PDF de los productos de la tienda desde el
 * panel de administración.
 *
 * Uso principal:
 * - Panel de administración.
 * - Gestión de productos.
 * - Generación de listados o informes en formato PDF.
 *
 * Flujo general:
 * 1. Se carga el controlador de administración.
 * 2. Se instancia AdminController.
 * 3. Se llama al método exportarProductosPdf().
 *
 * La lógica real de:
 * - comprobación de permisos;
 * - obtención del listado de productos;
 * - preparación de los datos;
 * - generación del documento PDF;
 * - descarga o visualización del archivo generado;
 *
 * debe estar dentro del método:
 * AdminController::exportarProductosPdf()
 */

require_once __DIR__ . '/../app/controladores/admin_controller.php';

/**
 * Instanciamos el controlador de administración.
 * ---------------------------------------------------------
 * Este controlador centraliza las acciones relacionadas con
 * el panel admin, entre ellas la exportación de productos.
 */
$controller = new AdminController();

/**
 * Ejecutamos la acción de exportación de productos a PDF.
 * ---------------------------------------------------------
 * Este método debe encargarse de generar el PDF correspondiente
 * y devolverlo al navegador, normalmente como descarga o como
 * documento visible en una nueva pestaña.
 */
$controller->exportarProductosPdf();