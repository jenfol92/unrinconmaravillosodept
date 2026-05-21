document.addEventListener("DOMContentLoaded", function () {

    const links = document.querySelectorAll(".admin-link");
    const sections = document.querySelectorAll(".admin-section");
    const openButtons = document.querySelectorAll(".admin-open-section");

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

    links.forEach(link => {
        link.addEventListener("click", function () {
            abrirSeccion(this.dataset.section);

            if (this.dataset.section === "subir") {
                mostrarFormulariosParaCrear();
            }
        });
    });

    openButtons.forEach(button => {
        button.addEventListener("click", function () {
            abrirSeccion(this.dataset.section);

            if (this.dataset.section === "subir") {
                mostrarFormulariosParaCrear();
            }
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
mostrarSoloFormularioEdicion("producto");
    const subirLink = document.querySelector('.admin-link[data-section="subir"]');

    if (subirLink) {
        subirLink.classList.add("active");
    }

    function setValue(id, value) {
        const el = document.getElementById(id);
        if (el) {
            el.value = value ?? "";
        } else {
            console.warn("No existe el campo:", id);
        }
    }

    // Rellenamos formulario
    setValue("productoId", btn.dataset.id);
    setValue("productoTitulo", btn.dataset.titulo);
    setValue("productoPrecio", btn.dataset.precio);
    setValue("productoDescripcion", btn.dataset.descripcion);
    setValue("productoContenido", btn.dataset.contenido);
    setValue("productoCategoria", btn.dataset.categoria);
    setValue("productoNivel", btn.dataset.nivel);
    setValue("productoEstado", btn.dataset.estado);

    const imagen = btn.dataset.imagen || "default.png";

    const previewProducto = document.getElementById("previewImagenProducto");
    const imgProducto = document.getElementById("imgActualProducto");

    if (previewProducto && imgProducto) {
        imgProducto.src = `/UNRINCONDEPT/static/images/img/${imagen}`;
        previewProducto.style.display = "block";
    }

    // Cambiamos texto visual del formulario
    const tituloFormulario = document.getElementById("tituloFormularioProducto");

    if (tituloFormulario) {
        tituloFormulario.innerText = "Editar recurso";
    }

    const btnGuardar = document.getElementById("btnGuardarProducto");

    if (btnGuardar) {
        btnGuardar.innerText = "Guardar cambios";
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
document.addEventListener("click", function (e) {

    const btn = e.target.closest('.admin-open-section[data-section="subir"]');

    if (!btn) return;

    // Si el botón NO es editar, limpiamos formulario para nuevo recurso
    if (btn.classList.contains("btn-editar-producto")) return;

    const form = document.getElementById("formSubirRecurso");

    if (form) {
        form.reset();
    }

    const productoId = document.getElementById("productoId");
    if (productoId) {
        productoId.value = "";
    }

    const tituloFormulario = document.getElementById("tituloFormularioProducto");
    if (tituloFormulario) {
        tituloFormulario.innerText = "Subir Recurso";
    }

    const btnGuardar = document.getElementById("btnGuardarProducto");
    if (btnGuardar) {
        btnGuardar.innerText = "Guardar recurso";
    }
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


/**
 * Carga los mensajes de un ticket de soporte y los muestra
 * con formato visual de chat.
 *
 * - Mensajes del usuario: izquierda.
 * - Mensajes del administrador: derecha.
 * - Se utiliza escapeHtml() para evitar insertar HTML peligroso.
 * - Se mantiene el salto de línea del mensaje con replace().
 *
 * @param {number|string} ticketId ID del ticket seleccionado.
 */
function cargarMensajesTicket(ticketId) {

    const contenedor = document.getElementById("ticketMensajes");

    if (!contenedor) return;

    contenedor.innerHTML = `
        <div class="soporte-chat-loading">
            <span class="spinner-border spinner-border-sm me-2"></span>
            Cargando conversación...
        </div>
    `;

    fetch(`/UNRINCONDEPT/public/admin_ajax_soporte_leer.php?ticket_id=${ticketId}`)
        .then(res => res.json())
        .then(data => {

            if (!data.ok) {
                contenedor.innerHTML = `
                    <div class="alert alert-danger">
                        ${escapeHtml(data.error)}
                    </div>
                `;
                return;
            }

            if (!data.mensajes.length) {
                contenedor.innerHTML = `
                    <div class="soporte-chat-empty">
                        <i class="bi bi-chat-dots"></i>
                        <p>No hay mensajes en esta conversación.</p>
                    </div>
                `;
                return;
            }

            let html = "";

            data.mensajes.forEach(m => {

                const esAdmin = m.remitente === "admin";

                const clase = esAdmin
                    ? "mensaje-admin"
                    : "mensaje-usuario";

                const nombreRemitente = m.remitente_nombre
                    ? m.remitente_nombre
                    : (esAdmin ? "Administración" : "Usuario");

                const icono = esAdmin
                    ? "bi-shield-check"
                    : "bi-person-circle";

                const mensajeSeguro = escapeHtml(m.mensaje || "").replace(/\n/g, "<br>");
                const fechaSegura = escapeHtml(m.fecha || "");

                html += `
                    <div class="soporte-msg ${clase}">
                        <div class="soporte-msg-bubble">

                            <div class="soporte-msg-autor">
                                <i class="bi ${icono}"></i>
                                ${escapeHtml(nombreRemitente)}
                            </div>

                            <div class="soporte-msg-texto">
                                ${mensajeSeguro}
                            </div>

                            <div class="soporte-msg-hora">
                                ${fechaSegura}
                            </div>

                        </div>
                    </div>
                `;
            });

            contenedor.innerHTML = html;

            /*
                Bajamos automáticamente al último mensaje.
                Si tienes efectos-jquery.js, también se encargará,
                pero esto asegura que funcione aunque no esté cargado.
            */
            contenedor.scrollTop = contenedor.scrollHeight;
        })
        .catch(error => {
            console.error("Error cargando mensajes del ticket:", error);

            contenedor.innerHTML = `
                <div class="alert alert-danger">
                    Se ha producido un error al cargar la conversación.
                </div>
            `;
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
    const buscador = document.getElementById("adminBuscarProducto");
    const filtroCategoria = document.getElementById("adminFiltroCategoria");
    const filtroEstado = document.getElementById("adminFiltroEstado");

    const filtroPeriodoDatos = document.getElementById("adminFiltroPeriodoDatosProducto");
    const datosDesde = document.getElementById("adminDatosDesdeProducto");
    const datosHasta = document.getElementById("adminDatosHastaProducto");

    if (btnFiltrar) {
        btnFiltrar.addEventListener("click", function () {
            cargarProductosAdmin(1);
        });
    }

    if (buscador) {
        buscador.addEventListener("keyup", function () {
            cargarProductosAdmin(1);
        });
    }

    if (filtroCategoria) {
        filtroCategoria.addEventListener("change", function () {
            cargarProductosAdmin(1);
        });
    }

    if (filtroEstado) {
        filtroEstado.addEventListener("change", function () {
            cargarProductosAdmin(1);
        });
    }

    if (filtroPeriodoDatos) {
        filtroPeriodoDatos.addEventListener("change", function () {
            cargarProductosAdmin(1);
        });
    }

    if (datosDesde) {
        datosDesde.addEventListener("change", function () {
            cargarProductosAdmin(1);
        });
    }

    if (datosHasta) {
        datosHasta.addEventListener("change", function () {
            cargarProductosAdmin(1);
        });
    }
});

function cargarProductosAdmin(pagina = 1) {

    const tbody = document.getElementById("adminProductosTbody");

    if (!tbody) return;

    const busqueda = document.getElementById("adminBuscarProducto")?.value || "";
    const categoria = document.getElementById("adminFiltroCategoria")?.value || "";
    const estado = document.getElementById("adminFiltroEstado")?.value || "";

    const periodoDatos = document.getElementById("adminFiltroPeriodoDatosProducto")?.value || "todos";
    const datosDesde = document.getElementById("adminDatosDesdeProducto")?.value || "";
    const datosHasta = document.getElementById("adminDatosHastaProducto")?.value || "";

    tbody.innerHTML = `
        <tr>
            <td colspan="9" class="text-center py-4">
                Cargando productos...
            </td>
        </tr>
    `;

    const url = `/UNRINCONDEPT/public/ajax_admin_panel_productos.php?pagina=${pagina}` +
        `&busqueda=${encodeURIComponent(busqueda)}` +
        `&categoria=${encodeURIComponent(categoria)}` +
        `&estado=${encodeURIComponent(estado)}` +
        `&periodo_datos=${encodeURIComponent(periodoDatos)}` +
        `&datos_desde=${encodeURIComponent(datosDesde)}` +
        `&datos_hasta=${encodeURIComponent(datosHasta)}`;

    console.log("URL productos admin:", url);

    fetch(url)
        .then(res => res.text())
        .then(texto => {

            let data;

            try {
                data = JSON.parse(texto);
            } catch (e) {
                console.error("Respuesta no JSON en productos admin:", texto);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center text-danger">
                            El servidor no ha devuelto JSON válido.
                        </td>
                    </tr>
                `;
                return;
            }

            if (!data.ok) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center text-danger">
                            ${data.error}
                        </td>
                    </tr>
                `;
                return;
            }

            pintarProductosAdmin(data.productos);
            pintarPaginacionProductosAdmin(data.total_paginas, data.pagina_actual);
        })
        .catch(error => {
            console.error("Error cargando productos admin:", error);

            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center text-danger">
                        Error cargando productos.
                    </td>
                </tr>
            `;
        });
}

function pintarProductosAdmin(productos) {

    const tbody = document.getElementById("adminProductosTbody");
    const cardsContainer = document.getElementById("adminProductosCardsMovil");

    if (!tbody) return;

    if (!productos || productos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center text-muted py-4">
                    No hay productos que coincidan con los filtros.
                </td>
            </tr>
        `;

        if (cardsContainer) {
            cardsContainer.innerHTML = `
                <div class="text-center text-muted py-4">
                    No hay productos que coincidan con los filtros.
                </div>
            `;
        }

        return;
    }

    let htmlTabla = "";
    let htmlCards = "";

    productos.forEach(p => {

        const precio = parseFloat(p.precio || 0).toFixed(2);

        const estadoActivo = p.estado === "activo";

        const estadoBadge = estadoActivo
            ? `<span class="badge bg-success">Activo</span>`
            : `<span class="badge bg-secondary">Inactivo</span>`;

        const imagen = p.imagen || "default.png";
        const titulo = escapeHtml(p.titulo || "Producto");
        const categoria = escapeHtml(p.categoria_nombre || "Sin categoría");

        const totalCompras = p.total_compras ?? 0;
        const totalDescargas = p.total_descargas ?? 0;
        const totalResenas = p.total_resenas ?? 0;
        const totalClicks = p.total_clicks ?? 0;

        // ==========================
        // TABLA ESCRITORIO/TABLET
        // ==========================
        htmlTabla += `
            <tr>
                <td>
                    <div class="admin-product-info">
                        <img 
                            src="/UNRINCONDEPT/static/images/img/${escapeHtml(imagen)}" 
                            alt="${titulo}"
                            onerror="this.onerror=null;this.src='/UNRINCONDEPT/static/images/img/default.png';"
                        >

                        <div>
                            <strong>${titulo}</strong>
                            <div class="text-muted small">ID #${p.id}</div>
                        </div>
                    </div>
                </td>

                <td>
                    <span class="admin-badge">${categoria}</span>
                </td>

                <td>
                    <strong>${precio} €</strong>
                </td>

                <td>${totalCompras}</td>

                <td>${totalDescargas}</td>

                <td>${totalResenas}</td>

                <td>${totalClicks}</td>

                <td>${estadoBadge}</td>

                <td class="text-end">
                    <div class="d-flex justify-content-end gap-2 flex-wrap">

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary btn-editar-producto"
                            data-id="${p.id}"
                            data-titulo="${titulo}"
                            data-precio="${p.precio}"
                            data-descripcion="${escapeHtml(p.descripcion ?? "")}"
                            data-contenido="${escapeHtml(p.contenido ?? "")}"
                            data-categoria="${p.categoria_id}"
                            data-nivel="${p.nivel_id}"
                            data-estado="${p.estado}"
                            data-imagen="${escapeHtml(imagen)}">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-warning btn-ver-resenas-producto"
                            data-producto-id="${p.id}"
                            data-producto-titulo="${titulo}">
                            <i class="bi bi-star"></i>
                        </button>

                        <a
                            href="/UNRINCONDEPT/public/detalle.php?id=${p.id}"
                            class="btn btn-sm btn-outline-secondary"
                            target="_blank">
                            <i class="bi bi-eye"></i>
                        </a>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger btn-eliminar-producto"
                            data-producto-id="${p.id}"
                            data-producto-titulo="${titulo}">
                            <i class="bi bi-trash"></i>
                        </button>

                    </div>
                </td>
            </tr>
        `;

        // ==========================
        // TARJETA MÓVIL
        // ==========================
        htmlCards += `
            <article class="admin-product-card-mobile">

                <div class="admin-product-card-mobile__top">

                    <div class="admin-product-card-mobile__image">
                        <img 
                            src="/UNRINCONDEPT/static/images/img/${escapeHtml(imagen)}" 
                            alt="${titulo}"
                            onerror="this.onerror=null;this.src='/UNRINCONDEPT/static/images/img/default.png';"
                        >
                    </div>

                    <div class="admin-product-card-mobile__info">
                        <h6>${titulo}</h6>
                        <span>ID #${p.id}</span>
                        <div class="admin-product-card-mobile__category">
                            <i class="bi bi-tag"></i>
                            ${categoria}
                        </div>
                    </div>

                    <div class="dropdown">
                        <button class="btn admin-product-card-mobile__menu"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">

                            <li>
                                <button
                                    type="button"
                                    class="dropdown-item btn-editar-producto"
                                    data-id="${p.id}"
                                    data-titulo="${titulo}"
                                    data-precio="${p.precio}"
                                    data-descripcion="${escapeHtml(p.descripcion ?? "")}"
                                    data-contenido="${escapeHtml(p.contenido ?? "")}"
                                    data-categoria="${p.categoria_id}"
                                    data-nivel="${p.nivel_id}"
                                    data-estado="${p.estado}"
                                    data-imagen="${escapeHtml(imagen)}">
                                    <i class="bi bi-pencil me-2"></i>
                                    Editar
                                </button>
                            </li>

                            <li>
                                <button
                                    type="button"
                                    class="dropdown-item btn-ver-resenas-producto"
                                    data-producto-id="${p.id}"
                                    data-producto-titulo="${titulo}">
                                    <i class="bi bi-star me-2"></i>
                                    Ver reseñas
                                </button>
                            </li>

                            <li>
                                <a
                                    href="/UNRINCONDEPT/public/detalle.php?id=${p.id}"
                                    class="dropdown-item"
                                    target="_blank">
                                    <i class="bi bi-eye me-2"></i>
                                    Ver producto
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <button
                                    type="button"
                                    class="dropdown-item text-danger btn-eliminar-producto"
                                    data-producto-id="${p.id}"
                                    data-producto-titulo="${titulo}">
                                    <i class="bi bi-trash me-2"></i>
                                    Eliminar
                                </button>
                            </li>

                        </ul>
                    </div>

                </div>

                <div class="admin-product-card-mobile__price">
                    ${precio} €
                </div>

                <div class="admin-product-card-mobile__stats">

                    <div class="admin-product-card-mobile__stat">
                        <i class="bi bi-bag-check"></i>
                        <strong>${totalCompras}</strong>
                        <span>Compras</span>
                    </div>

                    <div class="admin-product-card-mobile__stat">
                        <i class="bi bi-download"></i>
                        <strong>${totalDescargas}</strong>
                        <span>Descargas</span>
                    </div>

                    <div class="admin-product-card-mobile__stat">
                        <i class="bi bi-cursor"></i>
                        <strong>${totalClicks}</strong>
                        <span>Clics</span>
                    </div>

                    <div class="admin-product-card-mobile__stat">
                        <i class="bi bi-star"></i>
                        <strong>${totalResenas}</strong>
                        <span>Reseñas</span>
                    </div>

                </div>

                <div class="admin-product-card-mobile__footer">
                    ${estadoBadge}
                </div>

            </article>
        `;
    });

    tbody.innerHTML = htmlTabla;

    if (cardsContainer) {
        cardsContainer.innerHTML = htmlCards;
    }
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
// GUARDAR PRODUCTO Y MOSTRAR OPCIÓN DE SUBIR ARCHIVO
document.addEventListener("DOMContentLoaded", function () {

    const formProducto = document.getElementById("formSubirRecurso");

    if (!formProducto) return;

    formProducto.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(formProducto);

        fetch("/UNRINCONDEPT/public/admin_guardar_producto.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {

                const respuesta = document.getElementById("respuestaGuardarProducto");
                const bloqueArchivo = document.getElementById("bloqueAsociarArchivo");
                const archivoProductoId = document.getElementById("archivoProductoId");
                const productoIdInput = document.getElementById("productoId");

                if (!data.ok) {
                    if (respuesta) {
                        respuesta.innerHTML = `
                        <div class="alert alert-danger">
                            ${data.mensaje}
                        </div>
                    `;
                    }
                    return;
                }

                if (respuesta) {
                    respuesta.innerHTML = `
                    <div class="alert alert-success">
                        ${data.mensaje}
                    </div>
                `;
                }

                // Guardamos el ID devuelto en el formulario principal
                if (productoIdInput) {
                    productoIdInput.value = data.producto_id;
                }

                // Pasamos ese ID al formulario de subida de archivo
                if (archivoProductoId) {
                    archivoProductoId.value = data.producto_id;
                }

                // Mostramos el bloque para asociar PDF/ZIP
                if (bloqueArchivo) {
                    bloqueArchivo.style.display = "block";
                    bloqueArchivo.scrollIntoView({
                        behavior: "smooth",
                        block: "center"
                    });
                }

                // Si tienes tabla AJAX de productos, la refrescamos
                if (typeof cargarProductosAdmin === "function") {
                    cargarProductosAdmin(1);
                }
            })
            .catch(error => {
                console.error("Error guardando producto:", error);

                const respuesta = document.getElementById("respuestaGuardarProducto");

                if (respuesta) {
                    respuesta.innerHTML = `
                    <div class="alert alert-danger">
                        Se ha producido un error al guardar el producto.
                    </div>
                `;
                }
            });
    });
});


/// SUBIR ARCHIVO PDF/ZIP Y ASOCIARLO AL PRODUCTO
document.addEventListener("submit", function (e) {

    const formArchivo = e.target.closest("#formAsociarArchivo");

    if (!formArchivo) return;

    e.preventDefault();

    const formData = new FormData(formArchivo);

    const respuesta = document.getElementById("respuestaArchivoRecurso");

    if (respuesta) {
        respuesta.innerHTML = `
            <div class="alert alert-info">
                Subiendo archivo, espera unos segundos...
            </div>
        `;
    }

    fetch("/UNRINCONDEPT/public/admin_ajax_subir_recurso.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {

            if (!data.ok) {
                if (respuesta) {
                    respuesta.innerHTML = `
                    <div class="alert alert-danger">
                        ${data.mensaje}
                    </div>
                `;
                }
                return;
            }

            if (respuesta) {
                respuesta.innerHTML = `
                <div class="alert alert-success">
                    <strong>${data.mensaje}</strong><br>
                    <small>${data.archivo_s3_key}</small>
                </div>
            `;
            }

            formArchivo.reset();

            if (typeof cargarProductosAdmin === "function") {
                cargarProductosAdmin(1);
            }
        })
        .catch(error => {
            console.error("Error subiendo archivo:", error);

            if (respuesta) {
                respuesta.innerHTML = `
                <div class="alert alert-danger">
                    Se ha producido un error al subir el archivo.
                </div>
            `;
            }
        });
});

// ELIMINAR PRODUCTO Y SU ARCHIVO EN CLOUDFLARE R2
document.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-eliminar-producto");

    if (!btn) return;

    const productoId = btn.dataset.productoId;
    const titulo = btn.dataset.productoTitulo || "este recurso";

    const confirmar = confirm(
        `¿Seguro que quieres eliminar "${titulo}"?\n\n` +
        `Si tiene archivo PDF/ZIP asociado, también se eliminará de Cloudflare.`
    );

    if (!confirmar) return;

    const formData = new FormData();
    formData.append("producto_id", productoId);

    fetch("/UNRINCONDEPT/public/admin_eliminar_producto.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.text())
        .then(texto => {

            let data;

            try {
                data = JSON.parse(texto);
            } catch (e) {
                console.error("Respuesta no JSON:", texto);
                alert("El servidor no ha devuelto una respuesta válida.");
                return;
            }

            alert(data.mensaje || data.error);

            if (data.ok) {
                if (typeof cargarProductosAdmin === "function") {
                    cargarProductosAdmin(1);
                } else {
                    location.reload();
                }
            }
        })
        .catch(error => {
            console.error("Error eliminando producto:", error);
            alert("Se ha producido un error al eliminar el recurso.");
        });
});
//FILTROS DE LA TABLA PARA EL REPORTE.
document.addEventListener("click", function (e) {

    const btn = e.target.closest("#btnExportarReporte");

    if (!btn) return;

    e.preventDefault();

    const busqueda = document.getElementById("adminBuscarProducto")?.value || "";
    const categoria = document.getElementById("adminFiltroCategoria")?.value || "";
    const estado = document.getElementById("adminFiltroEstado")?.value || "";

    const url = "/UNRINCONDEPT/public/admin_exportar_reporte_pdf.php" +
        "?busqueda=" + encodeURIComponent(busqueda) +
        "&categoria=" + encodeURIComponent(categoria) +
        "&estado=" + encodeURIComponent(estado);

    window.open(url, "_blank");
});
// GUARDAR RECURSO GRATUITO
document.addEventListener("submit", function (e) {

    const form = e.target.closest("#formRecursoGratuito");

    if (!form) return;

    e.preventDefault();

    const formData = new FormData(form);
    const respuesta = document.getElementById("respuestaRecursoGratuito");

    fetch("/UNRINCONDEPT/public/ajax_guardar_recurso_gratuito.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {

            if (!data.ok) {
                respuesta.innerHTML = `<div class="alert alert-danger">${data.mensaje}</div>`;
                return;
            }

            respuesta.innerHTML = `<div class="alert alert-success">${data.mensaje}</div>`;

            setTimeout(() => {
                location.reload();
            }, 800);
        })
        .catch(error => {
            console.error("Error guardando recurso gratuito:", error);
            respuesta.innerHTML = `<div class="alert alert-danger">Error guardando recurso gratuito.</div>`;
        });
});


// EDITAR RECURSO GRATUITO
document.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-editar-gratuito");

    if (!btn) return;

    document.querySelectorAll(".admin-section").forEach(section => {
        section.classList.remove("active");
    });

    document.getElementById("admin-section-subir").classList.add("active");
mostrarSoloFormularioEdicion("gratuito");
    document.querySelectorAll(".admin-link").forEach(link => {
        link.classList.remove("active");
    });

    const subirLink = document.querySelector('.admin-link[data-section="subir"]');

    if (subirLink) {
        subirLink.classList.add("active");
    }

    document.getElementById("gratuitoId").value = btn.dataset.id;
    document.getElementById("gratuitoTitulo").value = btn.dataset.titulo;
    document.getElementById("gratuitoCategoria").value = btn.dataset.categoria;
    document.getElementById("gratuitoDriveUrl").value = btn.dataset.drive;
    document.getElementById("gratuitoEstado").value = btn.dataset.estado;
    const imagen = btn.dataset.imagen || "default.png";

    const previewGratuito = document.getElementById("previewImagenGratuito");
    const imgGratuito = document.getElementById("imgActualGratuito");

    if (previewGratuito && imgGratuito) {
        imgGratuito.src = `/UNRINCONDEPT/static/images/img/${imagen}`;
        previewGratuito.style.display = "block";
    }

    const titulo = document.getElementById("tituloFormularioGratuito");

    if (titulo) {
        titulo.innerText = "Editar recurso gratuito";
    }
});


// LIMPIAR FORMULARIO GRATUITO
document.addEventListener("click", function (e) {

    const btn = e.target.closest("#btnLimpiarGratuito");

    if (!btn) return;

    const form = document.getElementById("formRecursoGratuito");

    if (form) {
        form.reset();
    }

    document.getElementById("gratuitoId").value = "";

    const titulo = document.getElementById("tituloFormularioGratuito");

    if (titulo) {
        titulo.innerText = "Recurso gratuito";
    }
});


// CREAR CATEGORÍA GRATUITA
document.addEventListener("submit", function (e) {

    const form = e.target.closest("#formNuevaCategoriaGratuita");

    if (!form) return;

    e.preventDefault();

    const formData = new FormData(form);
    const respuesta = document.getElementById("respuestaCategoriaGratuita");

    fetch("/UNRINCONDEPT/public/admin_crear_categoria_gratuita.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {

            if (!data.ok) {
                respuesta.innerHTML = `<div class="alert alert-danger">${data.mensaje}</div>`;
                return;
            }

            const select = document.getElementById("gratuitoCategoria");

            const option = document.createElement("option");
            option.value = data.categoria.id;
            option.textContent = data.categoria.nombre;
            option.selected = true;

            select.appendChild(option);

            respuesta.innerHTML = `<div class="alert alert-success">${data.mensaje}</div>`;

            form.reset();
        })
        .catch(error => {
            console.error("Error creando categoría gratuita:", error);
        });
});


/// ELIMINAR RECURSO GRATUITO
document.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-eliminar-gratuito");

    if (!btn) return;

    const id = btn.dataset.id;
    const titulo = btn.dataset.titulo;

    if (!confirm(`¿Seguro que quieres eliminar "${titulo}"?`)) {
        return;
    }

    const formData = new FormData();
    formData.append("id", id);

    fetch("/UNRINCONDEPT/public/admin_eliminar_recurso_gratuito.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(texto => {

        let data;

        try {
            data = JSON.parse(texto);
        } catch (e) {
            console.error("Respuesta no JSON al eliminar recurso gratuito:", texto);
            alert("El servidor no ha devuelto JSON. Mira la consola para ver el error real.");
            return;
        }

        alert(data.mensaje || data.error || "Operación finalizada.");

        if (data.ok) {
            location.reload();
        }
    })
    .catch(error => {
        console.error("Error eliminando recurso gratuito:", error);
        alert("Error eliminando recurso gratuito.");
    });
});

