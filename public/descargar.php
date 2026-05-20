<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Producto.php';
require_once __DIR__ . '/../app/servicios/R2Service.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: /UNRINCONDEPT/public/login.php');
    exit;
}

$token = $_GET['token'] ?? '';

if ($token === '') {
    die('Token de descarga no válido.');
}

$usuarioId = (int)$_SESSION['usuario_id'];

$model = new Producto();

$descarga = $model->obtenerDescargaPorToken($token, $usuarioId);

if (!$descarga) {
    die('No tienes permiso para descargar este recurso.');
}

if (empty($descarga['archivo_s3_key'])) {
    die('Este producto no tiene archivo asociado.');
}

$numeroDescargas = (int)($descarga['numero_descargas'] ?? 0);
$maxDescargas = (int)($descarga['max_descargas'] ?? 5);

if ($numeroDescargas >= $maxDescargas) {
    die('Has alcanzado el límite máximo de descargas.');
}

if (!empty($descarga['fecha_expiracion'])) {
    $fechaExpiracion = strtotime($descarga['fecha_expiracion']);

    if ($fechaExpiracion !== false && $fechaExpiracion < time()) {
        die('El enlace de descarga ha caducado.');
    }
}

/*
    Si has llegado hasta aquí:
    - el usuario está logueado;
    - el token existe;
    - el token pertenece a ese usuario;
    - no ha superado el máximo de descargas;
    - el enlace no está caducado;
    - el producto tiene archivo asociado en Cloudflare R2.

    Ahora generamos una URL temporal firmada de Cloudflare R2.
*/

try {
    $r2Service = new R2Service();

    $urlTemporal = $r2Service->generarUrlDescargaTemporal(
        $descarga['archivo_s3_key'],
        10
    );

    /*
        Incrementamos el contador justo antes de redirigir
        al usuario a la descarga real.
    */
    $model->incrementarNumeroDescargas((int)$descarga['descarga_id']);

    /*
        Redirigimos al usuario a Cloudflare R2.
        La URL temporal caduca en 10 minutos.
    */
    header('Location: ' . $urlTemporal);
    exit;

} catch (Exception $e) {
    die('Error preparando la descarga: ' . htmlspecialchars($e->getMessage()));
}