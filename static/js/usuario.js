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
document.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-ver-ticket-usuario");

    if (!btn) return;

    const ticketId = btn.dataset.ticketId;
    const asunto = btn.dataset.asunto;

    document.getElementById("modalTicketUsuarioTitulo").innerText = asunto;

    cargarMensajesTicketUsuario(ticketId);

    const modal = new bootstrap.Modal(
        document.getElementById("modalTicketUsuario")
    );

    modal.show();
});


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
        });
}