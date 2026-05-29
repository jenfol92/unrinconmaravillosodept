<?php

/**
 * DescargaController
 * ---------------------------------------------------------
 * Controlador encargado de gestionar las descargas privadas
 * de productos comprados por los usuarios.
 *
 * Responsabilidades:
 * - Comprobar sesión.
 * - Validar token de descarga.
 * - Comprobar que la descarga pertenece al usuario.
 * - Comprobar límite máximo de descargas.
 * - Comprobar fecha de expiración.
 * - Generar URL temporal de Cloudflare R2.
 * - Incrementar contador de descargas.
 * - Redirigir al archivo real.
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../servicios/R2Service.php';

class DescargaController
{
    /**
     * Modelo Producto.
     *
     * Actualmente contiene los métodos relacionados con
     * descargas de productos comprados.
     *
     * @var Producto
     */
    private $productoModel;

    /**
     * Constructor del controlador.
     */
    public function __construct()
    {
        $this->productoModel = new Producto();
    }

    /**
     * Gestiona la descarga de un producto mediante token.
     * ---------------------------------------------------------
     * Este método valida que el usuario pueda descargar el recurso
     * y, si todo es correcto, genera una URL temporal de Cloudflare R2.
     *
     * Entrada esperada por GET:
     * - token
     *
     * @return void
     */
    public function descargarPorToken()
    {
        /**
         * Comprobamos que el usuario esté logueado.
         */
        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . PUBLIC_URL . 'login.php');
            exit;
        }

        /**
         * Recogemos el token de descarga.
         */
        $token = trim($_GET['token'] ?? '');

        if ($token === '') {
            $this->mostrarError('Token de descarga no válido.');
        }

        $usuarioId = (int) $_SESSION['usuario_id'];

        /**
         * Obtenemos la descarga asociada al token y al usuario.
         *
         * Importante:
         * El modelo debe comprobar que el token pertenece
         * realmente al usuario logueado.
         */
        $descarga = $this->productoModel->obtenerDescargaPorToken(
            $token,
            $usuarioId
        );

        if (!$descarga) {
            $this->mostrarError('No tienes permiso para descargar este recurso.');
        }

        /**
         * Comprobamos que exista archivo asociado en R2.
         */
        if (empty($descarga['archivo_s3_key'])) {
            $this->mostrarError('Este producto no tiene archivo asociado.');
        }

        /**
         * Comprobamos límite máximo de descargas.
         */
        $numeroDescargas = (int)($descarga['numero_descargas'] ?? 0);
        $maxDescargas = (int)($descarga['max_descargas'] ?? 5);

        if ($numeroDescargas >= $maxDescargas) {
            $this->mostrarError('Has alcanzado el límite máximo de descargas.');
        }

        /**
         * Comprobamos caducidad del enlace.
         */
        if (!empty($descarga['fecha_expiracion'])) {
            $fechaExpiracion = strtotime($descarga['fecha_expiracion']);

            if ($fechaExpiracion !== false && $fechaExpiracion < time()) {
                $this->mostrarError('El enlace de descarga ha caducado.');
            }
        }

        /**
         * Generamos la URL temporal de Cloudflare R2.
         */
        try {
            $r2Service = new R2Service();

            $urlTemporal = $r2Service->generarUrlDescargaTemporal(
                $descarga['archivo_s3_key'],
                10
            );

            /**
             * Incrementamos el contador justo antes de redirigir.
             */
            $this->productoModel->incrementarNumeroDescargas(
                (int)$descarga['descarga_id']
            );

            /**
             * Redirigimos al usuario a la URL temporal real.
             */
            header('Location: ' . $urlTemporal);
            exit;

        } catch (Exception $e) {
            $this->mostrarError('Error preparando la descarga.');
        }
    }

    /**
     * Muestra un error sencillo y detiene la ejecución.
     * ---------------------------------------------------------
     * De momento usamos una salida simple.
     * Más adelante se puede sustituir por una vista de error.
     *
     * @param string $mensaje Mensaje visible para el usuario.
     *
     * @return void
     */
    private function mostrarError($mensaje)
    {
        echo '<!DOCTYPE html>';
        echo '<html lang="es">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<title>Error de descarga</title>';
        echo '</head>';
        echo '<body>';
        echo '<h1>Error de descarga</h1>';
        echo '<p>' . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p><a href="' . PUBLIC_URL . 'perfil.php">Volver a mi perfil</a></p>';
        echo '</body>';
        echo '</html>';
        exit;
    }
}