// MOSTRAR SOLO EL FORMULARIO QUE SE ESTÁ EDITANDO
function mostrarSoloFormularioEdicion(tipo) {
    const colProducto = document.getElementById("colFormularioProducto");
    const colGratuito = document.getElementById("colFormularioGratuito");
    const bloqueArchivo = document.getElementById("bloqueAsociarArchivo");

    if (!colProducto || !colGratuito) {
        console.warn("Faltan colFormularioProducto o colFormularioGratuito");
        return;
    }

    colProducto.classList.remove("d-none", "col-xl-6", "col-xl-8", "col-xl-10", "mx-auto");
    colGratuito.classList.remove("d-none", "col-xl-6", "col-xl-8", "col-xl-10", "mx-auto");

    if (tipo === "producto") {
        colProducto.classList.add("col-xl-8", "mx-auto");
        colGratuito.classList.add("d-none");

        if (bloqueArchivo) {
            bloqueArchivo.style.display = "none";
        }

        return;
    }

    if (tipo === "gratuito") {
        colProducto.classList.add("d-none");
        colGratuito.classList.add("col-xl-8", "mx-auto");

        if (bloqueArchivo) {
            bloqueArchivo.style.display = "none";
        }

        return;
    }

    colProducto.classList.add("col-xl-6");
    colGratuito.classList.add("col-xl-6");
}


