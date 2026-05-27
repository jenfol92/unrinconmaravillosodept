<?php

/**
 * Vista: index_view.php
 * ---------------------------------------------------------
 * Muestra la página principal de la web.
 *
 * Esta vista recibe datos desde ProductoController::home().
 *
 * Variables recibidas:
 *
 * - $categorias:
 *   Categorías generales de productos.
 *
 * - $destacados:
 *   Recursos destacados de pago que se muestran en la home.
 *
 * - $gratuitosHome:
 *   Recursos gratuitos destacados que se muestran en la home.
 *
 * Funcionalidades principales:
 *
 * - Mostrar el bloque hero de bienvenida.
 * - Mostrar acceso a tienda y recursos gratuitos.
 * - Mostrar recursos destacados.
 * - Mostrar carrusel móvil de recursos destacados.
 * - Mostrar grid de recursos destacados en tablet/escritorio.
 * - Mostrar sección informativa "Sobre mí".
 * - Mostrar recursos gratuitos.
 * - Mostrar carrusel móvil de recursos gratuitos.
 * - Mostrar grid de recursos gratuitos en tablet/escritorio.
 *
 * Archivos relacionados:
 *
 * - ProductoController.php:
 *   Prepara las variables $categorias, $destacados y $gratuitosHome.
 *
 * - Producto.php:
 *   Obtiene recursos destacados desde la base de datos.
 *
 * - recursosGratuitos.php:
 *   Obtiene recursos gratuitos para la home.
 *
 * Seguridad:
 *
 * - Se utiliza htmlspecialchars() para imprimir datos dinámicos.
 * - Los IDs se convierten a entero con (int) antes de usarse en URLs o JS.
 * - Los precios se convierten a float antes de formatearse.
 */
require_once __DIR__ . '/../../templates/header.php';
?>

<!--
     PÁGINA DE INICIO
     Contenedor principal de la home de la web.
-->
<main class="home-page">

    <!-- 
         HERO PRINCIPAL
         Bloque superior de bienvenida.
         Incluye texto de presentación, botones principales
         y logo/imagen de la marca.
    -->
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

                    <!-- Botones principales de navegación -->
                    <div class="home-actions">

                        <a href="<?= BASE_URL ?>public/tienda.php" class="home-btn-primary">
                            Explorar recursos
                            <i class="bi bi-arrow-right"></i>
                        </a>

                        <a href="<?= BASE_URL ?>public/recursos_gratuitos.php" class="home-btn-link">
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
                            src="<?= BASE_URL ?>static/images/logo/logo.jpeg"
                            alt="Un rincón maravilloso de PT"
                            class="home-hero-image">

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- 
         RECURSOS DESTACADOS
         Muestra productos de pago destacados.
         
         En móvil se muestran en carrusel Bootstrap.
         En tablet y escritorio se muestran en grid.
   -->
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

            <!-- Si no hay recursos destacados, se muestra mensaje informativo -->
            <?php if (empty($destacados)): ?>

                <div class="home-empty">
                    Todavía no hay recursos destacados.
                </div>

            <?php else: ?>

                <?php
                /*
                    Array de clases visuales para alternar colores de tarjetas.
                    Se aplica una clase distinta según la posición del recurso.
                */
                $colores = ['home-card-pink', 'home-card-green', 'home-card-yellow'];
                ?>

                <!--
                     CARRUSEL DESTACADOS - SOLO MÓVIL
                     Bootstrap muestra una tarjeta por slide en pantallas pequeñas.
              -->
                <div id="homeDestacadosCarousel" class="carousel slide d-md-none" data-bs-ride="carousel">

                    <!-- Indicadores del carrusel -->
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
                            /*
                                Preparamos datos visuales del recurso destacado.
                                No se cambia la lógica, solo se documenta:
                                - colorClase: clase CSS alterna.
                                - categoria: nombre de categoría o valor por defecto.
                                - precio: precio formateado a dos decimales.
                            */
                            $colorClase = $colores[$index % count($colores)];

                            $categoria = $recurso['categoria_nombre']
                                ?? $recurso['categoria']
                                ?? 'Recurso';

                            $precio = number_format((float)$recurso['precio'], 2);
                            ?>

                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">

                                <!--
                                    Tarjeta de recurso destacado.
                                    Al hacer clic en la tarjeta, se abre el detalle del producto.
                                -->
                                <article
                                    class="home-resource-card <?= $colorClase ?>"
                                    onclick="window.location.href='/UNRINCONDEPT/public/detalle.php?id=<?= (int)$recurso['id'] ?>'">

                                    <span class="home-card-badge">
                                        <?= htmlspecialchars($categoria) ?>
                                    </span>

                                    <div class="home-card-img-wrap">
                                        <img
                                            src="<?= BASE_URL ?>static/images/img/<?= htmlspecialchars($recurso['imagen']) ?>"
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

                                            <!--
                                                Botón para añadir al carrito.
                                            -->
                                            <button
                                                type="button"
                                                class="home-card-cart btn-agregar-carrito"
                                                data-id="<?= (int)$recurso['id'] ?>"
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

                    <!-- Controles anterior/siguiente del carrusel móvil -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#homeDestacadosCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>

                    <button class="carousel-control-next" type="button" data-bs-target="#homeDestacadosCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>

                </div>


                <!-- 
                     GRID DESTACADOS - TABLET / ESCRITORIO
                     En pantallas medianas y grandes los recursos destacados
                     se muestran como tarjetas en columnas.
                 -->
                <div class="row g-4 d-none d-md-flex">

                    <?php foreach ($destacados as $index => $recurso): ?>

                        <?php
                        /*
                            Se repite la preparación de datos para la versión grid.
                        */
                        $colorClase = $colores[$index % count($colores)];

                        $categoria = $recurso['categoria_nombre']
                            ?? $recurso['categoria']
                            ?? 'Recurso';

                        $precio = number_format((float)$recurso['precio'], 2);
                        ?>

                        <div class="col-md-6 col-lg-4">

                            <article
                                class="home-resource-card <?= $colorClase ?>"
                                onclick="window.location.href='<?= BASE_URL ?>public/detalle.php?id=<?= (int)$recurso['id'] ?>'">

                                <span class="home-card-badge">
                                    <?= htmlspecialchars($categoria) ?>
                                </span>

                                <div class="home-card-img-wrap">
                                    <img
                                        src="<?= BASE_URL ?>static/images/img/<?= htmlspecialchars($recurso['imagen']) ?>"
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

    <!-- 
         SOBRE MÍ
         Sección estática de presentación de la creadora del proyecto.
    -->
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

                        <!-- Características principales de la propuesta educativa -->
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

    <!-- 
         RECURSOS GRATUITOS
         Muestra recursos gratuitos destacados en la home.
         
         En móvil se muestran en carrusel Bootstrap.
         En tablet y escritorio se muestran en grid.
