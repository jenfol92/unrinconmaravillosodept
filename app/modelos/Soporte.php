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
                u.email AS usuario_email
            FROM soporte_tickets t
            INNER JOIN usuarios u ON u.id = t.usuario_id
            ORDER BY t.fecha DESC";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}