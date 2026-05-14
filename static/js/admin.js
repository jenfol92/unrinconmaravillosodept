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
document.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-ver-resenas-producto");

    if (!btn) return;

    const productoId = btn.dataset.productoId;
    const titulo = btn.dataset.productoTitulo;

    document.getElementById("modalResenasTitulo").innerText = "Reseñas: " + titulo;

    cargarResenasProductoAdmin(productoId);

    const modal = new bootstrap.Modal(
        document.getElementById("modalResenasProductoAdmin")
    );

    modal.show();
});


function cargarResenasProductoAdmin(productoId) {

    const contenedor = document.getElementById("contenedorResenasProductoAdmin");

    contenedor.innerHTML = "Cargando reseñas...";

    fetch(`/UNRINCONDEPT/public/ajax_reseñas_producto.php?producto_id=${productoId}`)
        .then(res => res.json())
        .then(data => {

            if (!data.ok) {
                contenedor.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                return;
            }

            if (!data.resenas.length) {
                contenedor.innerHTML = `<p>No hay reseñas para este producto.</p>`;
                return;
            }

            let html = "";

            data.resenas.forEach(r => {

                html += `
                    <div class="admin-ticket mb-3">
                        <div>
                            <strong>${r.nombre} ${r.apellidos ?? ""}</strong>
                            <div class="text-warning">${"★".repeat(r.puntuacion)}${"☆".repeat(5 - r.puntuacion)}</div>
                            <p>${r.comentario}</p>
                            <small>${r.email} · ${r.fecha} · Estado: ${r.estado}</small>
                        </div>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger btn-denunciar-resena"
                            data-resena-id="${r.id}"
                            data-usuario-id="${r.usuario_id}">
                            Denunciar y bloquear reseñas
                        </button>
                    </div>
                `;
            });

            contenedor.innerHTML = html;
        });
}

//VER RESEÑAS
document.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-denunciar-resena");

    if (!btn) return;

    if (!confirm("¿Seguro que quieres denunciar esta reseña y bloquear al usuario para futuras reseñas?")) {
        return;
    }

    const formData = new FormData();
    formData.append("resena_id", btn.dataset.resenaId);
    formData.append("usuario_id", btn.dataset.usuarioId);

    fetch("/UNRINCONDEPT/public/ajax_denunciar_reseña.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {

            if (!data.ok) {
                alert(data.error);
                return;
            }

            alert(data.mensaje);

            btn.closest(".admin-ticket").remove();
        });
});
//PAGINACION
document.addEventListener("DOMContentLoaded", function () {

    if (!document.getElementById("adminProductosTbody")) return;

    cargarProductosAdmin(1);

    const btnFiltrar = document.getElementById("btnFiltrarProductosAdmin");

    if (btnFiltrar) {
        btnFiltrar.addEventListener("click", function () {
            cargarProductosAdmin(1);
        });
    }

    const buscador = document.getElementById("adminBuscarProducto");

    if (buscador) {
        buscador.addEventListener("keyup", function () {
            cargarProductosAdmin(1);
        });
    }
});

function cargarProductosAdmin(pagina = 1) {

    const tbody = document.getElementById("adminProductosTbody");
    const paginacion = document.getElementById("adminProductosPaginacion");

    const busqueda = document.getElementById("adminBuscarProducto")?.value || "";
    const categoria = document.getElementById("adminFiltroCategoria")?.value || "";
    const estado = document.getElementById("adminFiltroEstado")?.value || "";

    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center py-4">
                Cargando productos...
            </td>
        </tr>
    `;

    const url = `/UNRINCONDEPT/public/ajax_admin_panel_productos.php?pagina=${pagina}` +
        `&busqueda=${encodeURIComponent(busqueda)}` +
        `&categoria=${encodeURIComponent(categoria)}` +
        `&estado=${encodeURIComponent(estado)}`;

    fetch(url)
        .then(res => res.json())
        .then(data => {

            if (!data.ok) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-danger">
                            ${data.error}
                        </td>
                    </tr>
                `;
                return;
            }

            pintarProductosAdmin(data.productos);
            pintarPaginacionProductosAdmin(data.total_paginas, data.pagina_actual);
        });
}