-->
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

            <!-- Mensaje si no existen recursos gratuitos para mostrar -->
            <?php if (empty($gratuitosHome)): ?>

                <div class="home-empty">
                    Todavía no hay recursos gratuitos disponibles.
                </div>

            <?php else: ?>

                <!-- 
                     CARRUSEL GRATUITOS - SOLO MÓVIL
                     Muestra los recursos gratuitos destacados en formato
                     carrusel para pantallas pequeñas.
            -->
                <div id="homeGratuitosCarousel" class="carousel slide d-md-none" data-bs-ride="carousel">

                    <!-- Indicadores del carrusel -->
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

                                    <!-- Imagen del recurso gratuito o icono alternativo -->
                                    <?php if (!empty($gratis['imagen'])): ?>

                                        <div class="home-free-img-wrap">
                                            <img
                                                src="<?=BASE_URL?>static/images/img/<?= htmlspecialchars($gratis['imagen']) ?>"
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

                    <!-- Controles anterior/siguiente del carrusel móvil -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#homeGratuitosCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>

                    <button class="carousel-control-next" type="button" data-bs-target="#homeGratuitosCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>

                </div>


                <!-- 
                     GRID GRATUITOS - TABLET / ESCRITORIO
                     Muestra los recursos gratuitos en tarjetas.
                 -->
                <div class="row g-4 d-none d-md-flex">

                    <?php foreach ($gratuitosHome as $gratis): ?>

                        <div class="col-md-6 col-lg-4">

                            <article class="home-free-card">

                                <?php if (!empty($gratis['imagen'])): ?>

                                    <div class="home-free-img-wrap">
                                        <img
                                            src="<?= BASE_URL ?>static/images/img/<?= htmlspecialchars($gratis['imagen']) ?>"
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

                <!-- Enlace a la página completa de recursos gratuitos -->
                <div class="text-center mt-4">

                    <a href="<?= BASE_URL ?>public/recursos_gratuitos.php" class="home-btn-outline">
                        Ver todos los recursos gratuitos
                        <i class="bi bi-arrow-right"></i>
                    </a>

                </div>

            <?php endif; ?>

        </div>

    </section>

</main>
<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
</script>
<script src="<?= BASE_URL ?>static/js/tienda.js"></script>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>