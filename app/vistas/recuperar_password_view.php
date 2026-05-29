<?php

/**
 * VISTA: RECUPERAR CONTRASEÑA
 * ---------------------------------------------------------
 * Esta vista muestra el formulario para iniciar el proceso
 * de recuperación de contraseña.
 *
 * Funcionalidad:
 * - Permite al usuario introducir su correo electrónico.
 * - Envía el formulario a recuperar-password.php.
 * - Muestra errores si el correo no es válido o no existe.
 * - Muestra mensaje de éxito si se ha enviado el enlace.
 * - Ofrece enlace para volver al login.
 *
 * Variables que puede recibir desde el controller:
 *
 * $errorRecuperacion:
 * - Mensaje de error generado durante el proceso de recuperación.
 *
 * $mensajeRecuperacion:
 * - Mensaje de éxito si el correo se ha procesado correctamente.
 *
 * $email:
 * - Correo introducido previamente por el usuario.
 * - Se conserva para no obligar al usuario a volver a escribirlo
 *   si hay algún error de validación.
 *
 * Rutas:
 * - BASE_URL se usa para archivos estáticos: imágenes, CSS, JS, etc.
 * - PUBLIC_URL se usa para páginas públicas PHP.
 */


/**
 * Cargamos el header común de la web.
 *
 * Normalmente incluye:
 * - apertura del HTML
 * - etiqueta head
 * - carga de CSS
 * - navbar
 * - definición global de window.BASE_URL y window.PUBLIC_URL
 */
require_once __DIR__ . '/../../templates/header.php';

?>

<main class="auth-page">

    <!-- =====================================================
         ZONA IZQUIERDA
         -----------------------------------------------------
         Bloque visual de apoyo para la pantalla de recuperación.
         Contiene:
         - imagen/logo
         - título informativo
         - texto explicativo
    ====================================================== -->
    <section class="auth-left login-left">

        <!-- Tarjeta visual con imagen de marca -->
        <div class="auth-preview-card">

            <!-- 
                Imagen/logo de la web.
                Usamos BASE_URL porque es un archivo estático.
            -->
            <img
                src="<?= BASE_URL ?>static/images/logo/logo.jpeg"
                alt="Recursos educativos">

        </div>

        <!-- Texto informativo de la parte izquierda -->
        <div class="auth-left-content">

            <h2>
                Recupera el acceso a tu cuenta.
            </h2>

            <p>
                Te enviaremos un enlace temporal para que puedas crear una nueva contraseña.
            </p>

        </div>

    </section>


    <!-- =====================================================
         ZONA DERECHA
         -----------------------------------------------------
         Contiene:
         - enlace para volver al login
         - logo
         - formulario de recuperación
         - mensajes de error o éxito
    ====================================================== -->
    <section class="auth-right">

        <!-- 
            Enlace para volver al login.
            Usamos PUBLIC_URL porque login.php es una página pública.
        -->
        <a href="<?= PUBLIC_URL ?>login.php" class="auth-back">
            ← Volver al login
        </a>


        <!-- =====================================================
             LOGO SUPERIOR
             -----------------------------------------------------
             Identidad visual de la pantalla de autenticación.
        ====================================================== -->
        <div class="auth-logo">

            <!-- Imagen/logo cargado desde static -->
            <img
                src="<?= BASE_URL ?>static/images/logo/logo.jpeg"
                alt="Logo">

            <span>
                UnRincónPT
            </span>

        </div>


        <!-- =====================================================
             CAJA PRINCIPAL DEL FORMULARIO
        ====================================================== -->
        <div class="auth-box">

            <!-- Título principal de la pantalla -->
            <h1>
                ¿Olvidaste tu contraseña?
            </h1>

            <!-- Explicación breve del proceso -->
            <p class="auth-subtitle">
                Introduce tu correo electrónico y te enviaremos un enlace para restablecerla.
            </p>


            <!-- =====================================================
                 MENSAJE DE ERROR
                 -----------------------------------------------------
                 Se muestra si el controller ha definido
                 $errorRecuperacion.
            ====================================================== -->
            <?php if (!empty($errorRecuperacion)): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($errorRecuperacion) ?>
                </div>

            <?php endif; ?>


            <!-- =====================================================
                 MENSAJE DE ÉXITO
                 -----------------------------------------------------
                 Se muestra si el controller ha definido
                 $mensajeRecuperacion.
            ====================================================== -->
            <?php if (!empty($mensajeRecuperacion)): ?>

                <div class="alert alert-success">
                    <?= htmlspecialchars($mensajeRecuperacion) ?>
                </div>

            <?php endif; ?>


            <!-- =====================================================
                 FORMULARIO DE RECUPERACIÓN DE CONTRASEÑA
                 -----------------------------------------------------
                 action:
                 - Envía el formulario a recuperar-password.php.

                 method="POST":
                 - Envía el email de forma no visible en la URL.

                 novalidate:
                 - Desactiva la validación automática HTML5 del navegador.
                 - Permite que la validación se gestione desde tu JS/backend.
            ====================================================== -->
            <form
                action="<?= PUBLIC_URL ?>recuperar-password.php"
                method="POST"
                novalidate>

                <!-- Campo email -->
                <div class="auth-field">

                    <label>
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="<?= htmlspecialchars($email ?? '') ?>"
                        placeholder="ejemplo@correo.com"
                        required>

                </div>


                <!-- Botón de envío del formulario -->
                <button type="submit" class="auth-main-btn">
                    Enviar enlace de recuperación
                </button>

            </form>


            <!-- =====================================================
                 ENLACE DE VUELTA AL LOGIN
                 -----------------------------------------------------
                 Se muestra para usuarios que finalmente recuerdan
                 su contraseña.
            ====================================================== -->
            <p class="auth-switch">

                ¿Ya recuerdas tu contraseña?

                <a href="<?= PUBLIC_URL ?>login.php">
                    Inicia sesión aquí
                </a>

            </p>

        </div>

    </section>

</main>

<?php

/**
 * Cargamos el footer común de la web.
 *
 * Normalmente incluye:
 * - footer visual
 * - scripts globales
 * - Bootstrap JS
 * - cierre de body y html
 */
require_once __DIR__ . '/../../templates/footer.php';

?>