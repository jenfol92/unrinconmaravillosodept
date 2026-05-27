<?php

/**
 * Controlador AdminUsuarioController
 * ---------------------------------------------------------
 * Controlador encargado de gestionar acciones AJAX relacionadas
 * con usuarios desde el panel de administración.
 *
 * Este controlador NO carga vistas.
 * Sus métodos devuelven respuestas JSON porque son llamados
 * desde JavaScript mediante fetch/AJAX.
 *
 * Responsabilidades:
 * - Comprobar permisos de administrador/gestor.
 * - Validar datos recibidos por GET o POST.
 * - Llamar al modelo correspondiente.
 * - Devolver respuestas JSON al navegador.
 *
 * Modelos usados:
 * - Usuario:
 *   Para descargas, datos de usuario y cambio de estado.
 *
 * - Producto:
 *   Para consultar productos favoritos de un usuario.
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../modelos/Producto.php';

class AdminUsuarioController
{
    /**
     * Obtiene las descargas asociadas a un usuario.
     * ---------------------------------------------------------
     * Acción usada desde el panel de administración para consultar
     * los recursos/productos descargados o adquiridos por un usuario.
     *
     * Entrada esperada por GET:
     * - usuario_id
     *
     * Respuesta correcta:
     * {
     *   "ok": true,
     *   "descargas": [...]
     * }
     *
     * @return void
     */
    public function obtenerDescargasUsuarioAjax()
    {
        header('Content-Type: application/json; charset=utf-8');

        /**
         * Comprobación de permisos.
         * -----------------------------------------------------
         * usuarioEsAdminOGestor() debe estar definido en:
         * includes/session.php
         */
        if (!usuarioEsAdminOGestor()) {
            responderNoAutorizado();
        }

        /**
         * Recogemos y normalizamos el ID del usuario.
         */
        $usuarioId = isset($_GET['usuario_id'])
            ? (int) $_GET['usuario_id']
            : 0;

        /**
         * Validamos que el ID sea un entero positivo.
         */
        if ($usuarioId <= 0) {
            echo json_encode([
                'ok' => false,
                'error' => 'Usuario no válido'
            ]);
            exit;
        }

        try {
            /**
             * Instanciamos el modelo Usuario.
             */
            $usuarioModel = new Usuario();

            /**
             * Obtenemos las descargas del usuario.
             */
            $descargas = $usuarioModel->obtenerDescargasUsuario($usuarioId);

            /**
             * Respuesta correcta.
             */
            echo json_encode([
                'ok' => true,
                'descargas' => $descargas
            ]);
            exit;

        } catch (Exception $e) {
            /**
             * Respuesta en caso de error.
             *
             * En producción conviene no mostrar $e->getMessage()
             * para no exponer detalles internos.
             */
            echo json_encode([
                'ok' => false,
                'error' => 'Error cargando descargas.'
            ]);
            exit;
        }
    }


    /**
     * Obtiene los favoritos asociados a un usuario.
     * ---------------------------------------------------------
     * Acción usada desde el panel de administración para consultar
     * qué productos ha marcado como favoritos un usuario.
     *
     * Entrada esperada por GET:
     * - usuario_id
     *
     * Respuesta correcta:
     * {
     *   "ok": true,
     *   "favoritos": [...]
     * }
     *
     * @return void
     */
    public function obtenerFavoritosUsuarioAjax()
    {
        header('Content-Type: application/json; charset=utf-8');

        /**
         * Comprobamos permisos de administrador o gestor.
         */
        if (!usuarioEsAdminOGestor()) {
            responderNoAutorizado();
        }

        /**
         * Recogemos y normalizamos el ID del usuario.
         */
        $usuarioId = isset($_GET['usuario_id'])
            ? (int) $_GET['usuario_id']
            : 0;

        /**
         * Validamos que el ID sea correcto.
         */
        if ($usuarioId <= 0) {
            echo json_encode([
                'ok' => false,
                'error' => 'Usuario no válido'
            ]);
            exit;
        }

        try {
            /**
             * Instanciamos el modelo Producto.
             * -------------------------------------------------
             * Los favoritos son productos, por eso se consultan
             * desde el modelo Producto.
             */
            $productoModel = new Producto();

            /**
             * Obtenemos los productos favoritos del usuario.
             */
            $favoritos = $productoModel->obtenerProductosFavoritos($usuarioId);

            /**
             * Respuesta correcta.
             */
            echo json_encode([
                'ok' => true,
                'favoritos' => $favoritos
            ]);
            exit;

        } catch (Exception $e) {
            /**
             * Respuesta en caso de error.
             */
            echo json_encode([
                'ok' => false,
                'error' => 'Error cargando favoritos.'
            ]);
            exit;
        }
    }


    /**
     * Cambia el estado de un usuario.
     * ---------------------------------------------------------
     * Permite activar o bloquear un usuario desde el panel
     * de administración.
     *
     * Entrada esperada por POST:
     * - usuario_id
     * - activo
     *
     * Valores esperados de activo:
     * - 1 → usuario activo.
     * - 0 → usuario bloqueado.
     *
     * Respuesta correcta:
     * {
     *   "ok": true,
     *   "mensaje": "Usuario activado."
     * }
     *
     * @return void
     */
    public function cambiarEstadoUsuarioAjax()
    {
        header('Content-Type: application/json; charset=utf-8');

        /**
         * Comprobamos permisos de administrador o gestor.
         */
        if (!usuarioEsAdminOGestor()) {
            responderNoAutorizado();
        }

        /**
         * Recogemos y normalizamos usuario_id.
         */
        $usuarioId = isset($_POST['usuario_id'])
            ? (int) $_POST['usuario_id']
            : 0;

        /**
         * Recogemos activo.
         * -----------------------------------------------------
         * Es importante comprobar con isset() porque activo puede
         * venir como 0, y 0 es un valor válido para bloquear.
         */
        $activo = isset($_POST['activo'])
            ? (int) $_POST['activo']
            : null;

        /**
         * Validación básica.
         */
        if ($usuarioId <= 0 || $activo === null) {
            echo json_encode([
                'ok' => false,
                'error' => 'Datos incompletos'
            ]);
            exit;
        }

        /**
         * Validamos que activo solo pueda ser 0 o 1.
         */
        if (!in_array($activo, [0, 1], true)) {
            echo json_encode([
                'ok' => false,
                'error' => 'Estado de usuario no válido'
            ]);
            exit;
        }

        try {
            /**
             * Instanciamos el modelo Usuario.
             */
            $usuarioModel = new Usuario();

            /**
             * Cambiamos el estado del usuario.
             */
            $usuarioModel->cambiarEstadoUsuario($usuarioId, $activo);

            /**
             * Mensaje según el nuevo estado.
             */
            $mensaje = $activo === 1
                ? 'Usuario activado.'
                : 'Usuario bloqueado.';

            /**
             * Respuesta correcta.
             */
            echo json_encode([
                'ok' => true,
                'mensaje' => $mensaje
            ]);
            exit;

        } catch (Exception $e) {
            /**
             * Respuesta en caso de error.
             */
            echo json_encode([
                'ok' => false,
                'error' => 'Error actualizando el estado del usuario.'
            ]);
            exit;
        }
    }
}