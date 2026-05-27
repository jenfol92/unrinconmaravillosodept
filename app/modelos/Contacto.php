<?php

/**
 * Modelo Contacto
 * ---------------------------------------------------------
 * Este modelo agrupa las operaciones relacionadas con los
 * mensajes enviados desde formularios de contacto públicos.
 *
 * Responsabilidades:
 * - Guardar mensajes de contacto en la base de datos.
 * - Mantener el SQL separado de los archivos públicos.
 *
 * Tabla usada:
 * - contacto_mensajes
 */

require_once __DIR__ . '/../../config/conexion.php';

class Contacto
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
     * Al crear el modelo, abrimos conexión con la base de datos.
     */
    public function __construct()
    {
        $this->conexion = conectarBD();
    }

    /**
     * Guarda un mensaje de contacto.
     * ---------------------------------------------------------
     * Inserta en la tabla contacto_mensajes los datos enviados
     * desde el formulario público.
     *
     * producto_id puede ser null si la consulta no está asociada
     * a ningún producto concreto.
     *
     * @param int|null $productoId ID del producto relacionado, si existe.
     * @param string $nombre Nombre del remitente.
     * @param string $email Email del remitente.
     * @param string $asunto Asunto del mensaje.
     * @param string $mensaje Contenido del mensaje.
     *
     * @return bool
     */
    public function guardarMensaje($productoId, $nombre, $email, $asunto, $mensaje)
    {
        $sql = "INSERT INTO contacto_mensajes 
                    (producto_id, nombre, email, asunto, mensaje)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $productoId,
            $nombre,
            $email,
            $asunto,
            $mensaje
        ]);
    }
}