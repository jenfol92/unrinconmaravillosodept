<?php
/**
 * Vista: carrito_view.php
 * ---------------------------------------------------------
 * Muestra el carrito de compra del usuario.
 *
 * Esta vista recibe los datos preparados previamente desde
 * ProductoController::carrito().
 *
 * Variables recibidas:
 *
 * - $productos_carrito:
 *   Array con los productos añadidos al carrito.
 *
 * - $subtotal:
 *   Suma del precio de todos los productos del carrito.
 *
 * - $iva:
 *   Importe de IVA si se aplicase. Actualmente se mantiene en 0.
 *
 * - $total:
 *   Importe final del pedido. En este caso coincide con el subtotal.
 *
 * Funcionalidades principales:
 *
 * - Mostrar mensaje de carrito vacío.
 * - Listar productos añadidos al carrito.
 * - Mostrar imagen, título, categoría, descripción, cantidad y precio.
 * - Permitir aumentar, reducir o eliminar productos del carrito.
 * - Mostrar resumen del pedido.
 * - Enviar el formulario de pago a Stripe.
 *
 * Archivos relacionados:
 *
 * - ProductoController.php:
 *   Prepara los productos y totales del carrito.
 *
 * - carrito.js:
 *   Gestiona las acciones dinámicas del carrito mediante botones
 *   con data-id y data-accion.
 *
 * - PagoController.php:
 *   Crea la sesión de Stripe Checkout.
 *
 * Seguridad:
 *
 * - Los datos impresos en HTML se protegen con htmlspecialchars().
 * - Los importes se calculan desde servidor, no desde JavaScript.
 */
require_once __DIR__ . "/../../templates/header.php";
?>
<!-- 
     PÁGINA DEL CARRITO
     Contenedor principal de la vista del carrito.
     Muestra los productos seleccionados y el resumen del pedido.
 -->
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
<!-- 
             CARRITO VACÍO
             Si no hay productos en $productos_carrito, se muestra
             un mensaje informativo y un botón para volver a la tienda.
       -->
        <?php if (empty($productos_carrito)): ?>

            <div class="carrito-empty">

                <h2>Tu carrito está vacío</h2>

                <p>
                    Todavía no has añadido ningún recurso a tu carrito.
                </p>

                <a href="<?= PUBLIC_URL ?>tienda.php" class="btn">
                    Explorar recursos
                </a>

            </div>

        <?php else: ?>
             <!-- 
                 LAYOUT PRINCIPAL DEL CARRITO
                 Divide la pantalla en dos columnas:
                 - Izquierda: productos del carrito.
                 - Derecha: resumen del pedido y botón de pago.
             -->

            <div class="carrito-layout">

                <!-- COLUMNA IZQUIERDA: PRODUCTOS DEL CARRITO
                   Lista todos los recursos añadidos por el usuario.
                 -->
                <section class="carrito-main">
                     <!-- Cabecera del listado de productos -->

                    <div class="carrito-list-header">

                        <h2>
                            <?= count($productos_carrito) ?>
                            <?= count($productos_carrito) === 1 ? 'producto seleccionado' : 'productos seleccionados' ?>
                        </h2>

                        <a href="<?= PUBLIC_URL?>tienda.php">
                            Seguir comprando
                        </a>

                    </div>
 <!-- 
                         RECORRIDO DE PRODUCTOS
                         Cada producto del carrito se muestra como una tarjeta.
                         Los datos proceden de $productos_carrito.
                  -->
                    <?php foreach ($productos_carrito as $p): ?>

                        <?php
                          /*
                            Normalizamos los datos del producto para evitar
                            errores si algún campo no existe.

                            Esto permite que la vista sea más resistente ante
                            datos incompletos.
                        */
                        $imagen = $p['imagen'] ?? '';
                        $titulo = $p['titulo'] ?? 'Recurso';
                        $categoria = $p['categoria_nombre'] ?? $p['categoria'] ?? 'Recurso educativo';
                        $descripcion = $p['descripcion'] ?? 'Material educativo listo para descargar.';
                        $cantidad = (int)($p['cantidad'] ?? 1);
                        $precio = (float)($p['precio'] ?? 0);

                           /*
                            Calculamos el total de esta línea:
                            precio unitario x cantidad.
                        */
                        $lineaTotal = $precio * $cantidad;

                     
                        ?>

                        <article class="carrito-item">
                             <!-- Imagen del producto -->

                            <div class="carrito-item-img">
                                <?php if (!empty($imagen)): ?>
                                    <img
                                        src="<?= BASE_URL ?>static/images/img/<?= htmlspecialchars($imagen) ?>"
                                        alt="<?= htmlspecialchars($titulo) ?>">
                                <?php else: ?>
                                     <!-- Imagen alternativa si el producto no tiene imagen -->
                                    <div class="carrito-img-placeholder">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
<!-- Información textual del producto -->
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
                             <!-- 
                                 CONTROLES DE CANTIDAD
                                 Los botones usan data-id y data-accion.
                                 carrito.js escucha estos botones y actualiza el carrito.
                             -->

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
<!-- Precio total del producto y botón eliminar -->
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


                    <!-- BLOQUE DE CONFIANZA
                    
                         Refuerza información importante antes de la compra:
                         descarga instantánea y calidad del material.
                    -->
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


                <!--  
             
                     COLUMNA DERECHA: RESUMEN DEL PEDIDO
                     Muestra subtotal, gastos, impuestos y total.
                     También contiene el formulario que inicia el pago.
             -->
                <aside class="carrito-side">

                    <div class="carrito-summary">

                        <div class="carrito-summary-body">

                            <h2>Resumen del pedido</h2>

                            <!-- Subtotal calculado en servidor -->

                            <div class="carrito-summary-row">
                                <span>Subtotal</span>
                                <strong><?= number_format($subtotal, 2) ?>€</strong>
                            </div>
<!-- Actualmente no se aplican gastos de gestión -->
                            <div class="carrito-summary-row">
                                <span>Gastos de gestión</span>
                                <strong>Gratis</strong>
                            </div>
<!-- Información visible para el usuario -->
                            <div class="carrito-summary-row">
                                <span>Impuestos</span>
                                <strong>Incluidos</strong>
                            </div>
<!-- Total final del pedido -->
                            <div class="carrito-summary-total">
                                <span>Total</span>
                                <strong><?= number_format($total, 2) ?>€</strong>
                            </div>
 <!--
                                 FORMULARIO DE CHECKOUT
                                 Envía al endpoint que crea la sesión de Stripe.
                                 El cálculo del pedido y la sesión de pago se realizan
                                 en servidor, no en esta vista.
                            -->
                            <form action="<?= PUBLIC_URL ?>crear_checkout_stripe.php" method="POST">
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
 <!-- Enlace para volver a la tienda -->
                    <a href="<?= PUBLIC_URL?>tienda.php" class="carrito-continue">
                        <i class="bi bi-arrow-left"></i>
                        Seguir explorando recursos
                    </a>

                </aside>

            </div>

        <?php endif; ?>

    </div>

</main>
<!-- 
     SCRIPT DEL CARRITO
     carrito.js gestiona las acciones dinámicas:
     - Añadir unidad.
     - Restar unidad.
     - Eliminar producto.
     - Actualizar el carrito tras cada acción.
 -->

<script src="<?= BASE_URL ?>static/js/carrito.js"></script>

<?php
require_once __DIR__ . '/../../templates/footer.php';
?>