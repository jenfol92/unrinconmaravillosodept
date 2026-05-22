<?php

/**
 * Vista: login_view.php
 * ---------------------------------------------------------
 * Muestra la página de inicio de sesión de usuarios.
 *
 * Esta vista recibe datos desde AuthController::login().
 *
 * Variables que puede recibir:
 *
 * - $email:
 *   Email escrito previamente por el usuario. Se reutiliza si hubo error
 *   para que no tenga que volver a escribirlo.
 *
 * - $errorLogin:
 *   Mensaje de error generado por el controlador cuando el email o la
 *   contraseña no son correctos, o cuando faltan datos obligatorios.
 *
 * Funcionalidades principales:
 *
 * - Mostrar formulario de acceso.
 * - Permitir introducir email y contraseña.
 * - Mostrar errores de login.
 * - Mantener el email introducido en caso de error.
 * - Enlazar con la pantalla de registro.
 * - Enlazar con la página principal.
 *
 * Archivos relacionados:
 *
 * - AuthController.php:
 *   Procesa el login, valida los datos y crea la sesión del usuario.
 *
 * - validaciones.js:
 *   Puede validar el formulario en cliente mediante el id formLogin.
 *
 * Seguridad:
 *
 * - El email y los errores se imprimen con htmlspecialchars().
 * - La contraseña no se rellena nunca automáticamente.
 * - La comprobación real de credenciales se realiza en servidor.
 */
?>

<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<!-- 
     PÁGINA DE AUTENTICACIÓN
     Contenedor principal de la pantalla de login.
     Se divide en dos zonas:
     - Izquierda: bloque visual/promocional.
     - Derecha: formulario de inicio de sesión.
-->
<main class="auth-page">

    <!-- 
         COLUMNA IZQUIERDA
         Zona visual de apoyo para la pantalla de login.
         Muestra una imagen/logo y un mensaje de bienvenida.
    -->
    <section class="auth-left login-left">

        <!-- Tarjeta visual con imagen de la marca -->
        <div class="auth-preview-card">
            <img src="/UNRINCONDEPT/static/images/logo/logo.jpeg" alt="Recursos educativos">
        </div>

        <!-- Texto promocional de la zona izquierda -->
        <div class="auth-left-content">
            <h2>Crea momentos mágicos de aprendizaje todos los días.</h2>
            <p>
                Únete a nuestra comunidad y accede a cientos de recursos diseñados
                para hacer brillar a tus pequeños.
            </p>
        </div>

    </section>

    <!--
         COLUMNA DERECHA
         Contiene el formulario de inicio de sesión.
-->
    <section class="auth-right">

        <!-- Enlace para volver a la página principal -->
        <a href="/UNRINCONDEPT/public/index.php" class="auth-back">
            ← Volver al inicio
        </a>

        <!-- Logo pequeño de la zona de autenticación -->
        <div class="auth-logo">
            <img src="/UNRINCONDEPT/static/images/logo/logo.jpeg" alt="Logo">
            <span>UnRincónPT</span>
        </div>

        <!-- Caja principal del formulario -->
        <div class="auth-box">

            <h1>¡Hola de nuevo!</h1>
            <p class="auth-subtitle">
                Entra para descargar tus materiales favoritos.
            </p>

            <!-- 
                 PESTAÑAS LOGIN / REGISTRO
                 Permiten cambiar entre inicio de sesión y registro.
                 La pestaña activa es "Entrar".
             -->
            <div class="auth-tabs">
                <a href="/UNRINCONDEPT/public/login.php" class="active">Entrar</a>
                <a href="/UNRINCONDEPT/public/registro.php">Registrarse</a>
            </div>

            <!-- 
                 FORMULARIO DE LOGIN
                 Envía los datos por POST a login.php.
                 
                 id="formLogin":
                 Permite que validaciones.js localice este formulario
                 para aplicar validaciones en cliente.
                 
                 novalidate:
                 Desactiva la validación automática del navegador para
                 poder controlar los mensajes desde nuestro JavaScript.
        -->
            <form id="formLogin" action="/UNRINCONDEPT/public/login.php" method="POST" novalidate>

                <!-- Campo email -->
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

                <!-- Campo contraseña -->
                <div class="auth-field">

                    <div class="auth-label-row">
                        <label>Contraseña</label>

                        <!--
                            Enlace reservado para recuperación de contraseña.
                          
                        -->
                        <a href="/UNRINCONDEPT/public/recuperar-password.php">¿Olvidaste tu contraseña?</a>
                    </div>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="••••••••"
                        required>

                    <!-- 
                         ERROR DE LOGIN
                         Si el controlador define $errorLogin, se muestra aquí.
                         Se usa htmlspecialchars() para imprimir el mensaje
                         de forma segura.
                   -->
                    <?php if (!empty($errorLogin)): ?>
                        <div class="text-danger small mt-2">
                            <?= htmlspecialchars($errorLogin) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 
                     RECORDAR SESIÓN
                     Checkbox visual preparado para una funcionalidad futura.
                     Actualmente no tiene name, por lo que no se envía al servidor.
                -->
                <div class="auth-check">
                    <input type="checkbox" id="remember">
                    <label for="remember">Recordar mi sesión</label>
                </div>

                <!-- Botón principal de envío -->
                <button type="submit" class="auth-main-btn">
                    Entrar ahora
                </button>

            </form>

            <!-- 
                 SEPARADOR SOCIAL
                 Bloque visual para separar el login tradicional de
                 posibles accesos mediante proveedores externos.
            -->
            <div class="auth-separator">
                <span>O CONTINÚA CON</span>
            </div>

            <!-- 
                 BOTONES SOCIALES
                 Botones preparados visualmente para una futura integración
                 con OAuth, como Google o Facebook.
                 Actualmente son botones type="button", por lo que no envían formulario.
          -->
            <div class="auth-socials">
                <a
                    href="/UNRINCONDEPT/public/oauth-google.php"
                    class="auth-social-btn">
                    Google
                </a>

                <button type="button">Facebook</button>
            </div>

        </div>

        <!--
             AYUDA DE ACCESO
             Enlace visual para usuarios con problemas para iniciar sesión.
             Puede conectarse más adelante con contacto o soporte.
        -->
        <div class="auth-footer-help">
            ¿Tienes problemas para entrar?
            <a href="#">Escríbenos aquí</a>
        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>