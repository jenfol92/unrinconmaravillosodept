<?php

/**
 * Modelo Seguridad
 * ---------------------------------------------------------
 * Este modelo agrupa operaciones relacionadas con la seguridad
 * de las cuentas de usuario.
 *
 * En este caso se encarga de procesar una alerta cuando el usuario
 * indica que no reconoce un acceso a su cuenta.
 *
 * Responsabilidades de este modelo:
 * - Consultar los datos actuales del usuario.
 * - Registrar una alerta de seguridad.
 * - Registrar una IP sospechosa.
 * - Marcar la cuenta para cambio obligatorio de contraseña.
 * - Incrementar session_version para invalidar sesiones anteriores.
 *
 * Tablas usadas:
 * - usuarios
 * - alertas_seguridad
 * - ips_sospechosas
 */

require_once __DIR__ . '/../../config/conexion.php';

class Seguridad
{
    /**
     * Conexión PDO a la base de datos.
     *
     * @var PDO
     */
    private $conexion;

    /**
     * Constructor del modelo.
     * ---------------------------------------------------------
     * Al crear el modelo, abrimos conexión con la base de datos
     * usando la función conectarBD().
     */
    public function __construct()
    {
        $this->conexion = conectarBD();
    }

    /**
     * Procesa una alerta de acceso no reconocido.
     * ---------------------------------------------------------
     * Esta operación se realiza dentro de una transacción porque
     * afecta a varias tablas.
     *
     * Si algo falla:
     * - se revierte todo con rollBack();
     * - se lanza la excepción para que el controlador devuelva JSON.
     *
     * Acciones realizadas:
     * 1. Obtiene los datos del usuario.
     * 2. Determina la IP sospechosa.
     * 3. Inserta una alerta en alertas_seguridad.
     * 4. Inserta la IP en ips_sospechosas.
     * 5. Actualiza la seguridad del usuario:
     *    - session_version + 1
     *    - requiere_cambio_password = 1
     *
     * @param int $usuarioId ID del usuario que activa la alerta.
     * @param string|null $ipPeticion IP desde la que se recibe la petición.
     * @param string|null $userAgent Navegador/dispositivo del usuario.
     *
     * @return array Datos relevantes del proceso.
     *
     * @throws Exception Si el usuario no existe o falla la operación.
     */
    public function registrarAlertaAccesoNoReconocido($usuarioId, $ipPeticion = null, $userAgent = null)
    {
        try {
            /**
             * Iniciamos transacción.
             * -----------------------------------------------------
             * Esto garantiza que todas las operaciones se ejecuten
             * como una unidad.
             */
            $this->conexion->beginTransaction();

            /**
             * Obtenemos datos del usuario.
             * -----------------------------------------------------
             * Nos interesa especialmente:
             * - acceso_actual_ip
             * - ultimo_ip
             * - email
             *
             * acceso_actual_ip será la IP que el usuario no reconoce.
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

            $stmtUsuario = $this->conexion->prepare($sqlUsuario);
            $stmtUsuario->execute([
                $usuarioId
            ]);

            $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

            /**
             * Si el usuario no existe, detenemos el proceso.
             */
            if (!$usuario) {
                throw new Exception('Usuario no encontrado.');
            }

            /**
             * Determinamos la IP sospechosa.
             * -----------------------------------------------------
             * Prioridad:
             * 1. acceso_actual_ip guardada en la tabla usuarios.
             * 2. IP actual de la petición.
             */
            $ipSospechosa = !empty($usuario['acceso_actual_ip'])
                ? $usuario['acceso_actual_ip']
                : $ipPeticion;

            /**
             * Registramos la alerta de seguridad.
             * -----------------------------------------------------
             * Esta tabla permite que el administrador pueda revisar
             * posteriormente qué usuario informó de un acceso extraño.
             */
            $sqlAlerta = "INSERT INTO alertas_seguridad
                          (usuario_id, tipo, mensaje, ip, user_agent, fecha, estado)
                          VALUES (?, ?, ?, ?, ?, NOW(), ?)";

            $stmtAlerta = $this->conexion->prepare($sqlAlerta);

            $stmtAlerta->execute([
                $usuarioId,
                'acceso_no_reconocido',
                'El usuario indica que no reconoce el acceso actual a su cuenta.',
                $ipSospechosa,
                $userAgent,
                'pendiente'
            ]);

            /**
             * Registramos la IP como sospechosa.
             * -----------------------------------------------------
             * De momento no se bloquea automáticamente.
             * Solo se guarda para revisión.
             */
            if (!empty($ipSospechosa)) {
                $sqlIp = "INSERT INTO ips_sospechosas
                          (usuario_id, ip, motivo, fecha, estado)
                          VALUES (?, ?, ?, NOW(), ?)";

                $stmtIp = $this->conexion->prepare($sqlIp);

                $stmtIp->execute([
                    $usuarioId,
                    $ipSospechosa,
                    'IP registrada como sospechosa por acceso no reconocido.',
                    'pendiente'
                ]);
            }

            /**
             * Marcamos la cuenta como comprometida.
             * -----------------------------------------------------
             * session_version = session_version + 1:
             * - sirve para invalidar sesiones anteriores.
             *
             * requiere_cambio_password = 1:
             * - indica que el usuario debe cambiar contraseña.
             */
            $sqlUsuarioSeguridad = "UPDATE usuarios
                                    SET session_version = session_version + 1,
                                        requiere_cambio_password = 1
                                    WHERE id = ?";

            $stmtUsuarioSeguridad = $this->conexion->prepare($sqlUsuarioSeguridad);
            $stmtUsuarioSeguridad->execute([
                $usuarioId
            ]);

            /**
             * Confirmamos la transacción.
             */
            $this->conexion->commit();

            /**
             * Devolvemos información útil por si el controlador
             * necesita usarla.
             */
            return [
                'usuario' => $usuario,
                'ip_sospechosa' => $ipSospechosa
            ];

        } catch (Exception $e) {
            /**
             * Si algo falla y la transacción está activa,
             * deshacemos los cambios.
             */
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $e;
        }
    }
}