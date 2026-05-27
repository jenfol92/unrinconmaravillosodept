<?php

/**
 * Endpoint AJAX para guardar un recurso gratuito desde el panel de administración.
 *
 * Este archivo actúa como punto de entrada público.
 * Su única responsabilidad es:
 *
 * 1. Cargar el controlador de administración.
 * 2. Crear una instancia de AdminController.
 * 3. Ejecutar el método encargado de procesar la petición AJAX.
 *
 * La lógica real de validación, guardado en base de datos,
 * subida de archivos o generación de respuesta JSON debe estar
 * dentro del método guardarRecursoGratuitoAjax() del controlador.
 */

// Cargamos el controlador de administración.
// __DIR__ representa la carpeta actual donde se encuentra este archivo.
// Con '/../app/controladores/admin_controller.php' subimos un nivel
// y accedemos al controlador correspondiente.
require_once __DIR__ . '/../app/controladores/admin_controller.php';

// Creamos una instancia del controlador de administración.
$controller = new AdminController();

// Ejecutamos el método que gestiona el guardado del recurso gratuito por AJAX.
$controller->guardarRecursoGratuitoAjax();