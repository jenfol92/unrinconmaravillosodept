<?php

/**
 * HEADER PRINCIPAL DE LA WEB
 * ---------------------------------------------------------
 * Este archivo contiene la cabecera común de la aplicación:
 *
 * - Carga de sesión.
 * - Cálculo de usuario logueado.
 * - Enlaces del menú principal.
 * - Acceso a carrito.
 * - Acceso a perfil o panel de administración según rol.
 * - Carga de Bootstrap, CSS principal, iconos y fuentes.
 *
 * IMPORTANTE:
 * Las rutas se construyen usando BASE_URL para que funcionen tanto:
 *
 * - En local:
 *   http://localhost/UNRINCONDEPT/
 *
 * - En producción:
 *   https://tudominio.es/
 */


/**
 * Cargamos el archivo de sesión.
 * ---------------------------------------------------------
 * Este archivo normalmente:
 * - Inicia session_start().
 * - Inicializa el carrito.
 * - Puede cargar también config/app.php, donde está definida BASE_URL.
 */
require_once __DIR__ . '/../includes/session.php';


/**
 * Comprobamos si el usuario está logueado.
 *
 * Si existe $_SESSION['usuario_id'], entendemos que hay sesión iniciada.
 */
$usuarioLogueado = isset($_SESSION['usuario_id']);


/**
 * Nombre que se mostrará en el navbar.
 *
 * Si no existe nombre en sesión, usamos "Mi cuenta" como texto por defecto.
 */
$nombreUsuario = $_SESSION['nombre_usuario'] ?? 'Mi cuenta';


/**
 * Rol del usuario.
 *
 * Se usa para decidir si el usuario debe ir:
 * - al panel de administración
 * - o al perfil normal de cliente
 */
$rolUsuario = $_SESSION['rol'] ?? null;


/**
 * URL del panel del usuario.
 * ---------------------------------------------------------
 * Si el rol es 1 o 2, mandamos al panel de administración.
 * Si no, mandamos al perfil de usuario normal.
 *
 * Usamos BASE_URL o PUBLIC_URL para evitar rutas absolutas fijas como:
 * /UNRINCONDEPT/public/admin.php
 */
$urlPanelUsuario = ($rolUsuario == 1 || $rolUsuario == 2)
  ? PUBLIC_URL . 'admin.php'
  : PUBLIC_URL . 'perfil.php';


/**
 * Contador del carrito.
 * ---------------------------------------------------------
 * Suma todas las cantidades guardadas en $_SESSION['carrito'].
 *
 * Ejemplo:
 * $_SESSION['carrito'] = [
 *   5 => 2,
 *   8 => 1
 * ];
 *
 * Resultado:
 * $contadorCarrito = 3;
 */
$contadorCarrito = array_sum($_SESSION['carrito']);

?>

<!DOCTYPE html>
<html lang="en">

<head>

  <!-- Codificación de caracteres -->
  <meta charset="UTF-8">

  <!-- Adaptación responsive para móviles y tablets -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Título de la página -->
  <title>Un rincon de PT</title>


  <!-- =====================================================
       BOOTSTRAP LOCAL
       -----------------------------------------------------
       Se carga desde la carpeta static usando BASE_URL.
       Así funciona correctamente en local y producción.
  ====================================================== -->
  <link rel="stylesheet" href="<?= BASE_URL ?>static/bootstrap-5.3.8-dist/css/bootstrap.min.css">


  <!-- =====================================================
       CSS PRINCIPAL DEL PROYECTO
       -----------------------------------------------------
       Aquí se carga el CSS compilado de la aplicación.
  ====================================================== -->
  <link rel="stylesheet" href="<?= BASE_URL ?>static/css/style.css?v=20260529-5">


  <!-- =====================================================
       ICONOS DE BOOTSTRAP
       -----------------------------------------------------
       Se cargan desde CDN.
  ====================================================== -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">


  <!-- =====================================================
       FUENTES DE GOOGLE
       -----------------------------------------------------
       Fuentes usadas en el diseño visual de la web.
  ====================================================== -->
  <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Lilita+One&family=Varela+Round&display=swap" rel="stylesheet">

</head>

