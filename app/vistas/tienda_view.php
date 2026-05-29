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

            <!-- Buscador visual del hero  utiliza tienda.js-->
            <div class="tienda-search mx-auto">
                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="busqueda"
                    class="form-control"
                    placeholder="Buscar por recurso...">
            </div>

        </div>

    </section>


    <!-- CONTENIDO TIENDA -->
    <section class="tienda-content">

        <div class="container">

            <div class="row g-4 align-items-start">

                <!-- FILTROS LATERALES -->
                <div class="col-12 col-lg-3">
                    <div class="tienda-sidebar-sticky">

                        <aside class="tienda-filter-card">

                            <h5>
                                <i class="bi bi-funnel"></i>
                                Filtrar por
                            </h5>




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

                        <!-- 
     TIENDA - ÚLTIMOS PRODUCTOS VISTOS
     -----------------------------------------------------
     Se rellena mediante AJAX leyendo la cookie
     productos_recientes.
 -->

                        <div class="tienda-recientes-widget d-none" id="tiendaRecientesWidget">

                            <h5>
                                <i class="bi bi-clock-history"></i>
                                Últimos vistos
                            </h5>

                            <div id="tiendaRecientesLista">
                                <!-- Se rellena desde tienda.js -->
                            </div>

                        </div>
                    </div>
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

                                <?php if (!isset($_SESSION['usuario_id'])): ?>

                                    <!--
        Usuario NO logueado:
        Se envía al formulario público de contacto.
    -->
                                    <a
                                        href="<?= PUBLIC_URL ?>contacto.php"
                                        class="btn tienda-sugerencia-btn">
                                        Sugerir un recurso
                                        <i class="bi bi-arrow-right"></i>
                                    </a>

                                <?php else: ?>

                                    <!--
        Usuario logueado:
        Se abre un modal para enviar la sugerencia sin salir de la tienda.
    -->
                                    <button
                                        type="button"
                                        class="btn tienda-sugerencia-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalSugerenciaTienda">
                                        Sugerir un recurso
                                        <i class="bi bi-chat-heart"></i>
                                    </button>

                                <?php endif; ?>

                            </div>

                            <div class="tienda-sugerencia-img d-none d-md-block">
                                <img
                                    src="<?= BASE_URL ?>static/images/logo/logo.jpeg"
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
    <!-- =====================================================
     MODAL: SUGERIR RECURSO DESDE TIENDA
     -----------------------------------------------------
     Este modal solo se abre para usuarios logueados.

     Si el usuario no está logueado, el botón del widget
     lo envía a contacto.php.

     El formulario se envía mediante AJAX a:
     /public/ajax_sugerencia.php

     El endpoint guarda la sugerencia en la tabla correspondiente
     usando el modelo Soporte.
===================================================== -->
    <div class="modal fade" id="modalSugerenciaTienda" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content chat-autora-modal">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Sugerir un recurso
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="chat-box-intro mb-3">

                        <strong>¿Buscas algo específico?</strong>

                        <p class="mb-0">
                            Cuéntanos qué material necesitas y tendremos tu propuesta en cuenta
                            para próximos recursos.
                        </p>

                    </div>

                    <form id="formSugerenciaTienda">

                        <div class="mb-3">

                            <label class="form-label">
                                Tu sugerencia
                            </label>

                            <textarea
                                name="mensaje"
                                class="form-control"
                                rows="5"
                                placeholder="Ej: Me gustaría un recurso sobre comprensión lectora para 2º de Primaria..."
                                required></textarea>

                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Enviar sugerencia
                        </button>

                    </form>

                    <div id="respuestaSugerenciaTienda" class="mt-3"></div>

                </div>

            </div>

        </div>

    </div>

</main>



<script src="<?= BASE_URL ?>static/js/carrito.js?v=20260529-5"></script>
<script src="<?= BASE_URL ?>static/js/tienda.js?v=20260529-5"></script>
<script src="<?= BASE_URL ?>static/js/favoritos.js?v=20260529-5"></script>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>