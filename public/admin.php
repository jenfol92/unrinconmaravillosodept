<?php

/**
 * Panel de administración
 * ---------------------------------------------------------
 * Este archivo actúa como punto de entrada principal al panel
 * de administración de la aplicación.
 *
 * Uso principal:
 * - Cargar el dashboard del administrador.
 * - Mostrar las secciones de gestión de productos, usuarios,
 *   recursos gratuitos, pedidos, soporte, estadísticas, etc.
 *
 * Flujo general:
 * 1. Se carga la sesión del usuario.
 * 2. Se carga el controlador de administración.
 * 3. Se instancia AdminController.
 * 4. Se ejecuta el método dashboard().
 *
 * Seguridad:
 * - La comprobación de sesión y permisos puede realizarse en:
 *   - includes/session.php
 *   - AdminController::dashboard()
 *
 * El controlador debe encargarse de impedir el acceso a usuarios
 * no autorizados.
 */


/**
 * Cargamos el archivo de sesión.
 * ---------------------------------------------------------
 * Este archivo normalmente se encarga de iniciar la sesión
 * mediante session_start() y permite acceder a variables como:
 *
 * - $_SESSION['usuario_id']
 * - $_SESSION['rol']
 * - $_SESSION['nombre_usuario']
 *
 * También puede contener funciones auxiliares para comprobar
 * si el usuario está autenticado.
 */
require_once __DIR__ . "/../includes/session.php";


/**
 * Cargamos el controlador del administrador.
 * ---------------------------------------------------------
 * AdminController contiene la lógica principal del panel admin.
 *
 * Desde este controlador se suelen gestionar acciones como:
 * - mostrar el dashboard;
 * - listar productos;
 * - crear o editar productos;
 * - gestionar usuarios;
 * - gestionar recursos gratuitos;
 * - consultar pedidos;
 * - responder soporte;
 * - exportar datos.
 */
require_once __DIR__ . "/../app/controladores/admin_controller.php";


/**
 * Instanciamos el controlador de administración.
 * ---------------------------------------------------------
 * Creamos un objeto de AdminController para poder acceder
 * a sus métodos.
 */
$admin = new AdminController();


/**
 * Mostramos el dashboard del panel admin.
 * ---------------------------------------------------------
 * El método dashboard() debe encargarse de:
 *
 * - comprobar que el usuario tiene permisos suficientes;
 * - cargar los datos necesarios para el panel;
 * - incluir la vista correspondiente;
 * - mostrar estadísticas, productos, usuarios, soporte, etc.
 */
$admin->dashboard();