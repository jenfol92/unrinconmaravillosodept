<?php

/**
 * Inicio de OAuth con Google
 * ---------------------------------------------------------
 * Este archivo inicia el flujo OAuth.
 *
 * Cuando el usuario pulsa "Entrar con Google":
 * - Se crea el cliente OAuth.
 * - Se genera un state de seguridad.
 * - Se redirige al usuario a Google.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/oauth.php';

$client = new Google\Client();

$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);

/*
    Scopes mínimos para login:
    - email
    - perfil público básico
*/
$client->addScope(Google\Service\Oauth2::USERINFO_EMAIL);
$client->addScope(Google\Service\Oauth2::USERINFO_PROFILE);

/*
    Para login no necesitamos refresh token.
*/
$client->setAccessType('online');

/*
    Fuerza selector de cuenta de Google.
*/
$client->setPrompt('select_account');

/*
    State anti-CSRF.
    Sirve para comprobar que la respuesta viene de un flujo iniciado
    desde nuestra propia web.
*/
$state = bin2hex(random_bytes(16));
$_SESSION['google_oauth_state'] = $state;

$client->setState($state);

/*
    Redirección a Google.
*/
$authUrl = $client->createAuthUrl();

header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
exit;