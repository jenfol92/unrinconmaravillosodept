<?php

/**
 * GoogleDriveOAuthController
 * ---------------------------------------------------------
 * Controlador encargado de gestionar el flujo OAuth de Google Drive.
 *
 * Este controlador NO es para login de usuarios.
 *
 * Su finalidad es obtener un refresh_token de una cuenta de Google Drive
 * para poder subir archivos desde la aplicación usando esa cuenta central.
 *
 * Flujo:
 * 1. public/google_drive_auth.php llama a autorizar().
 * 2. Se redirige a Google.
 * 3. Google vuelve a public/google_drive_callback.php.
 * 4. callback() valida state y obtiene el refresh_token.
 *
 * Importante en producción:
 * - Este flujo debe estar protegido para admin/gestor.
 * - No debe estar abierto a cualquier usuario.
 * - El refresh_token no debe guardarse en GitHub.
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/oauth.php';

class GoogleDriveOAuthController
{
    /**
     * Inicia la autorización OAuth con Google Drive.
     *
     * @return void
     */
    public function autorizar()
    {
        $this->protegerSoloAdmin();

        $client = $this->crearClienteGoogle();

        /*
            Permiso para crear y gestionar archivos creados por la app.
            Es suficiente para subir recursos gratuitos al Drive.
        */
        $client->addScope(Google\Service\Drive::DRIVE_FILE);

        /*
            Necesario para que Google devuelva refresh_token.
        */
        $client->setAccessType('offline');

        /*
            Fuerza pantalla de consentimiento.
            Si no se indica, Google puede no devolver refresh_token.
        */
        $client->setPrompt('consent');

        /*
            State anti-CSRF.
        */
        $state = bin2hex(random_bytes(16));
        $_SESSION['google_drive_oauth_state'] = $state;

        $client->setState($state);

        $authUrl = $client->createAuthUrl();

        header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
        exit;
    }

    /**
     * Recibe la respuesta de Google y obtiene el refresh_token.
     *
     * @return void
     */
    public function callback()
    {
        $this->protegerSoloAdmin();

        $client = $this->crearClienteGoogle();

        /*
            Validamos errores devueltos por Google.
        */
        if (!empty($_GET['error'])) {
            $this->mostrarResultado(
                'Error de autorización',
                'Google ha devuelto el error: ' . htmlspecialchars($_GET['error'])
            );
        }

        /*
            Validamos state anti-CSRF.
        */
        $stateRecibido = $_GET['state'] ?? '';
        $stateSesion = $_SESSION['google_drive_oauth_state'] ?? '';

        if ($stateRecibido === '' || $stateSesion === '' || !hash_equals($stateSesion, $stateRecibido)) {
            $this->mostrarResultado(
                'Error de seguridad',
                'El parámetro state no es válido. Vuelve a iniciar la autorización.'
            );
        }

        /*
            Evitamos reutilizar el state.
        */
        unset($_SESSION['google_drive_oauth_state']);

        /*
            Validamos código OAuth.
        */
        $code = $_GET['code'] ?? '';

        if ($code === '') {
            $this->mostrarResultado(
                'Código no recibido',
                'Google no ha devuelto el código de autorización.'
            );
        }

        /*
            Intercambiamos el código por tokens.
        */
        $token = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            $this->mostrarResultado(
                'Error obteniendo token',
                htmlspecialchars($token['error_description'] ?? $token['error'])
            );
        }

        $refreshToken = $client->getRefreshToken();

        if (!$refreshToken && !empty($token['refresh_token'])) {
            $refreshToken = $token['refresh_token'];
        }

        if (!$refreshToken) {
            $this->mostrarResultado(
                'Refresh token no recibido',
                'Google no ha devuelto refresh_token. Revoca el permiso de la app en tu cuenta de Google y vuelve a autorizar con prompt=consent.'
            );
        }

        /*
            Mostramos el refresh_token para copiarlo manualmente.
            En producción no lo guardes en GitHub.
        */
        $this->mostrarRefreshToken($refreshToken);
    }

    /**
     * Crea y configura el cliente de Google.
     *
     * @return Google\Client
     */
    private function crearClienteGoogle()
    {
        $client = new Google\Client();

        $client->setClientId(GOOGLE_CLIENT_ID);
        $client->setClientSecret(GOOGLE_CLIENT_SECRET);
        $client->setRedirectUri(GOOGLE_DRIVE_REDIRECT_URI);

        return $client;
    }

    /**
     * Protege el flujo OAuth para que solo lo use administración.
     *
     * @return void
     */
    private function protegerSoloAdmin()
    {
        if (!usuarioLogueado()) {
            header('Location: ' . PUBLIC_URL . 'login.php');
            exit;
        }

        /*
            Si en tu proyecto tienes usuarioEsAdminOGestor(), usamos esa función.
            Si no existe, validamos por rol de sesión.
        */
        if (function_exists('usuarioEsAdminOGestor')) {
            if (!usuarioEsAdminOGestor()) {
                http_response_code(403);
                echo 'No tienes permiso para acceder a esta autorización.';
                exit;
            }

            return;
        }

        $rol = (int)($_SESSION['rol'] ?? 0);

        if (!in_array($rol, [1, 2], true)) {
            http_response_code(403);
            echo 'No tienes permiso para acceder a esta autorización.';
            exit;
        }
    }

    /**
     * Muestra el refresh_token obtenido.
     *
     * @param string $refreshToken
     * @return void
     */
    private function mostrarRefreshToken($refreshToken)
    {
        echo '<!DOCTYPE html>';
        echo '<html lang="es">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<title>Refresh Token Google Drive</title>';
        echo '<style>
                body { font-family: Arial, sans-serif; max-width: 900px; margin: 40px auto; line-height: 1.5; }
                textarea { width: 100%; min-height: 140px; }
                .box { border: 1px solid #ddd; padding: 20px; border-radius: 10px; background: #f8f8f8; }
              </style>';
        echo '</head>';
        echo '<body>';

        echo '<h1>Refresh token obtenido correctamente</h1>';

        echo '<div class="box">';
        echo '<p>Copia este valor y guárdalo en una configuración privada del servidor, nunca en GitHub.</p>';
        echo '<textarea readonly>' . htmlspecialchars($refreshToken, ENT_QUOTES, 'UTF-8') . '</textarea>';
        echo '</div>';

        echo '<p>Después de guardarlo, puedes desactivar o eliminar temporalmente estos endpoints OAuth si ya no los necesitas.</p>';
        echo '<p><a href="' . PUBLIC_URL . 'index.php">Volver al inicio</a></p>';

        echo '</body>';
        echo '</html>';
        exit;
    }

    /**
     * Muestra un resultado simple.
     *
     * @param string $titulo
     * @param string $mensaje
     * @return void
     */
    private function mostrarResultado($titulo, $mensaje)
    {
        echo '<!DOCTYPE html>';
        echo '<html lang="es">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<title>' . htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') . '</title>';
        echo '</head>';
        echo '<body>';
        echo '<h1>' . htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') . '</h1>';
        echo '<p>' . $mensaje . '</p>';
        echo '<p><a href="' . PUBLIC_URL . 'index.php">Volver</a></p>';
        echo '</body>';
        echo '</html>';
        exit;
    }
}
