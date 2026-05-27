<?php require_once __DIR__ . '/../../templates/header.php';
/**
 * Vista: producto_detalle.php
 * ---------------------------------------------------------
 * Muestra la ficha completa de un producto/recurso de la tienda.
 *
 * Esta vista se carga desde ProductoController::detalle().
 *
 * Variables recibidas desde el controlador:
 *
 * - $producto:
 *   Datos principales del producto seleccionado.
 *
 * - $resenas:
 *   Reseñas asociadas al producto.
 *
 * - $relacionados:
 *   Productos relacionados por categoría.
 *
 * Funcionalidades principales:
 *
 * - Mostrar imagen principal del producto.
 * - Mostrar nivel, categoría, título, precio y características.
 * - Permitir añadir el producto al carrito.
 * - Permitir comprar el producto directamente.
 * - Mostrar descripción y contenido del recurso.
 * - Mostrar reseñas de usuarios.
 * - Mostrar productos relacionados.
 * - Permitir contactar con la autora.
 * - Si el usuario no está logueado, redirige a contacto público.
 * - Si el usuario está logueado, abre un modal de consulta.
 *
 * Archivos relacionados:
 *
 * - ProductoController.php:
 *   Carga producto, reseñas y relacionados.
 *
 * - Producto.php:
 *   Obtiene producto por ID, reseñas y productos relacionados.
 *
 * - tienda.js:
 *   Gestiona acciones de sesión/carrito.
 *
 * - detalle_producto.js:
 *   Gestiona el formulario de contacto con la autora desde el modal.
 *
 * Seguridad:
 *
 * - Se utiliza htmlspecialchars() al imprimir datos dinámicos.
 * - Los IDs se convierten o imprimen de forma controlada.
 * - El contenido del producto se separa en elementos visibles.
 */
?>
<div class="container py-5">

    <!-- MIGAS 
     Muestra una ruta visual para ubicar al usuario dentro
         de la tienda según nivel y categoría del producto.
-->
    <div class="mb-4 text-muted small">
        Tienda > <?= htmlspecialchars($producto['nivel_nombre']) ?> >
        <?= htmlspecialchars($producto['categoria_nombre']) ?>
    </div>
    <!--
    BLOQUE SUPERIOR DEL DETALLE
         Divide la ficha en dos columnas:
         - Izquierda: imagen principal y miniaturas.
         - Derecha: información comercial del producto
-->
    <div class="row g-4 g-lg-5 producto-detalle-top">

    <!-- IZQUIERDA -->
<div class="col-lg-6">

    <!-- 
        IMAGEN / VÍDEO PRINCIPAL
        -----------------------------------------------------
        Por defecto se muestra la imagen.
        El vídeo existe en el HTML, pero empieza oculto con d-none.
    -->
    <div class="card border-0 shadow rounded-4 p-3 mb-3 producto-img-box">

        <!-- IMAGEN PRINCIPAL -->
        <img
            id="productoImagenPrincipal"
            src="<?= BASE_URL ?>static/images/img/<?= htmlspecialchars($producto['imagen']) ?>"
            class="img-fluid rounded-4 producto-media-img"
            alt="<?= htmlspecialchars($producto['titulo']) ?>">

        <!-- VIDEO PRINCIPAL -->
        <?php if (!empty($producto['video_url'])): ?>

            <video
                id="productoVideoPrincipal"
                class="rounded-4 producto-media-video d-none"
                controls
                preload="metadata"
                playsinline>

                <source
                    src="<?= BASE_URL ?>static/videos/<?= htmlspecialchars($producto['video_url']) ?>"
                    type="video/mp4">

                Tu navegador no soporta la reproducción de vídeo.
            </video>

        <?php endif; ?>

    </div>

    <!-- 
        MINIATURAS
        -----------------------------------------------------
        La miniatura de imagen muestra la imagen.
        La miniatura de vídeo muestra el vídeo.
    -->
    <div class="d-flex gap-3">

        <!-- MINIATURA IMAGEN -->
        
        <img
            id="thumbImagenProducto"
            src="<?= BASE_URL ?>static/images/img/<?= htmlspecialchars($producto['imagen']) ?>"
            class="rounded-3 border producto-thumb active"
            width="90"
            style="cursor:pointer;"
            onclick="mostrarImagenProducto()">


        <!-- MINIATURA VIDEO -->
        <?php if (!empty($producto['video_url'])): ?>

            <div
                id="thumbVideoProducto"
                class="border rounded-3 p-2 d-flex align-items-center justify-content-center producto-thumb-video"
                style="width:90px; height:90px; cursor:pointer;"
                onclick="mostrarVideoProducto()">

                ▶ Video

            </div>

        <?php endif; ?>

    </div>