// MOSTRAR LOS DOS FORMULARIOS PARA CREAR RECURSOS NUEVOS
function mostrarFormulariosParaCrear() {
    const colProducto = document.getElementById("colFormularioProducto");
    const colGratuito = document.getElementById("colFormularioGratuito");
    const bloqueArchivo = document.getElementById("bloqueAsociarArchivo");

    if (!colProducto || !colGratuito) {
        console.warn("Faltan colFormularioProducto o colFormularioGratuito");
        return;
    }

    // Restaurar columnas
    colProducto.classList.remove("d-none", "col-xl-8", "col-xl-10", "mx-auto");
    colGratuito.classList.remove("d-none", "col-xl-8", "col-xl-10", "mx-auto");

    colProducto.classList.add("col-xl-6");
    colGratuito.classList.add("col-xl-6");

    // Ocultar bloque de asociar PDF/ZIP
    if (bloqueArchivo) {
        bloqueArchivo.style.display = "none";
    }

    // Limpiar formulario de producto de tienda
    const formProducto = document.getElementById("formSubirRecurso");
    if (formProducto) {
        formProducto.reset();
    }

    const productoId = document.getElementById("productoId");
    if (productoId) {
        productoId.value = "";
    }

    const tituloProducto = document.getElementById("tituloFormularioProducto");
    if (tituloProducto) {
        tituloProducto.innerText = "Subir recurso de tienda";
    }

    const btnGuardarProducto = document.getElementById("btnGuardarProducto");
    if (btnGuardarProducto) {
        btnGuardarProducto.innerText = "Guardar recurso";
    }

    const respuestaGuardarProducto = document.getElementById("respuestaGuardarProducto");
    if (respuestaGuardarProducto) {
        respuestaGuardarProducto.innerHTML = "";
    }

    const respuestaArchivo = document.getElementById("respuestaArchivoRecurso");
    if (respuestaArchivo) {
        respuestaArchivo.innerHTML = "";
    }

    const previewProducto = document.getElementById("previewImagenProducto");
    const imgProducto = document.getElementById("imgActualProducto");

    if (previewProducto) {
        previewProducto.style.display = "none";
    }

    if (imgProducto) {
        imgProducto.src = "";
    }

    // Limpiar formulario de recurso gratuito
    const formGratuito = document.getElementById("formRecursoGratuito");
    if (formGratuito) {
        formGratuito.reset();
    }

    const gratuitoId = document.getElementById("gratuitoId");
    if (gratuitoId) {
        gratuitoId.value = "";
    }

    const gratuitoDriveUrl = document.getElementById("gratuitoDriveUrl");
    if (gratuitoDriveUrl) {
        gratuitoDriveUrl.value = "";
    }

    const tituloGratuito = document.getElementById("tituloFormularioGratuito");
    if (tituloGratuito) {
        tituloGratuito.innerText = "Recurso gratuito";
    }

    const respuestaGratuito = document.getElementById("respuestaRecursoGratuito");
    if (respuestaGratuito) {
        respuestaGratuito.innerHTML = "";
    }

    const previewGratuito = document.getElementById("previewImagenGratuito");
    const imgGratuito = document.getElementById("imgActualGratuito");

    if (previewGratuito) {
        previewGratuito.style.display = "none";
    }

    if (imgGratuito) {
        imgGratuito.src = "";
    }
}
// FILTROS CONTENIDO GRATUITO


