<?php
require_once __DIR__ . '/../includes/session.php';

// Variables propias del header
$usuarioLogueado = isset($_SESSION['usuario_id']);
$nombreUsuario = $_SESSION['nombre_usuario'] ?? 'Mi cuenta';
$rolUsuario = $_SESSION['rol'] ?? null;

$urlPanelUsuario = ($rolUsuario == 1 || $rolUsuario == 2)
    ? '/UNRINCONDEPT/public/admin.php'
    : '/UNRINCONDEPT/public/perfil.php';

$contadorCarrito = array_sum($_SESSION['carrito']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Un rincon de PT</title>
  <!--boostrap local-->
  <link rel="stylesheet" href="../static/bootstrap-5.3.8-dist/css/bootstrap.min.css">
  <!--css-->
  <link rel="stylesheet" href="../static/css/style.css">

  <!-- Iconos y fuentes -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

  <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Lilita+One&family=Varela+Round&display=swap" rel="stylesheet">

</head>

<body>

 <header>
  <nav class="navbar navbar-expand-lg" id="navbar-principal">
    <div class="container">

     <!-- LOGO IZQUIERDA / CENTRO -->
      <a class="navbar-brand  d-flex align-items-center gap-2" href="index.php">
        <img src="../static/images/logo/logo.jpeg" class="mi-logo">
        <span class="mi-logo-texto">Unrinconmaravillosodept</span>
      </a>

            <!-- BOTÓN HAMBURGUESA  Siempre junto al brand-->
      <button class="navbar-toggler" type="button"
              data-bs-toggle="collapse"
              data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>


      <!-- OCULTO PARA MOVIL todo lo que despliega el menu movil-->
      <div class="collapse navbar-collapse " id="navbarNav">

      <!--LINKS, carrito y acceso / perfil -->
        <ul class="navbar-nav me-auto mx-auto">
          <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'activo' : '' ?>" 
            href="index.php">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'recursos_gratuitos.php' ? 'activo' : '' ?>" href="recursos_gratuitos.php">Recursos Gratis</a>
          </li>
          <li  class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'tienda.php' ? 'activo' : '' ?>"
              href="tienda.php" >Tienda</a>
              </li>

          <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'contacto.php' ? 'activo' : '' ?>" 
               href="contacto.php">Contacto</a>
          </li>
           <li class="nav-item d-lg-none">
    <a class="nav-link" href="carrito.php">
      <i class="bi bi-bag me-2"></i>Carrito
    </a>
  </li>
  <li class="nav-item d-lg-none">
      <?php if (isset($_SESSION['usuario_id'])): ?>
<a class="nav-link" href="<?= $urlPanelUsuario ?>">
   <i class="bi bi-person me-2"></i>Mi Perfil
</a>
 <?php else: ?>
    <a class="nav-link" href="login.php">
      <i class="bi bi-box-arrow-in-right me-2"></i>Acceder
    </a>
    <?php endif; ?>
  </li>
</ul>
        
      </div>

      <!-- ICONOS DERECHA -->
      <div class="d-none d-lg-flex align-items-center gap-3">

<!-- Carrito -->
<a href="/UNRINCONDEPT/public/carrito.php" class="mi-icono position-relative">
    <i class="bi bi-bag"></i>

    <span id="cart-count" class="mi-badge">
        <?= $contadorCarrito ?>
    </span>
</a>
  <!-- Sesión -->
<?php 

if ($usuarioLogueado): ?>

    <a href="<?= $urlPanelUsuario ?>" class="usuario-navbar">
        <i class="bi bi-person-circle"></i>
        <span><?= htmlspecialchars($nombreUsuario) ?></span>
    </a>

<?php else: ?>

    <a href="/UNRINCONDEPT/public/login.php" class="mi-btn-acceder">
        Acceder
    </a>

<?php endif; ?>
      </div>

    </div>
  </nav>
</header>