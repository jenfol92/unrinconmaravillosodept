<?php

/**
 * Callback de OAuth Google
 * ---------------------------------------------------------
 * Google redirige aquí después de que el usuario inicie sesión.
 *
 * Este archivo:
 * - Valida state.
 * - Intercambia el code por access token.
 * - Obtiene datos básicos del usuario.
 * - Crea o vincula el usuario en la base de datos.
 * - Inicia sesión en la web.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/oauth.php';
require_once __DIR__ . '/../app/modelos/Usuario.php';

if (!empty($_GET['error'])) {
    header('Location: /UNRINCONDEPT/public/login.php?error=oauth_cancelado');
    exit;
}

if (empty($_GET['code'])) {
    header('Location: /UNRINCONDEPT/public/login.php?error=oauth_sin_codigo');
    exit;
}

if (
    empty($_GET['state']) ||
    empty($_SESSION['google_oauth_state']) ||
    $_GET['state'] !== $_SESSION['google_oauth_state']
) {
    unset($_SESSION['google_oauth_state']);

    header('Location: /UNRINCONDEPT/public/login.php?error=oauth_state_invalido');
    exit;
}

unset($_SESSION['google_oauth_state']);

try {
    $client = new Google\Client();

    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_REDIRECT_URI);

    $client->addScope(Google\Service\Oauth2::USERINFO_EMAIL);
    $client->addScope(Google\Service\Oauth2::USERINFO_PROFILE);

    /*
        Intercambiamos el código recibido por un token.
    */
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!empty($token['error'])) {
        throw new Exception($token['error_description'] ?? $token['error']);
    }

    $client->setAccessToken($token);

    /*
        Obtenemos datos básicos del perfil Google.
    */
    $oauth = new Google\Service\Oauth2($client);
    $googleUser = $oauth->userinfo->get();

    $email = $googleUser->getEmail();

    if (empty($email)) {
        throw new Exception('Google no ha devuelto un email válido.');
    }

    /*
        Creamos o vinculamos usuario.
    */
    $usuarioModel = new Usuario();

    $usuario = $usuarioModel->autenticarConGoogle([
        'google_id' => $googleUser->getId(),
        'email' => $email,
        'nombre' => $googleUser->getName(),
        'avatar' => $googleUser->getPicture()
    ]);

    /*
        Iniciamos sesión en la aplicación.
    */
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'] ?? 'Usuario';
    $_SESSION['rol'] = $usuario['rol_id'] ?? 3;
    
    $usuarioModel->registrarAccesoUsuario($usuario['id']);

    header('Location: /UNRINCONDEPT/public/perfil.php');
    exit;

} catch (Exception $e) {
    header('Location: /UNRINCONDEPT/public/login.php?error=oauth_google');
    exit;
}