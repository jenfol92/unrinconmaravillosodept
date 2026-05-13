document.addEventListener("DOMContentLoaded", function () {

    // Botones del sidebar
    const links = document.querySelectorAll(".admin-link");

    // Secciones del panel
    const sections = document.querySelectorAll(".admin-section");

    // Botones que abren una sección concreta
    const openButtons = document.querySelectorAll(".admin-open-section");

    // Función para abrir sección
    function abrirSeccion(sectionName) {

        links.forEach(link => link.classList.remove("active"));
        sections.forEach(section => section.classList.remove("active"));

        const target = document.getElementById(`admin-section-${sectionName}`);

        if (target) {
            target.classList.add("active");
        }

        const activeLink = document.querySelector(`.admin-link[data-section="${sectionName}"]`);

        if (activeLink) {
            activeLink.classList.add("active");
        }
    }

    // Click en sidebar
    links.forEach(link => {
        link.addEventListener("click", function () {
            abrirSeccion(this.dataset.section);
        });
    });

    // Click en botones internos
    openButtons.forEach(button => {
        button.addEventListener("click", function () {
            abrirSeccion(this.dataset.section);
        });
    });

});
// Editar producto desde la tabla
document.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-editar-producto");

    if (!btn) return;

    // Abrimos sección subir/editar
    const target = document.getElementById("admin-section-subir");

    document.querySelectorAll(".admin-section").forEach(section => {
        section.classList.remove("active");
    });

    document.querySelectorAll(".admin-link").forEach(link => {
        link.classList.remove("active");
    });

    if (target) {
        target.classList.add("active");
    }

    const subirLink = document.querySelector('.admin-link[data-section="subir"]');

    if (subirLink) {
        subirLink.classList.add("active");
    }

    // Rellenamos formulario
    document.getElementById("productoId").value = btn.dataset.id;
    document.getElementById("productoTitulo").value = btn.dataset.titulo;
    document.getElementById("productoPrecio").value = btn.dataset.precio;
    document.getElementById("productoDescripcion").value = btn.dataset.descripcion;
    document.getElementById("productoContenido").value = btn.dataset.contenido;
    document.getElementById("productoCategoria").value = btn.dataset.categoria;
    document.getElementById("productoNivel").value = btn.dataset.nivel;
    document.getElementById("productoEstado").value = btn.dataset.estado;

    // Cambiamos texto visual del formulario
    const tituloFormulario = document.getElementById("tituloFormularioProducto");

    if (tituloFormulario) {
        tituloFormulario.innerText = "Editar recurso";
    }
});
// Crear nueva categoría desde modal
document.addEventListener("DOMContentLoaded", function () {

    const formNuevaCategoria = document.getElementById("formNuevaCategoria");

    if (!formNuevaCategoria) return;

    formNuevaCategoria.addEventListener("submit", function (e) {

        e.preventDefault();

        const formData = new FormData(formNuevaCategoria);

        fetch("/UNRINCONDEPT/public/admin_ajax_crear_categoria.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {

            const respuesta = document.getElementById("respuestaNuevaCategoria");

            if (!data.ok) {
                respuesta.innerHTML = `
                    <div class="alert alert-danger">
                        ${data.error}
                    </div>
                `;
                return;
            }

            const selectCategoria = document.getElementById("productoCategoria");

            const option = document.createElement("option");
            option.value = data.categoria.id;
            option.textContent = data.categoria.nombre;
            option.selected = true;

            selectCategoria.appendChild(option);

            respuesta.innerHTML = `
                <div class="alert alert-success">
                    Categoría creada correctamente.
                </div>
            `;

            formNuevaCategoria.reset();
        })
        .catch(error => {
            console.error("Error creando categoría:", error);
        });

    });

});
// ===============================
// SOPORTE ADMIN - RESPONDER TICKET
// ===============================

document.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-responder-ticket");

    if (!btn) return;

    const ticketId = btn.dataset.ticketId;
    const asunto = btn.dataset.asunto;

    document.getElementById("ticketIdRespuesta").value = ticketId;
    document.getElementById("modalTicketTitulo").innerText = "Responder: " + asunto;

    cargarMensajesTicket(ticketId);

    const modal = new bootstrap.Modal(document.getElementById("modalResponderTicket"));
    modal.show();
});


function cargarMensajesTicket(ticketId) {

    const contenedor = document.getElementById("ticketMensajes");

    contenedor.innerHTML = "Cargando mensajes...";

    fetch(`/UNRINCONDEPT/public/admin_ajax_soporte_leer.php?ticket_id=${ticketId}`)
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

            if (!data.mensajes.length) {
                contenedor.innerHTML = "<p>No hay mensajes.</p>";
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
                            <strong>${m.remitente_nombre || (m.remitente === "admin" ? "Admin" : "Usuario")}</strong>
                            <p>${m.mensaje}</p>
                            <small>${m.fecha}</small>
                        </div>
                    </div>
                `;
            });

            contenedor.innerHTML = html;
        });
}


// Enviar respuesta admin
document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("formResponderTicket");

    if (!form) return;

    form.addEventListener("submit", function (e) {

        e.preventDefault();

        const ticketId = document.getElementById("ticketIdRespuesta").value;
        const mensaje = document.getElementById("mensajeRespuestaTicket").value;

        const formData = new FormData();
        formData.append("ticket_id", ticketId);
        formData.append("mensaje", mensaje);

        fetch("/UNRINCONDEPT/public/admin_ajax_soporte_responder.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {

            const respuesta = document.getElementById("respuestaTicketAdmin");

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

            document.getElementById("mensajeRespuestaTicket").value = "";

            cargarMensajesTicket(ticketId);
        });
    });
});