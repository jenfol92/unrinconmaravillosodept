<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/header.php';
?>

<main class="container py-5 text-center">

    <div class="carrito-empty">

        <h2>Pago cancelado</h2>

        <p>
            No se ha realizado ningún cargo. Puedes volver al carrito y continuar cuando quieras.
        </p>

        <a href="/UNRINCONDEPT/public/carrito.php" class="btn">
            Volver al carrito
        </a>

    </div>

</main>

<?php
require_once __DIR__ . '/../templates/footer.php';
?>