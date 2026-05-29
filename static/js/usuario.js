/**
 * Archivo: panel_usuario.js / perfil_usuario.js
 * ---------------------------------------------------------
 * Funcionalidad general:
 *
 * Este archivo controla distintas acciones del panel de usuario:
 *
 * - Cambio de secciones del panel mediante enlaces del sidebar.
 * - Envío de tickets de soporte.
 * - Apertura de conversaciones de soporte del usuario.
 * - Lectura de mensajes de un ticket.
 * - Respuesta a tickets desde el usuario.
 * - Finalización de tickets.
 * - Apertura del modal para crear reseñas.
 * - Guardado de reseñas.
 * - Envío de sugerencias.
 * - Envío de alerta de seguridad.
 *
 * Trabaja principalmente con:
 *
 * - Eventos DOMContentLoaded.
 * - Eventos click delegados.
 * - Formularios enviados mediante fetch.
 * - Modales de Bootstrap.
 * - Respuestas JSON procedentes de PHP.
 *
 * IMPORTANTE:
 * Este archivo depende de que exista una constante global BASE_URL
 * definida previamente en la página HTML/PHP.
 */


// Esperamos a que cargue completamente el HTML antes de buscar elementos del DOM.
document.addEventListener("DOMContentLoaded", function () {

    /**
     * Enlaces laterales del panel.
     *
     * Cada enlace debe tener la clase .panel-link
     * y un atributo data-section indicando la sección que debe abrir.
     *
     * Ejemplo:
     * <button class="panel-link" data-section="pedidos">Pedidos</button>
     */
    const links = document.querySelectorAll(".panel-link");

    /**
     * Secciones del contenido derecho del panel.
     *
     * Cada sección debe tener la clase .panel-section
     * y un id con este formato:
     *
     * section-NOMBRE
     *
     * Ejemplo:
     * <div id="section-pedidos" class="panel-section"></div>
     */
    const sections = document.querySelectorAll(".panel-section");

    // Recorremos todos los enlaces del sidebar.
    links.forEach(link => {

        /**
         * Al pulsar en un enlace del sidebar:
         *
         * - Quitamos la clase active a todos los enlaces.
         * - Ocultamos todas las secciones.
         * - Activamos visualmente el enlace pulsado.
         * - Mostramos la sección correspondiente.
         */
        link.addEventListener("click", function () {

            // Quitamos active del sidebar.
            links.forEach(item => item.classList.remove("active"));

            // Ocultamos todas las secciones del panel derecho.
            sections.forEach(section => section.classList.remove("active"));

            // Activamos el botón o enlace que ha sido pulsado.
            this.classList.add("active");

            // Leemos el nombre de la sección desde data-section.
            const sectionName = this.dataset.section;

            // Buscamos la sección correspondiente usando el id section-NOMBRE.
            const targetSection = document.getElementById(`section-${sectionName}`);

            // Si existe la sección, la mostramos añadiendo la clase active.
            if (targetSection) {
                targetSection.classList.add("active");
            }
        });

    });

});



// Envío del formulario de soporte


document.addEventListener("DOMContentLoaded", function () {

    /**
     * Formulario para crear un nuevo ticket de soporte.
     *
     * Debe existir en el HTML con este id:
     * formSoporte
     */
    const formSoporte = document.getElementById("formSoporte");

    // Si no existe el formulario en esta página, detenemos la ejecución.
    if (!formSoporte) return;

    /**
     * Interceptamos el envío del formulario para enviarlo por AJAX
     * sin recargar la página.
     */
    formSoporte.addEventListener("submit", function (e) {

        // Evitamos el envío tradicional del formulario.
        e.preventDefault();

        // Recogemos todos los campos del formulario.
        const formData = new FormData(formSoporte);

        /**
         * Enviamos el ticket al endpoint PHP.
         *
         * Este archivo PHP debe:
         * - Recibir los datos por POST.
         * - Crear el ticket.
         * - Devolver una respuesta JSON.
         */
        fetch(PUBLIC_URL+"ajax_soporte_crear.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {

                // Contenedor donde se mostrará el resultado al usuario.
                const respuesta = document.getElementById("soporteRespuesta");

                // Si PHP devuelve ok=false, mostramos el error.
                if (!data.ok) {
                    respuesta.innerHTML = `
                        <div class="alert alert-danger">
                            ${data.error}
                        </div>
                    `;
                    return;
                }

                // Si todo ha ido correctamente, mostramos mensaje de éxito.
                respuesta.innerHTML = `
                    <div class="alert alert-success">
                        ${data.mensaje}
                    </div>
                `;

                // Limpiamos el formulario tras crear el ticket.
                formSoporte.reset();
            })
            .catch(error => {
                // Error de red, error de servidor o JSON inválido.
                console.error("Error soporte:", error);
            });
    });
});




// Abrir modal de reseña


/**
 * Evento delegado para abrir el modal de reseña.
 *
 * Se activa cuando se pulsa un elemento con clase .btn-abrir-resena.
 */
document.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-abrir-resena");

    // Si no se ha pulsado un botón de reseña, salimos.
    if (!btn) return;

    /**
     * Datos del producto obtenidos desde atributos data-*.
     *
     * Ejemplo:
     * data-producto-id="5"
     * data-producto-titulo="Producto de ejemplo"
     */
    const productoId = btn.dataset.productoId;
    const titulo = btn.dataset.productoTitulo;

    // Guardamos el ID del producto en un input oculto del formulario.
    document.getElementById("resenaProductoId").value = productoId;

    // Personalizamos el título del modal.
    document.getElementById("modalResenaTitulo").innerText = "Reseña: " + titulo;

    // Limpiamos mensajes anteriores.
    document.getElementById("respuestaResena").innerHTML = "";

    // Abrimos el modal Bootstrap.
    const modal = new bootstrap.Modal(document.getElementById("modalResena"));
    modal.show();
});


