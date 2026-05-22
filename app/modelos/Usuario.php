<?php

/**
 * Modelo Usuario
 * ---------------------------------------------------------
 * Este modelo se encarga de gestionar todas las operaciones
 * relacionadas con los usuarios de la aplicación.
 *
 * Funcionalidades principales:
 *
 * - Crear usuarios clientes.
 * - Cifrar contraseñas con password_hash().
 * - Obtener usuarios por email.
 * - Obtener usuarios por ID.
 * - Listar usuarios clientes para el panel de administración.
 * - Activar o bloquear usuarios.
 * - Consultar descargas/compras de un usuario.
 * - Consultar reseñas realizadas por un usuario.
 * - Obtener recursos adquiridos para mostrarlos en el perfil.
 *
 * Tablas principales utilizadas:
 *
 * - usuarios
 * - descargas
 * - productos
 * - reseñas
 */

require_once __DIR__ . '/../../config/conexion.php';

class Usuario
{
    /**
     * Conexión PDO con la base de datos.
     *
     * @var PDO
     */
    private $conexion;

    /**
     * Constructor del modelo.
     * ---------------------------------------------------------
     * Al crear una instancia de Usuario, se establece la conexión
     * con la base de datos mediante la función conectarBD().
     */
    public function __construct()
    {
        $this->conexion = conectarBD();
    }

    /**
     * Crea un nuevo usuario cliente.
     * ---------------------------------------------------------
     * Esta función se utiliza durante el registro público de usuarios.
     *
     * Seguridad aplicada:
     *
     * - La contraseña no se guarda en texto plano.
     * - Se cifra mediante password_hash() con PASSWORD_BCRYPT.
     * - Se utiliza una consulta preparada con PDO para evitar inyección SQL.
     *
     * Por defecto, el usuario creado recibe rol_id = 3, que corresponde
     * al usuario cliente.
     *
     * Datos guardados:
     *
     * - nombre
     * - email
     * - password_hash
     * - rol_id
     * - apellidos
     * - localidad
     * - cp
     *
     * @param string $nombre Nombre del usuario.
     * @param string $email Email del usuario.
     * @param string $password Contraseña recibida desde el formulario.
     * @param string $apellidos Apellidos del usuario.
     * @param string $localidad Localidad del usuario.
     * @param string $cp Código postal del usuario.
     *
     * @return bool True si el usuario se crea correctamente, false si falla.
     */
    public function crear($nombre, $email, $password, $apellidos, $localidad, $cp)
    {
        /*
            Ciframos la contraseña antes de guardarla.

            Esto evita almacenar contraseñas en texto plano en la base de datos.
            PASSWORD_BCRYPT genera un hash seguro adecuado para autenticación.
        */
        $hash = password_hash($password, PASSWORD_BCRYPT);

        /*
            Insertamos el usuario en la tabla usuarios.

            El rol 3 se asigna directamente porque este formulario crea
            usuarios de tipo cliente.
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
     * ---------------------------------------------------------
     * Se utiliza principalmente en dos situaciones:
     *
     * 1. Login:
     *    - Buscar el usuario por email.
     *    - Obtener su password_hash para comprobar la contraseña con password_verify().
     *
     * 2. Registro:
     *    - Comprobar si el email ya existe antes de crear una nueva cuenta.
     *
     * @param string $email Email del usuario.
     *
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
     * ---------------------------------------------------------
     * Se utiliza para cargar los datos personales del usuario
     * en su perfil.
     *
     * También puede utilizarse desde administración para consultar
     * información concreta de una cuenta.
     *
     * @param int $id ID del usuario.
     *
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
     * Obtiene todos los usuarios clientes para el panel administrador.
     * ---------------------------------------------------------
     * Devuelve únicamente usuarios con rol_id = 3.
     *
     * Además de los datos personales básicos, calcula:
     *
     * - Total de recursos adquiridos.
     * - Total de descargas realizadas.
     *
     * Estos datos se obtienen cruzando usuarios con la tabla descargas.
     *
     * Datos devueltos:
     *
     * - id
     * - nombre
     * - apellidos
     * - localidad
     * - cp
     * - email
     * - fecha_registro
     * - activo
     * - puede_resenar
     * - total_recursos_adquiridos
     * - total_descargas
     *
     * @return array Listado de usuarios clientes.
     */
    public function obtenerUsuariosClientes()
    {
        /*
            LEFT JOIN permite que aparezcan usuarios aunque todavía
            no hayan comprado ni descargado ningún recurso.
        */
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
     * ---------------------------------------------------------
     * Permite cambiar el estado de una cuenta desde el panel admin.
     *
     * El campo activo puede funcionar así:
     *
     * - 1: usuario activo.
     * - 0: usuario bloqueado o desactivado.
     *
     * @param int $usuario_id ID del usuario.
     * @param int $activo Nuevo estado del usuario.
     *
     * @return bool True si se actualiza correctamente.
     */
    public function cambiarEstadoUsuario($usuario_id, $activo)
    {
        $sql = "UPDATE usuarios 
                SET activo = ?
                WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $activo,
            $usuario_id
        ]);
    }

