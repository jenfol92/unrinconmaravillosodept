/**
 * FAVORITOS GLOBAL
 * ---------------------------------------------------------
 * Gestiona favoritos en index, tienda, detalle y perfil.
 *
 * Versión corregida:
 * - NO usa localStorage.
 * - Si el usuario no está logueado, no guarda favoritos temporales.
 * - Si el usuario está logueado, usa ajax_favorito.php.
 * - Evita que el archivo se ejecute dos veces si se carga duplicado.
 */

(function () {

    /**
     * Evita doble ejecución si favoritos.js se carga dos veces.
     */
    if (window.__FAVORITOS_GLOBAL_CARGADO__ === true) {
        console.warn("favoritos.js ya estaba cargado. Se evita duplicar listeners.");
        return;
    }

    window.__FAVORITOS_GLOBAL_CARGADO__ = true;

    /**
     * Comprueba si el usuario está logueado.
     * ---------------------------------------------------------
     * Acepta true, 1, "1" o "true" para evitar errores si PHP
     * imprime el valor de forma distinta.
     */
    function usuarioEstaLogueado() {
        return (
            window.USUARIO_LOGUEADO === true ||
            window.USUARIO_LOGUEADO === 1 ||
            window.USUARIO_LOGUEADO === "1" ||
            window.USUARIO_LOGUEADO === "true"
        );
    }

    /**
     * Actualiza todos los botones de favorito del mismo producto.
     */
function actualizarBotonesFavorito(productoId, activo) {
    document
        .querySelectorAll(`.btn-favorito[data-id="${productoId}"]`)
        .forEach(boton => {
            boton.classList.toggle("activo", activo);

            const icono = boton.querySelector("i");

            if (icono) {
                icono.classList.toggle("bi-heart-fill", activo);
                icono.classList.toggle("bi-heart", !activo);
            }

            boton.setAttribute(
                "title",
                activo ? "Quitar de favoritos" : "Añadir a favoritos"
            );

            boton.setAttribute(
                "aria-label",
                activo ? "Quitar de favoritos" : "Añadir a favoritos"
            );
        });
}

    /**
     * Elimina visualmente un favorito del perfil/listado.
     */
    function eliminarFavoritoDelPerfil(productoId) {
        document
            .querySelectorAll(
                `#favorito-row-${productoId}, #favorito-card-${productoId}, [data-favorito-item-id="${productoId}"]`
            )
            .forEach(elemento => elemento.remove());
    }

    /**
     * Compatibilidad:
     * tienda.js llama a window.aplicarFavoritosTemporales().
     * La dejamos vacía para no romper nada.
     */
    window.aplicarFavoritosTemporales = function () {
        return;
    };

    /**
     * Función pública para que carrito.js pueda quitar visualmente
     * un favorito cuando se añade al carrito.
     */
    window.quitarFavoritoVisual = function (productoId) {
        productoId = String(productoId);

        actualizarBotonesFavorito(productoId, false);
        eliminarFavoritoDelPerfil(productoId);
    };

    /**
     * Click global en botones de favorito.
     */
    document.addEventListener("click", function (e) {

        const boton = e.target.closest(".btn-favorito");

        if (!boton) return;

        e.preventDefault();
        e.stopPropagation();

        if (typeof e.stopImmediatePropagation === "function") {
            e.stopImmediatePropagation();
        }

        const productoId = boton.dataset.id;

        if (!productoId || productoId === "0") {
            console.error("Botón favorito sin ID válido:", boton);
            alert("Error: no se ha podido identificar el recurso.");
            return;
        }

        console.log("CLICK FAVORITO:", {
            productoId: productoId,
            USUARIO_LOGUEADO: window.USUARIO_LOGUEADO,
            interpretadoComoLogueado: usuarioEstaLogueado()
        });

        /**
         * Si no está logueado, NO guardamos en navegador.
         */
        if (!usuarioEstaLogueado()) {
            alert("Para guardar favoritos debes iniciar sesión.");
            window.location.href = PUBLIC_URL + "login.php";
            return;
        }

        /**
         * Usuario logueado:
         * se guarda o elimina en base de datos.
         */
        fetch(PUBLIC_URL + "ajax_favorito.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            credentials: "same-origin",
            body: "producto_id=" + encodeURIComponent(productoId)
        })
            .then(res => res.text())
            .then(text => {

                console.log("Respuesta ajax_favorito.php:", text);

                let data;

                try {
                    data = JSON.parse(text);
                } catch (error) {
                    console.error("La respuesta de favoritos no es JSON:", text);
                    alert("El servidor no ha devuelto JSON válido.");
                    return;
                }

                if (!data.ok) {
                    alert(data.error || "No se pudo modificar el favorito.");
                    return;
                }

                if (data.estado === "guardado") {
                    actualizarBotonesFavorito(productoId, true);
                } else {
                    actualizarBotonesFavorito(productoId, false);
                    eliminarFavoritoDelPerfil(productoId);
                }

            })
            .catch(error => {
                console.error("Error en favorito:", error);
                alert("Error al modificar favorito.");
            });
    });

})();