// Guardar reseña

document.addEventListener("DOMContentLoaded", function () {

    /**
     * Formulario para guardar una reseña.
     */
    const formResena = document.getElementById("formResena");

    // Si no existe el formulario en esta página, salimos.
    if (!formResena) return;

    formResena.addEventListener("submit", function (e) {

        // Evitamos recarga de página.
        e.preventDefault();

        // Recogemos todos los campos de la reseña.
        const formData = new FormData(formResena);

        /**
         * Endpoint PHP que guarda la reseña.
         
         */
        fetch(PUBLIC_URL+"ajax_guardar_reseña.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {

                const respuesta = document.getElementById("respuestaResena");

                // Si hay error, lo mostramos.
                if (!data.ok) {
                    respuesta.innerHTML = `
                    <div class="alert alert-danger">
                        ${data.error}
                    </div>
                `;
                    return;
                }

                // Si la reseña se guarda correctamente, mostramos confirmación.
                respuesta.innerHTML = `
                <div class="alert alert-success">
                    ${data.mensaje}
                </div>
            `;

                // Limpiamos el formulario.
                formResena.reset();
            })
            .catch(error => {
                console.error("Error guardando reseña:", error);
            });
    });
});


// Enviar sugerencia


document.addEventListener("DOMContentLoaded", function () {

    /**
     * Formulario para enviar sugerencias desde el usuario.
     */
    const formSugerencia = document.getElementById("formSugerencia");

    // Si no existe en la página actual, salimos.
    if (!formSugerencia) return;

    formSugerencia.addEventListener("submit", function (e) {

        // Evitamos el envío tradicional.
        e.preventDefault();

        // Recogemos los datos del formulario.
        const formData = new FormData(formSugerencia);

        /**
         * Endpoint PHP encargado de crear la sugerencia.
     
         */
        fetch(PUBLIC_URL+"ajax_sugerencia_crear.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {

            const respuesta = document.getElementById("respuestaSugerencia");

            // Si PHP devuelve error, lo mostramos.
            if (!data.ok) {
                respuesta.innerHTML = `
                    <div class="alert alert-danger">
                        ${data.error}
                    </div>
                `;
                return;
            }

            // Si la sugerencia se guarda correctamente, mostramos mensaje.
            respuesta.innerHTML = `
                <div class="alert alert-success">
                    ${data.mensaje}
                </div>
            `;

            // Limpiamos el formulario.
            formSugerencia.reset();
        })
        .catch(error => {
            console.error("Error sugerencia:", error);
        });
    });
});


/**
 * Muestra la alerta de seguridad y envía la acción al servidor.
 * ---------------------------------------------------------
 *
 * Esta función queda expuesta globalmente en window para poder ser llamada
 * directamente desde el HTML, por ejemplo:
 *
 * onclick="enviarAlertaSeguridad()"
 *
 * Al confirmar:
 *
 * - Se registra la IP como sospechosa.
 * - Se cierran las sesiones activas.
 * - Se redirige al usuario al cambio o recuperación de contraseña.
 */
window.enviarAlertaSeguridad = function () {

    /**
     * Pedimos confirmación antes de ejecutar una acción sensible.
     *
     * Esta operación afecta a la seguridad de la cuenta, por lo que
     * no debe ejecutarse accidentalmente.
     */
    const confirmar = confirm(
        "Se registrará la IP como sospechosa, se cerrarán todas las sesiones activas y tendrás que cambiar tu contraseña. ¿Quieres continuar?"
    );

    // Si el usuario cancela, no hacemos nada.
    if (!confirmar) {
        return;
    }

    /**
     * Enviamos la alerta de seguridad al servidor.
    
     */
    fetch(`${PUBLIC_URL}ajax_alerta_seguridad.php`, {
        method: "POST"
    })
        /*
            Leemos la respuesta como texto en lugar de JSON directamente.

            Esto permite ver en consola posibles errores PHP, warnings
            o HTML devuelto por el servidor, que impedirían parsear JSON.
        */
        .then(res => res.text())

        .then(text => {

            // Mostramos la respuesta completa para depuración.
            console.log("Respuesta ajax_alerta_seguridad.php:", text);

            let data;

            /**
             * Intentamos convertir manualmente la respuesta a JSON.
             *
             * Si PHP devuelve un warning, notice, HTML o cualquier texto
             * que no sea JSON válido, entrará en el catch.
             */
            try {
                data = JSON.parse(text);
            } catch (error) {
                console.error("La respuesta no es JSON:", text);
                alert("El servidor no ha devuelto una respuesta válida. Revisa la consola.");
                return;
            }

            // Si el servidor devuelve ok=false, mostramos el mensaje de error.
            if (!data.ok) {
                alert(data.mensaje || "No se pudo procesar la alerta de seguridad.");
                return;
            }

            // Mostramos el mensaje de éxito devuelto por PHP.
            alert(data.mensaje);

            /*
                Si PHP devuelve una URL de redirección,
                mandamos al usuario al flujo de cambio/recuperación.
            */
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        })

        .catch(error => {
            // Error de red o problema inesperado en la petición.
            console.error("Error alerta seguridad:", error);
            alert("Error al enviar la alerta de seguridad.");
        });
};