    /**
     * Obtiene las descargas o compras de un usuario.
     * ---------------------------------------------------------
     * Se utiliza principalmente desde el panel administrador
     * para consultar qué recursos ha adquirido un usuario.
     *
     * Devuelve información de:
     *
     * - Registro de descarga.
     * - Usuario.
     * - Producto adquirido.
     * - Fechas de compra y expiración.
     * - Límite máximo de descargas.
     * - Número de descargas usadas.
     * - Token de descarga.
     * - Archivo asociado en Cloudflare R2.
     *
     * @param int $usuario_id ID del usuario.
     *
     * @return array Listado de descargas/compras del usuario.
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
     * ---------------------------------------------------------
     * Se utiliza para consultar la actividad del usuario respecto
     * a valoraciones y comentarios.
     *
     * Devuelve:
     *
     * - ID de la reseña.
     * - Producto valorado.
     * - Comentario.
     * - Puntuación.
     * - Estado de la reseña.
     * - Fecha.
     * - Título del producto.
     *
     * @param int $usuario_id ID del usuario.
     *
     * @return array Listado de reseñas del usuario.
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
     * ---------------------------------------------------------
     * Esta función se utiliza en perfil_view.php para mostrar al cliente
     * los recursos que puede descargar.
     *
     * Devuelve información necesaria para:
     *
     * - Mostrar productos comprados.
     * - Generar botones de descarga.
     * - Comprobar número máximo de descargas.
     * - Mostrar número de descargas utilizadas.
     * - Comprobar fecha de expiración.
     * - Descargar mediante token seguro.
     *
     * Diferencia respecto a obtenerDescargasUsuario():
     *
     * - Esta función está pensada para la vista del usuario.
     * - obtenerDescargasUsuario() se usa más para consulta desde admin.
     *
     * @param int $usuario_id ID del usuario.
     *
     * @return array Recursos adquiridos por el usuario.
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
  /**
 * Guarda un token de recuperación de contraseña.
 * ---------------------------------------------------------
 * Se guarda el hash del token, no el token real.
 *
 * @param int $usuario_id
 * @param string $token_hash
 * @return bool
 */
public function guardarTokenRecuperacion($usuario_id, $token_hash)
{
    $sql = "INSERT INTO password_resets 
            (usuario_id, token_hash, fecha_expiracion, usado)
            VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR), 0)";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([
        $usuario_id,
        $token_hash
    ]);
}

/**
 * Invalida tokens anteriores de un usuario.
 * ---------------------------------------------------------
 * Evita que existan varios enlaces válidos al mismo tiempo.
 *
 * @param int $usuario_id
 * @return bool
 */