function pintarProductosAdmin(productos) {

    const tbody = document.getElementById("adminProductosTbody");

    if (!productos || productos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    No hay productos.
                </td>
            </tr>
        `;
        return;
    }

    let html = "";

    productos.forEach(p => {

        const precio = parseFloat(p.precio || 0).toFixed(2);

        const estadoBadge = p.estado === "activo"
            ? `<span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>`
            : `<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inactivo</span>`;

        html += `
            <tr>
                <td>
                    <div class="admin-product-info">
                        <img 
                            src="/UNRINCONDEPT/static/images/img/${p.imagen}" 
                            alt="${p.titulo}"
                        >
                        <div>
                            <strong>${p.titulo}</strong>
                            <div class="text-muted small">ID #${p.id}</div>
                        </div>
                    </div>
                </td>

                <td>
                    <span class="admin-badge">${p.categoria_nombre ?? ""}</span>
                </td>

                <td>
                    <strong>${precio} €</strong>
                </td>

                <td>${p.total_descargas ?? 0}</td>

                <td>${p.total_resenas ?? 0}</td>

                <td>${p.total_clicks ?? 0}</td>

                <td>${estadoBadge}</td>

                <td class="text-end">
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary btn-editar-producto"
                        data-id="${p.id}"
                        data-titulo="${escapeHtml(p.titulo)}"
                        data-precio="${p.precio}"
                        data-descripcion="${escapeHtml(p.descripcion ?? "")}"
                        data-contenido="${escapeHtml(p.contenido ?? "")}"
                        data-categoria="${p.categoria_id}"
                        data-nivel="${p.nivel_id}"
                        data-estado="${p.estado}">
                        <i class="bi bi-pencil"></i>
                    </button>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-warning btn-ver-resenas-producto"
                        data-producto-id="${p.id}"
                        data-producto-titulo="${escapeHtml(p.titulo)}">
                        <i class="bi bi-star"></i>
                    </button>

                    <a
                        href="/UNRINCONDEPT/public/detalle.php?id=${p.id}"
                        class="btn btn-sm btn-outline-secondary"
                        target="_blank">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
}

function pintarPaginacionProductosAdmin(totalPaginas, paginaActual) {

    const contenedor = document.getElementById("adminProductosPaginacion");

    if (!contenedor || totalPaginas <= 1) {
        contenedor.innerHTML = "";
        return;
    }

    let html = "";

    if (paginaActual > 1) {
        html += `
            <button 
                class="btn btn-outline-primary btn-sm mx-1"
                onclick="cargarProductosAdmin(${paginaActual - 1})">
                ‹ Anterior
            </button>
        `;
    }

    for (let i = 1; i <= totalPaginas; i++) {
        const clase = i === paginaActual ? "btn-primary" : "btn-outline-primary";

        html += `
            <button 
                class="btn ${clase} btn-sm mx-1"
                onclick="cargarProductosAdmin(${i})">
                ${i}
            </button>
        `;
    }

    if (paginaActual < totalPaginas) {
        html += `
            <button 
                class="btn btn-outline-primary btn-sm mx-1"
                onclick="cargarProductosAdmin(${paginaActual + 1})">
                Siguiente ›
            </button>
        `;
    }

    contenedor.innerHTML = html;
}

function escapeHtml(text) {
    return String(text)
        .replaceAll("&", "&amp;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;");
}


//FUNCION PARA VER TODOS LOS RECURSOS ADQUIRIDOS DE UN USUARIO Y LAS DESCARGAS QUE HA REALIZADO .
document.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-ver-descargas-usuario");

    if (!btn) return;

    const usuarioId = btn.dataset.usuarioId;
    const nombre = btn.dataset.usuarioNombre;

    document.getElementById("modalAdminUsuariosTitulo").innerText =
        "Descargas de " + nombre;

    const contenedor = document.getElementById("modalAdminUsuariosContenido");
    contenedor.innerHTML = "Cargando descargas...";

    fetch(`/UNRINCONDEPT/public/admin_ajax_descargas_usuario.php?usuario_id=${usuarioId}`)
        .then(res => res.json())
        .then(data => {

            if (!data.ok) {
                contenedor.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                new bootstrap.Modal(document.getElementById("modalAdminUsuarios")).show();
                return;
            }

            if (!data.descargas.length) {
                contenedor.innerHTML = `<p>No tiene descargas.</p>`;
                new bootstrap.Modal(document.getElementById("modalAdminUsuarios")).show();
                return;
            }

            let html = `
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Nº descargas</th>
                                <th>Máximo</th>
                                <th>Fecha compra</th>
                                <th>Expira</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            data.descargas.forEach(d => {
                html += `
                    <tr>
                        <td>${d.titulo}</td>
                        <td>${d.numero_descargas ?? 0}</td>
                        <td>${d.max_descargas ?? 0}</td>
                        <td>${d.fecha_compra ?? ""}</td>
                        <td>${d.fecha_expiracion ?? ""}</td>
                    </tr>
                `;
            });

            html += `</tbody></table></div>`;

            contenedor.innerHTML = html;

            new bootstrap.Modal(document.getElementById("modalAdminUsuarios")).show();
        });
});
//FUNCION PARA VER TODAS LAS RESEÑAS DE UN USUARIO.

document.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-ver-resenas-usuario");

    if (!btn) return;

    const usuarioId = btn.dataset.usuarioId;
    const nombre = btn.dataset.usuarioNombre;

    document.getElementById("modalAdminUsuariosTitulo").innerText =
        "Reseñas de " + nombre;

    const contenedor = document.getElementById("modalAdminUsuariosContenido");
    contenedor.innerHTML = "Cargando reseñas...";

    fetch(`/UNRINCONDEPT/public/ajax_admin_reseñas_usuario.php?usuario_id=${usuarioId}`)
        .then(res => res.json())
        .then(data => {

            if (!data.ok) {
                contenedor.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                new bootstrap.Modal(document.getElementById("modalAdminUsuarios")).show();
                return;
            }

            if (!data.resenas.length) {
                contenedor.innerHTML = `<p>No tiene reseñas.</p>`;
                new bootstrap.Modal(document.getElementById("modalAdminUsuarios")).show();
                return;
            }

            let html = "";

            data.resenas.forEach(r => {
                html += `
                    <div class="admin-ticket mb-3">
                        <div>
                            <strong>${r.producto_titulo}</strong>
                            <div class="text-warning">
                                ${"★".repeat(r.puntuacion)}${"☆".repeat(5 - r.puntuacion)}
                            </div>
                            <p>${r.comentario}</p>
                            <small>Estado: ${r.estado} · ${r.fecha}</small>
                        </div>

                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            <button class="btn btn-sm btn-outline-success btn-cambiar-estado-resena"
                                data-resena-id="${r.id}"
                                data-estado="visible">
                                Visible
                            </button>

                            <button class="btn btn-sm btn-outline-secondary btn-cambiar-estado-resena"
                                data-resena-id="${r.id}"
                                data-estado="oculta">
                                Ocultar
                            </button>

                            <button class="btn btn-sm btn-outline-danger btn-cambiar-estado-resena"
                                data-resena-id="${r.id}"
                                data-estado="denunciada">
                                Denunciar
                            </button>
                        </div>
                    </div>
                `;
            });

            contenedor.innerHTML = html;

            new bootstrap.Modal(document.getElementById("modalAdminUsuarios")).show();
        });
});
//FUNCION PARA CAMBIAR EL ESTADO DE LA RESEÑA VISIBLE, OCULTO, DENUNCIAR.

document.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-cambiar-estado-resena");

    if (!btn) return;

    const formData = new FormData();
    formData.append("resena_id", btn.dataset.resenaId);
    formData.append("estado", btn.dataset.estado);

    fetch("/UNRINCONDEPT/public/ajax_admin_estado_reseña.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            alert(data.mensaje || data.error);
        });
});
// BLOQUEAR / DESBLOQUEAR USUARIO
document.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-cambiar-estado-usuario");

    if (!btn) return;

    const usuarioId = btn.dataset.usuarioId;

    // Si actualmente está activo (1), pasará a bloqueado (0).
    // Si actualmente está bloqueado (0), pasará a activo (1).
    const estadoActual = btn.dataset.activo;
    const nuevoEstado = estadoActual === "1" ? "0" : "1";

    const accion = nuevoEstado === "1"
        ? "desbloquear"
        : "bloquear";

    if (!confirm(`¿Seguro que deseas ${accion} este usuario?`)) {
        return;
    }

    const formData = new FormData();
    formData.append("usuario_id", usuarioId);
    formData.append("activo", nuevoEstado);

    fetch("/UNRINCONDEPT/public/ajax_admin_estado_usuario.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.mensaje || data.error);

        if (data.ok) {
            location.reload();
        }
    })
    .catch(error => {
        console.error("Error al cambiar el estado del usuario:", error);
        alert("Se ha producido un error al procesar la solicitud.");
    });
});