<?php

/**
 * VISTA: RECURSOS GRATUITOS
 * ---------------------------------------------------------
 * Esta vista muestra la página pública de recursos gratuitos.
 *
 * Funcionalidad:
 * - Muestra un hero principal con título, descripción y buscador.
 * - Muestra filtros por categorías gratuitas.
 * - Lista los recursos gratuitos disponibles.
 * - Permite buscar y filtrar recursos mediante JavaScript.
 * - Cada recurso enlaza a ver_recursos_gratis.php para registrar/mostrar el material.
 *
 * Variables que debe recibir esta vista desde el controller:
 *
 * $categoriasGratuitas:
 * - Array con las categorías disponibles para filtrar recursos gratuitos.
 *
 * $recursosGratuitos:
 * - Array con los recursos gratuitos iniciales que se pintan al cargar la página.
 *
 * Rutas:
 * - BASE_URL se usa para archivos estáticos: imágenes, JS, CSS, etc.
 * - PUBLIC_URL se usa para enlaces a páginas públicas PHP.
 */


/**
 * Cargamos el header común de la web.
 *
 * Normalmente aquí se incluyen:
 * - apertura de HTML
 * - head
 * - navbar
 * - carga de CSS
 * - definición global de window.BASE_URL y window.PUBLIC_URL si lo tienes ahí
 */
require_once __DIR__ . '/../../templates/header.php';

?>

