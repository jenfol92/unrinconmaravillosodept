<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Usuario.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], [1, 2])) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

$usuario_id = $_POST['usuario_id'] ?? null;
$activo = $_POST['activo'] ?? null;

if (!$usuario_id || $activo === null) {
    echo json_encode(['ok' => false, 'error' => 'Datos incompletos']);
    exit;
}

$model = new Usuario();
$model->cambiarEstadoUsuario($usuario_id, $activo);

echo json_encode([
    'ok' => true,
    'mensaje' => ((int)$activo === 1) ? 'Usuario activado.' : 'Usuario bloqueado.'
]);

exit;