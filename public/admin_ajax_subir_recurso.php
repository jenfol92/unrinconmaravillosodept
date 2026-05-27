<?php

/**
 * AJAX: subir archivo de producto a Cloudflare R2
 * ---------------------------------------------------------
 * Punto de entrada público para subir o reemplazar el archivo
 * descargable asociado a un producto.
 *
 * Este archivo no contiene lógica de negocio ni acceso directo
 * al modelo o al servicio R2.
 *
 * Flujo:
 * public/archivo_actual.php
 * → AdminProductoController::subirArchivoProductoR2Ajax()
 * → Producto::obtenerProductosID()
 * → R2Service::subirArchivoProducto()
 * → Producto::actualizarArchivoR2()
 *
 * Entrada esperada por POST:
 * - producto_id
 *
 * Entrada esperada por FILES:
 * - archivo_recurso
 * - archivo
 *
 * Respuesta:
 * - JSON con ok, mensaje y archivo_s3_key.
 */

require_once __DIR__ . '/../app/controladores/admin_producto_controller.php';

/**
 * Instanciamos el controlador de productos del panel admin.
 */
$controller = new AdminProductoController();

/**
 * Ejecutamos la acción AJAX correspondiente.
 */
$controller->subirArchivoProductoR2Ajax();