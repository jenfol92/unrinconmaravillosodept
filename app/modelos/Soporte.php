<?php

// Conexión a la base de datos
require_once __DIR__ . '/../../config/conexion.php';

class Soporte
{
    private $conexion;

    public function __construct()
    {
        // Guardamos la conexión para usarla en todas las funciones
        $this->conexion = conectarBD();
    }

    // Crear un nuevo ticket de soporte
    public function crearTicket($usuario_id, $asunto, $mensaje)
    {
        // Insertamos el ticket
        $sql = "INSERT INTO soporte_tickets (usuario_id, asunto) 
                VALUES (?, ?)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id, $asunto]);

        // Obtenemos el ID del ticket creado
        $ticket_id = $this->conexion->lastInsertId();

        // Insertamos el primer mensaje del usuario
        $sql = "INSERT INTO soporte_mensajes (ticket_id, remitente, mensaje) 
                VALUES (?, 'usuario', ?)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$ticket_id, $mensaje]);

        return $ticket_id;
    }

    // Obtener tickets de un usuario concreto
    public function obtenerTicketsUsuario($usuario_id)
    {
        $sql = "SELECT *
                FROM soporte_tickets
                WHERE usuario_id = ?
                ORDER BY fecha DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener mensajes de un ticket
    public function obtenerMensajesTicket($ticket_id)
    {
        $sql = "SELECT *
                FROM soporte_mensajes
                WHERE ticket_id = ?
                ORDER BY fecha ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$ticket_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Añadir mensaje a un ticket
   public function enviarMensaje($ticket_id, $remitente, $mensaje, $remitente_nombre = null)
{
    $sql = "SELECT estado FROM soporte_tickets WHERE id = ?";
    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket || $ticket['estado'] === 'cerrado') {
        return false;
    }

    $sql = "INSERT INTO soporte_mensajes 
            (ticket_id, remitente, remitente_nombre, mensaje)
            VALUES (?, ?, ?, ?)";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$ticket_id, $remitente, $remitente_nombre, $mensaje]);

    $estado = ($remitente === 'admin') ? 'respondido' : 'abierto';

    $sql = "UPDATE soporte_tickets 
            SET estado = ?
            WHERE id = ?";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$estado, $ticket_id]);

    return true;
}
public function obtenerTicketsAdmin()
{
    $sql = "SELECT 
                t.id,
                t.usuario_id,
                t.asunto,
                t.estado,
                t.fecha,
                u.nombre AS usuario_nombre,
                u.apellidos AS usuario_apellidos,
                u.email AS usuario_email,
                u.localidad
            FROM soporte_tickets t
            INNER JOIN usuarios u ON u.id = t.usuario_id
            ORDER BY t.fecha DESC";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function finalizarTicket($ticket_id, $usuario_id)
{
    $sql = "UPDATE soporte_tickets 
            SET estado = 'cerrado'
            WHERE id = ? AND usuario_id = ?";

    $stmt = $this->conexion->prepare($sql);
    return $stmt->execute([$ticket_id, $usuario_id]);
}
//OBTENER TODAS LAS SUGERENCIAS PARA ADMIN.
public function obtenerSugerencias()
{
    $sql = "SELECT 
                s.id,
                s.mensaje,
                s.fecha,
                s.leida,
                u.nombre,
                u.email,
                u.localidad,
                u.cp
            FROM sugerencias s
            LEFT JOIN usuarios u ON u.id = s.usuario_id
            ORDER BY s.fecha DESC";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//CREAR SUGERENCIAS
public function crearSugerencia($usuario_id, $mensaje)
{
    $sql = "INSERT INTO sugerencias (usuario_id, mensaje)
            VALUES (?, ?)";

    $stmt = $this->conexion->prepare($sql);
    return $stmt->execute([$usuario_id, $mensaje]);
}
//MENSAJES DE USUARIOS NO REGISTRADOS
public function obtenerMensajesContactoAdmin()
{
    $sql = "SELECT 
                cm.id,
                cm.producto_id,
                cm.nombre,
                cm.email,
                cm.asunto,
                cm.mensaje,
                cm.leido,
                cm.fecha,
                p.titulo AS producto_titulo
            FROM contacto_mensajes cm
            LEFT JOIN productos p ON p.id = cm.producto_id
            ORDER BY cm.fecha DESC";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}