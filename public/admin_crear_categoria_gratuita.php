<?php
/*
 * Este archivo actúa como punto de entrada para crear una
 * nueva categoría de recursos gratuitos desde el panel de
 * administración.
 *
 * Uso principal:
 * - Panel de administración.
 * - Gestión de categorías de recursos gratuitos.
 * - Peticiones AJAX enviadas desde JavaScript.
 *
 * Flujo general:
 * 1. Se carga el controlador de administración.
 * 2. Se instancia AdminController.
 * 3. Se llama al método crearCategoriaGratuitaAjax().
 *
 * La lógica real de:
 * - validación de permisos;
 * - validación del nombre de la categoría;
 * - creación en base de datos;
 * - respuesta JSON;
 *
 * debe estar dentro del método:
 * AdminController::crearCategoriaGratuitaAjax()
 */

require_once __DIR__ . '/../app/controladores/admin_controller.php';
/**
 * Instanciamos el controlador de administración.
 * ---------------------------------------------------------
 * Este controlador agrupa las acciones relacionadas con el
 * panel admin, entre ellas la gestión de recursos gratuitos
 * y sus categorías.
 */

$controller = new AdminController();

/**
 * Ejecutamos la acción AJAX para crear una categoría gratuita.
 * ---------------------------------------------------------
 * Este método debe encargarse de procesar la petición recibida,
 * crear la categoría si los datos son válidos y devolver una
 * respuesta JSON al navegador.
 */

$controller->crearCategoriaGratuitaAjax();