<?php

require_once __DIR__ . '/../../config/conexion.php';

/**
 * Clase Usuario
 * ---------------------------------------------------------
 * Modelo encargado de gestionar los datos de los usuarios.
 *
 * Funciones principales:
 * - Crear usuarios con contraseña cifrada.
 * - Obtener usuarios por email.
 * - Obtener usuarios por ID.
 * - Listar usuarios clientes para el panel de administración.
 * - Cambiar estado activo/bloqueado.
 * - Obtener compras, descargas y reseñas de un usuario.
 */
class Usuario
{
    private $conexion;

    /**
     * Constructor de la clase.
     *
     * Obtiene la conexión a la base de datos usando la función conectarBD().
     */
    public function __construct()
    {
        $this->conexion = conectarBD();
    }

    /**
     * Crea un nuevo usuario cliente.
     *
     * Seguridad:
     * - Se utiliza password_hash() para guardar la contraseña cifrada.
     * - Se utilizan consultas preparadas para evitar inyección SQL.
     *
     * Por defecto se asigna rol_id = 3, que corresponde al usuario cliente.
     *
     * @param string $nombre Nombre del usuario.
     * @param string $email Email del usuario.
     * @param string $password Contraseña en texto plano recibida del formulario.
     * @param string $apellidos Apellidos del usuario.
     * @param string $localidad Localidad del usuario.
     * @param string $cp Código postal del usuario.
     * @return bool Devuelve true si se crea correctamente, false si falla.
     */
    public function crear($nombre, $email, $password, $apellidos, $localidad, $cp)
    {
        // Ciframos la contraseña antes de guardarla en la base de datos.
        $hash = password_hash($password, PASSWORD_BCRYPT);

        /*
            Insertamos el usuario en la tabla usuarios.
            El rol 3 se asigna directamente porque el registro público
            crea usuarios de tipo cliente.
        */
        $sql = "INSERT INTO usuarios 
                    (nombre, email, password_hash, rol_id, apellidos, localidad, cp)
                VALUES 
                    (?, ?, ?, 3, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $nombre,
            $email,
            $hash,
            $apellidos,
            $localidad,
            $cp
        ]);
    }

    /**
     * Obtiene un usuario por su email.
     *
     * Se usa principalmente para:
     * - Login.
     * - Comprobar si un email ya está registrado.
     *
     * @param string $email Email del usuario.
     * @return array|false Datos del usuario o false si no existe.
     */
    public function obtenerPorEmail($email)
    {
        $sql = "SELECT * 
                FROM usuarios 
                WHERE email = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un usuario por su ID.
     *
     * Se usa en el perfil del usuario para cargar sus datos personales.
     *
     * @param int $id ID del usuario.
     * @return array|false Datos del usuario o false si no existe.
     */
    public function obtenerPorId($id)
    {
        $sql = "SELECT * 
                FROM usuarios 
                WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los usuarios clientes.
     *
     * Se usa en el panel de administración.
     *
     * Devuelve:
     * - Datos básicos del usuario.
     * - Localidad y código postal.
     * - Fecha de registro.
     * - Estado activo/bloqueado.
     * - Permiso para reseñar.
     * - Total de recursos adquiridos.
     * - Total de descargas realizadas.
     *
     * @return array Listado de usuarios clientes.
     */
    public function obtenerUsuariosClientes()
    {
        $sql = "SELECT 
                    u.id,
                    u.nombre,
                    u.apellidos,
                    u.localidad,
                    u.cp,
                    u.email,
                    u.fecha_registro,
                    u.activo,
                    u.puede_resenar,
                    COUNT(d.id) AS total_recursos_adquiridos,
                    COALESCE(SUM(d.numero_descargas), 0) AS total_descargas
                FROM usuarios u
                LEFT JOIN descargas d 
                    ON d.usuario_id = u.id
                WHERE u.rol_id = 3
                GROUP BY 
                    u.id,
                    u.nombre,
                    u.apellidos,
                    u.localidad,
                    u.cp,
                    u.email,
                    u.fecha_registro,
                    u.activo,
                    u.puede_resenar
                ORDER BY u.fecha_registro DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Activa o bloquea un usuario.
     *
     * @param int $usuario_id ID del usuario.
     * @param int $activo 1 para activo, 0 para bloqueado.
     * @return bool Resultado de la operación.
     */
    public function cambiarEstadoUsuario($usuario_id, $activo)
    {
        $sql = "UPDATE usuarios 
                SET activo = ?
                WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$activo, $usuario_id]);
    }

    /**
     * Obtiene las descargas/compras de un usuario para el panel admin.
     *
     * @param int $usuario_id ID del usuario.
     * @return array Listado de recursos adquiridos.
     */
    public function obtenerDescargasUsuario($usuario_id)
    {
        $sql = "SELECT 
                    d.id AS descarga_id,
                    d.usuario_id,
                    d.producto_id,
                    d.archivo_path,
                    d.fecha_compra,
                    d.fecha_expiracion,
                    d.max_descargas,
                    d.numero_descargas,
                    d.token_descarga,

                    p.id AS producto_id_real,
                    p.titulo,
                    p.imagen,
                    p.precio,
                    p.archivo_s3_key

                FROM descargas d

                INNER JOIN productos p 
                    ON p.id = d.producto_id

                WHERE d.usuario_id = ?

                ORDER BY d.fecha_compra DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene las reseñas realizadas por un usuario.
     *
     * @param int $usuario_id ID del usuario.
     * @return array Listado de reseñas.
     */
    public function obtenerResenasUsuario($usuario_id)
    {
        $sql = "SELECT 
                    r.id,
                    r.producto_id,
                    r.comentario,
                    r.puntuacion,
                    r.estado,
                    r.fecha,
                    p.titulo AS producto_titulo
                FROM reseñas r
                INNER JOIN productos p 
                    ON p.id = r.producto_id
                WHERE r.usuario_id = ?
                ORDER BY r.fecha DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los recursos adquiridos por un usuario para su perfil.
     *
     * Se usa en perfil_view.php.
     *
     * Devuelve información necesaria para:
     * - Mostrar recursos comprados.
     * - Generar botón de descarga.
     * - Controlar número máximo de descargas.
     * - Comprobar fecha de expiración.
     *
     * @param int $usuario_id ID del usuario.
     * @return array Recursos adquiridos.
     */
    public function obtenerRecursosAdquiridosUsuario($usuario_id)
    {
        $sql = "SELECT 
                    d.id AS descarga_id,
                    d.usuario_id,
                    d.producto_id,
                    d.archivo_path,
                    d.fecha_compra,
                    d.fecha_expiracion,
                    d.max_descargas,
                    d.numero_descargas,
                    d.token_descarga,

                    p.id AS id,
                    p.titulo,
                    p.imagen,
                    p.precio,
                    p.archivo_s3_key

                FROM descargas d

                INNER JOIN productos p 
                    ON p.id = d.producto_id

                WHERE d.usuario_id = ?

                ORDER BY d.fecha_compra DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}