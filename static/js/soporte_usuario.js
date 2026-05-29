/**
 * SOPORTE USUARIO - CHAT AJAX
 * ---------------------------------------------------------
 * Este archivo gestiona el modal de soporte del usuario.
 *
 * Funcionalidad:
 * - Abre el modal de conversación de un ticket.
 * - Carga los mensajes del ticket mediante AJAX.
 * - Envía respuestas del usuario mediante AJAX.
 * - Finaliza/cierra consultas mediante AJAX.
 * - Pinta nombre del remitente, fecha y mensaje.
 *
 * Requisitos:
 * - Debe existir una constante global BASE_URL antes de cargar este archivo.
 * - Debe estar cargado Bootstrap JS si usamos bootstrap.Modal.
 *
 * Ejemplo en la vista PHP:
 *
 * <script>
 *     const BASE_URL = "<?= BASE_URL ?>";
 * </script>
 *
 * <script src="<?= BASE_URL ?>static/js/soporte_usuario.js"></script>
 */

document.addEventListener("DOMContentLoaded", function () {

    /**
     * Referencias a elementos del modal de soporte.
     * ---------------------------------------------------------
     * Si alguno de estos elementos no existe en la vista actual,
     * el script se detendrá para evitar errores en consola.
     */
    const modalElement = document.getElementById("modalTicketUsuario");
    const modalTitulo = document.getElementById("modalTicketUsuarioTitulo");
    const inputTicketId = document.getElementById("ticketIdUsuarioRespuesta");
    const contenedorMensajes = document.getElementById("ticketMensajesUsuario");
    const formResponder = document.getElementById("formResponderTicketUsuario");
    const textareaMensaje = document.getElementById("mensajeRespuestaUsuario");
    const btnFinalizar = document.getElementById("btnFinalizarTicketUsuario");
    const respuestaAjax = document.getElementById("respuestaTicketUsuario");

    /**
     * Si no estamos en una página donde exista el modal de soporte,
     * no ejecutamos nada.
     */
    if (
        !modalElement ||
        !modalTitulo ||
        !inputTicketId ||
        !contenedorMensajes ||
        !formResponder ||
        !textareaMensaje ||
        !btnFinalizar ||
        !respuestaAjax
    ) {
        return;
    }

    /**
     * ABRIR MODAL DE CONVERSACIÓN
     * ---------------------------------------------------------
     * Usamos delegación de eventos para detectar cualquier botón
     * con la clase .btn-ver-ticket-usuario.
     *
     * El botón debe tener:
     * - data-ticket-id
     * - data-asunto
     *
     * Ejemplo:
     *
     * <button
     *     type="button"
     *     class="btn btn-sm btn-outline-primary btn-ver-ticket-usuario"
     *     data-ticket-id="<?= (int)$ticket['id'] ?>"
     *     data-asunto="<?= htmlspecialchars($ticket['asunto']) ?>">
     *     Ver conversación
     * </button>
     */
    document.addEventListener("click", function (e) {

        const btn = e.target.closest(".btn-ver-ticket-usuario");

        if (!btn) {
            return;
        }

        const ticketId = btn.dataset.ticketId;
        const asunto = btn.dataset.asunto || "Conversación";

        if (!ticketId) {
            alert("No se ha encontrado el ID del ticket.");
            return;
        }

        /**
         * Guardamos el ID del ticket seleccionado en el input oculto.
         */
        inputTicketId.value = ticketId;

        /**
         * Actualizamos el título del modal.
         */
        modalTitulo.textContent = asunto;

        /**
         * Limpiamos datos anteriores por si el usuario abre otro ticket.
         */
        textareaMensaje.value = "";
        textareaMensaje.disabled = false;
        btnFinalizar.disabled = false;
        respuestaAjax.innerHTML = "";

        /**
         * Cargamos la conversación.
         */
        cargarMensajesTicketUsuario(ticketId);

        /**
         * Mostramos el modal de Bootstrap.
         */
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    });

    /**
     * ENVIAR RESPUESTA DEL USUARIO
     * ---------------------------------------------------------
     * Este submit no recarga la página.
     * Envía ticket_id y mensaje al endpoint AJAX.
     */
    formResponder.addEventListener("submit", function (e) {
        e.preventDefault();

        const ticketId = inputTicketId.value;
        const mensaje = textareaMensaje.value.trim();

        if (!ticketId) {
            mostrarRespuesta("No se ha encontrado el ticket seleccionado.", "danger");
            return;
        }

        if (mensaje === "") {
            mostrarRespuesta("Escribe un mensaje antes de enviar.", "warning");
            return;
        }

        const formData = new FormData();
        formData.append("ticket_id", ticketId);
        formData.append("mensaje", mensaje);

        fetch(PUBLIC_URL + "ajax_usuario_soporte_responder.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.text())
            .then(text => {

                /**
                 * Leemos primero como texto para detectar errores PHP.
                 * Si PHP devuelve un Warning/Fatal Error, aquí veremos HTML.
                 */
                console.log("Respuesta responder soporte usuario:", text);

                let data;

                try {
                    data = JSON.parse(text);
                } catch (error) {
                    console.error("Respuesta no JSON al responder ticket:", text);
                    mostrarRespuesta("El servidor no ha devuelto JSON válido. Revisa la consola.", "danger");
                    return;
                }

                if (!data.ok) {
                    mostrarRespuesta(data.error || "No se pudo enviar la respuesta.", "danger");
                    return;
                }

                /**
                 * Limpiamos el textarea y recargamos los mensajes.
                 */
                textareaMensaje.value = "";
                mostrarRespuesta(data.mensaje || "Respuesta enviada correctamente.", "success");
                cargarMensajesTicketUsuario(ticketId);
            })
            .catch(error => {
                console.error("Error enviando respuesta de soporte:", error);
                mostrarRespuesta("Error enviando la respuesta.", "danger");
            });
    });

    /**
     * FINALIZAR CONSULTA
     * ---------------------------------------------------------
     * Cierra el ticket seleccionado.
     */
    btnFinalizar.addEventListener("click", function () {

        const ticketId = inputTicketId.value;

        if (!ticketId) {
            mostrarRespuesta("No se ha encontrado el ticket seleccionado.", "danger");
            return;
        }

        const confirmado = confirm("¿Seguro que quieres finalizar esta consulta?");

        if (!confirmado) {
            return;
        }

        const formData = new FormData();
        formData.append("ticket_id", ticketId);

        fetch(PUBLIC_URL + "ajax_soporte_finalizar.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.text())
            .then(text => {

                console.log("Respuesta finalizar soporte usuario:", text);

                let data;

                try {
                    data = JSON.parse(text);
                } catch (error) {
                    console.error("Respuesta no JSON al finalizar ticket:", text);
                    mostrarRespuesta("El servidor no ha devuelto JSON válido. Revisa la consola.", "danger");
                    return;
                }

                if (!data.ok) {
                    mostrarRespuesta(data.error || "No se pudo finalizar la consulta.", "danger");
                    return;
                }

                mostrarRespuesta(data.mensaje || "Consulta finalizada correctamente.", "success");

                /**
                 * Bloqueamos controles para evitar nuevos envíos
                 * sobre una consulta ya cerrada.
                 */
                textareaMensaje.disabled = true;
                btnFinalizar.disabled = true;

                /**
                 * Recargamos para actualizar el estado del ticket en la lista.
                 */
                setTimeout(function () {
                    location.reload();
                }, 800);
            })
            .catch(error => {
                console.error("Error finalizando consulta:", error);
                mostrarRespuesta("Error finalizando la consulta.", "danger");
            });
    });

    /**
     * Carga los mensajes de un ticket del usuario.
     *
     * @param {string|number} ticketId ID del ticket.
     */
    function cargarMensajesTicketUsuario(ticketId) {

        contenedorMensajes.innerHTML = "Cargando mensajes...";

        fetch(PUBLIC_URL + "ajax_usuario_soporte_leer.php?ticket_id=" + encodeURIComponent(ticketId))
            .then(res => res.text())
            .then(text => {

                console.log("Respuesta leer soporte usuario:", text);

                let data;

                try {
                    data = JSON.parse(text);
                } catch (error) {
                    console.error("Respuesta no JSON al leer ticket:", text);

                    contenedorMensajes.innerHTML = `
                        <div class="alert alert-danger">
                            El servidor no ha devuelto JSON válido.
                        </div>
                    `;

                    return;
                }

                if (!data.ok) {
                    contenedorMensajes.innerHTML = `
                        <div class="alert alert-danger">
                            ${escaparHtml(data.error || "No se pudo cargar la conversación.")}
                        </div>
                    `;

                    return;
                }

                pintarMensajes(data.mensajes || []);
            })
            .catch(error => {
                console.error("Error cargando mensajes de soporte:", error);

                contenedorMensajes.innerHTML = `
                    <div class="alert alert-danger">
                        Error cargando la conversación.
                    </div>
                `;
            });
    }

    /**
     * Pinta los mensajes dentro del modal.
     *
     * @param {Array} mensajes Listado de mensajes recibidos desde PHP.
     */
    function pintarMensajes(mensajes) {

        if (mensajes.length === 0) {
            contenedorMensajes.innerHTML = `
                <div class="alert alert-info">
                    Todavía no hay mensajes en esta conversación.
                </div>
            `;
            return;
        }

        contenedorMensajes.innerHTML = mensajes.map(function (msg) {

            const esUsuario = msg.remitente === "usuario";

            /**
             * Clases CSS diferenciadas para usuario y soporte/admin.
             */
            const clase = esUsuario
                ? "soporte-mensaje soporte-mensaje-usuario"
                : "soporte-mensaje soporte-mensaje-admin";

            /**
             * El modelo debe devolver nombre_visible.
             * Fallbacks por seguridad:
             * - remitente_nombre
             * - Usuario / Soporte
             */
            const nombre = msg.nombre_visible
                || msg.remitente_nombre
                || (esUsuario ? "Usuario" : "Soporte");

            /**
             * La fecha ideal viene desde MySQL como fecha_formateada:
             * DATE_FORMAT(sm.fecha, '%d/%m/%Y %H:%i') AS fecha_formateada
             *
             * No usamos fecha.js aquí porque la fecha del chat debe venir
             * de base de datos, no de la fecha actual del navegador.
             */
            const fecha = msg.fecha_formateada || msg.fecha || "";

            return `
                <div class="${clase}">
                    <div class="soporte-mensaje-cabecera">
                        <strong>${escaparHtml(nombre)}</strong>
                        <small>${escaparHtml(fecha)}</small>
                    </div>

                    <div class="soporte-mensaje-texto">
                        ${escaparHtml(msg.mensaje || "")}
                    </div>
                </div>
            `;
        }).join("");
    }

    /**
     * Muestra una respuesta debajo del formulario del modal.
     *
     * @param {string} mensaje Texto a mostrar.
     * @param {string} tipo Tipo Bootstrap: success, danger, warning, info.
     */
    function mostrarRespuesta(mensaje, tipo) {
        respuestaAjax.innerHTML = `
            <div class="alert alert-${tipo}">
                ${escaparHtml(mensaje)}
            </div>
        `;
    }

    /**
     * Escapa texto antes de insertarlo en HTML.
     * ---------------------------------------------------------
     * Evita que un mensaje escrito por usuario pueda inyectar HTML.
     *
     * @param {string} texto Texto original.
     * @returns {string} Texto escapado.
     */
    function escaparHtml(texto) {
        return String(texto)
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }
});