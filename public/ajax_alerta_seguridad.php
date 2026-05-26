<?php

/**
 * AJAX - ALERTA DE SEGURIDAD
 * ---------------------------------------------------------
 * Este archivo se ejecuta cuando el usuario pulsa:
 * "No reconozco este acceso".
 *
 * Acciones:
 *
 * 1. Comprueba que el usuario está logueado.
 * 2. Obtiene la IP sospechosa.
 * 3. Registra una alerta de seguridad.
 * 4. Registra la IP como sospechosa.
 * 5. Marca la cuenta para cambio obligatorio de contraseña.
 * 6. Incrementa session_version para cerrar sesiones anteriores.
 * 7. Cierra la sesión actual.
 * 8. Devuelve JSON al navegador.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/conexion.php';

header('Content-Type: application/json; charset=utf-8');

/*
    Si no hay usuario logueado, no podemos gestionar la alerta.
*/
if (empty($_SESSION['usuario_id'])) {
    echo json_encode([
        'ok' => false,
        'mensaje' => 'Debes iniciar sesión para realizar esta acción.'
    ]);
    exit;
}

$usuario_id = (int) $_SESSION['usuario_id'];

try {
    /*
        Conexión a la base de datos.
    */
    $conexion = conectarBD();

    /*
        Obtenemos datos del usuario.

        Nos interesa:
        - acceso_actual_ip: IP del acceso actual.
        - ultimo_ip: IP anterior.
        - email: por si luego quieres mostrarlo en admin.
    */
    $sqlUsuario = "SELECT 
                        id,
                        email,
                        acceso_actual,
                        acceso_actual_ip,
                        ultimo_acceso,
                        ultimo_ip
                   FROM usuarios
                   WHERE id = ?
                   LIMIT 1";

    $stmtUsuario = $conexion->prepare($sqlUsuario);
    $stmtUsuario->execute([$usuario_id]);

    $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo json_encode([
            'ok' => false,
            'mensaje' => 'Usuario no encontrado.'
        ]);
        exit;
    }

    /*
        IP sospechosa.

        Si existe acceso_actual_ip, usamos esa.
        Si todavía no existe, usamos la IP de esta petición.
    */
    $ipSospechosa = !empty($usuario['acceso_actual_ip'])
        ? $usuario['acceso_actual_ip']
        : ($_SERVER['REMOTE_ADDR'] ?? null);

    /*
        User Agent.

        Esto es información orientativa del navegador/dispositivo.
        No es imprescindible, pero ayuda en una revisión posterior.
    */
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

    /*
        Iniciamos transacción para que todo se haga junto.
    */
    $conexion->beginTransaction();

    /*
        1. Registramos la alerta de seguridad.
    */
    $sqlAlerta = "INSERT INTO alertas_seguridad
                  (usuario_id, tipo, mensaje, ip, user_agent, fecha, estado)
                  VALUES (?, ?, ?, ?, ?, NOW(), ?)";

    $stmtAlerta = $conexion->prepare($sqlAlerta);

    $stmtAlerta->execute([
        $usuario_id,
        'acceso_no_reconocido',
        'El usuario indica que no reconoce el acceso actual a su cuenta.',
        $ipSospechosa,
        $userAgent,
        'pendiente'
    ]);

    /*
        2. Registramos la IP como sospechosa.

        De momento NO la bloqueamos automáticamente.
        Solo la guardamos para revisión.
    */
    if (!empty($ipSospechosa)) {
        $sqlIp = "INSERT INTO ips_sospechosas
                  (usuario_id, ip, motivo, fecha, estado)
                  VALUES (?, ?, ?, NOW(), ?)";

        $stmtIp = $conexion->prepare($sqlIp);

        $stmtIp->execute([
            $usuario_id,
            $ipSospechosa,
            'IP registrada como sospechosa por acceso no reconocido.',
            'pendiente'
        ]);
    }

    /*
        3. Marcamos la cuenta para cambio obligatorio de contraseña.

        session_version = session_version + 1
        sirve para invalidar sesiones anteriores.

        requiere_cambio_password = 1
        indica que el usuario debe cambiar la contraseña.
    */
    $sqlUsuarioSeguridad = "UPDATE usuarios
                            SET session_version = session_version + 1,
                                requiere_cambio_password = 1
                            WHERE id = ?";

    $stmtUsuarioSeguridad = $conexion->prepare($sqlUsuarioSeguridad);
    $stmtUsuarioSeguridad->execute([$usuario_id]);

    /*
        Confirmamos los cambios en base de datos.
    */
    $conexion->commit();

    /*
        4. Cerramos la sesión actual.
    */
    session_unset();
    session_destroy();

    /*
        5. Devolvemos respuesta al JS.
    */
    echo json_encode([
        'ok' => true,
        'mensaje' => 'Se ha registrado la IP como sospechosa y se han cerrado las sesiones activas.',
        'redirect' => '/UNRINCONDEPT/public/recuperar-password.php?seguridad=1'
    ]);
    exit;

} catch (Exception $e) {

    /*
        Si algo falla y la transacción está activa, la deshacemos.
    */
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }

    echo json_encode([
        'ok' => false,
        'mensaje' => 'Error al procesar la alerta de seguridad.',
        'debug' => $e->getMessage()
    ]);
    exit;
}