/**
 * CARRITO - OPERACIONES AJAX UNIFICADAS
 * ---------------------------------------------------------
 * Este script gestiona acciones del carrito sin enviar formularios
 * tradicionales ni recargar la página manualmente.
 *
 * Funcionalidad:
 * - Detecta clics en botones con la clase .btn-carrito-accion.
 * - Lee el ID del producto desde data-id.
 * - Lee la acción desde data-accion.
 * - Envía los datos por POST a ajax_operaciones_carrito.php.
 * - Actualiza el contador del carrito.
 * - Si el usuario no está logueado, muestra aviso.
 * - Si el producto estaba en favoritos y PHP lo elimina, actualiza la vista.
 * - Puede eliminar visualmente la fila de favoritos si existe.
 *
 * Requisito:
 * Debe existir una constante global BASE_URL antes de cargar este archivo.
 *
 * Ejemplo:
 *
 * <script>
 *     const BASE_URL = "<?= BASE_URL ?>";
 * </script>
 */


/**
 * Escuchamos clics en todo el documento.
 * ---------------------------------------------------------
 * Usamos delegación de eventos porque muchos botones del carrito
 * se generan dinámicamente con JavaScript.
 */
document.addEventListener("click", function (e) {

    /**
     * Buscamos si el clic se ha producido sobre un botón
     * con clase .btn-carrito-accion o dentro de él.
     */
    const btn = e.target.closest(".btn-carrito-accion");

    /**
     * Si el clic no corresponde a una acción del carrito, salimos.
     */
    if (!btn) return;

    /**
     * Obtenemos el ID del producto desde data-id.
     */
    const productoId = btn.dataset.id;

    /**
     * Obtenemos la acción desde data-accion.
     *
     * Acciones esperadas por tu CarritoController:
     * - add_carrito
     * - restar_carrito
     * - eliminar_carrito
     */
    const accion = btn.dataset.accion;

    /**
     * Creamos el FormData para enviar los datos por POST.
     */
    const formData = new FormData();
    formData.append("id", productoId);
    formData.append("accion", accion);

    /**
     * Enviamos la petición al endpoint del carrito.
     *
     * Leemos primero como texto para poder ver errores PHP si el servidor
     * devuelve HTML en lugar de JSON.
     */
    fetch(`${BASE_URL}public/ajax_operaciones_carrito.php`, {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(text => {

        /**
         * Esto ayuda a depurar.
         * Si PHP devuelve un warning o fatal error, aparecerá aquí.
         */
        console.log("Respuesta ajax_operaciones_carrito.php:", text);

        let data;

        /**
         * Intentamos convertir la respuesta a JSON.
         */
        try {
            data = JSON.parse(text);
        } catch (error) {
            console.error("La respuesta del carrito no es JSON:", text);
            alert("El servidor no ha devuelto JSON válido. Revisa la consola.");
            return;
        }

        /**
         * Si el servidor devuelve error controlado, mostramos el mensaje.
         */
        if (data.status !== "success") {
            alert(data.message || "No se pudo actualizar el carrito.");
            return;
        }

        /**
         * Si el usuario no está logueado, mostramos aviso.
         */
        if (data.usuario_logueado === false) {
            alert("No has iniciado sesión. Tus datos se guardarán en el navegador temporalmente.");
        } else {
            alert(data.message || "Carrito actualizado correctamente.");
        }

        /**
         * Actualizamos contador de carrito en escritorio.
         */
        const cartCount = document.getElementById("cart-count");

        if (cartCount) {
            cartCount.innerText = data.contador_carrito;
        }

        /**
         * Actualizamos contador de carrito en móvil.
         */
        const cartCountMobile = document.getElementById("cart-count-mobile");

        if (cartCountMobile) {
            cartCountMobile.innerText = data.contador_carrito;
        }

        /**
         * Si PHP ha eliminado el producto de favoritos,
         * actualizamos la interfaz.
         *
         * Esto conserva la funcionalidad que tenías en gestionarSesion().
         */
        if (data.favorito_eliminado === true) {

            /**
             * Quitamos el estado activo del botón favorito en la tienda.
             */
            const botonFavorito = document.querySelector(
                `.btn-favorito[data-id="${data.producto_id}"]`
            );

            if (botonFavorito) {
                botonFavorito.classList.remove("activo");
            }

            /**
             * Si estamos en el perfil/favoritos y existe una fila
             * con ese producto, la eliminamos visualmente.
             */
            const filaFavorito = document.getElementById(
                "favorito-row-" + data.producto_id
            );

            if (filaFavorito) {
                filaFavorito.remove();
            }
        }

        /**
         * Si la acción es restar o eliminar, normalmente interesa recargar
         * la página del carrito para recalcular totales.
         *
         * En tienda, para add_carrito, no hace falta recargar.
         */
        if (accion === "restar_carrito" || accion === "eliminar_carrito") {
            location.reload();
        }
    })
    .catch(error => {
        console.error("Error carrito:", error);
        alert("Error al actualizar el carrito.");
    });
});