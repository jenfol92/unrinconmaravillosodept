<?php

/**
 * SoporteController
 * ---------------------------------------------------------
 * Controlador encargado de gestionar acciones AJAX relacionadas
 * con soporte, tickets, mensajes, consultas sobre productos
 * y sugerencias de usuarios.
 *
 * Este controlador NO carga vistas.
 * Todos sus métodos devuelven respuestas JSON porque son llamados
 * desde JavaScript mediante fetch/AJAX.
 *
 * Responsabilidades:
 * - Comprobar permisos de usuario.
 * - Validar datos recibidos por GET o POST.
 * - Crear tickets de soporte.
 * - Leer mensajes de tickets.
 * - Responder tickets desde usuario o administración.
 * - Finalizar tickets.
 * - Crear consultas relacionadas con productos.
 * - Guardar sugerencias de usuarios logueados.
 *
 * Modelos usados:
 * - Soporte:
 *   Gestiona tickets, mensajes, finalización de tickets
 *   y creación de sugerencias.
 *
 * - Producto:
 *   Se usa únicamente cuando una consulta de soporte está
 *   relacionada con un producto concreto.
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../modelos/Soporte.php';
require_once __DIR__ . '/../modelos/Producto.php';

class SoporteController
{
    /**
     * Modelo de soporte.
     *
     * Se utiliza para:
     * - Crear tickets.
     * - Leer mensajes.
     * - Enviar respuestas.
     * - Finalizar tickets.
     * - Guardar sugerencias.
     */
    private $soporteModel;

    /**
     * Modelo de producto.
     *
     * Se utiliza únicamente para comprobar la existencia de un producto
     * cuando el usuario envía una consulta desde la ficha de producto.
     */
    private $productoModel;

    /**
     * Constructor del controlador.
     * ---------------------------------------------------------
     * Se ejecuta automáticamente al crear una instancia de SoporteController.
     *
     * Aquí inicializamos los modelos para no tener que crear:
     * new Soporte()
     * new Producto()
     *
     * en cada método del controlador.
     */
    public function __construct()
    {
        $this->soporteModel = new Soporte();
        $this->productoModel = new Producto();
    }

    /**
     * Lee los mensajes de un ticket desde el panel de administración.
     * ---------------------------------------------------------
     * Acción usada cuando un administrador o gestor abre una conversación
     * de soporte para revisar sus mensajes.
     *
     * Archivo público que llama a este método:
     * - public/admin_ajax_soporte_leer.php
     *
     * Entrada esperada por GET:
     * - ticket_id
     *
     * Respuesta correcta:
     * {
     *   "ok": true,
     *   "mensajes": [...]
     * }
     *
     * @return void
     */
    public function leerTicketAdminAjax()
    {
        $this->prepararRespuestaJson();

        /*
            Comprobamos permisos.

            usuarioEsAdminOGestor() viene de includes/session.php.
            Según tu estructura:
            - rol 1: administrador
            - rol 2: gestor / profesor / usuario autorizado
        */
        if (!usuarioEsAdminOGestor()) {
            responderNoAutorizado();
        }

        /*
            Recogemos el ID del ticket recibido por GET.
        */
        $ticketId = isset($_GET['ticket_id'])
            ? (int) $_GET['ticket_id']
            : 0;

        /*
            Validamos que el ticket sea correcto.
        */
        if ($ticketId <= 0) {
            $this->responderJson([
                'ok' => false,
                'error' => 'Ticket no válido'
            ]);
        }

        try {
            /*
                Obtenemos los mensajes del ticket desde el modelo Soporte.
            */
            $mensajes = $this->soporteModel->obtenerMensajesTicket($ticketId);

            /*
                Devolvemos los mensajes al navegador.
            */
            $this->responderJson([
                'ok' => true,
                'mensajes' => $mensajes
            ]);

        } catch (Exception $e) {
            $this->responderJson([
                'ok' => false,
                'error' => 'Error cargando mensajes del ticket.'
            ]);
        }
    }

    /**
     * Responde a un ticket desde administración.
     * ---------------------------------------------------------
     * Acción usada por un administrador o gestor para contestar
     * una conversación de soporte desde el panel de administración.
     *
     * Archivo público que llama a este método:
     * - public/admin_ajax_soporte_responder.php
     *
     * Entrada esperada por POST:
     * - ticket_id
     * - mensaje
     *
     * Respuesta correcta:
     * {
     *   "ok": true,
     *   "mensaje": "Respuesta enviada correctamente."
     * }
     *
     * @return void
     */
    public function responderTicketAdminAjax()
    {
        $this->prepararRespuestaJson();

        /*
            Solo administradores o gestores pueden responder desde admin.
        */
        if (!usuarioEsAdminOGestor()) {
            responderNoAutorizado();
        }

        /*
            Recogemos los datos enviados por POST.
        */
        $ticketId = isset($_POST['ticket_id'])
            ? (int) $_POST['ticket_id']
            : 0;

        $mensaje = trim($_POST['mensaje'] ?? '');

        /*
            Validamos datos obligatorios.
        */
        if ($ticketId <= 0 || $mensaje === '') {
            $this->responderJson([
                'ok' => false,
                'error' => 'Datos incompletos'
            ]);
        }

        try {
           
    $ok = $this->soporteModel->enviarMensaje(
    $ticketId,
    'admin',
    $mensaje,
    'Soporte'
);

            if (!$ok) {
                $this->responderJson([
                    'ok' => false,
                    'error' => 'Esta consulta está cerrada y no admite nuevos mensajes.'
                ]);
            }

            $this->responderJson([
                'ok' => true,
                'mensaje' => 'Respuesta enviada correctamente.'
            ]);

        } catch (Exception $e) {
            $this->responderJson([
                'ok' => false,
                'error' => 'Error enviando la respuesta.'
            ]);
        }
    }


    /**
     * Crea un ticket de soporte desde el panel del usuario.
     * ---------------------------------------------------------
     * Acción usada cuando un usuario logueado abre una consulta
     * desde su sección de soporte.
     *
     * Archivo público que llama a este método:
     * - public/ajax_soporte_crear.php
     *
     * Entrada esperada por POST:
     * - asunto
     * - mensaje
     *
     * Respuesta correcta:
     * {
     *   "ok": true,
     *   "ticket_id": 1,
     *   "mensaje": "Tu consulta se ha enviado correctamente."
     * }
     *
     * @return void
     */
    public function crearTicketAjax()
    {
        $this->prepararRespuestaJson();

        /*
            Comprobamos que exista usuario logueado.

            usuarioLogueado() viene de includes/session.php.
        */
        if (!usuarioLogueado()) {
            responderNoAutorizado();
        }

        /*
            Recogemos los datos del formulario.
        */
        $asunto = trim($_POST['asunto'] ?? '');
        $mensaje = trim($_POST['mensaje'] ?? '');

        /*
            Validamos que asunto y mensaje no estén vacíos.
        */
        if ($asunto === '' || $mensaje === '') {
            $this->responderJson([
                'ok' => false,
                'error' => 'Completa asunto y mensaje'
            ]);
        }

        try {
            /*
                Creamos el ticket usando el ID del usuario logueado.

                usuarioId() viene de includes/session.php.
            */
            $ticketId = $this->soporteModel->crearTicket(
                usuarioId(),
                $asunto,
                $mensaje
            );

            /*
                Respuesta correcta.
            */
            $this->responderJson([
                'ok' => true,
                'ticket_id' => $ticketId,
                'mensaje' => 'Tu consulta se ha enviado correctamente.'
            ]);

        } catch (Exception $e) {
            $this->responderJson([
                'ok' => false,
                'error' => 'Error creando la consulta.'
            ]);
        }
    }

    /**
     * Finaliza un ticket de soporte.
     * ---------------------------------------------------------
     * Acción usada por un usuario logueado para cerrar/finalizar
     * una consulta desde su panel.
     *
     * Archivo público que llama a este método:
     * - public/ajax_soporte_finalizar.php
     *
     * Entrada esperada por POST:
     * - ticket_id
     *
     * Respuesta correcta:
     * {
     *   "ok": true,
     *   "mensaje": "Consulta finalizada correctamente."
     * }
     *
     * @return void
     */
    public function finalizarTicketAjax()
    {
        $this->prepararRespuestaJson();

        /*
            Solo un usuario logueado puede finalizar sus consultas.
        */
        if (!usuarioLogueado()) {
            responderNoAutorizado();
        }

        /*
            Recogemos el ID del ticket.
        */
        $ticketId = isset($_POST['ticket_id'])
            ? (int) $_POST['ticket_id']
            : 0;

        /*
            Validamos el ticket.
        */
        if ($ticketId <= 0) {
            $this->responderJson([
                'ok' => false,
                'error' => 'Ticket no válido'
            ]);
        }

        try {
            /*
                Finalizamos el ticket.

                Pasamos también usuarioId() para que el modelo pueda comprobar
                que el ticket pertenece realmente al usuario logueado.
            */
            $this->soporteModel->finalizarTicket(
                $ticketId,
                usuarioId()
            );

            /*
                Respuesta correcta.
            */
            $this->responderJson([
                'ok' => true,
                'mensaje' => 'Consulta finalizada correctamente.'
            ]);

        } catch (Exception $e) {
            $this->responderJson([
                'ok' => false,
                'error' => 'Error finalizando la consulta.'
            ]);
        }
    }

    /**
     * Crea un ticket de soporte relacionado con un producto.
     * ---------------------------------------------------------
     * Acción usada cuando un usuario logueado consulta a la autora
     * desde la ficha de un producto.
     *
     * Archivo público que llama a este método:
     * - public/ajax_soporte_producto.php
     *
     * Entrada esperada por POST:
     * - producto_id
     * - mensaje
     *
     * Respuesta correcta:
     * {
     *   "ok": true,
     *   "mensaje": "Tu consulta se ha enviado correctamente..."
     * }
     *
     * @return void
     */
    public function crearTicketProductoAjax()
    {
        $this->prepararRespuestaJson();

        /*
            Para que la autora pueda responder, el usuario debe estar logueado.
        */
        if (!usuarioLogueado()) {
            responderNoAutorizado(
                'Para que la autora pueda responderte, debes iniciar sesión.'
            );
        }

        /*
            Recogemos los datos enviados por POST.
        */
        $productoId = isset($_POST['producto_id'])
            ? (int) $_POST['producto_id']
            : 0;

        $mensaje = trim($_POST['mensaje'] ?? '');

        /*
            Validamos producto.
        */
        if ($productoId <= 0) {
            $this->responderJson([
                'ok' => false,
                'error' => 'Producto no encontrado.'
            ]);
        }

        /*
            Validamos mensaje.
        */
        if ($mensaje === '') {
            $this->responderJson([
                'ok' => false,
                'error' => 'Debes escribir un mensaje.'
            ]);
        }

        try {
            /*
                Comprobamos que el producto existe en base de datos.
            */
            $producto = $this->productoModel->obtenerProductosID($productoId);

            if (!$producto) {
                $this->responderJson([
                    'ok' => false,
                    'error' => 'Producto no encontrado en la base de datos.'
                ]);
            }

            /*
                Construimos el asunto del ticket usando el título del producto.
            */
            $asunto = 'Consulta sobre: ' . $producto['titulo'];

            /*
                Creamos el ticket de soporte.
            */
            $this->soporteModel->crearTicket(
                usuarioId(),
                $asunto,
                $mensaje
            );

            /*
                Respuesta correcta.
            */
            $this->responderJson([
                'ok' => true,
                'mensaje' => 'Tu consulta se ha enviado correctamente. Podrás ver la respuesta en tu panel.'
            ]);

        } catch (Exception $e) {
            $this->responderJson([
                'ok' => false,
                'error' => 'Error enviando la consulta.'
            ]);
        }
    }

    /**
     * Lee los mensajes de un ticket desde el panel del usuario.
     * ---------------------------------------------------------
     * Esta acción se ejecuta cuando el usuario abre una conversación
     * de soporte desde su panel personal.
     *
     * Archivo público que llama a este método:
     * - public/ajax_usuario_soporte_leer.php
     *
     * Entrada esperada por GET:
     * - ticket_id
     *
     * Respuesta correcta:
     * {
     *   "ok": true,
     *   "mensajes": [...]
     * }
     *
     * @return void
     */
    public function leerTicketUsuarioAjax()
    {
        $this->prepararRespuestaJson();

        /*
            Comprobamos que el usuario esté logueado.
        */
        if (!usuarioLogueado()) {
            responderNoAutorizado();
        }

        /*
            Recogemos el ID del ticket recibido por GET.
        */
        $ticketId = isset($_GET['ticket_id'])
            ? (int) $_GET['ticket_id']
            : 0;

        /*
            Validamos que el ticket sea válido.
        */
        if ($ticketId <= 0) {
            $this->responderJson([
                'ok' => false,
                'error' => 'Ticket no válido'
            ]);
        }

        try {
            /*
                Obtenemos los mensajes del ticket.

                IMPORTANTE:
                Lo ideal es que el modelo Soporte compruebe que el ticket
                pertenece al usuario logueado. Si obtenerMensajesTicket()
                no comprueba propiedad, un usuario podría intentar leer
                otro ticket cambiando el ticket_id en la URL.
            */
            $mensajes = $this->soporteModel->obtenerMensajesTicket($ticketId);

            /*
                Respuesta correcta.
            */
            $this->responderJson([
                'ok' => true,
                'mensajes' => $mensajes
            ]);

        } catch (Exception $e) {
            $this->responderJson([
                'ok' => false,
                'error' => 'Error cargando la conversación.'
            ]);
        }
    }

    /**
     * Responde a un ticket desde el panel del usuario.
     * ---------------------------------------------------------
     * Esta acción permite que un usuario logueado añada un nuevo
     * mensaje a una conversación de soporte.
     *
     * Archivo público que llama a este método:
     * - public/ajax_usuario_soporte_responder.php
     *
     * Entrada esperada por POST:
     * - ticket_id
     * - mensaje
     *
     * Respuesta correcta:
     * {
     *   "ok": true,
     *   "mensaje": "Respuesta enviada correctamente."
     * }
     *
     * @return void
     */
    public function responderTicketUsuarioAjax()
    {
        $this->prepararRespuestaJson();

        /*
            Comprobamos que el usuario esté logueado.
        */
        if (!usuarioLogueado()) {
            responderNoAutorizado();
        }

        /*
            Recogemos los datos enviados por POST.
        */
        $ticketId = isset($_POST['ticket_id'])
            ? (int) $_POST['ticket_id']
            : 0;

        $mensaje = trim($_POST['mensaje'] ?? '');

        /*
            Validamos datos obligatorios.
        */
        if ($ticketId <= 0 || $mensaje === '') {
            $this->responderJson([
                'ok' => false,
                'error' => 'Datos incompletos'
            ]);
        }

        try {
            /*
                Nombre visible del remitente.

                Si en sesión no existe nombre_usuario, usamos "Usuario".
            */
            $remitenteNombre = $_SESSION['nombre_usuario'] ?? 'Usuario';

            /*
                Guardamos el mensaje del usuario.

                El segundo parámetro identifica al remitente:
                - usuario
                - admin
            */
    $ok = $this->soporteModel->enviarMensajeUsuario(
    $ticketId,
    usuarioId(),
    $mensaje
);

            /*
                Si el modelo devuelve false, interpretamos que el ticket
                no admite nuevos mensajes, por ejemplo porque está cerrado.
            */
            if (!$ok) {
                $this->responderJson([
                    'ok' => false,
                    'error' => 'Esta consulta está cerrada y no admite nuevos mensajes.'
                ]);
            }

            /*
                Respuesta correcta.
            */
            $this->responderJson([
                'ok' => true,
                'mensaje' => 'Respuesta enviada correctamente.'
            ]);

        } catch (Exception $e) {
            $this->responderJson([
                'ok' => false,
                'error' => 'Error enviando la respuesta.'
            ]);
        }
    }

    /**
     * Guarda una sugerencia enviada por un usuario logueado.
     * ---------------------------------------------------------
     * Acción usada cuando un usuario envía una sugerencia desde
     * la web mediante AJAX.
     *
     * Archivo público que llama a este método:
     * - public/ajax_sugerencia.php
     *
     * Entrada esperada por POST:
     * - mensaje
     *
     * Respuesta correcta:
     * {
     *   "ok": true,
     *   "mensaje": "Tu sugerencia se ha enviado correctamente."
     * }
     *
     * @return void
     */
    public function guardarSugerenciaAjax()
    {
        $this->prepararRespuestaJson();

        /*
            Las sugerencias pertenecen a usuarios registrados.
        */
        if (!usuarioLogueado()) {
            responderNoAutorizado(
                'Debes iniciar sesión para enviar sugerencias.'
            );
        }

        /*
            Recogemos el mensaje enviado por POST.
        */
        $mensaje = trim($_POST['mensaje'] ?? '');

        /*
            Validamos que el mensaje no esté vacío.
        */
        if ($mensaje === '') {
            $this->responderJson([
                'ok' => false,
                'error' => 'Debes escribir una sugerencia.'
            ]);
        }

        try {
            /*
                Guardamos la sugerencia en base de datos.

                usuarioId() viene de includes/session.php.
            */
            $this->soporteModel->crearSugerencia(
                usuarioId(),
                $mensaje
            );

            /*
                Respuesta correcta.
            */
            $this->responderJson([
                'ok' => true,
                'mensaje' => 'Tu sugerencia se ha enviado correctamente.'
            ]);

        } catch (Exception $e) {
            $this->responderJson([
                'ok' => false,
                'error' => 'No se pudo enviar la sugerencia. Inténtalo de nuevo más tarde.'
            ]);
        }
    }

    /**
     * Prepara la respuesta HTTP como JSON.
     * ---------------------------------------------------------
     * Centralizamos aquí el header para no repetirlo completo
     * en todos los métodos.
     *
     * @return void
     */
    private function prepararRespuestaJson()
    {
        header('Content-Type: application/json; charset=utf-8');
    }

    /**
     * Devuelve una respuesta JSON y detiene la ejecución.
     * ---------------------------------------------------------
     * Evita repetir en cada método:
     *
     * echo json_encode(...);
     * exit;
     *
     * JSON_UNESCAPED_UNICODE evita que los acentos se conviertan
     * en secuencias tipo \u00e1.
     *
     * @param array $data Datos que se devolverán al navegador.
     * @return void
     */
    private function responderJson(array $data)
    {
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}