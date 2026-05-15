<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<main class="recursos-gratis-page">

    <section class="recursos-gratis-hero">

        <div class="container text-center">

            <span class="gratis-pill">
                <i class="bi bi-stars"></i>
                Recursos 100% gratuitos
            </span>

            <h1>
                Biblioteca Mágica de <span>Recursos Educativos</span>
            </h1>

            <p>
                Descarga fichas, juegos y actividades diseñadas para transformar el aprendizaje
                en una aventura inolvidable.
            </p>

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

    <section class="recursos-gratis-content">

        <div class="container">

            <div class="gratis-filtros-wrapper">

                <div class="gratis-filtros">

                    <span class="filtro-label">
                        <i class="bi bi-funnel"></i>
                        Filtrar por:
                    </span>

                    <label class="gratis-chip active" data-todos="1">
                        <input type="checkbox" id="filtroTodosGratis" checked>
                        Todos
                    </label>

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

                <div id="contadorRecursosGratis" class="gratis-contador">
                    Mostrando <?= count($recursosGratuitos) ?> recursos encontrados
                </div>

            </div>

            <div id="recursosGratisGrid" class="row g-4">

                <?php foreach ($recursosGratuitos as $recurso): ?>

                    <div class="col-12 col-sm-6 col-lg-4">

                        <article class="gratis-card">

                            <div class="gratis-card-img">

                                <?php if (!empty($recurso['imagen'])): ?>
                                    <img
                                        src="/UNRINCONDEPT/static/images/img/<?= htmlspecialchars($recurso['imagen']) ?>"
                                        alt="<?= htmlspecialchars($recurso['titulo']) ?>">
                                <?php else: ?>
                                    <div class="gratis-card-placeholder">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($recurso['categoria_nombre'])): ?>
                                    <span class="gratis-card-badge">
                                        <?= htmlspecialchars($recurso['categoria_nombre']) ?>
                                    </span>
                                <?php endif; ?>

                            </div>

                            <div class="gratis-card-body">

                                <h3>
                                    <?= htmlspecialchars($recurso['titulo']) ?>
                                </h3>

                                <a
                                    href="/UNRINCONDEPT/public/ver_recursos_gratis.php?id=<?= (int)$recurso['id'] ?>"
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

            <div id="recursosGratisEmpty" class="panel-empty mt-4 d-none">
                No hay recursos gratuitos con esos filtros.
            </div>

        </div>

    </section>

</main>

<script src="/UNRINCONDEPT/static/js/recursos_gratuitos.js"></script>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>