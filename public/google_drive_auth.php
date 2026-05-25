<?php

/**
 * Autorización OAuth para Google Drive
 * ---------------------------------------------------------
 * Este archivo NO es para login.
 *
 * Sirve para pedir permiso a una cuenta de Google Drive
 * y obtener un refresh_token.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/oauth.php';

$client = new Google\Client();

$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_DRIVE_REDIRECT_URI);

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
    Si no lo pones, muchas veces Google no devuelve refresh_token.
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