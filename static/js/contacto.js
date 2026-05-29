/**
 * CONTACTO PÚBLICO - ENVÍO AJAX
 * ---------------------------------------------------------
 * Este script gestiona el envío del formulario público de contacto
 * sin recargar la página.
 *
 * Funcionalidad:
 * - Espera a que cargue el HTML.
 * - Busca el formulario con id formContactoPublico.
 * - Intercepta el envío del formulario.
 * - Envía los datos mediante fetch() a PHP.
 * - Muestra mensaje de éxito o error en pantalla.
 * - Limpia el formulario si el envío ha sido correcto.
 *
 * Requisitos HTML:
 *
 * Debe existir un formulario con este id:
 *
 * <form id="formContactoPublico">
 *     ...
 * </form>
 *
 * También debe existir un contenedor para mostrar la respuesta:
 *
 * <div id="respuestaContactoPublico"></div>
 *
 * Requisito JS:
 * Debe existir una constante global BASE_URL definida antes de cargar
 * este archivo JavaScript.
 *
 * Ejemplo en la vista PHP:
 *
 * <script>
 *     const BASE_URL = "<?= BASE_URL ?>";
 * </script>
 */


/**
 * Esperamos a que todo el HTML esté cargado antes de buscar elementos.
 */
document.addEventListener("DOMContentLoaded", function () {

    /**
     * Buscamos el formulario público de contacto.
     */
    const form = document.getElementById("formContactoPublico");

    /**
     * Si el formulario no existe en esta página, detenemos la ejecución.
     *
     * Esto evita errores si este archivo JS se carga en páginas
     * donde no está el formulario de contacto.
     */
    if (!form) return;

    /**
     * Interceptamos el evento submit del formulario.
     */
    form.addEventListener("submit", function (e) {

        /**
         * Evitamos el envío tradicional del formulario.
         *
         * Sin esto, la página se recargaría.
         */
        e.preventDefault();

        /**
         * Contenedor donde mostraremos el mensaje de respuesta:
         * - éxito
         * - error
         */
        const respuesta = document.getElementById("respuestaContactoPublico");

        /**
         * Recogemos todos los campos del formulario.
         *
         * FormData permite enviar los datos igual que si fuera
         * un formulario normal por POST.
         */
        const formData = new FormData(form);

        /**
         * Enviamos los datos al endpoint PHP mediante fetch().
         * Así funcionará tanto en local como en producción,
         * siempre que BASE_URL esté correctamente definida.
         */
        fetch(`${PUBLIC_URL}ajax_contacto_mensajes.php`, {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {

            /**
             * Si PHP devuelve ok=false, mostramos el error.
             *
             * Se espera una respuesta tipo:
             *
             * {
             *   ok: false,
             *   error: "Mensaje de error"
             * }
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
             *
             * Se espera una respuesta tipo:
             *
             * {
             *   ok: true,
             *   mensaje: "Mensaje enviado correctamente"
             * }
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
            console.error("Error contacto:", error);

            /**
             * Mostramos un mensaje genérico al usuario.
             */
            respuesta.innerHTML = `
                <div class="alert alert-danger">
                    No se pudo enviar el mensaje.
                </div>
            `;
        });

    });

});