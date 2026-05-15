document.addEventListener("DOMContentLoaded", function () {

    const grid = document.getElementById("recursosGratisGrid");
    const contador = document.getElementById("contadorRecursosGratis");
    const empty = document.getElementById("recursosGratisEmpty");
    const buscador = document.getElementById("buscarRecursoGratis");
    const filtroTodos = document.getElementById("filtroTodosGratis");
    const checksCategorias = document.querySelectorAll(".filtro-categoria-gratis");

    if (!grid) return;

    function obtenerCategoriasSeleccionadas() {
        return Array.from(checksCategorias)
            .filter(chk => chk.checked)
            .map(chk => chk.value);
    }

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

    function cargarRecursosGratis() {
        const categorias = obtenerCategoriasSeleccionadas();
        const busqueda = buscador ? buscador.value : "";

        const url = `/UNRINCONDEPT/public/ajax_recursos_gratuitos.php?categorias=${encodeURIComponent(JSON.stringify(categorias))}&busqueda=${encodeURIComponent(busqueda)}`;

        grid.innerHTML = `
            <div class="col-12 text-center py-5 text-muted">
                Cargando recursos...
            </div>
        `;

        fetch(url)
            .then(res => res.json())
            .then(data => {

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

                pintarRecursos(data.recursos);

                if (contador) {
                    contador.textContent = `Mostrando ${data.total} recursos encontrados`;
                }
            })
            .catch(error => {
                console.error("Error cargando recursos gratuitos:", error);
            });
    }

    function pintarRecursos(recursos) {
        if (!recursos || recursos.length === 0) {
            grid.innerHTML = "";
            if (empty) empty.classList.remove("d-none");
            return;
        }

        if (empty) empty.classList.add("d-none");

        let html = "";

        recursos.forEach(recurso => {
            const imagen = recurso.imagen
                ? `/UNRINCONDEPT/static/images/img/${escapeHtml(recurso.imagen)}`
                : "";

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
                                href="/UNRINCONDEPT/public/ver_recursos_gratis.php?id=${recurso.id}"
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

        grid.innerHTML = html;
    }

    if (filtroTodos) {
        filtroTodos.addEventListener("change", function () {
            if (this.checked) {
                checksCategorias.forEach(chk => chk.checked = false);
            }

            actualizarChips();
            cargarRecursosGratis();
        });
    }

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

    if (buscador) {
        buscador.addEventListener("keyup", function () {
            cargarRecursosGratis();
        });
    }

});
function escapeHtml(text) {
    return String(text ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;");
}