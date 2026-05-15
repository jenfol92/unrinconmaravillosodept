// Esperamos a que cargue todo el HTML antes de ejecutar el JS
document.addEventListener("DOMContentLoaded", function () {

    // Solo ejecutamos la tienda si existe el contenedor de productos
    if (!document.getElementById("contenedor-productos")) return;

    // Cargamos los productos al entrar en la tienda
    cargarProductos();

    // Detectar cambios en los filtros de categoría y nivel
    document.querySelectorAll(".filtro-categoria, .filtro-nivel").forEach(el => {
        el.addEventListener("change", () => cargarProductos());
    });

    // Detectar escritura en el buscador
    const buscador = document.getElementById("busqueda");

    if (buscador) {
        buscador.addEventListener("keyup", () => cargarProductos());
    }

});


// Función principal para cargar productos por AJAX
function cargarProductos(pagina = 1) {

    let categorias = [];
    let niveles = [];

    document.querySelectorAll(".filtro-categoria:checked").forEach(el => {
        categorias.push(el.value);
    });

    document.querySelectorAll(".filtro-nivel:checked").forEach(el => {
        niveles.push(el.value);
    });

    const buscador = document.getElementById("busqueda");
    let busqueda = buscador ? buscador.value : "";

    const url = `/UNRINCONDEPT/public/ajax_productos.php?pagina=${pagina}` +
        `&categorias=${encodeURIComponent(JSON.stringify(categorias))}` +
        `&niveles=${encodeURIComponent(JSON.stringify(niveles))}` +
        `&busqueda=${encodeURIComponent(busqueda)}`;

    fetch(url)
        .then(res => res.json())
        .then(data => {

            let html = "";

            if (!data.productos || data.productos.length === 0) {

                html = `
                    <div class="col-12">
                        <div class="panel-empty text-center">
                            No hay productos disponibles con estos filtros.
                        </div>
                    </div>
                `;

            } else {

                data.productos.forEach(p => {

                    const claseFavorito = p.es_favorito ? "activo" : "";
                    const precio = parseFloat(p.precio || 0).toFixed(2);

                    html += `
                        <div class="col-12 col-sm-6 col-lg-4 mb-4">

                            <article class="tienda-card">

                                <img 
                                    src="/UNRINCONDEPT/static/images/img/${escapeHtml(p.imagen)}" 
                                    class="tienda-card-img"
                                    alt="${escapeHtml(p.titulo)}"
                                    loading="lazy"
                                >

                                <div class="card-body">

                                    <div class="d-flex justify-content-between align-items-center gap-2">

                                        <span class="badge-cat">
                                            ${escapeHtml(p.nivel_nombre || "")} - ${escapeHtml(p.categoria_nombre || "")}
                                        </span>

                                        <div class="stars-mini">
                                            ★★★★☆
                                        </div>

                                    </div>

                                    <h5>
                                        ${escapeHtml(p.titulo)}
                                    </h5>

                                    <div class="tienda-card-price">
                                        ${precio} €
                                    </div>

                                    <div class="tienda-card-actions">

                                        <a 
                                            href="/UNRINCONDEPT/public/detalle.php?id=${p.id}" 
                                            class="btn-ver-recurso">
                                            Ver
                                        </a>

                                        <button 
                                            type="button"
                                            class="btn-card-icon btn-favorito ${claseFavorito}" 
                                            data-id="${p.id}"
                                            title="Añadir o quitar favorito"
                                        >
                                            <i class="bi bi-heart-fill"></i>
                                        </button>

                                        <button 
                                            type="button"
                                            onclick="gestionarSesion(${p.id}, 'add_carrito')" 
                                            class="btn-card-icon btn-carrito"
                                            title="Añadir al carrito"
                                        >
                                            <i class="bi bi-cart"></i>
                                        </button>

                                    </div>

                                </div>

                            </article>

                        </div>
                    `;
                });
            }

            document.getElementById("contenedor-productos").innerHTML = html;

            renderPaginacion(data.total_paginas, pagina);

        })
        .catch(error => {
            console.error("Error al cargar productos:", error);
        });
}