</div>

        <!-- DERECHA -->
        <div class="col-lg-6 producto-info-col">

            <!-- BADGES 
              Muestran información rápida:
                 - Nivel educativo.
                 - Categoría.
                 - Formato del recurso.-->
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

            <!-- ESTRELLAS
              Actualmente se muestra una valoración fija.
             Las reseñas reales se muestran más abajo.-->
            <div class="text-warning fs-5 mb-3">
                ★★★★☆
                <span class="text-muted fs-6">(48 valoraciones)</span>
            </div>

            <!--Guardar id del producto para mostrar cookie de ultimos productos visitados-->
            <?php if (!empty($producto['id'])): ?>
                <div id="productoDetalleActual"
                    data-producto-id="<?= (int)$producto['id'] ?>">
                </div>
            <?php endif; ?>
            
            <!-- 
             CAJA DE PRECIO Y COMPRA
                 Muestra el precio del producto y los botones principales:
                 - Añadir al carrito.
                 - Comprar ahora.-->
            <div class="card border-0 shadow rounded-4 p-4 mb-4 price-box">

                <h2 class="fw-bold text-success mb-3">
                    <?= number_format($producto['precio'], 2) ?> €
                </h2>

                <button
                    type="button"
                    class="btn btn-outline-success"
                    onclick="gestionarSesion(<?= $producto['id'] ?>, 'add_carrito')">
                    <i class="bi bi-cart"></i>
                    Añadir al carrito
                </button>

                <button
                    type="button"
                    class="btn btn-primary"
                    onclick="comprarAhora(<?= $producto['id'] ?>)">
                    Comprar ahora
                </button>

                <!--  CHECKS INFORMATIVOS
            Refuerzan las características principales del producto. -->
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
            <!-- CREADO POR. BLOQUE AUTORA
                 Presenta a la creadora del recurso y enlaza a Instagram. -->
            <div class="autor-box">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <strong>Creado por Raquel</strong><br>
                        <small class="text-muted">
                            Maestra de PT con +10 años de experiencia
                        </small>
                    </div>

                    <a
                        href="https://www.instagram.com/unrinconmaravillosodept"
                        target="_blank"
                        class="text-decoration-none fw-bold">
                        Ver perfil
                    </a>

                </div>

            </div>

        </div>

    </div>

    <!--  BLOQUE INFERIOR DEL DETALLE
      Divide la información extendida en:
         - Izquierda: descripción y reseñas.
         - Derecha: contenido del recurso y ayuda. -->
    <div class="row mt-5 g-5 align-items-start">

        <!-- IZQUIERDA: DESCRIPCIÓN + RESEÑAS -->
        <div class="col-lg-7">
            <!-- 
                 DESCRIPCIÓN DEL PRODUCTO
                 Muestra la descripción larga introducida desde admin.
                 nl2br() respeta saltos de línea.
            -->
            <div class="descripcion-box mb-4">
                <h3 class="title_section mb-3">Sobre este recurso</h3>

                <p>
                    <?= nl2br(htmlspecialchars($producto['descripcion'])) ?>
                </p>
            </div>
            <!-- 
                 RESEÑAS DEL PRODUCTO
                 Muestra las opiniones de usuarios si existen.
         -->
            <section class="resenas-section">

                <h3 class="section-title">
                    Reseñas sobre este producto
                </h3>

                <div class="resenas-scroll">

                    <?php if (!empty($resenas)) : ?>

                        <?php foreach ($resenas as $r) : ?>

                            <div class="resena-card">
                                <!-- Puntuación visual mediante estrellas -->
                                <div class="stars mb-2">
                                    <?= str_repeat("★", $r['puntuacion']) ?>
                                    <?= str_repeat("☆", 5 - $r['puntuacion']) ?>
                                </div>
                                <!-- Comentario de la reseña -->
                                <p class="comentario">
                                    "<?= htmlspecialchars($r['comentario']) ?>"
                                </p>
                                <!-- Autor de la reseña -->
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
            <!-- 
                 CONTENIDO DEL RECURSO
                 Divide el campo contenido en varios ítems usando puntos
                 o saltos de línea como separadores.
        -->
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
            <!-- 
                 BLOQUE DE AYUDA / CONTACTO CON AUTORA
                 Si el usuario no está logueado, se le envía a contacto.
                 Si está logueado, se abre un modal para enviar consulta.
           -->
            <div class="help-box mt-4">
                <h5>¿Necesitas ayuda?</h5>

                <p>
                    Si tienes alguna duda sobre este material,
                    escríbeme y te responderé en menos de 24h.
                </p>

                <?php if (!isset($_SESSION['usuario_id'])): ?>

                    <a
                        href="<?= BASE_URL ?>public/contacto.php?producto_id=<?= $producto['id'] ?>"
                        class="btn btn-outline-primary">
                        <i class="bi bi-chat-dots"></i>
                        Contactar con la autora
                    </a>

                <?php else: ?>

                    <button
                        type="button"
                        class="btn btn-outline-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#modalChatProducto">
                        <i class="bi bi-chat-dots"></i>
                        Contactar con la autora
                    </button>

                <?php endif; ?>
            </div>

        </div>

    </div>

    <!--  PRODUCTOS RELACIONADOS
     -----------------------------------------------------
     Esta sección muestra productos de la misma categoría
     o productos que pueden interesar al usuario.

     Funcionalidades:
     - Cada tarjeta completa es clicable y lleva al detalle del producto.
     - El botón del carrito añade el producto al carrito sin abrir el detalle.
     - El botón de favoritos guarda el producto como favorito sin abrir el detalle.
     - Se alternan clases visuales para dar fondos distintos a cada tarjeta -->
    <div class="mt-5">
        <!--
 CABECERA DE PRODUCTOS RELACIONADOS
         Muestra el título de la sección y un enlace a la tienda.
