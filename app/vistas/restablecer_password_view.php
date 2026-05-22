<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<main class="auth-page">

    <section class="auth-left login-left">

        <div class="auth-preview-card">
            <img src="/UNRINCONDEPT/static/images/logo/logo.jpeg" alt="Recursos educativos">
        </div>

        <div class="auth-left-content">
            <h2>Crea una nueva contraseña segura.</h2>
            <p>
                Este enlace es temporal. Si ha caducado, tendrás que solicitar uno nuevo.
            </p>
        </div>

    </section>

    <section class="auth-right">

        <a href="/UNRINCONDEPT/public/login.php" class="auth-back">
            ← Volver al login
        </a>

        <div class="auth-logo">
            <img src="/UNRINCONDEPT/static/images/logo/logo.jpeg" alt="Logo">
            <span>UnRincónPT</span>
        </div>

        <div class="auth-box">

            <h1>Restablecer contraseña</h1>

            <p class="auth-subtitle">
                Introduce tu nueva contraseña.
            </p>

            <?php if (!empty($errorReset)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($errorReset) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($mensajeReset)): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($mensajeReset) ?>
                </div>

                <p class="auth-switch">
                    <a href="/UNRINCONDEPT/public/login.php">Ir al inicio de sesión</a>
                </p>
            <?php else: ?>

                <form action="/UNRINCONDEPT/public/restablecer-password.php" method="POST" novalidate>

                    <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

                    <div class="auth-field">
                        <label>Nueva contraseña</label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Nueva contraseña"
                            required>
                    </div>

                    <div class="auth-field">
                        <label>Confirmar nueva contraseña</label>

                        <input
                            type="password"
                            name="password_confirm"
                            class="form-control"
                            placeholder="Repite la nueva contraseña"
                            required>
                    </div>

                    <button type="submit" class="auth-main-btn">
                        Guardar nueva contraseña
                    </button>

                </form>

            <?php endif; ?>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>