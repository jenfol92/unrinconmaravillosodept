<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<main class="auth-page">

    <section class="auth-left login-left">

        <div class="auth-preview-card">
            <img src="<?= BASE_URL ?>static/images/logo/logo.jpeg" alt="Recursos educativos">
        </div>

        <div class="auth-left-content">
            <h2>Recupera el acceso a tu cuenta.</h2>
            <p>
                Te enviaremos un enlace temporal para que puedas crear una nueva contraseña.
            </p>
        </div>

    </section>

    <section class="auth-right">

        <a href="<?= BASE_URL ?>public/login.php" class="auth-back">
            ← Volver al login
        </a>

        <div class="auth-logo">
            <img src="<?= BASE_URL ?>static/images/logo/logo.jpeg" alt="Logo">
            <span>UnRincónPT</span>
        </div>

        <div class="auth-box">

            <h1>¿Olvidaste tu contraseña?</h1>

            <p class="auth-subtitle">
                Introduce tu correo electrónico y te enviaremos un enlace para restablecerla.
            </p>

            <?php if (!empty($errorRecuperacion)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($errorRecuperacion) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($mensajeRecuperacion)): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($mensajeRecuperacion) ?>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>public/recuperar-password.php" method="POST" novalidate>

                <div class="auth-field">
                    <label>Correo electrónico</label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="<?= htmlspecialchars($email ?? '') ?>"
                        placeholder="ejemplo@correo.com"
                        required>
                </div>

                <button type="submit" class="auth-main-btn">
                    Enviar enlace de recuperación
                </button>

            </form>

            <p class="auth-switch">
                ¿Ya recuerdas tu contraseña?
                <a href="<?= BASE_URL ?>public/login.php">Inicia sesión aquí</a>
            </p>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>