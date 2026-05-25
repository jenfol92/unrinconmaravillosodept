<?php

/**
 * Callback OAuth para Google Drive
 * ---------------------------------------------------------
 * Este archivo NO es para login.
 *
 * Google redirige aquí después de autorizar permisos de Drive.
 * Sirve para obtener el refresh_token.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/oauth.php';

if (!empty($_GET['error'])) {
    die('Google ha cancelado o rechazado la autorización de Drive.');
}

if (empty($_GET['code'])) {
    die('No se ha recibido código de autorización de Google Drive.');
}

if (
    empty($_GET['state']) ||
    empty($_SESSION['google_drive_oauth_state']) ||
    $_GET['state'] !== $_SESSION['google_drive_oauth_state']
) {
    unset($_SESSION['google_drive_oauth_state']);
    die('State inválido. Vuelve a iniciar la autorización de Drive.');
}

unset($_SESSION['google_drive_oauth_state']);

try {
    $client = new Google\Client();

    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_DRIVE_REDIRECT_URI);

    $client->addScope(Google\Service\Drive::DRIVE_FILE);
    $client->setAccessType('offline');
    $client->setPrompt('consent');

    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!empty($token['error'])) {
        echo '<h2>Error obteniendo token</h2>';
        echo '<pre>';
        print_r($token);
        echo '</pre>';
        exit;
    }

    $refreshToken = $token['refresh_token'] ?? null;

    if (!$refreshToken) {
        echo '<h2>No se ha recibido refresh_token</h2>';
        echo '<p>Puede pasar si esta cuenta ya había autorizado antes la aplicación.</p>';
        echo '<p>Solución: revoca permisos de la app en Google y vuelve a entrar por google_drive_auth.php.</p>';
        echo '<pre>';
        print_r($token);
        echo '</pre>';
        exit;
    }

} catch (Exception $e) {
    die('Error OAuth Drive: ' . htmlspecialchars($e->getMessage()));
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Refresh Token Google Drive</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 30px;">

    <h1>Refresh token generado correctamente</h1>

    <p>Copia este token y pégalo en <strong>config/oauth.php</strong>:</p>

    <textarea style="width:100%; height:140px;"><?= htmlspecialchars($refreshToken) ?></textarea>

    <p>Debe quedar así:</p>

    <pre>define('GOOGLE_DRIVE_REFRESH_TOKEN', '<?= htmlspecialchars($refreshToken) ?>');</pre>

</body>
</html>