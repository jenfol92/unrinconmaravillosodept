<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<div class="container py-5">

    <!-- MIGAS -->
    <div class="mb-4 text-muted small">
        Tienda > <?= htmlspecialchars($producto['nivel_nombre']) ?> >
        <?= htmlspecialchars($producto['categoria_nombre']) ?>
    </div>

    <div class="row g-4 g-lg-5 producto-detalle-top">

        <!-- IZQUIERDA -->
        <div class="col-lg-6">

            <!-- IMAGEN PRINCIPAL -->
            <div class="card border-0 shadow rounded-4 p-3 mb-3 producto-img-box">

                <img
                    src="/UNRINCONDEPT/static/images/img/<?= htmlspecialchars($producto['imagen']) ?>"
                    class="img-fluid rounded-4"
                    alt="<?= htmlspecialchars($producto['titulo']) ?>">

            </div>

            <!-- MINIATURAS -->
            <div class="d-flex gap-3">

                <img
                    src="/UNRINCONDEPT/static/images/img/<?= htmlspecialchars($producto['imagen']) ?>"
                    class="rounded-3 border"
                    width="90">

                <!-- VIDEO MINIATURA -->
                <div class="border rounded-3 p-2 d-flex align-items-center justify-content-center"
                    style="width:90px; height:90px; cursor:pointer;">
                    ▶ Video
                </div>

            </div>

        </div>

        <!-- DERECHA -->
        <div class="col-lg-6 producto-info-col">

            <!-- BADGES -->
            <div class="d-flex gap-2 flex-wrap mb-3">
                <span class="badge bg-info text-dark rounded-pill px-3 py-2">
                    <?= htmlspecialchars($producto['nivel_nombre']) ?>
                </span>

                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                    <?= htmlspecialchars($producto['categoria_nombre']) ?>
                </span>

                <span class="badge bg-danger rounded-pill px-3 py-2">
                    PDF Digital
                </span>
            </div>

            <!-- TITULO -->
            <h1 class="fw-bold mb-3">
                <?= htmlspecialchars($producto['titulo']) ?>
            </h1>

            <!-- ESTRELLAS -->
            <div class="text-warning fs-5 mb-3">
                ★★★★☆
                <span class="text-muted fs-6">(48 valoraciones)</span>
            </div>

            <!-- PRECIO BOX -->
<div class="card border-0 shadow rounded-4 p-4 mb-4 price-box">

    <h2 class="fw-bold text-success mb-3">
        <?= number_format($producto['precio'], 2) ?> €
    </h2>

    <button class="btn btn-success w-100 mb-3 py-3 rounded-3">
        🛒 Añadir al carrito
    </button>

    <button class="btn btn-outline-dark w-100 py-3 rounded-3 mb-4">
        Comprar ahora
    </button>

    <!-- CHECKS -->
    <div class="small text-muted">

        <div class="d-flex align-items-center mb-2">
            <span class="me-2 text-success">✔</span>
            Archivo PDF listo para imprimir
        </div>

        <div class="d-flex align-items-center mb-2">
            <span class="me-2 text-success">✔</span>
            Licencia para uso en aula o en casa
        </div>

        <div class="d-flex align-items-center">
            <span class="me-2 text-success">✔</span>
            Actualizaciones futuras incluidas
        </div>

    </div>

</div>
            <!-- CREADO POR -->
            <div class="autor-box">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <strong>Creado por Raquel</strong><br>
                        <small class="text-muted">
                            Maestra de Primaria con +10 años de experiencia
                        </small>
                    </div>

                    <a
                        href="https://instagram.com/"
                        target="_blank"
                        class="text-decoration-none fw-bold">
                        Ver perfil
                    </a>

                </div>

            </div>

        </div>

    </div>

    <!-- ABAJO -->
<div class="row mt-5 g-5 align-items-start">

    <!-- IZQUIERDA: DESCRIPCIÓN + RESEÑAS -->
    <div class="col-lg-7">

        <div class="descripcion-box mb-4">
            <h3 class="title_section mb-3">Sobre este recurso</h3>

            <p>
                <?= nl2br(htmlspecialchars($producto['descripcion'])) ?>
            </p>
        </div>

        <section class="resenas-section">

            <h3 class="section-title">
                Reseñas sobre este producto
            </h3>

            <div class="resenas-scroll">

                <?php if (!empty($resenas)) : ?>

                    <?php foreach ($resenas as $r) : ?>

                        <div class="resena-card">

                            <div class="stars mb-2">
                                <?= str_repeat("★", $r['puntuacion']) ?>
                                <?= str_repeat("☆", 5 - $r['puntuacion']) ?>
                            </div>

                            <p class="comentario">
                                "<?= htmlspecialchars($r['comentario']) ?>"
                            </p>

                            <div class="autor">
                                — <?= htmlspecialchars($r['usuario_nombre'] ?? 'Usuario') ?>
                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else : ?>

                    <p class="text-muted">Este producto aún no tiene reseñas.</p>

                <?php endif; ?>

            </div>

        </section>

    </div>

    <!-- DERECHA: CONTENIDO + AYUDA -->
    <div class="col-lg-5">

        <h3 class="title_section mb-3">
            Contenido
        </h3>

        <div class="card border-0 shadow-sm rounded-4 p-4 contenido-box">

            <?php
            $items = preg_split('/\.\s+|\n+/', $producto['contenido']);

            foreach ($items as $item):
                $item = trim($item);
                if (!empty($item)):
            ?>

                <div class="d-flex align-items-start mb-3">
                    <span class="me-2 text-success">✔</span>
                    <span><?= htmlspecialchars($item) ?></span>
                </div>

            <?php
                endif;
            endforeach;
            ?>

        </div>

        <div class="help-box mt-4">
            <h5>¿Necesitas ayuda?</h5>

            <p>
                Si tienes alguna duda sobre este material,
                escríbeme y te responderé en menos de 24h.
            </p>

            <a href="https://www.instagram.com/tu_instagram/"
               target="_blank"
               class="btn w-100">
                Contactar con la autora
            </a>
        </div>

    </div>

</div>

<!-- RELACIONADOS -->
<div class="mt-5">

   <div class="relacionados-header d-flex justify-content-between align-items-center mb-4">

        <h3 class="title_section">
            También te puede interesar
        </h3>

        <a href="tienda.php" class="text-decoration-none fw-bold">
            Ver toda la colección →
        </a>

    </div>

    <div class="row g-4">

        <?php foreach ($relacionados as $index => $item): ?>

            <?php
                $colores = ['card-pink', 'card-green', 'card-yellow'];
                $colorCard = $colores[$index % count($colores)];
            ?>

            <div class="col-12 col-sm-6 col-md-3">

                <div class="resource-card <?= $colorCard ?>">

                    <span class="card-badge">
                        <?= htmlspecialchars($item['categoria_nombre']) ?>
                    </span>

                    <img
                        src="/UNRINCONDEPT/static/images/img/<?= htmlspecialchars($item['imagen']) ?>"
                        alt="<?= htmlspecialchars($item['titulo']) ?>">

                    <div class="card-body">

                        <h3>
                            <?= htmlspecialchars($item['titulo']) ?>
                        </h3>

                        <div class="card-footer-custom">

                            <span class="price">
                                <?= number_format($item['precio'], 2) ?> €
                            </span>

                            <a href="detalle.php?id=<?= $item['id'] ?>"
                               class="btn-carrito">
                                🛒
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>