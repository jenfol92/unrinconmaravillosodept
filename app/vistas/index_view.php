<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<main class="home-page">

    <!-- HERO -->
    <section class="home-hero">

        <div class="container">

            <div class="row align-items-center g-5">

                <!-- TEXTO -->
                <div class="col-12 col-lg-6">

                    <span class="home-pill">
                        <i class="bi bi-stars me-1"></i>
                        Nuevos recursos cada semana
                    </span>

                    <h1 class="home-title">
                        ¡Bienvenidos a <span>Un mundo maravilloso</span> de PT!
                    </h1>

                    <p class="home-subtitle">
                        Aquí encontrarás propuestas reales, materiales pictografiados,
                        adaptaciones curriculares y herramientas prácticas para facilitar
                        el aprendizaje del alumnado con necesidades educativas.
                    </p>

                    <div class="home-actions">

                        <a href="/UNRINCONDEPT/public/tienda.php" class="home-btn-primary">
                            Explorar recursos
                            <i class="bi bi-arrow-right"></i>
                        </a>

                        <a href="/UNRINCONDEPT/public/recursos_gratuitos.php" class="home-btn-link">
                            Ver material gratuito
                        </a>

                    </div>

                </div>

                <!-- IMAGEN -->
                <div class="col-12 col-lg-6">

                    <div class="home-hero-image-wrap">

                        <div class="home-hero-bg-one"></div>
                        <div class="home-hero-bg-two"></div>

                        <img
                            src="/UNRINCONDEPT/static/images/logo/logo.jpeg"
                            alt="Un rincón maravilloso de PT"
                            class="home-hero-image">

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- RECURSOS DESTACADOS -->
<section class="home-section home-destacados">

    <div class="container">

        <div class="home-section-heading">

            <span class="home-small-label">
                <i class="bi bi-fire"></i>
                Más visitados
            </span>

            <h2>Recursos destacados</h2>

            <p>
                Los materiales que más interés están generando entre los usuarios.
            </p>

        </div>

        <?php if (empty($destacados)): ?>

            <div class="home-empty">
                Todavía no hay recursos destacados.
            </div>

        <?php else: ?>

            <?php
            $colores = ['home-card-pink', 'home-card-green', 'home-card-yellow'];
            ?>

            <!-- CARRUSEL SOLO MÓVIL -->
            <div id="homeDestacadosCarousel" class="carousel slide d-md-none" data-bs-ride="carousel">

                <div class="carousel-indicators">
                    <?php foreach ($destacados as $index => $recurso): ?>
                        <button 
                            type="button" 
                            data-bs-target="#homeDestacadosCarousel" 
                            data-bs-slide-to="<?= $index ?>" 
                            class="<?= $index === 0 ? 'active' : '' ?>"
                            aria-current="<?= $index === 0 ? 'true' : 'false' ?>">
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="carousel-inner">

                    <?php foreach ($destacados as $index => $recurso): ?>

                        <?php
                        $colorClase = $colores[$index % count($colores)];

                        $categoria = $recurso['categoria_nombre']
                            ?? $recurso['categoria']
                            ?? 'Recurso';

                        $precio = number_format((float)$recurso['precio'], 2);
                        ?>

                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">

                            <article
                                class="home-resource-card <?= $colorClase ?>"
                                onclick="window.location.href='/UNRINCONDEPT/public/detalle.php?id=<?= (int)$recurso['id'] ?>'">

                                <span class="home-card-badge">
                                    <?= htmlspecialchars($categoria) ?>
                                </span>

                                <div class="home-card-img-wrap">
                                    <img
                                        src="/UNRINCONDEPT/static/images/img/<?= htmlspecialchars($recurso['imagen']) ?>"
                                        alt="<?= htmlspecialchars($recurso['titulo']) ?>">
                                </div>

                                <div class="home-card-body">

                                    <h3>
                                        <?= htmlspecialchars($recurso['titulo']) ?>
                                    </h3>

                                    <div class="home-card-meta">

                                        <span class="home-card-price">
                                            <?= $precio ?> €
                                        </span>

                                        <button
                                            type="button"
                                            class="home-card-cart"
                                            title="Añadir al carrito"
                                            onclick="event.stopPropagation(); gestionarSesion(<?= (int)$recurso['id'] ?>, 'add_carrito')">
                                            <i class="bi bi-bag"></i>
                                        </button>

                                    </div>

                                </div>

                            </article>

                        </div>

                    <?php endforeach; ?>

                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#homeDestacadosCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>

                <button class="carousel-control-next" type="button" data-bs-target="#homeDestacadosCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>

            </div>


            <!-- GRID TABLET / ESCRITORIO -->
            <div class="row g-4 d-none d-md-flex">

                <?php foreach ($destacados as $index => $recurso): ?>

                    <?php
                    $colorClase = $colores[$index % count($colores)];

                    $categoria = $recurso['categoria_nombre']
                        ?? $recurso['categoria']
                        ?? 'Recurso';

                    $precio = number_format((float)$recurso['precio'], 2);
                    ?>

                    <div class="col-md-6 col-lg-4">

                        <article
                            class="home-resource-card <?= $colorClase ?>"
                            onclick="window.location.href='/UNRINCONDEPT/public/detalle.php?id=<?= (int)$recurso['id'] ?>'">

                            <span class="home-card-badge">
                                <?= htmlspecialchars($categoria) ?>
                            </span>

                            <div class="home-card-img-wrap">
                                <img
                                    src="/UNRINCONDEPT/static/images/img/<?= htmlspecialchars($recurso['imagen']) ?>"
                                    alt="<?= htmlspecialchars($recurso['titulo']) ?>">
                            </div>

                            <div class="home-card-body">

                                <h3>
                                    <?= htmlspecialchars($recurso['titulo']) ?>
                                </h3>

                                <div class="home-card-meta">

                                    <span class="home-card-price">
                                        <?= $precio ?> €
                                    </span>

                                    <button
                                        type="button"
                                        class="home-card-cart"
                                        title="Añadir al carrito"
                                        onclick="event.stopPropagation(); gestionarSesion(<?= (int)$recurso['id'] ?>, 'add_carrito')">
                                        <i class="bi bi-bag"></i>
                                    </button>

                                </div>

                            </div>

                        </article>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</section>

    <!-- SOBRE MÍ -->