<body>

  <!-- =====================================================
       HEADER / NAVBAR PRINCIPAL
       -----------------------------------------------------
       Barra de navegación visible en toda la web.
  ====================================================== -->
  <header>

    <nav class="navbar navbar-expand-lg" id="navbar-principal">

      <div class="container">


        <!-- =====================================================
             LOGO
             -----------------------------------------------------
             Enlace a la página de inicio.
             La imagen también usa BASE_URL para evitar problemas
             de rutas relativas.
        ====================================================== -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= PUBLIC_URL  ?>index.php">

          <img src="<?= BASE_URL ?>static/images/logo/logo.jpeg" class="mi-logo">

          <span class="mi-logo-texto">
            Unrinconmaravillosodept
          </span>

        </a>


        <!-- =====================================================
             CARRITO VISIBLE EN MÓVIL
             -----------------------------------------------------
             Este icono aparece solo en pantallas pequeñas
             gracias a la clase d-lg-none.
        ====================================================== -->
        <a href="<?= PUBLIC_URL ?>carrito.php"
          class="mi-icono mobile-cart-header position-relative d-lg-none ms-auto me-3">

          <i class="bi bi-bag"></i>

          <!-- Mostramos la burbuja solo si hay productos en el carrito -->
          <?php if ($contadorCarrito > 0): ?>

            <span id="cart-count-mobile" class="mi-badge">
              <?= $contadorCarrito ?>
            </span>

          <?php endif; ?>

        </a>


        <!-- =====================================================
             BOTÓN HAMBURGUESA
             -----------------------------------------------------
             Botón que abre/cierra el menú en móvil.
        ====================================================== -->
        <button class="navbar-toggler" type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav">

          <span class="navbar-toggler-icon"></span>

        </button>


        <!-- =====================================================
             MENÚ DESPLEGABLE
             -----------------------------------------------------
             En escritorio se ve expandido.
             En móvil se despliega con el botón hamburguesa.
        ====================================================== -->
        <div class="collapse navbar-collapse" id="navbarNav">

          <ul class="navbar-nav me-auto mx-auto">


            <!-- =====================================================
                 ENLACE: INICIO
                 -----------------------------------------------------
                 Se añade la clase activo si la página actual es index.php.
            ====================================================== -->
            <li class="nav-item">

              <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'activo' : '' ?>"
                href="<?= PUBLIC_URL  ?>index.php">
                Inicio
              </a>

            </li>


            <!-- =====================================================
                 ENLACE: RECURSOS GRATIS
            ====================================================== -->
            <li class="nav-item">

              <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'recursos_gratuitos.php' ? 'activo' : '' ?>"
                href="<?= PUBLIC_URL  ?>recursos_gratuitos.php">
                Recursos Gratis
              </a>

            </li>


            <!-- =====================================================
                 ENLACE: TIENDA
            ====================================================== -->
            <li class="nav-item">

              <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'tienda.php' ? 'activo' : '' ?>"
                href="<?= PUBLIC_URL  ?>tienda.php">
                Tienda
              </a>

            </li>


            <!-- =====================================================
                 ENLACE: CONTACTO
            ====================================================== -->
            <li class="nav-item">

              <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'contacto.php' ? 'activo' : '' ?>"
                href="<?= PUBLIC_URL  ?>contacto.php">
                Contacto
              </a>

            </li>


            <!-- =====================================================
                 CARRITO EN MENÚ MÓVIL
                 -----------------------------------------------------
                 Este enlace aparece solo dentro del menú móvil.
            ====================================================== -->
            <li class="nav-item d-lg-none">

              <a class="nav-link" href="<?= PUBLIC_URL  ?>carrito.php">
                <i class="bi bi-bag me-2"></i>
                Carrito
              </a>

            </li>


            <!-- =====================================================
                 PERFIL / ACCESO EN MENÚ MÓVIL
                 -----------------------------------------------------
                 Si el usuario está logueado:
                 - mostramos acceso a Mi Perfil o Admin.

                 Si no está logueado:
                 - mostramos Acceder.
            ====================================================== -->
            <li class="nav-item d-lg-none">

              <?php if (isset($_SESSION['usuario_id'])): ?>

                <a class="nav-link" href="<?= $urlPanelUsuario ?>">
                  <i class="bi bi-person me-2"></i>
                  Mi Perfil
                </a>

              <?php else: ?>

                <a class="nav-link" href="<?= PUBLIC_URL  ?>login.php">
                  <i class="bi bi-box-arrow-in-right me-2"></i>
                  Acceder
                </a>

              <?php endif; ?>

            </li>

          </ul>

        </div>


        <!-- =====================================================
             ICONOS DERECHA EN ESCRITORIO
             -----------------------------------------------------
             Esta zona solo aparece en pantallas grandes.
        ====================================================== -->
        <div class="d-none d-lg-flex align-items-center gap-3">


          <!-- =====================================================
               CARRITO EN ESCRITORIO
          ====================================================== -->
          <a href="<?= PUBLIC_URL ?>carrito.php" class="mi-icono position-relative">

            <i class="bi bi-bag"></i>

            <span id="cart-count" class="mi-badge">
              <?= $contadorCarrito ?>
            </span>

          </a>


          <!-- =====================================================
               SESIÓN EN ESCRITORIO
               -----------------------------------------------------
               Si hay usuario logueado:
               - mostramos icono y nombre.

               Si no:
               - mostramos botón Acceder.
          ====================================================== -->
          <?php if ($usuarioLogueado): ?>

            <a href="<?= $urlPanelUsuario ?>" class="usuario-navbar">

              <i class="bi bi-person-circle"></i>

              <span>
                <?= htmlspecialchars($nombreUsuario) ?>
              </span>

            </a>

          <?php else: ?>

            <a href="<?= PUBLIC_URL  ?>login.php" class="mi-btn-acceder">
              Acceder
            </a>

          <?php endif; ?>

        </div>

      </div>

    </nav>
    <!--variables globales disponibles para todos los archivos .js-->
    <script>
      window.BASE_URL = "<?= BASE_URL ?>";
      window.PUBLIC_URL = "<?= PUBLIC_URL ?>";
       window.USUARIO_LOGUEADO = <?= usuarioLogueado() ? 'true' : 'false' ?>;
    </script>
  </header>