function normalizarTexto(texto) {
    return String(texto || "")
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .trim();
}

function filtrarRecursosGratuitos() {

    const inputBusqueda = document.getElementById("adminBuscarGratuito");
    const selectCategoria = document.getElementById("adminFiltroCategoriaGratuito");
    const selectEstado = document.getElementById("adminFiltroEstadoGratuito");

    const busqueda = normalizarTexto(inputBusqueda?.value || "");
    const categoria = selectCategoria?.value || "";
    const estado = selectEstado?.value || "";

    const filas = document.querySelectorAll("#tablaRecursosGratuitos .gratuito-row");
    const cards = document.querySelectorAll(".gratuito-card-mobile");
    const filaSinResultados = document.getElementById("filaSinResultadosGratuitos");

    let visibles = 0;

    function coincideElemento(elemento) {
        const tituloFila = normalizarTexto(elemento.dataset.titulo || "");
        const categoriaFila = elemento.dataset.categoria || "";
        const estadoFila = elemento.dataset.estado || "";

        const coincideBusqueda =
            busqueda === "" ||
            tituloFila.includes(busqueda);

        const coincideCategoria =
            categoria === "" ||
            categoriaFila === categoria;

        const coincideEstado =
            estado === "" ||
            estadoFila === estado;

        return coincideBusqueda && coincideCategoria && coincideEstado;
    }

    filas.forEach(fila => {
        if (coincideElemento(fila)) {
            fila.style.display = "";
            visibles++;
        } else {
            fila.style.display = "none";
        }
    });

    cards.forEach(card => {
        if (coincideElemento(card)) {
            card.style.display = "";
        } else {
            card.style.display = "none";
        }
    });

    if (filaSinResultados) {
        filaSinResultados.style.display = visibles === 0 ? "" : "none";
    }
}

