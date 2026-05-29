<?php

/**
 * VISTA: REGISTRO DE USUARIO
 * ---------------------------------------------------------
 * Esta vista muestra el formulario público de registro.
 *
 * Funcionalidad:
 * - Permite al usuario crear una cuenta.
 * - Muestra errores de validación si el controller los envía.
 * - Conserva los valores introducidos si hay errores.
 * - Incluye enlaces a login, privacidad, cookies y términos de compra.
 *
 * Variables que puede recibir desde el controller:
 *
 * $erroresRegistro:
 * - Array de errores detectados durante el registro.
 *
 * $nombre:
 * - Nombre introducido por el usuario.
 *
 * $apellidos:
 * - Apellidos introducidos por el usuario.
 *
 * $email:
 * - Correo electrónico introducido por el usuario.
 *
 * $localidad:
 * - Localidad introducida por el usuario.
 *
 * $cp:
 * - Código postal introducido por el usuario.
 *
 * Rutas:
 * - BASE_URL se usa para archivos estáticos: imágenes, CSS, JS, etc.
 * - PUBLIC_URL se usa para enlaces a páginas públicas PHP.
 */


/**
 * Cargamos el header común de la web.
 *
 * Normalmente incluye:
 * - apertura del documento HTML
 * - etiqueta head
 * - carga de CSS
 * - navbar
 * - definición global de BASE_URL y PUBLIC_URL para JavaScript
 */
require_once __DIR__ . '/../../templates/header.php';

?>

