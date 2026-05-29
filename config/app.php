<?php

/**
 * Configuración general de la aplicación.
 * ---------------------------------------------------------
 * APP_ENV permite distinguir entre entorno local y producción.
 *
 * BASE_URL:
 * - Raíz general del proyecto.
 * - Se usa para static, imágenes, CSS, JS, vendor público, etc.
 *
 * PUBLIC_URL:
 * - Raíz de los archivos públicos PHP.
 * - En local apunta a /public/.
 * - En producción apunta directamente al dominio.
 */

$host = $_SERVER['HTTP_HOST'] ?? '';

if ($host === 'localhost' || str_starts_with($host, '127.0.0.1')) {

    define('APP_ENV', 'local');

    // Raíz del proyecto en local.
    define('BASE_URL', 'http://localhost/UNRINCONDEPT/');

    // Archivos públicos en local.
    define('PUBLIC_URL', BASE_URL . 'public/');

} else {

    define('APP_ENV', 'production');

    // Raíz del dominio en producción.
    define('BASE_URL', 'https://unrinconmaravillosodept.es/');

    // En producción has subido public a la raíz.
    define('PUBLIC_URL', BASE_URL);

}