<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/header.php';
?>

<main class="container py-5 text-center">

    <div class="carrito-empty">

        <h2>Pago realizado correctamente</h2>

        <p>
            Tu compra se ha completado. En breve podrás acceder a tus recursos desde tu perfil.
        </p>

        <a href="/UNRINCONDEPT/public/perfil.php" class="btn">
            Ir a mi perfil
        </a>

    </div>

</main>

<?php
require_once __DIR__ . '/../templates/footer.php';
?>