document.addEventListener("click", function (e) {
    if (e.target.closest("#btnFiltrarGratuitos")) {
        filtrarRecursosGratuitos();
    }

    if (e.target.closest("#btnLimpiarFiltrosGratuitos")) {
        const buscador = document.getElementById("adminBuscarGratuito");
        const categoria = document.getElementById("adminFiltroCategoriaGratuito");
        const estado = document.getElementById("adminFiltroEstadoGratuito");

        if (buscador) buscador.value = "";
        if (categoria) categoria.value = "";
        if (estado) estado.value = "";

        filtrarRecursosGratuitos();
    }
});

document.addEventListener("input", function (e) {
    if (e.target && e.target.id === "adminBuscarGratuito") {
        filtrarRecursosGratuitos();
    }
});

document.addEventListener("change", function (e) {
    if (
        e.target &&
        (
            e.target.id === "adminFiltroCategoriaGratuito" ||
            e.target.id === "adminFiltroEstadoGratuito"
        )
    ) {
        filtrarRecursosGratuitos();
    }
});

// APLICAR PERIODO EN CONTENIDO GRATUITO


document.addEventListener("click", function (e) {

    const btn = e.target.closest("#btnAplicarPeriodoGratis");

    if (!btn) return;

    const periodo = document.getElementById("adminFiltroPeriodoGratuito")?.value || "todos";
    const desde = document.getElementById("gratisDesde")?.value || "";
    const hasta = document.getElementById("gratisHasta")?.value || "";

    let url = "/UNRINCONDEPT/public/admin.php?section=gratuitos&periodo_gratis=" + encodeURIComponent(periodo);

    if (periodo === "personalizado") {
        url += "&gratis_desde=" + encodeURIComponent(desde);
        url += "&gratis_hasta=" + encodeURIComponent(hasta);
    }

    window.location.href = url;
});
// ======================================================
// SIDEBAR ADMIN RESPONSIVE
// ======================================================

