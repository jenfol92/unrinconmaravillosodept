/**
 * Archivo: recursos_gratuitos.js
 * ---------------------------------------------------------
 * Funcionalidad:
 *
 * Controla la carga dinámica de recursos gratuitos en la página.
 *
 * Permite:
 * - Buscar recursos gratuitos por texto.
 * - Filtrar recursos por categoría.
 * - Mostrar todos los recursos.
 * - Actualizar visualmente los chips/filtros activos.
 * - Cargar los recursos mediante AJAX.
 * - Pintar las tarjetas de recursos en el grid.
 * - Mostrar un estado vacío cuando no hay resultados.
 *
 * Este archivo depende de:
 * - Una constante global BASE_URL definida previamente.
 * - Un endpoint PHP: public/ajax_recursos_gratuitos.php
 * - Una estructura HTML con los IDs y clases utilizados abajo.
 */


// Esperamos a que todo el HTML esté cargado antes de acceder al DOM.
document.addEventListener("DOMContentLoaded", function () {

    /**
     * Contenedor principal donde se pintan las tarjetas
     * de recursos gratuitos.
     */
    const grid = document.getElementById("recursosGratisGrid");

    /**
     * Elemento donde se muestra el número de recursos encontrados.
     *
     * Ejemplo:
     * "Mostrando 5 recursos encontrados"
     */
    const contador = document.getElementById("contadorRecursosGratis");

    /**
     * Elemento que se muestra cuando no existen recursos
     * que coincidan con los filtros aplicados.
     */
    const empty = document.getElementById("recursosGratisEmpty");

    /**
     * Campo de búsqueda para filtrar recursos por texto.
     */
    const buscador = document.getElementById("buscarRecursoGratis");

    /**
     * Checkbox o filtro que representa la opción de mostrar
     * todos los recursos gratuitos.
     */
    const filtroTodos = document.getElementById("filtroTodosGratis");

    /**
     * Checkboxes de categorías.
     *
     * Cada checkbox debe tener la clase:
     * .filtro-categoria-gratis
     */
    const checksCategorias = document.querySelectorAll(".filtro-categoria-gratis");

    /**
     * Si no existe el grid en la página actual, detenemos la ejecución.
     *
     * Esto evita errores si este JS se carga en páginas donde no hay
     * sección de recursos gratuitos.
     */
    if (!grid) return;


    /**
     * Obtiene las categorías seleccionadas por el usuario.
     *
     * Recorre todos los checkboxes de categorías y devuelve
     * un array con los value de los que estén marcados.
     *
     * @returns {Array<string>}
     * Array con los IDs o valores de las categorías seleccionadas.
     */
    function obtenerCategoriasSeleccionadas() {
        return Array.from(checksCategorias)
            .filter(chk => chk.checked)
            .map(chk => chk.value);
    }


    /**
     * Actualiza el aspecto visual de los chips de categorías.
     *
     * Si el input interno de un chip está marcado, se añade la clase active.
     * Si no está marcado, se elimina la clase active.
     *
     * Esto permite que el filtro seleccionado se vea resaltado.
     *
     * @returns {void}
     */
    function actualizarChips() {
        document.querySelectorAll(".gratis-chip").forEach(label => {
            const input = label.querySelector("input");

            if (input && input.checked) {
                label.classList.add("active");
            } else {
                label.classList.remove("active");
            }
        });
    }


    /**
     * Carga los recursos gratuitos desde el servidor mediante fetch.
     *
     * Recoge:
     * - Categorías seleccionadas.
     * - Texto introducido en el buscador.
     *
     * Después construye una URL con esos parámetros y llama al endpoint PHP.
     *
     * @returns {void}
     */
    function cargarRecursosGratis() {
        /**
         * Array con las categorías seleccionadas actualmente.
         */
        const categorias = obtenerCategoriasSeleccionadas();

        /**
         * Texto de búsqueda introducido por el usuario.
         *
         * Si no existe el buscador, se usa una cadena vacía.
         */
        const busqueda = buscador ? buscador.value : "";

        /**
         * URL del endpoint AJAX.
         *
         * Las categorías se envían como JSON codificado en la URL.
         * La búsqueda también se codifica para evitar problemas con espacios,
         * tildes o caracteres especiales.
         */
        const url = `${PUBLIC_URL}ajax_recursos_gratuitos.php?categorias=${encodeURIComponent(JSON.stringify(categorias))}&busqueda=${encodeURIComponent(busqueda)}`;

        /**
         * Mostramos un mensaje temporal mientras se cargan los recursos.
         */
        grid.innerHTML = `
            <div class="col-12 text-center py-5 text-muted">
                Cargando recursos...
            </div>
        `;

        /**
         * Petición al servidor para obtener los recursos gratuitos.
         *
         * Se espera una respuesta JSON con una estructura similar a:
         *
         * {
         *   ok: true,
         *   recursos: [...],
         *   total: 5
         * }
         */
        fetch(url)
            .then(res => res.json())
            .then(data => {

                /**
                 * Si el servidor devuelve ok=false, mostramos un mensaje
                 * de error dentro del grid.
                 */
                if (!data.ok) {
                    grid.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-danger">
                                No se pudieron cargar los recursos.
                            </div>
                        </div>
                    `;
                    return;
                }

                /**
                 * Pintamos en pantalla los recursos recibidos.
                 */
                pintarRecursos(data.recursos);

                /**
                 * Si existe el contador, actualizamos el texto
                 * con el total devuelto por el servidor.
                 */
                if (contador) {
                    contador.textContent = `Mostrando ${data.total} recursos encontrados`;
                }
            })
            .catch(error => {
                /**
                 * Captura errores de red, errores del servidor
                 * o problemas al convertir la respuesta a JSON.
                 */
                console.error("Error cargando recursos gratuitos:", error);
            });
    }


    /**
     * Pinta las tarjetas de recursos gratuitos en el grid.
     *
     * Si no hay recursos:
     * - Vacía el grid.
     * - Muestra el bloque empty.
     *
     * Si hay recursos:
     * - Oculta el bloque empty.
     * - Genera el HTML de cada tarjeta.
     * - Inserta el resultado dentro del grid.
     *
     * @param {Array<Object>} recursos
     * Listado de recursos gratuitos recibido desde PHP.
     *
     * @returns {void}
     */
    function pintarRecursos(recursos) {
        /**
         * Si no hay recursos, mostramos el estado vacío.
         */
        if (!recursos || recursos.length === 0) {
            grid.innerHTML = "";
            if (empty) empty.classList.remove("d-none");
            return;
        }

        /**
         * Si sí hay recursos, ocultamos el estado vacío.
         */
        if (empty) empty.classList.add("d-none");

        /**
         * Variable acumuladora donde se irá construyendo
         * el HTML de todas las tarjetas.
         */
        let html = "";

        /**
         * Recorremos cada recurso y generamos su tarjeta.
         */
        recursos.forEach(recurso => {
            
            /**
             * Si el recurso tiene imagen, construimos su ruta completa.
             *
             * Se usa escapeHtml para evitar inyección de HTML
             * si el nombre de la imagen viniera manipulado.
             */
            const imagen = recurso.imagen
                ? `${BASE_URL}static/images/img/${escapeHtml(recurso.imagen)}`
                : "";

    console.log("Nombre imagen en BD:", recurso.imagen);
    console.log("Ruta final:", imagen);

            /**
             * Generamos el HTML de la tarjeta.
             *
             * Cada tarjeta muestra:
             * - Imagen o placeholder.
             * - Categoría, si existe.
             * - Título.
             * - Botón para ver el material gratis.
             */
            html += `
                <div class="col-12 col-sm-6 col-lg-4">
                    <article class="gratis-card">

                        <div class="gratis-card-img">
                            ${
                                imagen
                                    ? `<img src="${imagen}" alt="${escapeHtml(recurso.titulo)}">`
                                    : `<div class="gratis-card-placeholder">
                                            <i class="bi bi-file-earmark-text"></i>
                                       </div>`
                            }

                            ${
                                recurso.categoria_nombre
                                    ? `<span class="gratis-card-badge">${escapeHtml(recurso.categoria_nombre)}</span>`
                                    : ""
                            }
                        </div>

                        <div class="gratis-card-body">
                            <h3>${escapeHtml(recurso.titulo)}</h3>

                            <a
                                href="${PUBLIC_URL}ver_recursos_gratis.php?id=${recurso.id}"
                                target="_blank"
                                rel="noopener"
                                class="btn gratis-card-btn">
                                Ver material gratis
                            </a>
                        </div>

                    </article>
                </div>
            `;
        });

        /**
         * Insertamos todas las tarjetas generadas dentro del grid.
         */
        grid.innerHTML = html;
    }


    /**
     * Evento del filtro "Todos".
     *
     * Cuando el usuario marca "Todos":
     * - Se desmarcan las categorías concretas.
     * - Se actualizan los chips.
     * - Se recargan los recursos.
     */
    if (filtroTodos) {
        filtroTodos.addEventListener("change", function () {
            if (this.checked) {
                checksCategorias.forEach(chk => chk.checked = false);
            }

            actualizarChips();
            cargarRecursosGratis();
        });
    }


    /**
     * Eventos de cambio para cada checkbox de categoría.
     *
     * Cuando el usuario selecciona una categoría:
     * - Se comprueba si hay alguna categoría activa.
     * - Si hay alguna activa, se desmarca "Todos".
     * - Si no hay ninguna activa, se marca "Todos".
     * - Se actualizan los chips.
     * - Se recargan los recursos.
     */
    checksCategorias.forEach(chk => {
        chk.addEventListener("change", function () {
            const algunaCategoria = obtenerCategoriasSeleccionadas().length > 0;

            if (filtroTodos) {
                filtroTodos.checked = !algunaCategoria;
            }

            actualizarChips();
            cargarRecursosGratis();
        });
    });


    /**
     * Evento del buscador.
     *
     * Cada vez que el usuario escribe una tecla, se vuelve a cargar
     * el listado de recursos aplicando el texto de búsqueda.
     */
    if (buscador) {
        buscador.addEventListener("keyup", function () {
            cargarRecursosGratis();
        });
    }

});


/**
 * Escapa caracteres especiales para evitar inyección de HTML.
 *
 * Esta función convierte caracteres peligrosos en entidades HTML.
 *
 * Por ejemplo:
 *
 * < se convierte en &lt;
 * > se convierte en &gt;
 * " se convierte en &quot;
 * ' se convierte en &#039;
 * & se convierte en &amp;
 *
 * Se utiliza cuando se insertan datos dinámicos dentro de innerHTML.
 *
 * @param {*} text
 * Texto que se quiere escapar.
 *
 * @returns {string}
 * Texto seguro para insertar dentro de HTML.
 */
function escapeHtml(text) {
    return String(text ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;");
}