public function invalidarTokensRecuperacionUsuario($usuario_id)
{
    $sql = "UPDATE password_resets
            SET usado = 1
            WHERE usuario_id = ?
            AND usado = 0";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([
        $usuario_id
    ]);
}

/**
 * Obtiene un token válido de recuperación.
 * ---------------------------------------------------------
 * Solo devuelve tokens:
 * - no usados
 * - no caducados
 *
 * @param string $token_hash
 * @return array|false
 */
public function obtenerTokenRecuperacionValido($token_hash)
{
    $sql = "SELECT pr.*, u.email, u.nombre
            FROM password_resets pr
            INNER JOIN usuarios u ON u.id = pr.usuario_id
            WHERE pr.token_hash = ?
            AND pr.usado = 0
            AND pr.fecha_expiracion >= NOW()
            LIMIT 1";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([
        $token_hash
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Actualiza la contraseña de un usuario.
 * ---------------------------------------------------------
 * La contraseña se guarda cifrada con password_hash().
 *
 * @param int $usuario_id
 * @param string $password
 * @return bool
 */
public function actualizarPassword($usuario_id, $password)
{
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $sql = "UPDATE usuarios
            SET password_hash = ?
            WHERE id = ?";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([
        $hash,
        $usuario_id
    ]);
}

/**
 * Marca un token de recuperación como usado.
 * ---------------------------------------------------------
 * Impide que el mismo enlace pueda utilizarse más de una vez.
 *
 * @param int $id
 * @return bool
 */
public function marcarTokenRecuperacionUsado($id)
{
    $sql = "UPDATE password_resets
            SET usado = 1
            WHERE id = ?";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([
        $id
    ]);
}
/**
 * Inicia sesión o registra un usuario mediante Google OAuth.
 * ---------------------------------------------------------
 * Casos contemplados:
 *
 * 1. Si ya existe un usuario con google_id:
 *    - Devuelve ese usuario.
 *
 * 2. Si no existe google_id, pero sí existe el email:
 *    - Vincula esa cuenta local con Google.
 *    - Devuelve el usuario actualizado.
 *
 * 3. Si no existe ni google_id ni email:
 *    - Crea un nuevo usuario cliente.
 *    - Devuelve el usuario creado.
 *
 * @param array $datos Datos recibidos desde Google.
 *
 * @return array Usuario de la base de datos.
 */
public function autenticarConGoogle($datos)
{
    $googleId = $datos['google_id'];
    $email = $datos['email'];
    $nombre = $datos['nombre'] ?? 'Usuario';
    $avatar = $datos['avatar'] ?? null;

    /*
        1. Buscar usuario ya vinculado con Google.
    */
    $sql = "SELECT *
            FROM usuarios
            WHERE google_id = ?
            LIMIT 1";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$googleId]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        return $usuario;
    }

    /*
        2. Buscar usuario existente por email.
        Si existe, lo vinculamos con Google.
    */
    $sql = "SELECT *
            FROM usuarios
            WHERE email = ?
            LIMIT 1";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$email]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $sql = "UPDATE usuarios
                SET google_id = ?,
                    auth_provider = 'google',
                    avatar = ?
                WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            $googleId,
            $avatar,
            $usuario['id']
        ]);

        return $this->obtenerPorId($usuario['id']);
    }

    /*
        3. Crear nuevo usuario cliente.
        Se genera una contraseña aleatoria porque el acceso real
        será mediante Google.
    */
    $passwordTemporal = password_hash(bin2hex(random_bytes(32)), PASSWORD_BCRYPT);

    $sql = "INSERT INTO usuarios
            (nombre, apellidos, email, password, google_id, auth_provider, avatar, rol_id, fecha_registro)
            VALUES (?, '', ?, ?, ?, 'google', ?, 3, NOW())";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([
        $nombre,
        $email,
        $passwordTemporal,
        $googleId,
        $avatar
    ]);

    $usuarioId = $this->conexion->lastInsertId();

    return $this->obtenerPorId($usuarioId);
}
}