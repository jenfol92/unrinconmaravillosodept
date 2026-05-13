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
                id,
                nombre,
                apellidos,
                email,
                activo,
                fecha_registro
            FROM usuarios
            WHERE rol_id = 3
            ORDER BY fecha_registro DESC";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}