document.addEventListener("click", function (e) {

    const btn = e.target.closest("#btnAdminMobileMenu");

    if (!btn) return;

    const sidebar = document.getElementById("adminMobileSidebar");
    const overlay = document.getElementById("adminMobileOverlay");

    if (!sidebar || !overlay) return;

    const abierto = sidebar.classList.toggle("is-open");

    overlay.classList.toggle("is-open", abierto);
    btn.classList.toggle("is-open", abierto);
    btn.setAttribute("aria-expanded", abierto ? "true" : "false");
});

document.addEventListener("click", function (e) {

    const overlay = e.target.closest("#adminMobileOverlay");

    if (!overlay) return;

    cerrarSidebarAdminMovil();
});

document.addEventListener("click", function (e) {

    const link = e.target.closest(".admin-sidebar .admin-link");

    if (!link) return;

    if (window.innerWidth <= 768) {
        cerrarSidebarAdminMovil();
    }
});

function cerrarSidebarAdminMovil() {
    const btn = document.getElementById("btnAdminMobileMenu");
    const sidebar = document.getElementById("adminMobileSidebar");
    const overlay = document.getElementById("adminMobileOverlay");

    if (sidebar) sidebar.classList.remove("is-open");
    if (overlay) overlay.classList.remove("is-open");

    if (btn) {
        btn.classList.remove("is-open");
        btn.setAttribute("aria-expanded", "false");
    }
}

