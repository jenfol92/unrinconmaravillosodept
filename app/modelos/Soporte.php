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
 * Crea el ticket principal y guarda el primer mensaje
 * enviado por el usuario.
 *
 * El nombre visible del usuario no se guarda aquí.
 * Se obtendrá después mediante JOIN con la tabla usuarios.
 *
 * @param int $usuario_id ID del usuario que crea el ticket.
 * @param string $asunto Asunto del ticket.
 * @param string $mensaje Primer mensaje del usuario.
 *
 * @return int ID del ticket creado.
 */
public function crearTicket($usuario_id, $asunto, $mensaje)
{
    $sql = "INSERT INTO soporte_tickets (usuario_id, asunto) 
            VALUES (?, ?)";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([
        (int)$usuario_id,
        $asunto
    ]);

    $ticket_id = $this->conexion->lastInsertId();

    $sql = "INSERT INTO soporte_mensajes 
            (ticket_id, remitente, mensaje) 
            VALUES (?, 'usuario', ?)";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([
        (int)$ticket_id,
        $mensaje
    ]);

    return $ticket_id;
}
/**
 * Obtiene todos los tickets de soporte de un usuario concreto.
 * ---------------------------------------------------------
 * Se utiliza en el perfil del usuario para mostrar sus consultas
 * de soporte.
 *
 * Devuelve los tickets ordenados desde el más reciente al más antiguo.
 *
 * La fecha se devuelve también formateada para poder mostrarla
 * fácilmente en la vista.
 *
 * @param int $usuario_id ID del usuario logueado.
 *
 * @return array Listado de tickets del usuario.
 */
public function obtenerTicketsUsuario($usuario_id)
{
    $sql = "SELECT 
                id,
                usuario_id,
                asunto,
                estado,
                fecha,
                DATE_FORMAT(fecha, '%d/%m/%Y %H:%i') AS fecha_formateada
            FROM soporte_tickets
            WHERE usuario_id = ?
            ORDER BY fecha DESC";

    $stmt = $this->conexion->prepare($sql);

    $stmt->execute([
        (int)$usuario_id
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    /**
 * Obtiene los mensajes de un ticket perteneciente a un usuario.
 * ---------------------------------------------------------
 * Esta versión se usa en el panel del usuario.
 * Evita que un usuario pueda ver tickets de otro cambiando
 * el ticket_id en la URL.
 *
 * @param int $ticket_id ID del ticket.
 * @param int $usuario_id ID del usuario logueado.
 *
 * @return array Mensajes del ticket.
 */
/**
 * Obtiene los mensajes de un ticket perteneciente a un usuario.
 * ---------------------------------------------------------
 * Solo devuelve mensajes si el ticket pertenece al usuario
 * logueado.
 *
 * El nombre visible del usuario se obtiene desde la tabla usuarios.
 *
 * @param int $ticket_id ID del ticket.
 * @param int $usuario_id ID del usuario logueado.
 *
 * @return array Mensajes del ticket.
 */
public function obtenerMensajesTicketUsuario($ticket_id, $usuario_id)
{
    $sql = "SELECT 
                sm.id,
                sm.ticket_id,
                sm.remitente,
                sm.remitente_nombre,
                sm.mensaje,
                sm.fecha,
                DATE_FORMAT(sm.fecha, '%d/%m/%Y %H:%i') AS fecha_formateada,

                CASE 
                    WHEN sm.remitente = 'usuario' THEN 
                        COALESCE(
                            NULLIF(TRIM(CONCAT(u.nombre, ' ', COALESCE(u.apellidos, ''))), ''),
                            'Usuario'
                        )
                    ELSE 
                        'Soporte'
                END AS nombre_visible

            FROM soporte_mensajes sm

            INNER JOIN soporte_tickets st 
                ON st.id = sm.ticket_id

            LEFT JOIN usuarios u 
                ON u.id = st.usuario_id

            WHERE sm.ticket_id = ?
            AND st.usuario_id = ?

            ORDER BY sm.fecha ASC";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([
        (int)$ticket_id,
        (int)$usuario_id
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

   /**
 * Obtiene los mensajes de un ticket para administración.
 * ---------------------------------------------------------
 * Devuelve la conversación completa de un ticket.
 *
 * Si el mensaje es del usuario, el nombre visible se obtiene
 * desde la tabla usuarios mediante:
 *
 * soporte_mensajes.ticket_id
 *      → soporte_tickets.id
 *      → soporte_tickets.usuario_id
 *      → usuarios.id
 *
 * Si el mensaje es de administración, se muestra "Soporte".
 *
 * @param int $ticket_id ID del ticket.
 *
 * @return array Mensajes del ticket.
 */
public function obtenerMensajesTicket($ticket_id)
{
    $sql = "SELECT 
                sm.id,
                sm.ticket_id,
                sm.remitente,
                sm.remitente_nombre,
                sm.mensaje,
                sm.fecha,
                DATE_FORMAT(sm.fecha, '%d/%m/%Y %H:%i') AS fecha_formateada,

                CASE 
                    WHEN sm.remitente = 'usuario' THEN 
                        COALESCE(
                            NULLIF(TRIM(CONCAT(u.nombre, ' ', COALESCE(u.apellidos, ''))), ''),
                            'Usuario'
                        )
                    ELSE 
                        'Soporte'
                END AS nombre_visible

            FROM soporte_mensajes sm

            INNER JOIN soporte_tickets st 
                ON st.id = sm.ticket_id

            LEFT JOIN usuarios u 
                ON u.id = st.usuario_id

            WHERE sm.ticket_id = ?

            ORDER BY sm.fecha ASC";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([
        (int)$ticket_id
    ]);

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
 * Solo cierra el ticket si pertenece al usuario indicado
 * y si no estaba cerrado previamente.
 *
 * @param int $ticket_id ID del ticket.
 * @param int $usuario_id ID del usuario propietario.
 *
 * @return bool True si realmente se cerró el ticket.
 */
public function finalizarTicket($ticket_id, $usuario_id)
{
    $sql = "UPDATE soporte_tickets 
            SET estado = 'cerrado'
            WHERE id = ? 
            AND usuario_id = ?
            AND estado <> 'cerrado'";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([
        (int)$ticket_id,
        (int)$usuario_id
    ]);

    return $stmt->rowCount() > 0;
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
    /**
 * Envía un mensaje de usuario verificando propiedad del ticket.
 * ---------------------------------------------------------
 * Solo permite responder si:
 * - El ticket existe.
 * - Pertenece al usuario logueado.
 * - No está cerrado.
 *
 * No guardamos remitente_nombre porque el nombre visible
 * se obtiene desde usuarios mediante JOIN.
 *
 * @param int $ticket_id ID del ticket.
 * @param int $usuario_id ID del usuario logueado.
 * @param string $mensaje Mensaje enviado.
 *
 * @return bool
 */
public function enviarMensajeUsuario($ticket_id, $usuario_id, $mensaje)
{
    $sql = "SELECT estado
            FROM soporte_tickets
            WHERE id = ?
            AND usuario_id = ?";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([
        (int)$ticket_id,
        (int)$usuario_id
    ]);

    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket || $ticket['estado'] === 'cerrado') {
        return false;
    }

    $sql = "INSERT INTO soporte_mensajes 
            (ticket_id, remitente, mensaje)
            VALUES (?, 'usuario', ?)";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([
        (int)$ticket_id,
        $mensaje
    ]);

    $sql = "UPDATE soporte_tickets 
            SET estado = 'abierto'
            WHERE id = ?
            AND usuario_id = ?";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([
        (int)$ticket_id,
        (int)$usuario_id
    ]);

    return true;
}
}