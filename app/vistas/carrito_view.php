<?php
require_once __DIR__ . "/../../templates/header.php";
?>

<main class="carrito-page">

    <div class="container">

        <div class="carrito-header">
            <h1>
                Tu Carrito <span>Mágico</span>
            </h1>

            <p>
                Revisa tus recursos antes de finalizar la compra.
            </p>
        </div>

        <?php if (empty($productos_carrito)): ?>

            <div class="carrito-empty">

                <h2>Tu carrito está vacío</h2>

                <p>
                    Todavía no has añadido ningún recurso a tu carrito.
                </p>

                <a href="/UNRINCONDEPT/public/tienda.php" class="btn">
                    Explorar recursos
                </a>

            </div>

        <?php else: ?>

            <div class="carrito-layout">

                <!-- COLUMNA IZQUIERDA: PRODUCTOS -->
                <section class="carrito-main">

                    <div class="carrito-list-header">

                        <h2>
                            <?= count($productos_carrito) ?>
                            <?= count($productos_carrito) === 1 ? 'producto seleccionado' : 'productos seleccionados' ?>
                        </h2>

                        <a href="/UNRINCONDEPT/public/tienda.php">
                            Seguir comprando
                        </a>

                    </div>

                    <?php foreach ($productos_carrito as $p): ?>

                        <?php
                        $imagen = $p['imagen'] ?? '';
                        $titulo = $p['titulo'] ?? 'Recurso';
                        $categoria = $p['categoria_nombre'] ?? $p['categoria'] ?? 'Recurso educativo';
                        $descripcion = $p['descripcion'] ?? 'Material educativo listo para descargar.';
                        $cantidad = (int)($p['cantidad'] ?? 1);
                        $precio = (float)($p['precio'] ?? 0);
                        $lineaTotal = $precio * $cantidad;
                        ?>

                        <article class="carrito-item">

                            <div class="carrito-item-img">
                                <?php if (!empty($imagen)): ?>
                                    <img
                                        src="/UNRINCONDEPT/static/images/img/<?= htmlspecialchars($imagen) ?>"
                                        alt="<?= htmlspecialchars($titulo) ?>">
                                <?php else: ?>
                                    <div class="carrito-img-placeholder">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="carrito-item-info">

                                <span class="carrito-badge">
                                    <?= htmlspecialchars($categoria) ?>
                                </span>

                                <h3>
                                    <?= htmlspecialchars($titulo) ?>
                                </h3>

                                <p>
                                    <?= htmlspecialchars($descripcion) ?>
                                </p>

                            </div>

                            <div class="carrito-cantidad">

                                <button
                                    type="button"
                                    class="btn-carrito-accion"
                                    data-id="<?= (int)$p['id'] ?>"
                                    data-accion="restar_carrito"
                                    title="Restar unidad">
                                    −
                                </button>

                                <span>
                                    <?= $cantidad ?>
                                </span>

                                <button
                                    type="button"
                                    class="btn-carrito-accion"
                                    data-id="<?= (int)$p['id'] ?>"
                                    data-accion="add_carrito"
                                    title="Añadir unidad">
                                    +
                                </button>

                            </div>

                            <div class="carrito-item-price">

                                <strong>
                                    <?= number_format($lineaTotal, 2) ?>€
                                </strong>

                                <button
                                    type="button"
                                    class="btn-carrito-accion"
                                    data-id="<?= (int)$p['id'] ?>"
                                    data-accion="eliminar_carrito"
                                    title="Eliminar recurso">
                                    <i class="bi bi-trash"></i>
                                    Eliminar
                                </button>

                            </div>

                        </article>

                    <?php endforeach; ?>


                    <!-- BLOQUE DE CONFIANZA -->
                    <div class="carrito-benefits">

                        <div class="carrito-benefit">

                            <div class="icon">
                                <i class="bi bi-cloud-arrow-down"></i>
                            </div>

                            <div>
                                <h4>Descarga instantánea</h4>
                                <p>
                                    Accede a tus recursos desde tu perfil tras finalizar la compra.
                                </p>
                            </div>

                        </div>

                        <div class="carrito-benefit">

                            <div class="icon">
                                <i class="bi bi-patch-check"></i>
                            </div>

                            <div>
                                <h4>Material revisado</h4>
                                <p>
                                    Recursos pensados para el aula, el apoyo educativo y el aprendizaje visual.
                                </p>
                            </div>

                        </div>

                    </div>

                </section>


                <!-- COLUMNA DERECHA: RESUMEN -->
                <aside class="carrito-side">

                    <div class="carrito-summary">

                        <div class="carrito-summary-body">

                            <h2>Resumen del pedido</h2>

                            <div class="carrito-summary-row">
                                <span>Subtotal</span>
                                <strong><?= number_format($subtotal, 2) ?>€</strong>
                            </div>

                            <div class="carrito-summary-row">
                                <span>Gastos de gestión</span>
                                <strong>Gratis</strong>
                            </div>

                            <div class="carrito-summary-row">
                                <span>Impuestos</span>
                                <strong>Incluidos</strong>
                            </div>

                            <div class="carrito-summary-total">
                                <span>Total</span>
                                <strong><?= number_format($total, 2) ?>€</strong>
                            </div>

                            <form action="/UNRINCONDEPT/public/crear_checkout_stripe.php" method="POST">
                                <button
                                    type="submit"
                                    class="carrito-checkout-btn">
                                    Finalizar compra
                                </button>
                            </form>

                            <p class="carrito-summary-note">
                                Al hacer clic en “Finalizar compra” accederás al proceso de pago seguro.
                            </p>

                        </div>

                        <div class="carrito-summary-footer">

                            <p>
                                <i class="bi bi-shield-check"></i>
                                Pago seguro y protegido
                            </p>

                            <p>
                                <i class="bi bi-download"></i>
                                Acceso a tus recursos tras la compra
                            </p>

                        </div>

                    </div>

                    <a href="/UNRINCONDEPT/public/tienda.php" class="carrito-continue">
                        <i class="bi bi-arrow-left"></i>
                        Seguir explorando recursos
                    </a>

                </aside>

            </div>

        <?php endif; ?>

    </div>

</main>

<script src="/UNRINCONDEPT/static/js/carrito.js"></script>

<?php
require_once __DIR__ . '/../../templates/footer.php';
?>