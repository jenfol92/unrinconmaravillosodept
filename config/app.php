<?php

/**
 * Configuración general de la aplicación.
 * ---------------------------------------------------------
 * APP_ENV permite distinguir entre entorno local y producción.
 * APP_BASE_URL se usa para generar enlaces absolutos, por ejemplo,
 * el enlace de recuperación de contraseña.
 */

$host = $_SERVER['HTTP_HOST'] ?? '';

if ($host === 'localhost' || str_starts_with($host, '127.0.0.1')) {

    define('APP_ENV', 'local');
    define('BASE_URL', 'http://localhost/UNRINCONDEPT/');

} else {

    define('APP_ENV', 'production');
    define('BASE_URL', 'https://unrinconmaravillosodept.es/');

}