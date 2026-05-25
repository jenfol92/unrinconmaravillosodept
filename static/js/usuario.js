// Esperamos a que cargue el HTML
document.addEventListener("DOMContentLoaded", function () {

    // Botones del sidebar
    const links = document.querySelectorAll(".panel-link");

    // Secciones del contenido derecho
    const sections = document.querySelectorAll(".panel-section");

    links.forEach(link => {

        link.addEventListener("click", function () {

            // Quitamos active del sidebar
            links.forEach(item => item.classList.remove("active"));

            // Ocultamos todas las secciones
            sections.forEach(section => section.classList.remove("active"));

            // Activamos botón pulsado
            this.classList.add("active");

            // Leemos qué sección quiere abrir
            const sectionName = this.dataset.section;

            // Mostramos la sección derecha correspondiente
            const targetSection = document.getElementById(`section-${sectionName}`);

            if (targetSection) {
                targetSection.classList.add("active");
            }
        });

    });

});


// Envío del formulario de soporte
document.addEventListener("DOMContentLoaded", function () {

    // Buscamos el formulario de soporte
    const formSoporte = document.getElementById("formSoporte");

    // Si no existe, no hacemos nada
    if (!formSoporte) return;

    formSoporte.addEventListener("submit", function (e) {

        // Evitamos recargar la página
        e.preventDefault();

        // Recogemos los datos del formulario
        const formData = new FormData(formSoporte);

        // Enviamos el ticket a PHP
        fetch("/UNRINCONDEPT/public/ajax_soporte_crear.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {

                // Div donde mostramos respuesta
                const respuesta = document.getElementById("soporteRespuesta");

                // Si hay error
                if (!data.ok) {
                    respuesta.innerHTML = `
                        <div class="alert alert-danger">
                            ${data.error}
                        </div>
                    `;
                    return;
                }

                // Si todo ha ido bien
                respuesta.innerHTML = `
                    <div class="alert alert-success">
                        ${data.mensaje}
                    </div>
                `;

                // Limpiamos formulario
                formSoporte.reset();
            })
            .catch(error => {
                console.error("Error soporte:", error);
            });
    });
});
// Abrir conversación del ticket del usuario
document.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-ver-ticket-usuario");

    if (!btn) return;

    const ticketId = btn.dataset.ticketId;
    const asunto = btn.dataset.asunto;
    const estado = btn.dataset.ticketEstado;

    document.getElementById("ticketIdUsuarioRespuesta").value = ticketId;
    document.getElementById("modalTicketUsuarioTitulo").innerText = asunto;

    cargarMensajesTicketUsuario(ticketId);

    const textarea = document.getElementById("mensajeRespuestaUsuario");
    const btnEnviar = document.querySelector("#formResponderTicketUsuario button[type='submit']");
    const btnFinalizar = document.getElementById("btnFinalizarTicketUsuario");

    if (estado === "cerrado") {
        textarea.disabled = true;
        btnEnviar.disabled = true;
        btnFinalizar.disabled = true;
    } else {
        textarea.disabled = false;
        btnEnviar.disabled = false;
        btnFinalizar.disabled = false;
    }

    const modal = new bootstrap.Modal(
        document.getElementById("modalTicketUsuario")
    );

    modal.show();
});


// Cargar mensajes del ticket del usuario
function cargarMensajesTicketUsuario(ticketId) {

    const contenedor = document.getElementById("ticketMensajesUsuario");

    contenedor.innerHTML = "Cargando mensajes...";

    fetch(`/UNRINCONDEPT/public/ajax_usuario_soporte_leer.php?ticket_id=${ticketId}`)
        .then(res => res.json())
        .then(data => {

            if (!data.ok) {
                contenedor.innerHTML = `
                    <div class="alert alert-danger">
                        ${data.error}
                    </div>
                `;
                return;
            }

            let html = "";

            data.mensajes.forEach(m => {

                const clase = m.remitente === "admin"
                    ? "mensaje-admin"
                    : "mensaje-usuario";

                html += `
                    <div class="soporte-msg ${clase}">
                        <div class="soporte-msg-body">
                            <strong>${m.remitente_nombre || m.remitente}</strong>
                            <p>${m.mensaje}</p>
                            <small>${m.fecha}</small>
                        </div>
                    </div>
                `;
            });

            contenedor.innerHTML = html || "<p>No hay mensajes.</p>";
        })
        .catch(error => {
            console.error("Error leyendo ticket:", error);
            contenedor.innerHTML = `
                <div class="alert alert-danger">
                    Error al cargar la conversación.
                </div>
            `;
        });
}


