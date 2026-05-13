<?php

require_once __DIR__ . '/../../templates/header.php';

?>

<section class="hero_seccion">

    <div class="container">

        <div class="row align-items-center">

            <!-- TEXTO — izquierda en desktop, arriba en móvil col-12 d-flex flex-column-->

            <div class="col-md-6 d-flex- fex-column" style="min-height: 420px;">

                <div class="d-none d-md-inline-block">

                    <span class="hero_etiqueta"> <i class="bi bi-stars me-1"></i> Nuevos recursos cada semana</span>

                </div>

                <h1 class="hero_titulo">¡ Bienvenidos a <span>Un mundo maravilloso </span>de PT!.</h1>

                <div class="d-none d-md-inline-block">

                    <p> Aquí encontrarás propuestas reales, materiales pictografiados, adaptaciones curriculares, herramientas prácticas...Todo con el objetivo de facilitar el aprendizaje del alumnado con necesidades educativas. </p>

                </div>

                <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-3 flex-wrap mt-auto pb-4">

                    <a href="tienda.php" class="mi-btn-acceder">Explorar Recursos -></a>

                    <a href="recursos_gratuitos.php" class="hero_link">Material Gratuito</a>

                </div>

            </div>

            <!--Imagen-->

            <div class="col-md-6 d-none d-md-block">

                <div class="hero_deco">

                    <div class="hero_fondo_amarillo"></div>

                    <div class="hero_fondo_turq">

                        <img src="../static/images/logo/logo.jpeg" class="imagen_hero">

                    </div>

                </div>



            </div>

        </div>

    </div>

</section>


<section class="aqui_encontraras">
    <div class="container py-5">
            <div class="row">
            <div class="col-12 d-md-none aquiencontraras">
                <h1 class="title_section">Aquí encontrarás</h1>
                <p> Propuestas reales, materiales pictografiados, adaptaciones curriculares, herramientas prácticas...Todo con el objetivo de facilitar el aprendizaje del alumnado con necesidades educativas.</p>
            </div>
        </div>

        <div class="container my-5">
            <h2>Recursos Destacados</h2>

            <div id="resourceCarousel" class="carousel slide d-md-none" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php foreach ($destacados as $index => $recurso): ?>
                        <button type="button" data-bs-target="#resourceCarousel" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>" aria-current="true"></button>
                    <?php endforeach; ?>
                </div>

                <div class="carousel-inner">
                    <?php
                    $colores = ['card-pink', 'card-green', 'card-yellow'];
                    foreach ($destacados as $index => $recurso):
                        $colorClase = $colores[$index % count($colores)];
                    ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <div class="resource-card <?= $colorClase ?>">
                                <div class="card-badge"><?= htmlspecialchars($recurso['categoria']) ?></div>
                                <img src="../../static/images/img/<?= htmlspecialchars($recurso['imagen']) ?>" class="d-block w-100">
                                <div class="card-body">
                                    <h3><?= htmlspecialchars($recurso['titulo']) ?></h3>
                                    <div class="card-footer-custom">
                                        <span class="price"><?= htmlspecialchars($recurso['precio']) ?>€</span>
                                        <button class="btn-carrito"><i class="bi bi-bag"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#resourceCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#resourceCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            </div>

            <div class="row d-none d-md-flex">
                <?php foreach ($destacados as $index => $recurso):
                    $colorClase = $colores[$index % count($colores)];
                ?>
                    <div class="col-md-4 mb-4">
                        <div class="resource-card <?= $colorClase ?>">
                            <div class="card-badge"><?= htmlspecialchars($recurso['categoria']) ?></div>
                            <img src="../../static/images/img/<?= htmlspecialchars($recurso['imagen']) ?>" class="img-fluid">
                            <div class="card-body">
                                <h3><?= htmlspecialchars($recurso['titulo']) ?></h3>
                                <div class="card-footer-custom">
                                    <span class="price"><?= htmlspecialchars($recurso['precio']) ?>€</span>
                                    <button class="btn-carrito"><i class="bi bi-bag"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>


</section>

<section class="sobre_mi">

    <div class="container">

        <div class="row">

            <div class="col-12">

                <div class="col-12 d-flex justify-content-end">

                    <span class="etiqueta_blanca"> <i class="bi bi-stars me-1"></i> Sobre mi</span>

                </div>
                <div class="col-md-12 d-flex justify-content-center">
                    <p> "Soy Raquel, maestra especialista en Pedagogía Terapéutica y apasionada por la educación inclusiva.
                        Creo en una educación manipulativa, visual y funcional, donde todo el alumnado tenga su espacio, su ritmo y su manera de aprender.

                        Este espacio nace de la experiencia, la vocación y las ganas de seguir creciendo y compartiendo."
                        ¡Gracias por estar aquí!</p>
                </div>
            </div>

        </div>

    </div>

</section>


<?php

require_once __DIR__ . '/../../templates/footer.php';

?>