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

    // Arrays para guardar filtros seleccionados
    let categorias = [];
    let niveles = [];

    // Recoger categorías marcadas
    document.querySelectorAll(".filtro-categoria:checked").forEach(el => {
        categorias.push(el.value);
    });

    // Recoger niveles marcados
    document.querySelectorAll(".filtro-nivel:checked").forEach(el => {
        niveles.push(el.value);
    });

    // Recoger texto del buscador
    const buscador = document.getElementById("busqueda");
    let busqueda = buscador ? buscador.value : "";

    // Construimos la URL correctamente
    const url = `/UNRINCONDEPT/public/ajax_productos.php?pagina=${pagina}` +
        `&categorias=${encodeURIComponent(JSON.stringify(categorias))}` +
        `&niveles=${encodeURIComponent(JSON.stringify(niveles))}` +
        `&busqueda=${encodeURIComponent(busqueda)}`;

    // Petición AJAX para obtener productos
    fetch(url)
        .then(res => res.json())
        .then(data => {

            let html = "";

            // Si no hay productos
            if (!data.productos || data.productos.length === 0) {

                html = `
                    <p class="text-center">
                        No hay productos disponibles con estos filtros.
                    </p>
                `;

            } else {

                // Recorremos productos
                data.productos.forEach(p => {

                    // Si el producto es favorito, añadimos clase activo
                    const claseFavorito = p.es_favorito ? "activo" : "";

                    html += `
                        <div class="col-12 col-sm-6 col-md-4 mb-4">

                            <div class="card h-100">

                                <img 
                                    src="/UNRINCONDEPT/static/images/img/${p.imagen}" 
                                    class="card-img-top"
                                    alt="${p.titulo}"
                                >

                                <div class="card-body">

                                    <div class="d-flex justify-content-between align-items-center">

                                        <span class="badge-cat">
                                            ${p.nivel_nombre} - ${p.categoria_nombre}
                                        </span>

                                        <div class="text-warning">
                                            ★★★★☆
                                        </div>

                                    </div>

                                    <h5>${p.titulo}</h5>

                                    <p>
                                        <strong>${p.precio} €</strong>
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center mt-2">

                                        <a href="detalle.php?id=${p.id}" class="btn btn-primary">
                                            Ver
                                        </a>

                                        <div>

                                            <!-- Botón favoritos -->
                                            <button 
                                                type="button"
                                                class="btn btn-outline-danger btn-sm btn-favorito ${claseFavorito}" 
                                                data-id="${p.id}"
                                                title="Añadir o quitar favorito"
                                            >
                                                <i class="bi bi-heart-fill"></i>
                                            </button>

                                            <!-- Botón carrito -->
                                            <button 
                                                type="button"
                                                onclick="gestionarSesion(${p.id}, 'add_carrito')" 
                                                class="btn btn-outline-success btn-sm"
                                                title="Añadir al carrito"
                                            >
                                                <i class="bi bi-cart"></i>
                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                    `;
                });
            }

            // Pintamos productos
            document.getElementById("contenedor-productos").innerHTML = html;

            // Pintamos paginación
            renderPaginacion(data.total_paginas, pagina);

        })
        .catch(error => {
            console.error("Error al cargar productos:", error);
        });
}


// Función para pintar la paginación
function renderPaginacion(totalPaginas, paginaActual) {

    let pag = "";

    // Si solo hay una página, no mostramos paginación
    if (totalPaginas <= 1) {
        document.getElementById("paginacion").innerHTML = "";
        return;
    }

    // Botón anterior
    if (paginaActual > 1) {
        pag += `
            <button 
                class="btn btn-outline-primary mx-1"
                onclick="cargarProductos(${paginaActual - 1})"
            >
                ‹ Anterior
            </button>
        `;
    }

    // Rango de páginas visible
    let start = Math.max(1, paginaActual - 2);
    let end = Math.min(totalPaginas, paginaActual + 2);

    // Si estamos al inicio
    if (paginaActual <= 3) {
        end = Math.min(5, totalPaginas);
    }

    // Si estamos al final
    if (paginaActual >= totalPaginas - 2) {
        start = Math.max(1, totalPaginas - 4);
    }

    // Primera página
    if (start > 1) {
        pag += `
            <button 
                class="btn btn-outline-primary mx-1"
                onclick="cargarProductos(1)"
            >
                1
            </button>
        `;

        if (start > 2) {
            pag += `...`;
        }
    }

    // Páginas centrales
    for (let i = start; i <= end; i++) {

        const activeClass = (i == paginaActual)
            ? "btn-primary"
            : "btn-outline-primary";

        pag += `
            <button 
                class="btn ${activeClass} mx-1"
                onclick="cargarProductos(${i})"
            >
                ${i}
            </button>
        `;
    }

    // Última página
    if (end < totalPaginas) {

        if (end < totalPaginas - 1) {
            pag += `...`;
        }

        pag += `
            <button 
                class="btn btn-outline-primary mx-1"
                onclick="cargarProductos(${totalPaginas})"
            >
                ${totalPaginas}
            </button>
        `;
    }

    // Botón siguiente
    if (paginaActual < totalPaginas) {
        pag += `
            <button 
                class="btn btn-outline-primary mx-1"
                onclick="cargarProductos(${paginaActual + 1})"
            >
                Siguiente ›
            </button>
        `;
    }

    // Pintamos paginación
    document.getElementById("paginacion").innerHTML = pag;
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