/// Función para pintar la paginación
function renderPaginacion(totalPaginas, paginaActual) {

    const contenedor = document.getElementById("paginacion");

    if (!contenedor) return;

    let pag = "";

    totalPaginas = parseInt(totalPaginas || 1);
    paginaActual = parseInt(paginaActual || 1);

    if (totalPaginas <= 1) {
        contenedor.innerHTML = "";
        return;
    }

    if (paginaActual > 1) {
        pag += `
            <button 
                type="button"
                class="btn btn-sm"
                onclick="cargarProductos(${paginaActual - 1})">
                ‹ Anterior
            </button>
        `;
    }

    for (let i = 1; i <= totalPaginas; i++) {

        const claseActiva = i === paginaActual ? "active" : "";

        pag += `
            <button 
                type="button"
                class="btn btn-sm ${claseActiva}"
                onclick="cargarProductos(${i})">
                ${i}
            </button>
        `;
    }

    if (paginaActual < totalPaginas) {
        pag += `
            <button 
                type="button"
                class="btn btn-sm"
                onclick="cargarProductos(${paginaActual + 1})">
                Siguiente ›
            </button>
        `;
    }

    contenedor.innerHTML = pag;
}

// Función para añadir productos al carrito
function gestionarSesion(id, accion) {

    // Creamos FormData para enviar datos por POST
    const formData = new FormData();

    // Añadimos el ID del producto
    formData.append("id", id);

    // Añadimos la acción
    formData.append("accion", accion);

    // Enviamos la petición al PHP que gestiona el carrito
    fetch("/UNRINCONDEPT/public/ajax_operaciones_carrito.php", {
        method: "POST",
        body: formData
    })

        // IMPORTANTE:
        // Primero leemos como texto para comprobar si PHP devuelve JSON o HTML
        .then(res => res.text())

        .then(text => {

            // Esto te mostrará en consola la respuesta real de PHP
            console.log("Respuesta operaciones_carrito.php:", text);

            // Intentamos convertir a JSON
            return JSON.parse(text);
        })

        .then(data => {

            // Si la operación se ha realizado correctamente
            if (data.status === "success") {

                // Si el usuario NO está logueado
                if (data.usuario_logueado === false) {
                    alert(
                        "No has iniciado sesión. Tus datos se guardarán en el navegador temporalmente."
                    );
                } else {
                    alert(data.message);
                }

                // Actualizamos el contador del carrito
                const cartCount = document.getElementById("cart-count");

                if (cartCount) {
                    cartCount.innerText = data.contador_carrito;
                }
            } else {
                alert(data.message || "No se pudo añadir el producto al carrito.");
            }
        })

        .catch(error => {
            console.error("Error en carrito:", error);
            alert("Error al añadir el producto al carrito.");
        });
}


// Escuchamos clicks en favoritos
document.addEventListener("click", function (e) {

    // Detectamos si se ha pulsado un botón de favorito
    const boton = e.target.closest(".btn-favorito");

    // Si no es favorito, salimos
    if (!boton) return;

    // Recogemos el ID del producto
    const productoId = boton.dataset.id;

    // Enviamos petición AJAX para guardar o eliminar favorito
    fetch("/UNRINCONDEPT/public/ajax_favorito.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "producto_id=" + encodeURIComponent(productoId)
    })
        .then(res => res.json())
        .then(data => {

            // Si hay error
            if (!data.ok) {
                alert(data.error || "No se pudo modificar el favorito");
                return;
            }

            // Si se ha guardado, ponemos el corazón activo
            if (data.estado === "guardado") {
                boton.classList.add("activo");
            }

            // Si se ha eliminado, quitamos el corazón activo
            else {
                boton.classList.remove("activo");
            }

        })
        .catch(error => {
            console.error("Error en favorito:", error);
            alert("Error al guardar favorito");
        });
});
// Comprar ahora: añade al carrito y redirige al carrito
function comprarAhora(id) {

    const formData = new FormData();
    formData.append("id", id);
    formData.append("accion", "add_carrito");

    fetch("/UNRINCONDEPT/public/ajax_operaciones_carrito.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        if (data.status === "success") {
            window.location.href = "/UNRINCONDEPT/public/carrito.php";
        } else {
            alert(data.message || "No se pudo procesar la compra.");
        }

    })
    .catch(error => {
        console.error("Error comprar ahora:", error);
        alert("Error al procesar la compra.");
    });
}
function escapeHtml(text) {
    return String(text ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;");
}