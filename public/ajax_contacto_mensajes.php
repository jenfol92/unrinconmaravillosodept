<?php

require_once __DIR__ . '/../config/conexion.php';

header('Content-Type: application/json');

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$asunto = trim($_POST['asunto'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');
$producto_id = $_POST['producto_id'] ?? null;

if ($nombre === '' || $email === '' || $asunto === '' || $mensaje === '') {
    echo json_encode([
        'ok' => false,
        'error' => 'Completa todos los campos.'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'ok' => false,
        'error' => 'Introduce un email válido.'
    ]);
    exit;
}

$conexion = conectarBD();

$sql = "INSERT INTO contacto_mensajes 
            (producto_id, nombre, email, asunto, mensaje)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
$stmt->execute([
    $producto_id ?: null,
    $nombre,
    $email,
    $asunto,
    $mensaje
]);

echo json_encode([
    'ok' => true,
    'mensaje' => 'Tu mensaje se ha enviado correctamente. Si la consulta lo requiere, recibirás respuesta por email.'
]);

exit;