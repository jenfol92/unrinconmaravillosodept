// TIENDA.JS - CARGA DINÁMICA DE PRODUCTOS
// Finalidad:
// - Cargar los productos de la tienda mediante AJAX.
// - Aplicar filtros de categoría, nivel y búsqueda.
// - Pintar las tarjetas de producto de forma dinámica.
// - Gestionar favoritos y carrito.

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

// CARGAR PRODUCTOS
// Esta función:
// 1. Lee los filtros seleccionados.
// 2. Construye la URL del endpoint PHP.
// 3. Recibe los productos en JSON.
// 4. Genera el HTML de las tarjetas.
// 5. Actualiza la paginación.


function cargarProductos(pagina = 1) {

    let categorias = [];
    let niveles = [];
    /*
        Recogemos las categorías marcadas.
        Se envían como array JSON al PHP.
      */
    document.querySelectorAll(".filtro-categoria:checked").forEach(el => {
        categorias.push(el.value);
    });
    /*
    Recogemos los niveles marcados.
    Se envían como array JSON al PHP.
*/

    document.querySelectorAll(".filtro-nivel:checked").forEach(el => {
        niveles.push(el.value);
    });
    /*
    Recogemos la búsqueda escrita por el usuario.
*/

    const buscador = document.getElementById("busqueda");
    let busqueda = buscador ? buscador.value : "";

    /*
     Endpoint que devuelve los productos filtrados.
 */

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
                    /*
                      Estado visual del botón de favorito.
                  */

                    const claseFavorito = p.es_favorito ? "activo" : "";
                    /*
                       Precio formateado siempre con dos decimales.
                   */
                    const precio = parseFloat(p.precio || 0).toFixed(2);
                    /*
                       Imagen segura:
                       Si por algún motivo no viene imagen desde la base de datos,
                       usamos default.png para evitar una ruta rota.
                       
                   */
                    const imagen = p.imagen || "default.png";
                    const imagenUrl = `/UNRINCONDEPT/static/images/img/${escapeHtml(imagen)}`;
                    /*
                      Texto seguro para evitar inyección HTML.
                  */
                    const titulo = escapeHtml(p.titulo || "Recurso");
                    const nivel = escapeHtml(p.nivel_nombre || "");
                    const categoria = escapeHtml(p.categoria_nombre || "");

                    html += `
                        <div class="col-12 col-sm-6 col-lg-4 mb-4">

                            <article class="tienda-card">

                               <!-- Imagen única del producto -->
<div class="tienda-card-img-wrap">

    <img
        src="${imagenUrl}"
        class="tienda-card-img"
        alt="${titulo}"
        loading="lazy"
        onerror="this.onerror=null;this.src='/UNRINCONDEPT/static/images/img/default.png';"
    >

</div>

                                <div class="card-body">

                                    <div class="d-flex justify-content-between align-items-center gap-2">

                                        <span class="badge-cat">
                                            ${nivel} - ${categoria}
                                        </span>

                                        <div class="stars-mini">
                                            ★★★★☆
                                        </div>

                                    </div>

                                    <h5>
                                        ${titulo}
                                    </h5>

                                    <div class="tienda-card-price">
                                        ${precio} €
                                    </div>

                                    <div class="tienda-card-actions">

                                        <a
                                            href="/UNRINCONDEPT/public/detalle.php?id=${encodeURIComponent(p.id)}"
                                            class="btn-ver-recurso">
                                            Ver
                                        </a>

                                        <button
                                            type="button"
                                            class="btn-card-icon btn-favorito ${claseFavorito}"
                                            data-id="${escapeHtml(p.id)}"
                                            title="Añadir o quitar favorito"
                                        >
                                            <i class="bi bi-heart-fill"></i>
                                        </button>

                                        <button
                                            type="button"
                                            onclick="gestionarSesion(${Number(p.id)}, 'add_carrito')"
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
// PAGINACIÓN
// Pinta los botones de paginación según el total de páginas
// que devuelve ajax_productos.php.


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

// AÑADIR PRODUCTO AL CARRITO
// Esta función se usa desde el botón del carrito.
// Envía el producto al endpoint que gestiona la sesión/carrito.

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

// FAVORITOS
// Escucha cualquier click sobre un botón .btn-favorito.
// Como las tarjetas se generan por AJAX, usamos delegación
// de eventos sobre document.

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

// COMPRAR AHORA
// Añade el producto al carrito y redirige directamente
// a carrito.php.

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


// ESCAPAR HTML
// Evita que textos procedentes de la base de datos puedan
// romper el HTML o insertar código no deseado

function escapeHtml(text) {
    return String(text ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;");
}