-->
        <div class="relacionados-header d-flex justify-content-between align-items-center mb-4">

            <h3 class="title_section">
                También te puede interesar
            </h3>

            <a href="tienda.php" class="text-decoration-none fw-bold">
                Ver toda la colección →
            </a>

        </div>
        <!--
        GRID DE PRODUCTOS RELACIONADOS
         Se muestran en columnas responsive:
         - 1 columna en móvil.
         - 2 columnas en pantallas pequeñas.
         - 4 columnas en escritorio.
-->
        <div class="row g-4">

            <?php foreach ($relacionados as $index => $item): ?>

                <?php
                /*
                Array de clases visuales para alternar colores de tarjetas.

                Se usa el índice del foreach para ir aplicando una clase distinta
                según la posición del producto:

                Producto 1 → card-pink
                Producto 2 → card-green
                Producto 3 → card-yellow
                Producto 4 → vuelve a card-pink

                Esto reproduce la estética usada en el index.
            */
                $colores = ['card-pink', 'card-green', 'card-yellow'];
                /*
                Calculamos la clase visual que corresponde a esta tarjeta.
                El operador % permite repetir el patrón de colores.
            */
                $colorCard = $colores[$index % count($colores)];
                ?>

                <div class="col-12 col-sm-6 col-md-3">
                    <!--
          TARJETA DE PRODUCTO RELACIONADO
                     Toda la tarjeta es clicable.
                     Al hacer clic en cualquier parte de la tarjeta,
                     se redirige al detalle del producto.
        -->
                    <article
                        class="resource-card producto-relacionado-card <?= $colorCard ?>"
                        onclick="window.location.href='<?= BASE_URL ?>public/detalle.php?id=<?= (int)$item['id'] ?>'"
                        style="cursor: pointer;">

                        <!-- Categoría del producto -->
                        <span class="card-badge">
                            <?= htmlspecialchars($item['categoria_nombre']) ?>
                        </span>

                        <!-- 
                         IMAGEN DEL PRODUCTO
                         Se muestra dentro de un contenedor propio para mantener
                         tamaño, proporción y estética uniforme.
                -->
                        <div class="producto-relacionado-img-box">
                            <img
                                src="<?= BASE_URL ?>static/images/img/<?= htmlspecialchars($item['imagen']) ?>"
                                alt="<?= htmlspecialchars($item['titulo']) ?>">
                        </div>

                        <!-- Cuerpo de la tarjeta -->
                        <div class="card-body producto-relacionado-body">

                            <!-- Título del producto -->
                            <h3>
                                <?= htmlspecialchars($item['titulo']) ?>
                            </h3>

                            <!-- 
                             PIE DE TARJETA
                             Contiene:
                             - precio;
                             - botón añadir al carrito;
                             - botón añadir a favoritos.
                  -->
                            <div class="card-footer-custom producto-relacionado-footer">

                                <!-- Precio del producto -->
                                <span class="price">
                                    <?= number_format((float)$item['precio'], 2) ?> €
                                </span>

                                <div class="d-flex gap-2 align-items-center">

                                    <!-- 
                                     BOTÓN AÑADIR AL CARRITO
                                     event.stopPropagation() evita que se active también
                                     el onclick de la tarjeta completa.

                                     gestionarSesion() es la función de tienda.js que
                                     añade el producto a la sesión del carrito.
                        -->
                                    <button
                                        type="button"
                                        class="btn-carrito"
                                        onclick="event.stopPropagation(); gestionarSesion(<?= (int)$item['id'] ?>, 'add_carrito')"
                                        title="Añadir al carrito">
                                        <i class="bi bi-cart"></i>
                                    </button>

                                    <!-- 
                                     BOTÓN FAVORITOS
                                     Si el usuario está logueado, puede guardar el producto
                                     como favorito.

                                     Si no está logueado, se le envía al login.
                            -->
                                    <?php if (isset($_SESSION['usuario_id'])): ?>

                                        <button
                                            type="button"
                                            class="btn-carrito btn-favorito-relacionado"
                                            onclick="event.stopPropagation(); toggleFavorito(<?= (int)$item['id'] ?>, this)"
                                            title="Añadir a favoritos">
                                            <i class="bi bi-heart"></i>
                                        </button>

                                    <?php else: ?>

                                        <a
                                            href="<?= BASE_URL ?>public/login.php"
                                            class="btn-carrito btn-favorito-relacionado"
                                            onclick="event.stopPropagation();"
                                            title="Inicia sesión para guardar favoritos">
                                            <i class="bi bi-heart"></i>
                                        </a>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                    </article>

                </div>

            <?php endforeach; ?>

        </div>

    </div>
    <!-- 
         MODAL: CHAT CON LA AUTORA
         Disponible para usuarios logueados.
         Permite enviar una consulta relacionada con el producto.
  -->
    <div class="modal fade" id="modalChatProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content chat-autora-modal">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Chat con la autora
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Introducción con el producto consultado -->
                    <div class="chat-box-intro mb-3">
                        <strong><?= htmlspecialchars($producto['titulo']) ?></strong>
                        <p class="mb-0">
                            Escribe tu duda sobre este material. La autora podrá responderte desde tu panel.
                        </p>
                    </div>
                    <!-- Formulario AJAX de consulta sobre producto -->
                    <form id="formChatProducto">

                        <input type="hidden" name="producto_id" value="<?= htmlspecialchars($producto['id']) ?>">

                        <input
                            type="hidden"
                            name="asunto"
                            value="Consulta sobre: <?= htmlspecialchars($producto['titulo'], ENT_QUOTES) ?>">

                        <textarea
                            name="mensaje"
                            class="form-control"
                            rows="4"
                            placeholder="Escribe tu mensaje..."
                            required></textarea>

                        <button type="submit" class="btn btn-primary w-100 mt-3">
                            Enviar mensaje
                        </button>

                    </form>
                    <!-- Respuesta generada por detalle_producto.js -->
                    <div id="respuestaChatProducto" class="mt-3"></div>

                </div>

            </div>
        </div>
    </div>
    <!-- SCRIPTS DE LA VISTA
         tienda.js:
         - gestiona acciones de carrito/sesión.
         
         detalle_producto.js:
         - gestiona el envío del formulario de consulta a la autora
        -->
         <script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
</script>
    <script src="<?= BASE_URL ?>static/js/tienda.js"></script>
    <script src="<?= BASE_URL ?>static/js/carrito.js"></script>
    <script src="<?= BASE_URL ?>static/js/detalle_producto.js"></script>
    <script src="<?= BASE_URL ?>static/js/guardar_producto_visitado_recientemente.js"></script>
</div>

    <?php require_once __DIR__ . '/../../templates/footer.php'; ?>