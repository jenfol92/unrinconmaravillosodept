<?php
/**
 * Vista: pago_cancelado_view.php
 * ---------------------------------------------------------
 * Muestra un mensaje informativo cuando el usuario cancela
 * el proceso de pago o cuando el pago no llega a completarse.
 *
 * Esta vista normalmente se carga desde PagoController::pagoCancelado().
 *
 * Funcionalidades principales:
 *
 * - Informar al usuario de que el pago no se ha completado.
 * - Mantener una experiencia controlada tras cancelar Stripe Checkout.
 * - Ofrecer un botón para volver al carrito y reintentar la compra.
 *
 * Archivos relacionados:
 *
 * - PagoController.php:
 *   Gestiona la lógica de pago cancelado y puede marcar el pedido
 *   como cancelado si sigue en estado pendiente.
 *
 * - carrito_view.php:
 *   Vista a la que se redirige al usuario si quiere volver a revisar
 *   su carrito.
 *
 * Seguridad:
 *
 * - Esta vista no procesa datos sensibles.
 * - No muestra información del pedido ni del pago.
 * - Solo presenta un mensaje informativo al usuario.
 */
?>

<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<!--
     PÁGINA DE PAGO CANCELADO
     Se muestra cuando el usuario cancela el proceso de pago
     o cuando no se completa correctamente la compra.
 -->
<main class="container my-5">

    <!--
         MENSAJE DE AVISO
         Alert de Bootstrap utilizado para informar visualmente
         de que el pago no ha sido completado.
  -->
    <div class="alert alert-warning">

        <!-- Título principal de la página -->
        <h1>Pago cancelado</h1>

        <!-- Mensaje explicativo para el usuario -->
        <p>No se ha completado el pago.</p>

        <!-- 
             BOTÓN DE VUELTA AL CARRITO
             Permite al usuario regresar al carrito para revisar
             los productos o intentar completar la compra de nuevo.
         -->
        <a href="/UNRINCONDEPT/public/carrito.php" class="btn btn-secondary">
            Volver al carrito
        </a>

    </div>

</main>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>