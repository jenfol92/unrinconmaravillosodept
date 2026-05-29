/**
 * PRODUCTO DETALLE - CHAT DE SOPORTE Y CAMBIO IMAGEN/VÍDEO
 * ---------------------------------------------------------
 * Este archivo gestiona dos funcionalidades principales
 * dentro de la página de detalle de producto:
 *
 * 1. Envío de consultas sobre un producto mediante AJAX.
 * 2. Cambio visual entre imagen principal y vídeo del producto.
 *
 * Requisitos:
 * - Debe existir una constante global BASE_URL definida antes
 *   de cargar este archivo.
 *
 * Ejemplo en la vista PHP:
 *
 * <script>
 *     const BASE_URL = "<?= BASE_URL ?>";
 * </script>
 *
 * <script src="<?= BASE_URL ?>static/js/producto_detalle.js"></script>
 */


document.addEventListener("DOMContentLoaded", function () { 

    /**
     * Buscamos el formulario real del modal de producto.
     *
     * Este formulario debe tener el id:
     * formChatProducto
     *
     * Normalmente se encuentra dentro del modal de consulta
     * del producto.
     */
    const form = document.getElementById("formChatProducto");

    /**
     * Si el formulario no existe en esta página,
     * detenemos la ejecución.
     *
     * Esto evita errores si este JS se carga en páginas donde
     * no existe el modal de consulta de producto.
     */
    if (!form) return;

    /**
     * Interceptamos el envío del formulario.
     */
    form.addEventListener("submit", function (e) {

        /**
         * Evitamos que el formulario recargue la página.
         *
         * Así podemos enviarlo mediante fetch/AJAX.
         */
        e.preventDefault();

        /**
         * Recogemos todos los campos del formulario.
         *
         * Normalmente incluirá:
         * - producto_id
         * - asunto
         * - mensaje
         */
        const formData = new FormData(form);

        /**
         * Enviamos la consulta al endpoint PHP.
         */
    
        fetch(`${PUBLIC_URL}ajax_soporte_producto.php`, {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {

            /**
             * Div donde mostraremos la respuesta dentro del modal.
             */
            const respuesta = document.getElementById("respuestaChatProducto");

            /**
             * Si PHP devuelve ok=false, mostramos el error.
             */
            if (!data.ok) {
                respuesta.innerHTML = `
                    <div class="alert alert-danger">
                        ${data.error}
                    </div>
                `;
                return;
            }

            /**
             * Si PHP devuelve ok=true, mostramos mensaje de éxito.
             */
            respuesta.innerHTML = `
                <div class="alert alert-success">
                    ${data.mensaje}
                </div>
            `;

            /**
             * Limpiamos el formulario después de enviar correctamente.
             */
            form.reset();
        })
        .catch(error => {

            /**
             * Capturamos errores de red, errores del servidor
             * o respuestas que no sean JSON válido.
             */
            console.error("Error soporte producto:", error);

            /**
             * Buscamos de nuevo el contenedor de respuesta.
             */
            const respuesta = document.getElementById("respuestaChatProducto");

            /**
             * Si existe el contenedor, mostramos un mensaje genérico.
             */
            if (respuesta) {
                respuesta.innerHTML = `
                    <div class="alert alert-danger">
                        Error al enviar la consulta.
                    </div>
                `;
            }
        });
    });
});


/**
 * Muestra el vídeo principal del producto.
 * ---------------------------------------------------------
 * Esta función queda disponible de forma global porque se asigna
 * a window.
 *
 * Esto permite llamarla desde el HTML, por ejemplo:
 *
 * onclick="mostrarVideoProducto()"
 *
 * Funcionalidad:
 * - Oculta la imagen principal.
 * - Muestra el vídeo principal.
 * - Activa el modo vídeo en la caja contenedora.
 * - Cambia el estado visual de las miniaturas.
 * - Intenta reproducir el vídeo.
 */
window.mostrarVideoProducto = function () {

    /**
     * Imagen principal del producto.
     */
    const imagen = document.getElementById('productoImagenPrincipal');

    /**
     * Vídeo principal del producto.
     */
    const video = document.getElementById('productoVideoPrincipal');

    /**
     * Miniatura o botón asociado a la imagen.
     */
    const thumbImagen = document.getElementById('thumbImagenProducto');

    /**
     * Miniatura o botón asociado al vídeo.
     */
    const thumbVideo = document.getElementById('thumbVideoProducto');

    /**
     * Si no existe la imagen o el vídeo principal,
     * mostramos aviso en consola y detenemos la función.
     */
    if (!imagen || !video) {
        console.warn('No se ha encontrado la imagen o el vídeo principal.');
        return;
    }

    /**
     * Buscamos la caja contenedora del vídeo.
     *
     * Se usa para añadir una clase especial cuando está activo
     * el modo vídeo.
     */
    const caja = video.closest('.producto-img-box');

    /**
     * Ocultamos la imagen principal.
     */
    imagen.classList.add('d-none');

    /**
     * Mostramos el vídeo principal.
     */
    video.classList.remove('d-none');

    /**
     * Si existe la caja contenedora, añadimos modo-video.
     *
     * Esta clase puede usarse en SCSS/CSS para ajustar estilos
     * cuando se está mostrando el vídeo.
     */
    if (caja) {
        caja.classList.add('modo-video');
    }

    /**
     * Quitamos la clase active de la miniatura de imagen.
     */
    if (thumbImagen) {
        thumbImagen.classList.remove('active');
    }

    /**
     * Añadimos la clase active a la miniatura de vídeo.
     */
    if (thumbVideo) {
        thumbVideo.classList.add('active');
    }

    /**
     * Intentamos reproducir el vídeo.
     *
     * Algunos navegadores pueden bloquear la reproducción automática,
     * por eso se captura el error con catch().
     */
    video.play().catch(() => {
        console.log('El navegador ha bloqueado la reproducción automática.');
    });
};


/**
 * Muestra la imagen principal del producto.
 * ---------------------------------------------------------
 * Esta función queda disponible de forma global porque se asigna
 * a window.
 *
 * Esto permite llamarla desde el HTML, por ejemplo:
 *
 * onclick="mostrarImagenProducto()"
 *
 * Funcionalidad:
 * - Pausa el vídeo si existe.
 * - Reinicia el vídeo al segundo 0.
 * - Oculta el vídeo.
 * - Muestra la imagen principal.
 * - Actualiza el estado visual de las miniaturas.
 */
window.mostrarImagenProducto = function () {

    /**
     * Imagen principal del producto.
     */
    const imagen = document.getElementById('productoImagenPrincipal');

    /**
     * Vídeo principal del producto.
     */
    const video = document.getElementById('productoVideoPrincipal');

    /**
     * Miniatura o botón asociado a la imagen.
     */
    const thumbImagen = document.getElementById('thumbImagenProducto');

    /**
     * Miniatura o botón asociado al vídeo.
     */
    const thumbVideo = document.getElementById('thumbVideoProducto');

    /**
     * Si no existe la imagen principal,
     * no podemos cambiar correctamente al modo imagen.
     */
    if (!imagen) {
        console.warn('No se ha encontrado la imagen principal.');
        return;
    }

    /**
     * Si existe vídeo, lo pausamos, lo reiniciamos y lo ocultamos.
     */
    if (video) {
        const caja = video.closest('.producto-img-box');

        /**
         * Pausamos el vídeo.
         */
        video.pause();

        /**
         * Reiniciamos el vídeo al principio.
         */
        video.currentTime = 0;

        /**
         * Ocultamos el vídeo.
         */
        video.classList.add('d-none');

        /**
         * Quitamos el modo vídeo de la caja contenedora.
         */
        if (caja) {
            caja.classList.remove('modo-video');
        }
    }

    /**
     * Mostramos de nuevo la imagen principal.
     */
    imagen.classList.remove('d-none');

    /**
     * Quitamos la clase active de la miniatura de vídeo.
     */
    if (thumbVideo) {
        thumbVideo.classList.remove('active');
    }

    /**
     * Añadimos la clase active a la miniatura de imagen.
     */
    if (thumbImagen) {
        thumbImagen.classList.add('active');
    }
};