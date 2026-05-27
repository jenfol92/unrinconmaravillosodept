<?php
require_once __DIR__ . '/../config/app.php';
/**
 * Archivo de sesión global
 * ---------------------------------------------------------
 * Centraliza el inicio de sesión y funciones auxiliares para
 * comprobar autenticación y permisos.
 */


/**
 * Inicia la sesión solo si todavía no está iniciada.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/**
 * Inicializa el carrito en sesión si no existe.
 */
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}


/**
 * Inicializa favoritos en sesión si no existe.
 */
if (!isset($_SESSION['favoritos'])) {
    $_SESSION['favoritos'] = [];
}


/**
 * Comprueba si hay un usuario logueado.
 *
 * @return bool
 */
function usuarioLogueado()
{
    return !empty($_SESSION['usuario_id']);
}


/**
 * Devuelve el ID del usuario logueado.
 *
 * @return int
 */
function usuarioId()
{
    return (int)($_SESSION['usuario_id'] ?? 0);
}


/**
 * Devuelve el rol del usuario actual.
 *
 * @return int
 */
function usuarioRol()
{
    return (int)($_SESSION['rol'] ?? 0);
}


/**
 * Comprueba si el usuario tiene uno de los roles indicados.
 *
 * Ejemplo:
 * usuarioTieneRol([1, 2])
 *
 * @param array $rolesPermitidos
 * @return bool
 */
function usuarioTieneRol(array $rolesPermitidos)
{
    return usuarioLogueado()
        && in_array(usuarioRol(), $rolesPermitidos, true);
}


/**
 * Comprueba si el usuario es administrador o gestor autorizado.
 *
 * Roles usados en tu proyecto:
 * - 1: administrador
 * - 2: gestor / profesor / usuario autorizado
 *
 * @return bool
 */
function usuarioEsAdminOGestor()
{
    return usuarioTieneRol([1, 2]);
}


/**
 * Respuesta JSON para accesos no autorizados.
 * ---------------------------------------------------------
 * Sirve para evitar repetir el mismo json_encode en muchos
 * archivos AJAX.
 *
 * @param string $mensaje
 * @return void
 */
function responderNoAutorizado($mensaje = 'No autorizado')
{
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'ok' => false,
        'error' => $mensaje
    ]);

    exit;
}