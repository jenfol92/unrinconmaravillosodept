<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<main class="auth-page">

    <section class="auth-left login-left">

        <div class="auth-preview-card">
            <img src="/UNRINCONDEPT/static/images/logo/logo.jpeg" alt="Recursos educativos">
        </div>

        <div class="auth-left-content">
            <h2>Crea momentos mágicos de aprendizaje todos los días.</h2>
            <p>
                Únete a nuestra comunidad y accede a cientos de recursos diseñados
                para hacer brillar a tus pequeños.
            </p>
        </div>

    </section>

    <section class="auth-right">

        <a href="/UNRINCONDEPT/public/index.php" class="auth-back">
            ← Volver al inicio
        </a>

        <div class="auth-logo">
            <img src="/UNRINCONDEPT/static/images/logo/logo.jpeg" alt="Logo">
            <span>UnRincónPT</span>
        </div>

        <div class="auth-box">

            <h1>¡Hola de nuevo!</h1>
            <p class="auth-subtitle">
                Entra para descargar tus materiales favoritos.
            </p>

            <div class="auth-tabs">
                <a href="/UNRINCONDEPT/public/login.php" class="active">Entrar</a>
                <a href="/UNRINCONDEPT/public/registro.php">Registrarse</a>
            </div>

            <form action="/UNRINCONDEPT/public/login.php" method="POST">

                <div class="auth-field">
                    <label>Correo electrónico</label>
                    <input type="email" name="email" placeholder="ejemplo@correo.com" required>
                </div>

                <div class="auth-field">
                    <div class="auth-label-row">
                        <label>Contraseña</label>
                        <a href="#">¿Olvidaste tu contraseña?</a>
                    </div>

                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <div class="auth-check">
                    <input type="checkbox" id="remember">
                    <label for="remember">Recordar mi sesión</label>
                </div>

                <button type="submit" class="auth-main-btn">
                    Entrar ahora
                </button>

            </form>

            <div class="auth-separator">
                <span>O CONTINÚA CON</span>
            </div>

            <div class="auth-socials">
                <button type="button">Google</button>
                <button type="button">Facebook</button>
            </div>

        </div>

        <div class="auth-footer-help">
            ¿Tienes problemas para entrar?
            <a href="#">Escríbenos aquí</a>
        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>