<section class="home-about">

    <div class="container">

        <div class="home-about-card">

            <div class="row align-items-center g-4">

                <div class="col-12 col-lg-4">

                    <div class="home-about-avatar">
                        <span>R</span>
                    </div>

                    <div class="home-about-label">
                        <i class="bi bi-stars me-1"></i>
                        Sobre mí
                    </div>

                </div>

                <div class="col-12 col-lg-8">

                    <h2>
                        Recursos creados desde la experiencia real del aula
                    </h2>

                    <p>
                        Soy Raquel, maestra especialista en Pedagogía Terapéutica y apasionada
                        por la educación inclusiva.
                    </p>

                    <p>
                        Creo en una educación manipulativa, visual y funcional, donde todo el
                        alumnado tenga su espacio, su ritmo y su manera de aprender.
                    </p>

                    <div class="home-about-features">

                        <div>
                            <i class="bi bi-heart"></i>
                            Material visual
                        </div>

                        <div>
                            <i class="bi bi-puzzle"></i>
                            Aprendizaje manipulativo
                        </div>

                        <div>
                            <i class="bi bi-stars"></i>
                            Inclusión real
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

   <!-- RECURSOS GRATUITOS -->
<section class="home-section home-gratuitos">

    <div class="container">

        <div class="home-section-heading">

            <span class="home-small-label">
                <i class="bi bi-gift"></i>
                Material gratuito
            </span>

            <h2>Recursos gratuitos</h2>

            <p>
                Descarga materiales gratuitos listos para usar en el aula, en casa o en sesiones de apoyo.
            </p>

        </div>

        <?php if (empty($gratuitosHome)): ?>

            <div class="home-empty">
                Todavía no hay recursos gratuitos disponibles.
            </div>

        <?php else: ?>

            <!-- CARRUSEL SOLO MÓVIL -->
            <div id="homeGratuitosCarousel" class="carousel slide d-md-none" data-bs-ride="carousel">

                <div class="carousel-indicators">
                    <?php foreach ($gratuitosHome as $index => $gratis): ?>
                        <button
                            type="button"
                            data-bs-target="#homeGratuitosCarousel"
                            data-bs-slide-to="<?= $index ?>"
                            class="<?= $index === 0 ? 'active' : '' ?>"
                            aria-current="<?= $index === 0 ? 'true' : 'false' ?>">
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="carousel-inner">

                    <?php foreach ($gratuitosHome as $index => $gratis): ?>

                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">

                            <article class="home-free-card">

                                <?php if (!empty($gratis['imagen'])): ?>

                                    <div class="home-free-img-wrap">
                                        <img
                                            src="/UNRINCONDEPT/static/images/gratuitos/<?= htmlspecialchars($gratis['imagen']) ?>"
                                            alt="<?= htmlspecialchars($gratis['titulo']) ?>">
                                    </div>

                                <?php else: ?>

                                    <div class="home-free-placeholder">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </div>

                                <?php endif; ?>

                                <div class="home-free-body">

                                    <span class="home-free-badge">
                                        <?= htmlspecialchars($gratis['categoria_nombre'] ?? 'Gratuito') ?>
                                    </span>

                                    <h3>
                                        <?= htmlspecialchars($gratis['titulo']) ?>
                                    </h3>

                                    <a
                                        href="<?= htmlspecialchars($gratis['url_drive']) ?>"
                                        target="_blank"
                                        class="home-free-btn">
                                        Ver recurso
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>

                                </div>

                            </article>

                        </div>

                    <?php endforeach; ?>

                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#homeGratuitosCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>

                <button class="carousel-control-next" type="button" data-bs-target="#homeGratuitosCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>

            </div>


            <!-- GRID TABLET / ESCRITORIO -->
            <div class="row g-4 d-none d-md-flex">

                <?php foreach ($gratuitosHome as $gratis): ?>

                    <div class="col-md-6 col-lg-4">

                        <article class="home-free-card">

                            <?php if (!empty($gratis['imagen'])): ?>

                                <div class="home-free-img-wrap">
                                    <img
                                        src="/UNRINCONDEPT/static/images/img/<?= htmlspecialchars($gratis['imagen']) ?>"
                                        alt="<?= htmlspecialchars($gratis['titulo']) ?>">
                                </div>

                            <?php else: ?>

                                <div class="home-free-placeholder">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </div>

                            <?php endif; ?>

                            <div class="home-free-body">

                                <span class="home-free-badge">
                                    <?= htmlspecialchars($gratis['categoria_nombre'] ?? 'Gratuito') ?>
                                </span>

                                <h3>
                                    <?= htmlspecialchars($gratis['titulo']) ?>
                                </h3>

                                <a
                                    href="<?= htmlspecialchars($gratis['url_drive']) ?>"
                                    target="_blank"
                                    class="home-free-btn">
                                    Ver recurso
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>

                            </div>

                        </article>

                    </div>

                <?php endforeach; ?>

            </div>

            <div class="text-center mt-4">

                <a href="/UNRINCONDEPT/public/recursos_gratuitos.php" class="home-btn-outline">
                    Ver todos los recursos gratuitos
                    <i class="bi bi-arrow-right"></i>
                </a>

            </div>

        <?php endif; ?>

    </div>

</section>

</main>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>