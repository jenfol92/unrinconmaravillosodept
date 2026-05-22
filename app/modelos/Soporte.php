<?php

/**
 * Modelo Soporte
 * ---------------------------------------------------------
 * Este modelo se encarga de gestionar la comunicación entre
 * los usuarios y la administración de la web.
 *
 * Funcionalidades principales:
 *
 * - Crear tickets de soporte.
 * - Guardar mensajes dentro de un ticket.
 * - Obtener tickets de un usuario.
 * - Obtener mensajes de un ticket concreto.
 * - Permitir respuestas de usuario o administración.
 * - Cambiar el estado del ticket según quién responde.
 * - Cerrar tickets de soporte.
 * - Obtener todos los tickets para el panel administrador.
 * - Gestionar sugerencias enviadas por usuarios.
 * - Obtener mensajes enviados desde el formulario público de contacto.
 *
 * Tablas principales utilizadas:
 *
 * - soporte_tickets
 * - soporte_mensajes
 * - sugerencias
 * - contacto_mensajes
 * - usuarios
 * - productos
 */

// Conexión a la base de datos.
require_once __DIR__ . '/../../config/conexion.php';

class Soporte
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
     * Al crear una instancia de Soporte, se establece la conexión
     * con la base de datos mediante conectarBD().
     */
    public function __construct()
    {
        $this->conexion = conectarBD();
    }

    /**
     * Crea un nuevo ticket de soporte.
     * ---------------------------------------------------------
     * Esta función crea primero el ticket principal y después
     * guarda el primer mensaje enviado por el usuario.
     *
     * Flujo:
     *
     * 1. Inserta un nuevo registro en soporte_tickets.
     * 2. Obtiene el ID del ticket creado.
     * 3. Inserta el primer mensaje en soporte_mensajes.
     * 4. Devuelve el ID del ticket.
     *
     * @param int $usuario_id ID del usuario que crea el ticket.
     * @param string $asunto Asunto del ticket.
     * @param string $mensaje Primer mensaje del usuario.
     *
     * @return int ID del ticket creado.
     */
    public function crearTicket($usuario_id, $asunto, $mensaje)
    {
        /*
            Insertamos el ticket principal.

            En esta tabla se guarda la información general:
            - usuario_id
            - asunto
            - estado
            - fecha
        */
        $sql = "INSERT INTO soporte_tickets (usuario_id, asunto) 
                VALUES (?, ?)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id, $asunto]);

        /*
            Obtenemos el ID del ticket recién creado.

            Este ID se utilizará para asociar el primer mensaje
            dentro de soporte_mensajes.
        */
        $ticket_id = $this->conexion->lastInsertId();

        /*
            Insertamos el primer mensaje del usuario.

            El campo remitente se guarda como 'usuario' para diferenciarlo
            de los mensajes enviados por administración.
        */
        $sql = "INSERT INTO soporte_mensajes (ticket_id, remitente, mensaje) 
                VALUES (?, 'usuario', ?)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$ticket_id, $mensaje]);

        return $ticket_id;
    }

    /**
     * Obtiene todos los tickets de un usuario concreto.
     * ---------------------------------------------------------
     * Se utiliza en el perfil del usuario para mostrar sus consultas
     * de soporte.
     *
     * @param int $usuario_id ID del usuario.
     *
     * @return array Listado de tickets del usuario.
     */
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

    /**
     * Obtiene los mensajes de un ticket.
     * ---------------------------------------------------------
     * Devuelve todos los mensajes asociados a un ticket concreto,
     * ordenados de más antiguo a más reciente.
     *
     * Se utiliza para mostrar la conversación completa en formato chat.
     *
     * @param int $ticket_id ID del ticket.
     *
     * @return array Mensajes del ticket.
     */
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

    /**
     * Envía un mensaje dentro de un ticket de soporte.
     * ---------------------------------------------------------
     * Esta función permite que un usuario o un administrador añadan
     * mensajes a un ticket ya existente.
     *
     * Antes de insertar el mensaje:
     * - Comprueba que el ticket exista.
     * - Comprueba que el ticket no esté cerrado.
     *
     * Después de insertar el mensaje:
     * - Si responde admin, el ticket pasa a estado "respondido".
     * - Si responde usuario, el ticket pasa a estado "abierto".
     *
     * @param int $ticket_id ID del ticket.
     * @param string $remitente Puede ser 'usuario' o 'admin'.
     * @param string $mensaje Texto del mensaje.
     * @param string|null $remitente_nombre Nombre visible del remitente.
     *
     * @return bool True si se envía correctamente, false si no se puede enviar.
     */
    public function enviarMensaje($ticket_id, $remitente, $mensaje, $remitente_nombre = null)
    {
        /*
            Comprobamos el estado actual del ticket.

            Si el ticket no existe o está cerrado, no se permite añadir
            nuevos mensajes.
        */
        $sql = "SELECT estado 
                FROM soporte_tickets 
                WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$ticket_id]);

        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticket || $ticket['estado'] === 'cerrado') {
            return false;
        }

        /*
            Insertamos el nuevo mensaje en soporte_mensajes.

            Se guarda:
            - ticket_id
            - remitente
            - remitente_nombre
            - mensaje
        */
        $sql = "INSERT INTO soporte_mensajes 
                (ticket_id, remitente, remitente_nombre, mensaje)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$ticket_id, $remitente, $remitente_nombre, $mensaje]);

        /*
            Actualizamos el estado del ticket según quién haya respondido.

            - admin: respondido
            - usuario: abierto
        */
        $estado = ($remitente === 'admin') ? 'respondido' : 'abierto';

        $sql = "UPDATE soporte_tickets 
                SET estado = ?
                WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$estado, $ticket_id]);

        return true;
    }

    /**
     * Obtiene todos los tickets para el panel administrador.
     * ---------------------------------------------------------
     * Devuelve los tickets junto con datos del usuario que los creó.
     *
     * Se utiliza en la sección de soporte del panel admin.
     *
     * Datos obtenidos:
     * - ID del ticket.
     * - Usuario asociado.
     * - Asunto.
     * - Estado.
     * - Fecha.
     * - Nombre, apellidos, email y localidad del usuario.
     *
     * @return array Listado de tickets para administración.
     */
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

    /**
     * Finaliza o cierra un ticket de soporte.
     * ---------------------------------------------------------
     * Esta función permite cerrar un ticket concreto siempre que
     * pertenezca al usuario indicado.
     *
     * Se utiliza para que el usuario pueda dar por resuelta una consulta.
     *
     * @param int $ticket_id ID del ticket.
     * @param int $usuario_id ID del usuario propietario del ticket.
     *
     * @return bool True si se actualiza correctamente.
     */
    public function finalizarTicket($ticket_id, $usuario_id)
    {
        $sql = "UPDATE soporte_tickets 
                SET estado = 'cerrado'
                WHERE id = ? 
                AND usuario_id = ?";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([$ticket_id, $usuario_id]);
    }

    /**
     * Obtiene todas las sugerencias para el panel administrador.
     * ---------------------------------------------------------
     * Devuelve las sugerencias enviadas por usuarios junto con
     * algunos datos del usuario.
     *
     * Se utiliza en la sección de sugerencias del panel admin.
     *
     * Datos obtenidos:
     * - ID de sugerencia.
     * - Mensaje.
     * - Fecha.
     * - Estado de lectura.
     * - Nombre, email, localidad y código postal del usuario.
     *
     * @return array Listado de sugerencias.
     */
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

    /**
     * Crea una nueva sugerencia.
     * ---------------------------------------------------------
     * Permite guardar una sugerencia enviada por un usuario registrado.
     *
     * @param int $usuario_id ID del usuario que envía la sugerencia.
     * @param string $mensaje Texto de la sugerencia.
     *
     * @return bool True si se guarda correctamente.
     */
    public function crearSugerencia($usuario_id, $mensaje)
    {
        $sql = "INSERT INTO sugerencias (usuario_id, mensaje)
                VALUES (?, ?)";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([$usuario_id, $mensaje]);
    }

    /**
     * Obtiene mensajes enviados desde el formulario público de contacto.
     * ---------------------------------------------------------
     * Estos mensajes pueden proceder de usuarios no registrados.
     *
     * Si el mensaje está relacionado con un producto, se obtiene también
     * el título del producto mediante LEFT JOIN con la tabla productos.
     *
     * Se utiliza en la sección "Contacto web" del panel administrador.
     *
     * Datos obtenidos:
     * - ID del mensaje.
     * - Producto asociado, si existe.
     * - Nombre del remitente.
     * - Email.
     * - Asunto.
     * - Mensaje.
     * - Estado leído/no leído.
     * - Fecha.
     * - Título del producto relacionado.
     *
     * @return array Listado de mensajes de contacto web.
     */
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