<main class="recursos-gratis-page">

    <!-- =====================================================
         HERO DE RECURSOS GRATUITOS
         -----------------------------------------------------
         Bloque superior de la página.
         Contiene:
         - etiqueta visual
         - título principal
         - descripción
         - buscador
    ====================================================== -->
    <section class="recursos-gratis-hero">

        <div class="container text-center">

            <!-- Etiqueta superior decorativa -->
            <span class="gratis-pill">
                <i class="bi bi-stars"></i>
                Recursos 100% gratuitos
            </span>

            <!-- Título principal de la página -->
            <h1>
                Biblioteca Mágica de <span>Recursos Educativos</span>
            </h1>

            <!-- Texto introductorio -->
            <p>
                Descarga fichas, juegos y actividades diseñadas para transformar el aprendizaje
                en una aventura inolvidable.
            </p>

            <!-- =====================================================
                 BUSCADOR DE RECURSOS GRATUITOS
                 -----------------------------------------------------
                 Este input será usado por recursos_gratuitos.js
                 para filtrar recursos por texto.
            ====================================================== -->
            <div class="gratis-search mx-auto">
                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="buscarRecursoGratis"
                    placeholder="Busca por tema..."
                    class="form-control">
            </div>

        </div>

    </section>


    <!-- =====================================================
         CONTENIDO PRINCIPAL DE RECURSOS GRATUITOS
         -----------------------------------------------------
         Incluye:
         - filtros
         - contador
         - grid de recursos
         - mensaje vacío
    ====================================================== -->
    <section class="recursos-gratis-content">

        <div class="container">

            <!-- =====================================================
                 FILTROS Y CONTADOR
            ====================================================== -->
            <div class="gratis-filtros-wrapper">

                <div class="gratis-filtros">

                    <!-- Texto fijo del bloque de filtros -->
                    <span class="filtro-label">
                        <i class="bi bi-funnel"></i>
                        Filtrar por:
                    </span>

                    <!-- =====================================================
                         FILTRO: TODOS
                         -----------------------------------------------------
                         Por defecto aparece activo y marcado.

                         recursos_gratuitos.js usa:
                         - id="filtroTodosGratis"
                         - class="gratis-chip"
                    ====================================================== -->
                    <label class="gratis-chip active" data-todos="1">
                        <input type="checkbox" id="filtroTodosGratis" checked>
                        Todos
                    </label>

                    <!-- =====================================================
                         FILTROS POR CATEGORÍA
                         -----------------------------------------------------
                         Se generan dinámicamente desde $categoriasGratuitas.

                         Cada checkbox tiene:
                         - class="filtro-categoria-gratis"
                         - value con el ID de la categoría

                         recursos_gratuitos.js leerá estos valores para
                         enviar los filtros al endpoint AJAX.
                    ====================================================== -->
                    <?php foreach ($categoriasGratuitas as $cat): ?>

                        <label class="gratis-chip">

                            <input
                                type="checkbox"
                                class="filtro-categoria-gratis"
                                value="<?= (int)$cat['id'] ?>">

                            <?= htmlspecialchars($cat['nombre']) ?>

                        </label>

                    <?php endforeach; ?>

                </div>

                <!-- =====================================================
                     CONTADOR DE RECURSOS
                     -----------------------------------------------------
                     Muestra inicialmente el total de recursos cargados
                     desde PHP.

                     Después, recursos_gratuitos.js puede actualizarlo
                     cuando se apliquen filtros o búsqueda.
                ====================================================== -->
                <div id="contadorRecursosGratis" class="gratis-contador">
                    Mostrando <?= count($recursosGratuitos) ?> recursos encontrados
                </div>

            </div>


            <!-- =====================================================
                 GRID DE RECURSOS GRATUITOS
                 -----------------------------------------------------
                 Aquí se pintan inicialmente los recursos enviados por PHP.

                 Después, recursos_gratuitos.js puede vaciar y volver
                 a rellenar este contenedor con resultados filtrados.
            ====================================================== -->
            <div id="recursosGratisGrid" class="row g-4">

                <?php foreach ($recursosGratuitos as $recurso): ?>

                    <div class="col-12 col-sm-6 col-lg-4">

                        <!-- Tarjeta individual de recurso gratuito -->
                        <article class="gratis-card">

                            <!-- =====================================================
                                 IMAGEN / PLACEHOLDER
                                 -----------------------------------------------------
                                 Si el recurso tiene imagen, se muestra.
                                 Si no tiene imagen, se muestra un placeholder con icono.
                            ====================================================== -->
                            <div class="gratis-card-img">

                                <?php if (!empty($recurso['imagen'])): ?>

                                    <img
                                        src="<?= BASE_URL ?>static/images/img/<?= htmlspecialchars($recurso['imagen']) ?>"
                                        alt="<?= htmlspecialchars($recurso['titulo']) ?>">

                                <?php else: ?>

                                    <div class="gratis-card-placeholder">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>

                                <?php endif; ?>


                                <!-- =====================================================
                                     CATEGORÍA DEL RECURSO
                                     -----------------------------------------------------
                                     Solo se muestra si el recurso tiene categoría asociada.
                                ====================================================== -->
                                <?php if (!empty($recurso['categoria_nombre'])): ?>

                                    <span class="gratis-card-badge">
                                        <?= htmlspecialchars($recurso['categoria_nombre']) ?>
                                    </span>

                                <?php endif; ?>

                            </div>


                            <!-- =====================================================
                                 CUERPO DE LA TARJETA
                            ====================================================== -->
                            <div class="gratis-card-body">

                                <!-- Título del recurso gratuito -->
                                <h3>
                                    <?= htmlspecialchars($recurso['titulo']) ?>
                                </h3>

                                <!-- =====================================================
                                     BOTÓN VER MATERIAL GRATIS
                                     -----------------------------------------------------
                                     Se usa PUBLIC_URL porque ver_recursos_gratis.php
                                     es un archivo público.

                                     LOCAL:
                                     http://localhost/UNRINCONDEPT/public/ver_recursos_gratis.php?id=...

                                     PRODUCCIÓN:
                                     https://unrinconmaravillosodept.es/ver_recursos_gratis.php?id=...
                                ====================================================== -->
                                <a
                                    href="<?= PUBLIC_URL ?>ver_recursos_gratis.php?id=<?= (int)$recurso['id'] ?>"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn gratis-card-btn">
                                    Ver material gratis
                                </a>

                            </div>

                        </article>

                    </div>

                <?php endforeach; ?>

            </div>


            <!-- =====================================================
                 MENSAJE CUANDO NO HAY RESULTADOS
                 -----------------------------------------------------
                 Por defecto está oculto con d-none.

                 recursos_gratuitos.js lo mostrará cuando no haya
                 recursos que coincidan con los filtros aplicados.
            ====================================================== -->
            <div id="recursosGratisEmpty" class="panel-empty mt-4 d-none">
                No hay recursos gratuitos con esos filtros.
            </div>

        </div>

    </section>

</main>


<!-- =====================================================
     JAVASCRIPT DE RECURSOS GRATUITOS
     -----------------------------------------------------
     Se carga desde static/js usando BASE_URL porque es un
     archivo estático.

     Este JS se encarga de:
     - escuchar el buscador
     - leer categorías seleccionadas
     - llamar al endpoint AJAX
     - pintar los recursos filtrados
     - actualizar el contador
     - mostrar/ocultar el mensaje vacío
====================================================== -->
<script src="<?= BASE_URL ?>static/js/recursos_gratuitos.js"></script>

<?php

/**
 * Cargamos el footer común de la web.
 *
 * Normalmente contiene:
 * - cierre de estructura HTML
 * - scripts globales
 * - Bootstrap JS
 * - footer visual
 */
require_once __DIR__ . '/../../templates/footer.php';

?>