<?php

/**
 * Controlador ResenaController
 * ---------------------------------------------------------
 * Controlador encargado de gestionar acciones AJAX relacionadas
 * con reseñas.
 *
 * Este controlador NO carga vistas.
 * Sus métodos devuelven respuestas JSON porque son llamados
 * desde JavaScript mediante fetch/AJAX.
 *
 * Responsabilidades:
 * - Comprobar permisos.
 * - Recoger y validar datos GET/POST.
 * - Llamar al modelo correspondiente.
 * - Devolver respuesta JSON.
 *
 * Modelos usados:
 * - Usuario:
 *   Para obtener las reseñas realizadas por un usuario.
 *
 * - Producto:
 *   Para guardar reseñas, cambiar el estado de una reseña,
 *   obtener reseñas de un producto o denunciar una reseña.
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../modelos/Producto.php';

class ResenaController
{
    /**
     * Obtiene las reseñas realizadas por un usuario.
     * ---------------------------------------------------------
     * Acción pensada para el panel admin, por ejemplo al abrir
     * el detalle de un usuario y consultar su historial de reseñas.
     *
     * Archivo público que llama a este método:
     * - public/ajax_admin_reseñas_usuario.php
     *
     * Entrada esperada por GET:
     * - usuario_id
     *
     * Respuesta correcta:
     * {
     *   "ok": true,
     *   "resenas": [...]
     * }
     *
     * @return void
     */
    public function obtenerResenasUsuarioAjax()
    {
        header('Content-Type: application/json; charset=utf-8');

        /**
         * Comprobación de permisos.
         * -----------------------------------------------------
         * Solo administradores o gestores pueden consultar las
         * reseñas de otros usuarios desde el panel admin.
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
         * Validamos que sea un ID válido.
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
             * El modelo Usuario contiene el método que consulta
             * las reseñas realizadas por un usuario concreto.
             */
            $usuarioModel = new Usuario();

            echo json_encode([
                'ok' => true,
                'resenas' => $usuarioModel->obtenerResenasUsuario($usuarioId)
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                'ok' => false,
                'error' => 'Error cargando reseñas.'
            ]);
            exit;
        }
    }


    /**
     * Cambia el estado de una reseña.
     * ---------------------------------------------------------
     * Permite moderar reseñas desde el panel admin.
     *
     * Archivo público que llama a este método:
     * - public/ajax_admin_estado_resena.php
     *
     * Entrada esperada por POST:
     * - resena_id
     * - estado
     *
     * Ejemplos de estado:
     * - pendiente
     * - aprobada
     * - rechazada
     * - denunciada
     *
     * Los estados permitidos deben validarse en el modelo:
     * Producto::cambiarEstadoResena().
     *
     * @return void
     */
    public function cambiarEstadoResenaAjax()
    {
        header('Content-Type: application/json; charset=utf-8');

        /**
         * Comprobamos permisos de administrador o gestor.
         */
        if (!usuarioEsAdminOGestor()) {
            responderNoAutorizado();
        }

        /**
         * Recogemos y normalizamos datos.
         */
        $resenaId = isset($_POST['resena_id'])
            ? (int) $_POST['resena_id']
            : 0;

        $estado = trim($_POST['estado'] ?? '');

        /**
         * Validación básica.
         */
        if ($resenaId <= 0 || $estado === '') {
            echo json_encode([
                'ok' => false,
                'error' => 'Datos incompletos'
            ]);
            exit;
        }

        try {
            /**
             * El modelo Producto se encarga de comprobar si el estado
             * es válido y actualizar la reseña.
             */
            $productoModel = new Producto();

            if (!$productoModel->cambiarEstadoResena($resenaId, $estado)) {
                echo json_encode([
                    'ok' => false,
                    'error' => 'Estado no válido'
                ]);
                exit;
            }

            echo json_encode([
                'ok' => true,
                'mensaje' => 'Estado de reseña actualizado.'
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                'ok' => false,
                'error' => 'Error actualizando la reseña.'
            ]);
            exit;
        }
    }


    /**
     * Guarda una reseña publicada por un usuario.
     * ---------------------------------------------------------
     * Esta acción permite que un usuario logueado publique una
     * reseña sobre un producto.
     *
     * Archivo público que llama a este método:
     * - public/ajax_guardar_reseña.php
     *
     * Entrada esperada por POST:
     * - producto_id
     * - puntuacion
     * - comentario
     *
     * Respuesta correcta:
     * {
     *   "ok": true,
     *   "mensaje": "Reseña guardada correctamente."
     * }
     *
     * @return void
     */
    public function guardarResenaAjax()
    {
        header('Content-Type: application/json; charset=utf-8');

        /**
         * Comprobamos que el usuario esté logueado.
         */
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode([
                'ok' => false,
                'error' => 'Debes iniciar sesión para publicar una reseña.'
            ]);
            exit;
        }

        /**
         * Recogemos y normalizamos los datos recibidos.
         */
        $productoId = isset($_POST['producto_id'])
            ? (int) $_POST['producto_id']
            : 0;

        $puntuacion = isset($_POST['puntuacion'])
            ? (int) $_POST['puntuacion']
            : 0;

        $comentario = trim($_POST['comentario'] ?? '');

        /**
         * Validamos producto.
         */
        if ($productoId <= 0) {
            echo json_encode([
                'ok' => false,
                'error' => 'Producto no válido.'
            ]);
            exit;
        }

        /**
         * Validamos puntuación.
         */
        if ($puntuacion < 1 || $puntuacion > 5) {
            echo json_encode([
                'ok' => false,
                'error' => 'La puntuación debe estar entre 1 y 5.'
            ]);
            exit;
        }

        /**
         * Validamos comentario.
         */
        if ($comentario === '') {
            echo json_encode([
                'ok' => false,
                'error' => 'El comentario no puede estar vacío.'
            ]);
            exit;
        }

        try {
            /**
             * Instanciamos el modelo Producto.
             */
            $productoModel = new Producto();

            /**
             * Comprobamos si el usuario tiene permitido reseñar.
             * -------------------------------------------------
             * Esto permite bloquear usuarios que hayan hecho mal uso
             * del sistema de reseñas.
             */
            if (!$productoModel->usuarioPuedeResenar($_SESSION['usuario_id'])) {
                echo json_encode([
                    'ok' => false,
                    'error' => 'No tienes permitido publicar nuevas reseñas.'
                ]);
                exit;
            }

            /**
             * Guardamos la reseña.
             */
            $productoModel->guardarResena(
                $_SESSION['usuario_id'],
                $productoId,
                $puntuacion,
                $comentario
            );

            /**
             * Respuesta correcta.
             */
            echo json_encode([
                'ok' => true,
                'mensaje' => 'Reseña guardada correctamente.'
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                'ok' => false,
                'error' => 'Error guardando la reseña.'
            ]);
            exit;
        }
    }


    /**
     * Obtiene las reseñas de un producto desde administración.
     * ---------------------------------------------------------
     * Esta acción permite que el panel admin cargue las reseñas
     * asociadas a un producto concreto.
     *
     * Archivo público que llama a este método:
     * - public/ajax_reseñas_producto.php
     *
     * Entrada esperada por GET:
     * - producto_id
     *
     * Respuesta correcta:
     * {
     *   "ok": true,
     *   "resenas": [...]
     * }
     *
     * @return void
     */
    public function obtenerResenasProductoAdminAjax()
    {
        header('Content-Type: application/json; charset=utf-8');

        /**
         * Solo administradores o gestores pueden consultar reseñas
         * desde el panel admin.
         */
        if (!usuarioEsAdminOGestor()) {
            responderNoAutorizado();
        }

        /**
         * Recogemos y normalizamos el ID del producto.
         */
        $productoId = isset($_GET['producto_id'])
            ? (int) $_GET['producto_id']
            : 0;

        /**
         * Validamos producto.
         */
        if ($productoId <= 0) {
            echo json_encode([
                'ok' => false,
                'error' => 'Producto no válido'
            ]);
            exit;
        }

        try {
            /**
             * Instanciamos el modelo Producto.
             */
            $productoModel = new Producto();

            /**
             * Obtenemos las reseñas del producto para administración.
             */
            $resenas = $productoModel->obtenerResenasAdminPorProducto($productoId);

            /**
             * Respuesta correcta.
             */
            echo json_encode([
                'ok' => true,
                'resenas' => $resenas
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                'ok' => false,
                'error' => 'Error cargando reseñas del producto.'
            ]);
            exit;
        }
    }


    /**
     * Denuncia una reseña y bloquea al usuario para nuevas reseñas.
     * ---------------------------------------------------------
     * Acción de moderación más sensible.
     *
     * Archivo público que llama a este método:
     * - public/ajax_denunciar_resena.php
     *
     * Entrada esperada por POST:
     * - resena_id
     * - usuario_id
     *
     * Efectos esperados:
     * - La reseña queda denunciada o marcada según la lógica del modelo.
     * - El usuario queda bloqueado para publicar nuevas reseñas.
     *
     * @return void
     */
    public function denunciarResenaAjax()
    {
        header('Content-Type: application/json; charset=utf-8');

        /**
         * Comprobamos permisos de administrador o gestor.
         */
        if (!usuarioEsAdminOGestor()) {
            responderNoAutorizado();
        }

        /**
         * Recogemos y normalizamos datos.
         */
        $resenaId = isset($_POST['resena_id'])
            ? (int) $_POST['resena_id']
            : 0;

        $usuarioId = isset($_POST['usuario_id'])
            ? (int) $_POST['usuario_id']
            : 0;

        /**
         * Validación básica.
         */
        if ($resenaId <= 0 || $usuarioId <= 0) {
            echo json_encode([
                'ok' => false,
                'error' => 'Datos incompletos'
            ]);
            exit;
        }

        try {
            /**
             * El modelo Producto contiene la operación real de denuncia
             * y bloqueo para nuevas reseñas.
             */
            $productoModel = new Producto();

            $productoModel->denunciarResenaYBloquearUsuario($resenaId, $usuarioId);

            echo json_encode([
                'ok' => true,
                'mensaje' => 'Reseña denunciada y usuario bloqueado para nuevas reseñas.'
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                'ok' => false,
                'error' => 'Error al denunciar la reseña.'
            ]);
            exit;
        }
    }
}