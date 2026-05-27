<?php

/**
 * Controlador SeguridadController
 * ---------------------------------------------------------
 * Controlador encargado de gestionar acciones relacionadas
 * con la seguridad de la cuenta del usuario.
 *
 * En este caso gestiona la alerta:
 * "No reconozco este acceso".
 *
 * Responsabilidades del controlador:
 * - Comprobar sesión.
 * - Recoger datos de la petición.
 * - Llamar al modelo Seguridad.
 * - Cerrar la sesión actual.
 * - Devolver respuesta JSON al navegador.
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../modelos/Seguridad.php';

class SeguridadController
{
    /**
     * Procesa la alerta de seguridad mediante AJAX.
     * ---------------------------------------------------------
     * Este método se ejecuta cuando el usuario pulsa el botón
     * de alerta de seguridad en su panel.
     *
     * Flujo:
     * 1. Comprueba que el usuario esté logueado.
     * 2. Recoge ID de usuario desde sesión.
     * 3. Recoge IP actual y User Agent.
     * 4. Llama al modelo Seguridad.
     * 5. Cierra la sesión actual.
     * 6. Devuelve JSON con redirección al flujo de recuperación.
     *
     * @return void
     */
    public function alertaSeguridadAjax()
    {
        /**
         * Indicamos que la respuesta será JSON en UTF-8.
         */
        header('Content-Type: application/json; charset=utf-8');

        /**
         * Validación de sesión.
         * -----------------------------------------------------
         * Si no hay usuario logueado, no podemos registrar
         * una alerta asociada a una cuenta.
         */
        if (empty($_SESSION['usuario_id'])) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Debes iniciar sesión para realizar esta acción.'
            ]);
            exit;
        }

        /**
         * ID del usuario logueado.
         */
        $usuarioId = (int) $_SESSION['usuario_id'];

        /**
         * IP de la petición actual.
         * -----------------------------------------------------
         * Se usa como alternativa si en la tabla usuarios no existe
         * acceso_actual_ip.
         */
        $ipPeticion = $_SERVER['REMOTE_ADDR'] ?? null;

        /**
         * User Agent.
         * -----------------------------------------------------
         * Información del navegador/dispositivo.
         * Es orientativo, pero útil para revisión posterior.
         */
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        try {
            /**
             * Instanciamos el modelo Seguridad.
             */
            $seguridadModel = new Seguridad();

            /**
             * Registramos la alerta de acceso no reconocido.
             * -------------------------------------------------
             * Toda la parte SQL queda en el modelo.
             */
            $seguridadModel->registrarAlertaAccesoNoReconocido(
                $usuarioId,
                $ipPeticion,
                $userAgent
            );

            /**
             * Cerramos la sesión actual.
             * -------------------------------------------------
             * Después de registrar la alerta, el usuario debe
             * pasar por el flujo de recuperación/cambio de contraseña.
             */
            session_unset();
            session_destroy();

            /**
             * Respuesta correcta.
             * -------------------------------------------------
             * El JavaScript leerá redirect y enviará al usuario
             * a la recuperación de contraseña.
             */
            echo json_encode([
                'ok' => true,
                'mensaje' => 'Se ha registrado la IP como sospechosa y se han cerrado las sesiones activas.',
                'redirect' => BASE_URL . 'public/recuperar-password.php?seguridad=1'
            ]);
            exit;
        } catch (Exception $e) {
            /**
             * Respuesta en caso de error.
             * -------------------------------------------------
             * En local mostramos debug para poder depurar.
             * En producción sería recomendable ocultar este detalle.
             */
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Error al procesar la alerta de seguridad.',
                'debug' => $e->getMessage()
            ]);
            exit;
        }
    }
}
