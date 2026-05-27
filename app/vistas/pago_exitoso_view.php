<?php
/**
 * Vista: pago_exitoso_view.php
 * ---------------------------------------------------------
 * Muestra una confirmación al usuario cuando el pago se ha
 * completado correctamente.
 *
 * Esta vista normalmente se carga desde PagoController::pagoExitoso().
 *
 * Variables que puede recibir:
 *
 * - $pedido_id:
 *   Identificador interno del pedido marcado como pagado.
 *   Se muestra solo si existe y no está vacío.
 *
 * Funcionalidades principales:
 *
 * - Informar al usuario de que el pago se ha recibido correctamente.
 * - Confirmar que el pedido se ha registrado como pagado.
 * - Mostrar el número de pedido si está disponible.
 * - Ofrecer un acceso directo al perfil del usuario para consultar
 *   sus descargas.
 *
 * Archivos relacionados:
 *
 * - PagoController.php:
 *   Confirma el pago con Stripe, marca el pedido como pagado
 *   y carga esta vista.
 *
 * - perfil_view.php:
 *   Vista donde el usuario podrá acceder a sus recursos comprados
 *   y descargas disponibles.
 *
 * Seguridad:
 *
 * - El número de pedido se imprime con htmlspecialchars().
 * - No se muestran datos sensibles del pago.
 * - La validación real del pago se realiza previamente en el controlador.
 */
?>

<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<!-- 
     PÁGINA DE PAGO EXITOSO
     Se muestra cuando el usuario vuelve desde Stripe después
     de completar correctamente el proceso de pago.
 -->
<main class="container my-5">

    <!-- 
         MENSAJE DE CONFIRMACIÓN
         Alert de Bootstrap utilizado para informar visualmente
         de que el pago ha sido recibido correctamente.
   -->
    <div class="alert alert-success">

        <!-- Título principal de confirmación -->
        <h1>Pago recibido correctamente</h1>

        <!-- Mensaje explicativo para el usuario -->
        <p>Tu pedido se ha registrado como pagado.</p>

        <!-- =
             NÚMERO DE PEDIDO
             Si el controlador envía $pedido_id, se muestra al usuario
             como referencia interna del pedido.
         -->
        <?php if (!empty($pedido_id)): ?>
            <p>Número de pedido: <?= htmlspecialchars($pedido_id) ?></p>
        <?php endif; ?>

        <!-- 
             BOTÓN A MIS DESCARGAS
             Redirige al perfil del usuario, donde podrá acceder
             a los recursos adquiridos tras el pago.
      -->
        <a href="<?= BASE_URL ?>public/perfil.php" class="btn btn-primary">
            Ir a mis descargas
        </a>

    </div>

</main>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>