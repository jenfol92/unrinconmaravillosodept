<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<main class="auth-page">

    <section class="auth-left register-left">

        <div class="auth-preview-card">
            <img src="/UNRINCONDEPT/static/images/logo/logo.jpeg" alt="Clase educativa">
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

        <a href="/UNRINCONDEPT/public/login.php" class="auth-back">
            ← Volver
        </a>

        <div class="auth-logo">
            <img src="/UNRINCONDEPT/static/images/logo/logo.jpeg" alt="Logo">
            <span>Un Rincón Maravilloso de PT</span>
        </div>

        <div class="auth-box">

            <h1>Crear mi Cuenta</h1>
            <p class="auth-subtitle">
                Empieza hoy mismo a descargar materiales increíbles.
            </p>
            <div class="auth-tabs">
                <a href="/UNRINCONDEPT/public/login.php">Entrar</a>
                <a href="/UNRINCONDEPT/public/registro.php" class="active">Registrarse</a>
            </div>
            <form action="/UNRINCONDEPT/public/registro.php" method="POST">

                <div class="auth-two-cols">

                    <div class="auth-field">
                        <label>Nombre</label>
                        <input type="text" name="nombre" placeholder="Ej. María" required>
                    </div>

                    <div class="auth-field">
                        <label>Apellidos</label>
                        <input type="text" name="apellidos" placeholder="Ej. García" required>
                    </div>

                </div>

                <div class="auth-field">
                    <label>Correo Electrónico</label>
                    <input type="email" name="email" placeholder="hola@ejemplo.com" required>
                </div>

                <div class="auth-field">
                    <label>Contraseña</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <div class="auth-field">
                    <label>Repetir Contraseña</label>
                    <input type="password" name="password_confirm" placeholder="••••••••" required>
                </div>

                <div class="auth-check">
                    <input type="checkbox" id="terms" required>
                    <label for="terms">
                        Acepto las <a href="#">políticas de privacidad</a> y los términos de uso.
                    </label>
                </div>

                <button type="submit" class="auth-main-btn auth-register-btn">
                    ¡Crear mi cuenta!
                </button>

            </form>

            <p class="auth-switch">
                ¿Ya tienes cuenta?
                <a href="/UNRINCONDEPT/public/login.php">Inicia sesión aquí</a>
            </p>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>