// Responder y finalizar ticket desde el usuario
document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("formResponderTicketUsuario");

    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const ticketId = document.getElementById("ticketIdUsuarioRespuesta").value;
            const mensaje = document.getElementById("mensajeRespuestaUsuario").value;

            const formData = new FormData();
            formData.append("ticket_id", ticketId);
            formData.append("mensaje", mensaje);

            fetch("/UNRINCONDEPT/public/ajax_usuario_soporte_responder.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    const respuesta = document.getElementById("respuestaTicketUsuario");

                    if (!data.ok) {
                        respuesta.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                        return;
                    }

                    respuesta.innerHTML = `<div class="alert alert-success">${data.mensaje}</div>`;
                    document.getElementById("mensajeRespuestaUsuario").value = "";

                    cargarMensajesTicketUsuario(ticketId);
                });
        });
    }

    const btnFinalizar = document.getElementById("btnFinalizarTicketUsuario");

    if (btnFinalizar) {
        btnFinalizar.addEventListener("click", function () {

            const ticketId = document.getElementById("ticketIdUsuarioRespuesta").value;

            const formData = new FormData();
            formData.append("ticket_id", ticketId);

            fetch("/UNRINCONDEPT/public/ajax_soporte_finalizar.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    const respuesta = document.getElementById("respuestaTicketUsuario");

                    if (!data.ok) {
                        respuesta.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                        return;
                    }

                    respuesta.innerHTML = `<div class="alert alert-success">${data.mensaje}</div>`;
                    document.getElementById("mensajeRespuestaUsuario").disabled = true;
                    document.querySelector("#formResponderTicketUsuario button[type='submit']").disabled = true;
                    document.getElementById("btnFinalizarTicketUsuario").disabled = true;
                });
        });
    }

});
// Abrir modal de reseña
document.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-abrir-resena");

    if (!btn) return;

    const productoId = btn.dataset.productoId;
    const titulo = btn.dataset.productoTitulo;

    document.getElementById("resenaProductoId").value = productoId;
    document.getElementById("modalResenaTitulo").innerText = "Reseña: " + titulo;

    document.getElementById("respuestaResena").innerHTML = "";

    const modal = new bootstrap.Modal(document.getElementById("modalResena"));
    modal.show();
});


// Guardar reseña
document.addEventListener("DOMContentLoaded", function () {

    const formResena = document.getElementById("formResena");

    if (!formResena) return;

    formResena.addEventListener("submit", function (e) {

        e.preventDefault();

        const formData = new FormData(formResena);

        fetch("/UNRINCONDEPT/public/ajax_guardar_reseña.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {

                const respuesta = document.getElementById("respuestaResena");

                if (!data.ok) {
                    respuesta.innerHTML = `
                    <div class="alert alert-danger">
                        ${data.error}
                    </div>
                `;
                    return;
                }

                respuesta.innerHTML = `
                <div class="alert alert-success">
                    ${data.mensaje}
                </div>
            `;

                formResena.reset();
            })
            .catch(error => {
                console.error("Error guardando reseña:", error);
            });
    });
});
// Enviar sugerencia
document.addEventListener("DOMContentLoaded", function () {

    const formSugerencia = document.getElementById("formSugerencia");

    if (!formSugerencia) return;

    formSugerencia.addEventListener("submit", function (e) {

        e.preventDefault();

        const formData = new FormData(formSugerencia);

        fetch("/UNRINCONDEPT/public/ajax_sugerencia_crear.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {

            const respuesta = document.getElementById("respuestaSugerencia");

            if (!data.ok) {
                respuesta.innerHTML = `
                    <div class="alert alert-danger">
                        ${data.error}
                    </div>
                `;
                return;
            }

            respuesta.innerHTML = `
                <div class="alert alert-success">
                    ${data.mensaje}
                </div>
            `;

            formSugerencia.reset();
        })
        .catch(error => {
            console.error("Error sugerencia:", error);
        });
    });
});
/**
 * Muestra la alerta de seguridad y envía la acción al servidor.
 * ---------------------------------------------------------
 * Al confirmar:
 * - se registra la IP como sospechosa
 * - se cierran las sesiones
 * - se redirige al cambio/recuperación de contraseña
 */
window.enviarAlertaSeguridad = function () {

    const confirmar = confirm(
        "Se registrará la IP como sospechosa, se cerrarán todas las sesiones activas y tendrás que cambiar tu contraseña. ¿Quieres continuar?"
    );

    if (!confirmar) {
        return;
    }

    fetch("/UNRINCONDEPT/public/ajax_alerta_seguridad.php", {
        method: "POST"
    })
        /*
            Leemos como texto para poder ver errores PHP si los hubiera.
        */
        .then(res => res.text())

        .then(text => {

            console.log("Respuesta ajax_alerta_seguridad.php:", text);

            let data;

            try {
                data = JSON.parse(text);
            } catch (error) {
                console.error("La respuesta no es JSON:", text);
                alert("El servidor no ha devuelto una respuesta válida. Revisa la consola.");
                return;
            }

            if (!data.ok) {
                alert(data.mensaje || "No se pudo procesar la alerta de seguridad.");
                return;
            }

            alert(data.mensaje);

            /*
                Mandamos al usuario al flujo para cambiar contraseña.
            */
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        })

        .catch(error => {
            console.error("Error alerta seguridad:", error);
            alert("Error al enviar la alerta de seguridad.");
        });
};