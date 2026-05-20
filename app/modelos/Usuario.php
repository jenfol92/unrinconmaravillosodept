<?php
require_once __DIR__ . '/../../config/conexion.php';
//Esta clase permite crear un nuevo usuario con contraseña encriptada e identificarse como usuario registrado por email.
class Usuario {

    private $conexion;

    public function __construct() {
        $this->conexion = conectarBD();
    }

// Crear un nuevo usuario con contraseña encriptada para proteger de inyeccion SQL
    public function crear($nombre, $email,$password,$apellidos) {

        $hash = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO usuarios (nombre, email, password_hash, rol_id,apellidos)
                VALUES (?, ?, ?, 3,?)";

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$nombre, $email,$hash,$apellidos]);
    }

    public function obtenerPorEmail($email) {

        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$email]);

        return $stmt->fetch();
    }
    //Obtener usuario por ID (para perfil)
    public function obtenerPorId($id) {

        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch();
    }
// Obtener todos los usuarios clientes (rol_id = 3)
public function obtenerUsuariosClientes()
{
    $sql = "SELECT 
                u.id,
                u.nombre,
                u.apellidos,
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
                u.email,
                u.fecha_registro,
                u.activo,
                u.puede_resenar
            ORDER BY u.fecha_registro DESC";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Obtener clientes con total de descargas/compras


// Bloquear / desbloquear usuario
public function cambiarEstadoUsuario($usuario_id, $activo)
{
    $sql = "UPDATE usuarios 
            SET activo = ?
            WHERE id = ?";

    $stmt = $this->conexion->prepare($sql);
    return $stmt->execute([$activo, $usuario_id]);
}


// Obtener descargas/compras de un usuario para admin.
public function obtenerDescargasUsuario($usuario_id)
{
    /*
        Esta función se usa en el panel admin para ver qué recursos
        tiene adquiridos un usuario concreto.
    */

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


// Obtener reseñas de un usuario
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
            INNER JOIN productos p ON p.id = r.producto_id
            WHERE r.usuario_id = ?
            ORDER BY r.fecha DESC";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$usuario_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Función para consultar los recursos adquiridos por el usuario en su perfil.
public function obtenerRecursosAdquiridosUsuario($usuario_id)
{
    /*
        Esta función se usa en perfil_view.php.

        Devuelve los recursos que el usuario puede descargar.
        La descarga se controla desde la tabla descargas.

        Importante:
        - token_descarga sirve para generar el botón Descargar.
        - numero_descargas y max_descargas sirven para mostrar el límite.
        - fecha_expiracion sirve para saber si el recurso ha caducado.
    */

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