<main class="auth-page">

    <!-- =====================================================
         ZONA IZQUIERDA DEL REGISTRO
         -----------------------------------------------------
         Bloque visual de presentación.
         Contiene:
         - imagen/logo
         - texto motivacional
    ====================================================== -->
    <section class="auth-left register-left">

        <!-- Tarjeta visual con imagen de marca -->
        <div class="auth-preview-card">

            <!-- 
                Imagen/logo de la web.
                Usamos BASE_URL porque es un archivo estático.
            -->
            <img
                src="<?= BASE_URL ?>static/images/logo/logo.jpeg"
                alt="Clase educativa">

        </div>

        <!-- Texto de presentación del registro -->
        <div class="auth-left-content">

            <h2>
                ¡Únete a nuestra aventura!
            </h2>

            <p>
                Crea tu cuenta y accede a cientos de recursos diseñados para hacer
                del aprendizaje algo mágico.
            </p>

        </div>

    </section>


    <!-- =====================================================
         ZONA DERECHA DEL REGISTRO
         -----------------------------------------------------
         Contiene:
         - enlace para volver al login
         - logo
         - caja principal de registro
         - formulario
    ====================================================== -->
    <section class="auth-right">

        <!-- 
            Enlace para volver a la pantalla de login.
            Usamos PUBLIC_URL porque login.php es una página pública.
        -->
        <a href="<?= PUBLIC_URL ?>login.php" class="auth-back">
            ← Volver
        </a>

        <!-- Logo pequeño superior -->
        <div class="auth-logo">

            <!-- Imagen de marca -->
            <img
                src="<?= BASE_URL ?>static/images/logo/logo.jpeg"
                alt="Logo">

            <span>
                Un Rincón Maravilloso de PT
            </span>

        </div>


        <!-- =====================================================
             CAJA PRINCIPAL DEL FORMULARIO
        ====================================================== -->
        <div class="auth-box">

            <!-- Título principal del formulario -->
            <h1>
                Crear mi Cuenta
            </h1>

            <!-- Subtítulo descriptivo -->
            <p class="auth-subtitle">
                Empieza hoy mismo a descargar materiales increíbles.
            </p>


            <!-- =====================================================
                 PESTAÑAS LOGIN / REGISTRO
                 -----------------------------------------------------
                 Permiten alternar entre:
                 - Entrar
                 - Registrarse

                 La pestaña Registrarse aparece activa en esta vista.
            ====================================================== -->
            <div class="auth-tabs">

                <a href="<?= PUBLIC_URL ?>login.php">
                    Entrar
                </a>

                <a href="<?= PUBLIC_URL ?>registro.php" class="active">
                    Registrarse
                </a>

            </div>


            <!-- =====================================================
                 ERRORES DE REGISTRO
                 -----------------------------------------------------
                 Si el controller envía errores en $erroresRegistro,
                 se muestran en una alerta Bootstrap.
            ====================================================== -->
            <?php if (!empty($erroresRegistro)): ?>

                <div class="alert alert-danger">

                    <ul class="mb-0">

                        <?php foreach ($erroresRegistro as $error): ?>

                            <!-- 
                                Escapamos el error para evitar inyección HTML.
                            -->
                            <li>
                                <?= htmlspecialchars($error) ?>
                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php endif; ?>


            <!-- =====================================================
                 FORMULARIO DE REGISTRO
                 -----------------------------------------------------
                 id="formRegistro":
                 - Lo usa validaciones.js para validar en cliente.

                 action:
                 - Envía el formulario a registro.php.

                 method="POST":
                 - Envía los datos de forma no visible en la URL.

                 novalidate:
                 - Desactiva la validación HTML5 automática del navegador
                   para que pueda encargarse validaciones.js y el backend.
            ====================================================== -->
            <form
                id="formRegistro"
                action="<?= PUBLIC_URL ?>registro.php"
                method="POST"
                novalidate>

                <!-- =====================================================
                     FILA: NOMBRE Y APELLIDOS
                ====================================================== -->
                <div class="auth-two-cols">

                    <!-- Campo nombre -->
                    <div class="auth-field">

                        <label>
                            Nombre
                        </label>

                        <input
                            type="text"
                            name="nombre"
                            class="form-control"
                            placeholder="Ej. María"
                            value="<?= htmlspecialchars($nombre ?? '') ?>"
                            required>

                    </div>

                    <!-- Campo apellidos -->
                    <div class="auth-field">

                        <label>
                            Apellidos
                        </label>

                        <input
                            type="text"
                            name="apellidos"
                            class="form-control"
                            placeholder="Ej. García"
                            value="<?= htmlspecialchars($apellidos ?? '') ?>"
                            required>

                    </div>

                </div>


                <!-- =====================================================
                     CAMPO: CORREO ELECTRÓNICO
                ====================================================== -->
                <div class="auth-field">

                    <label>
                        Correo Electrónico
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="hola@ejemplo.com"
                        value="<?= htmlspecialchars($email ?? '') ?>"
                        required>

                </div>


                <!-- =====================================================
                     FILA: LOCALIDAD Y CÓDIGO POSTAL
                ====================================================== -->
                <div class="auth-two-cols">

                    <!-- Campo localidad -->
                    <div class="auth-field">

                        <label>
                            Localidad
                        </label>

                        <input
                            type="text"
                            name="localidad"
                            class="form-control"
                            placeholder="Ej. Elche"
                            value="<?= htmlspecialchars($localidad ?? '') ?>"
                            required>

                    </div>

                    <!-- Campo código postal -->
                    <div class="auth-field">

                        <label>
                            Código Postal
                        </label>

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


                <!-- =====================================================
                     CAMPO: CONTRASEÑA
                     -----------------------------------------------------
                     minlength="4":
                     - Coincide con la validación de cliente que tienes
                       en validaciones.js.
                ====================================================== -->
                <div class="auth-field">

                    <label>
                        Contraseña
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="••••••••"
                        minlength="4"
                        required>

                </div>


                <!-- =====================================================
                     CAMPO: REPETIR CONTRASEÑA
                     -----------------------------------------------------
                     Este campo debe coincidir con password.
                     La comprobación en cliente la hace validaciones.js.
                     El backend también debería validarlo.
                ====================================================== -->
                <div class="auth-field">

                    <label>
                        Repetir Contraseña
                    </label>

                    <input
                        type="password"
                        name="password_confirm"
                        class="form-control"
                        placeholder="••••••••"
                        minlength="4"
                        required>

                </div>


                <!-- =====================================================
                     ACEPTACIÓN DE POLÍTICAS Y TÉRMINOS
                     -----------------------------------------------------
                     El usuario debe aceptar:
                     - política de privacidad
                     - política de cookies
                     - términos de compra

                     validaciones.js también comprueba este campo.
                ====================================================== -->
                <div class="auth-check">

                    <input
                        type="checkbox"
                        id="terms"
                        name="terms"
                        required>

                    <label for="terms">

                        Acepto la

                        <a href="<?= PUBLIC_URL ?>politica-privacidad.php" target="_blank">
                            política de privacidad
                        </a>,

                        la

                        <a href="<?= PUBLIC_URL ?>politica-cookies.php" target="_blank">
                            política de cookies
                        </a>

                        y los

                        <a href="<?= PUBLIC_URL ?>terminos-compra.php" target="_blank">
                            términos de compra
                        </a>.

                    </label>

                </div>


                <!-- Botón principal de envío del formulario -->
                <button type="submit" class="auth-main-btn auth-register-btn">
                    ¡Crear mi cuenta!
                </button>

            </form>


            <!-- =====================================================
                 ENLACE A LOGIN
                 -----------------------------------------------------
                 Se muestra debajo del formulario para usuarios
                 que ya tienen cuenta.
            ====================================================== -->
            <p class="auth-switch">

                ¿Ya tienes cuenta?

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