// FILTROS RESPONSIVE: DESPLEGAR EN MÓVIL


/*document.addEventListener("click", function (e) {

    const btn = e.target.closest(".admin-filtros-toggle");

    if (!btn) return;

    const targetId = btn.dataset.filterTarget;

    if (!targetId) return;

    const body = document.getElementById(targetId);

    if (!body) return;

    btn.classList.toggle("is-open");
    body.classList.toggle("is-open");
});*/

// VER FAVORITOS DE UN USUARIO EN PANEL ADMIN


document.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-ver-favoritos-usuario");

    if (!btn) return;

    const usuarioId = btn.dataset.usuarioId;
    const nombre = btn.dataset.usuarioNombre || "usuario";

    const tituloModal = document.getElementById("modalAdminUsuariosTitulo");
    const contenedor = document.getElementById("modalAdminUsuariosContenido");

    if (tituloModal) {
        tituloModal.innerText = "Favoritos de " + nombre;
    }

    if (contenedor) {
        contenedor.innerHTML = "Cargando favoritos...";
    }

    fetch(`/UNRINCONDEPT/public/admin_ajax_favoritos_usuario.php?usuario_id=${encodeURIComponent(usuarioId)}`)
        .then(res => res.text())
        .then(texto => {

            let data;

            try {
                data = JSON.parse(texto);
            } catch (e) {
                console.error("Respuesta no JSON favoritos usuario:", texto);

                if (contenedor) {
                    contenedor.innerHTML = `
                        <div class="alert alert-danger">
                            El servidor no ha devuelto una respuesta válida.
                        </div>
                    `;
                }

                new bootstrap.Modal(document.getElementById("modalAdminUsuarios")).show();
                return;
            }

            if (!data.ok) {
                contenedor.innerHTML = `
                    <div class="alert alert-danger">
                        ${data.error || "No se pudieron cargar los favoritos."}
                    </div>
                `;

                new bootstrap.Modal(document.getElementById("modalAdminUsuarios")).show();
                return;
            }

            if (!data.favoritos || data.favoritos.length === 0) {
                contenedor.innerHTML = `
                    <p class="text-muted mb-0">
                        Este usuario no tiene productos favoritos.
                    </p>
                `;

                new bootstrap.Modal(document.getElementById("modalAdminUsuarios")).show();
                return;
            }

            let html = `
                <div class="table-responsive admin-mobile-cards">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Nivel</th>
                                <th>Precio</th>
                                <th>Fecha favorito</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            data.favoritos.forEach(f => {

                const precio = parseFloat(f.precio || 0).toFixed(2);
                const imagen = f.imagen || "default.png";

                html += `
                    <tr>
                        <td data-label="Producto">
                            <div class="admin-product-info">
                                <img
                                    src="/UNRINCONDEPT/static/images/img/${escapeHtml(imagen)}"
                                    alt="${escapeHtml(f.titulo)}"
                                    onerror="this.onerror=null;this.src='/UNRINCONDEPT/static/images/img/default.png';">
                                <div>
                                    <strong>${escapeHtml(f.titulo)}</strong>
                                    <div class="text-muted small">ID #${f.id}</div>
                                </div>
                            </div>
                        </td>

                        <td data-label="Categoría">
                            ${escapeHtml(f.categoria_nombre || "")}
                        </td>

                        <td data-label="Nivel">
                            ${escapeHtml(f.nivel_nombre || "")}
                        </td>

                        <td data-label="Precio">
                            <strong>${precio} €</strong>
                        </td>

                        <td data-label="Fecha favorito">
                            ${escapeHtml(f.fecha || "")}
                        </td>

                        <td data-label="Acciones" class="text-end">
                            <a href="/UNRINCONDEPT/public/detalle.php?id=${f.id}"
                               target="_blank"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            contenedor.innerHTML = html;

            new bootstrap.Modal(document.getElementById("modalAdminUsuarios")).show();
        })
        .catch(error => {
            console.error("Error cargando favoritos usuario:", error);

            contenedor.innerHTML = `
                <div class="alert alert-danger">
                    Error cargando favoritos del usuario.
                </div>
            `;

            new bootstrap.Modal(document.getElementById("modalAdminUsuarios")).show();
        });
});