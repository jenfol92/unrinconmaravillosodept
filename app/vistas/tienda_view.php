<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../templates/header.php';
?>

<main class="tienda-page">

    <!-- HERO TIENDA -->
    <section class="tienda-hero">

        <div class="container text-center">

            <span class="tienda-pill">
                <i class="bi bi-bag-heart"></i>
                Recursos educativos listos para imprimir
            </span>

            <h1>
                Tienda Mágica de <span>Recursos Educativos</span>
            </h1>

            <p>
                Encuentra fichas, juegos, actividades y materiales pensados para hacer
                del aprendizaje una experiencia más bonita, práctica y divertida.
            </p>

            <!-- Buscador visual del hero -->
            <div class="tienda-search mx-auto">
                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="busquedaHero"
                    class="form-control"
                    placeholder="Busca por tema: sumas, sílabas, lectoescritura...">
            </div>

        </div>

    </section>


    <!-- CONTENIDO TIENDA -->
    <section class="tienda-content">

        <div class="container">

            <div class="row g-4 align-items-start">

                <!-- FILTROS LATERALES -->
                <div class="col-12 col-lg-3">

                    <aside class="tienda-filter-card">

                        <h5>
                            <i class="bi bi-funnel"></i>
                            Filtrar por
                        </h5>

                        <!-- BUSCADOR REAL QUE USA tienda.js -->
                        <div class="mb-4">
                            <label class="form-label">Buscar recurso</label>
                            <input
                                type="text"
                                id="busqueda"
                                class="form-control"
                                placeholder="Buscar recurso...">
                        </div>

                        <!-- CATEGORÍAS -->
                        <div class="filter-group">

                            <h6>Categorías</h6>

                            <?php foreach ($categorias as $cat) { ?>
                                <label class="filter-check">
                                    <input
                                        type="checkbox"
                                        class="filtro-categoria"
                                        value="<?= $cat['id'] ?>">
                                    <span><?= htmlspecialchars($cat['nombre']) ?></span>
                                </label>
                            <?php } ?>

                        </div>

                        <!-- NIVELES -->
                        <div class="filter-group mt-4">

                            <h6>Nivel educativo</h6>

                            <?php foreach ($niveles as $nivel) { ?>
                                <label class="filter-check">
                                    <input
                                        type="checkbox"
                                        class="filtro-nivel"
                                        value="<?= $nivel['id'] ?>">
                                    <span><?= htmlspecialchars($nivel['nombre']) ?></span>
                                </label>
                            <?php } ?>

                        </div>

                    </aside>

                </div>


                <!-- ZONA DERECHA -->
                <div class="col-12 col-lg-9">

                    <!-- WIDGET SUGERENCIA -->
                    <section class="tienda-sugerencia-widget">

                        <div class="tienda-sugerencia-content">

                            <div class="tienda-sugerencia-text">

                                <h3>
                                    ¿Buscas algo específico?
                                </h3>

                                <p>
                                    Si no encuentras el material que necesitas, escríbenos y tendremos
                                    tu propuesta en cuenta para próximos recursos.
                                </p>

                                <a
                                    href="/UNRINCONDEPT/public/contacto.php"
                                    class="btn tienda-sugerencia-btn">
                                    Sugerir un recurso
                                    <i class="bi bi-arrow-right"></i>
                                </a>

                            </div>

                            <div class="tienda-sugerencia-img d-none d-md-block">
                                <img
                                    src="/UNRINCONDEPT/static/images/logo/logo.jpeg"
                                    alt="Sugerir un recurso">
                            </div>
                         
                        </div>

                    </section>


                    <!-- LISTADO DE PRODUCTOS -->
                    <div id="contenedor-productos" class="row g-4 tienda-productos-grid">
                    </div>

                    <!-- PAGINACIÓN -->
                    <div id="paginacion" class="tienda-paginacion mt-4 text-center">
                    </div>

                </div>

            </div>

        </div>

    </section>

</main>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const busquedaHero = document.getElementById("busquedaHero");
        const busquedaReal = document.getElementById("busqueda");

        if (busquedaHero && busquedaReal) {
            busquedaHero.addEventListener("input", function() {
                busquedaReal.value = this.value;
                busquedaReal.dispatchEvent(new Event("input"));
                busquedaReal.dispatchEvent(new Event("keyup"));
            });
        }
    });
</script>

<script src="/UNRINCONDEPT/static/js/tienda.js"></script>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>