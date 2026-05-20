<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<main class="container my-5">
    <div class="alert alert-success">
        <h1>Pago recibido correctamente</h1>
        <p>Tu pedido se ha registrado como pagado.</p>

        <?php if (!empty($pedido_id)): ?>
            <p>Número de pedido: <?= htmlspecialchars($pedido_id) ?></p>
        <?php endif; ?>

        <a href="/UNRINCONDEPT/public/perfil.php" class="btn btn-primary">
            Ir a mis descargas
        </a>
    </div>
</main>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>