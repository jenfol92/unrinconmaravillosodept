<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<main class="auth-page">

    <section class="auth-left register-left">

        <div class="auth-preview-card">
            <img src="<?= BASE_URL ?>static/images/logo/logo.jpeg" alt="Clase educativa">
        </div>

        <div class="auth-left-content">
            <h2>¡Únete a nuestra aventura!</h2>
            <p>
                Crea tu cuenta y accede a cientos de recursos diseñados para hacer
                del aprendizaje algo mágico.
            </p>
        </div>

    </section>

    <section class="auth-right">

        <a href="<?= BASE_URL ?>public/login.php" class="auth-back">
            ← Volver
        </a>

        <div class="auth-logo">
            <img src="<?= BASE_URL ?>static/images/logo/logo.jpeg" alt="Logo">
            <span>Un Rincón Maravilloso de PT</span>
        </div>

        <div class="auth-box">

            <h1>Crear mi Cuenta</h1>
            <p class="auth-subtitle">
                Empieza hoy mismo a descargar materiales increíbles.
            </p>
            <div class="auth-tabs">
                <a href="<?= BASE_URL ?>public/login.php">Entrar</a>
                <a href="<?= BASE_URL ?>public/registro.php" class="active">Registrarse</a>
            </div>
            <?php if (!empty($erroresRegistro)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($erroresRegistro as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
            <form id="formRegistro" action="<?= BASE_URL ?>public/registro.php" method="POST" novalidate>

                <div class="auth-two-cols">

                    <div class="auth-field">
                        <label>Nombre</label>
                        <input
                            type="text"
                            name="nombre"
                            class="form-control"
                            placeholder="Ej. María"
                            value="<?= htmlspecialchars($nombre ?? '') ?>"
                            required>
                    </div>

                    <div class="auth-field">
                        <label>Apellidos</label>
                        <input
                            type="text"
                            name="apellidos"
                            class="form-control"
                            placeholder="Ej. García"
                            value="<?= htmlspecialchars($apellidos ?? '') ?>"
                            required>
                    </div>

                </div>

                <div class="auth-field">
                    <label>Correo Electrónico</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="hola@ejemplo.com"
                        value="<?= htmlspecialchars($email ?? '') ?>"
                        required>
                </div>

                <div class="auth-two-cols">

                    <div class="auth-field">
                        <label>Localidad</label>
                        <input
                            type="text"
                            name="localidad"
                            class="form-control"
                            placeholder="Ej. Elche"
                            value="<?= htmlspecialchars($localidad ?? '') ?>"
                            required>
                    </div>

                    <div class="auth-field">
                        <label>Código Postal</label>
                        <input
                            type="text"
                            name="cp"
                            class="form-control"
                            placeholder="Ej. 03201"
                            maxlength="5"
                            inputmode="numeric"
                            value="<?= htmlspecialchars($cp ?? '') ?>"
                            required>
                    </div>

                </div>

                <div class="auth-field">
                    <label>Contraseña</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="••••••••"
                        minlength="4"
                        required>
                </div>

                <div class="auth-field">
                    <label>Repetir Contraseña</label>
                    <input
                        type="password"
                        name="password_confirm"
                        class="form-control"
                        placeholder="••••••••"
                        minlength="4"
                        required>
                </div>

                <div class="auth-check">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">
                        Acepto la
                        <a href="<?= BASE_URL ?>public/politica-privacidad.php" target="_blank">política de privacidad</a>,
                        la
                        <a href="<?= BASE_URL ?>public/politica-cookies.php" target="_blank">política de cookies</a>
                        y los
                        <a href="<?= BASE_URL ?>public/terminos-compra.php" target="_blank">términos de compra</a>.
                    </label>
                </div>
                <button type="submit" class="auth-main-btn auth-register-btn">
                    ¡Crear mi cuenta!
                </button>

            </form>

            <p class="auth-switch">
                ¿Ya tienes cuenta?
                <a href="<?= BASE_URL ?>public/login.php">Inicia sesión aquí</a>
            </p>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>