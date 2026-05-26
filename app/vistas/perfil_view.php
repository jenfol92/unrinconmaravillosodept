<?php

/**
 * Vista: perfil_view.php
 * ---------------------------------------------------------
 * Muestra el panel personal del usuario registrado.
 *
 * Esta vista se carga desde UsuarioController::perfil().
 *
 * Variables recibidas desde el controlador:
 *
 * - $usuario:
 *   Datos personales del usuario autenticado.
 *
 * - $favoritos:
 *   Recursos que el usuario ha marcado como favoritos.
 *
 * - $productosComprados:
 *   Recursos adquiridos por el usuario y disponibles para descarga.
 *
 * - $ticketsSoporte:
 *   Consultas de soporte abiertas o cerradas por el usuario.
 *
 * Funcionalidades principales:
 *
 * - Mostrar bienvenida personalizada.
 * - Mostrar recursos comprados y enlaces de descarga.
 * - Mostrar recursos favoritos.
 * - Mostrar datos de cuenta.
 * - Mostrar apartado de seguridad.
 * - Permitir cerrar sesión.
 * - Permitir abrir tickets de soporte.
 * - Permitir ver y responder conversaciones de soporte.
 * - Permitir finalizar consultas de soporte.
 * - Permitir enviar sugerencias.
 * - Permitir añadir o editar reseñas de recursos comprados.
 *
 * Archivos relacionados:
 *
 * - UsuarioController.php:
 *   Comprueba sesión, rol de cliente y carga los datos necesarios.
 *
 * - Usuario.php:
 *   Obtiene datos del usuario y recursos adquiridos.
 *
 * - Producto.php:
 *   Obtiene favoritos y gestiona reseñas.
 *
 * - Soporte.php:
 *   Obtiene tickets y mensajes de soporte.
 *
 * - usuario.js:
 *   Gestiona navegación del panel, AJAX de soporte, sugerencias y reseñas.
 *
 * Seguridad:
 *
 * - Los datos dinámicos se imprimen con htmlspecialchars().
 * - Los IDs se convierten a entero con (int).
 * - Los tokens de descarga se envían mediante URL codificada con urlencode().
 * - El acceso a esta vista debe estar protegido previamente desde el controlador.
 */
?>

<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<main class="perfil-page">


    <!-- CABECERA DEL PERFIL 
   -->
    <section class="perfil-hero">

        <div class="container">

            <div class="d-flex align-items-center gap-4 flex-wrap">

                <!-- Datos principales  del usuario-->
                <div>
                    <h1>
                        ¡Hola, <?= htmlspecialchars($usuario['nombre'] ?? 'Usuario') ?>!
                    </h1>
                    <!-- Fecha de registro del usuario -->
                    <p class="mb-0">
                        Miembro desde
                        <strong>
                            <?= !empty($usuario['fecha_registro'])
                                ? date('d/m/Y', strtotime($usuario['fecha_registro']))
                                : 'fecha no disponible' ?>
                        </strong>
                    </p>

                    <small>
                        Bienvenido a tu panel personal
                    </small>
                </div>

            </div>

        </div>

    </section>


    <!-- SIDEBAR PERFIL USUARIO
      
         Estructura principal dividida en:
         - Sidebar izquierdo con navegación.
         - Contenido derecho con secciones dinámicas.-->

    <section class="user-panel">

        <div class="container">

            <div class="row g-4">

                <!--
                SIDEBAR IZQUIERDO 
                 -->
                <div class="col-12 col-lg-3">

                    <aside class="panel-sidebar">

                        <!-- 
                          MENÚ DEL PANEL
                             Cada botón usa data-section para indicar a usuario.js
                             qué sección debe mostrarse.
                              -->

                        <nav class="panel-menu">

                            <!-- Botón Descargas -->
                            <button
                                type="button"
                                class="panel-link active"
                                data-section="descargas">
                                <i class="bi bi-download"></i>
                                Mis Descargas
                            </button>

                            <!-- Botón Favoritos -->
                            <button
                                type="button"
                                class="panel-link"
                                data-section="favoritos">
                                <i class="bi bi-heart"></i>
                                Favoritos
                            </button>

                            <!-- Botón Datos de cuenta -->
                            <button
                                type="button"
                                class="panel-link"
                                data-section="cuenta">
                                <i class="bi bi-person"></i>
                                Datos de Cuenta
                            </button>

                            <!-- Botón Seguridad -->
                            <button
                                type="button"
                                class="panel-link"
                                data-section="seguridad">
                                <i class="bi bi-shield-check"></i>
                                Seguridad
                            </button>


                        </nav>

                        <!-- BLOQUE DE AYUDA
                             Acceso rápido a la sección de soporte para abrir
                             o consultar incidencias.-->
                        <div class="panel-help mt-4">

                            <h6>
                                ¿Necesitas ayuda?
                            </h6>

                            <p>
                                Si tienes problemas con tus recursos o descargas, escríbenos.
                            </p>

                            <!-- Abre modal Bootstrap de soporte -->

                            <button
                                type="button"
                                class="panel-link"
                                data-section="soporte">
                                <i class="bi bi-life-preserver"></i>
                                Soporte
                            </button>

                        </div>
                        <button
                            type="button"
                            class="panel-link"
                            data-section="sugerencias">
                            <i class="bi bi-chat-heart"></i>
                            Buzón de sugerencias
                        </button>


                    </aside>

                </div>


                <!--   CONTENIDO DERECHO
                     Aquí se muestran las secciones del panel.
                     usuario.js activa u oculta cada sección según el botón
                     seleccionado en el sidebar.-->

                <div class="col-12 col-lg-9">

                    <div class="panel-content">


                        <!-- SECCIÓN MIS RECURSOS ADQUIRIDOS 
                        
                             Muestra los productos comprados por el usuario.
                             Desde aquí puede descargar, ver detalle o reseñar.-->

                        <section
                            id="section-descargas"
                            class="panel-section active">

                            <h2>Mis recursos adquiridos</h2>

                            <p>
                                Aquí tienes todos los recursos que has adquirido.
                                Puedes descargarlos de nuevo y dejar tu reseña.
                            </p>

                            <?php if (empty($productosComprados)): ?>

                                <!-- Sin productos comprados -->
                                <div class="panel-empty">
                                    Todavía no has comprado ningún recurso.
                                </div>

                            <?php else: ?>

                                <!-- 
                                   TABLA RESPONSIVE DE RECURSOS COMPRADOS
                                     Muestra fecha de compra, recurso, precio, número
                                     de descargas usadas y acciones disponibles.-->
                                <div class="table-responsive panel-mobile-cards">

                                    <table class="table align-middle panel-table">

                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Recurso</th>
                                                <th>Precio</th>
                                                <th>Descargas</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            <?php if (empty($productosComprados)): ?>

                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        Todavía no has adquirido ningún recurso.
                                                    </td>
                                                </tr>

                                            <?php else: ?>

                                                <?php foreach ($productosComprados as $producto): ?>

                                                    <tr>
                                                        <!-- Fecha de compra del recurso -->
                                                        <td data-label="Fecha">
                                                            <?= !empty($producto['fecha_compra'])
                                                                ? date('d/m/Y', strtotime($producto['fecha_compra']))
                                                                : 'Sin fecha' ?>
                                                        </td>
                                                        <!-- Información del recurso comprado -->
                                                        <td data-label="Recurso">
                                                            <div class="d-flex align-items-center gap-3 panel-recurso-info">
                                                                <?php if (!empty($producto['imagen'])): ?>
                                                                    <img
                                                                        src="/UNRINCONDEPT/static/images/img/<?= htmlspecialchars($producto['imagen']) ?>"
                                                                        alt="<?= htmlspecialchars($producto['titulo'] ?? 'Recurso') ?>"
                                                                        class="panel-product-img">
                                                                <?php else: ?>

                                                                    <div class="panel-product-img panel-product-img-empty">
                                                                        <i class="bi bi-file-earmark-text"></i>
                                                                    </div>

                                                                <?php endif; ?>

                                                                <div>
                                                                    <strong>
                                                                        <?= htmlspecialchars($producto['titulo'] ?? 'Recurso') ?>
                                                                    </strong>

                                                                    <div class="text-muted small">
                                                                        Recurso adquirido #<?= (int)($producto['descarga_id'] ?? 0) ?>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </td>
                                                        <!-- Precio del recurso comprado -->
                                                        <td data-label="Precio">
                                                            <strong>
                                                                <?= number_format((float)($producto['precio'] ?? 0), 2) ?> €
                                                            </strong>
                                                        </td>
                                                        <!-- Contador de descargas usadas frente al máximo permitido -->
                                                        <td data-label="Descargas">
                                                            <?= (int)($producto['numero_descargas'] ?? 0) ?> /
                                                            <?= (int)($producto['max_descargas'] ?? 0) ?>
                                                        </td>
                                                        <!-- Acciones disponibles sobre el recurso -->
                                                        <td data-label="Acciones" class="text-end">

                                                            <div class="panel-acciones-recursos">
                                                                <!-- Descarga mediante token seguro -->
                                                                <a href="/UNRINCONDEPT/public/descargar.php?token=<?= urlencode($producto['token_descarga']) ?>"
                                                                    class="btn btn-sm btn-success">
                                                                    <i class="bi bi-download"></i>
                                                                    Descargar
                                                                </a>
                                                                <!-- Acceso al detalle público del producto -->
                                                                <a
                                                                    href="/UNRINCONDEPT/public/detalle.php?id=<?= (int)($producto['id'] ?? 0) ?>"
                                                                    class="btn btn-sm btn-outline-primary"
                                                                    title="Ver detalle">
                                                                    <i class="bi bi-eye"></i>
                                                                </a>
                                                                <!-- Botón para abrir modal de reseña -->
                                                                <button
                                                                    type="button"
                                                                    class="btn btn-sm btn-outline-warning btn-abrir-resena"
                                                                    data-producto-id="<?= (int)($producto['id'] ?? 0) ?>"
                                                                    data-producto-titulo="<?= htmlspecialchars($producto['titulo'] ?? '', ENT_QUOTES) ?>"
                                                                    title="Añadir o editar reseña">
                                                                    <i class="bi bi-star"></i>
                                                                    Reseña
                                                                </button>

                                                            </div>

                                                        </td>

                                                    </tr>

                                                <?php endforeach; ?>

                                            <?php endif; ?>

                                        </tbody>

                                    </table>

                                </div>

                            <?php endif; ?>

                        </section>



                        <!-- SECCIÓN FAVORITOS 
                         
                             Muestra los recursos guardados como favoritos por
                             el usuario.-->

                        <section
                            id="section-favoritos"
                            class="panel-section">

                            <h2>
                                Mis Favoritos
                            </h2>

                            <p>
                                Estos son los recursos que has guardado como favoritos.
                            </p>

                            <?php if (empty($favoritos)): ?>

                                <!-- Mensaje si no hay favoritos -->
                                <div class="panel-empty">
                                    Todavía no tienes recursos favoritos.
                                </div>

                            <?php else: ?>

                                <!-- Tabla responsive para favoritos -->
                                <div class="table-responsive">

                                    <table class="table align-middle panel-table">

                                        <thead>
                                            <tr>
                                                <th>Recurso</th>
                                                <th>Precio</th>
                                                <th>Fecha favorito</th>
                                                <th class="text-end">Acción</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            <?php foreach ($favoritos as $fav): ?>

                                                < id="favorito-row-<?= (int)$fav['id'] ?>">

                                                    <!-- Imagen + título -->
                                                    <td>
                                                        <div class="d-flex align-items-center gap-3">

                                                            <img
                                                                src="/UNRINCONDEPT/static/images/img/<?= htmlspecialchars($fav['imagen']) ?>"
                                                                alt="<?= htmlspecialchars($fav['titulo']) ?>"
                                                                class="panel-product-img">

                                                            <strong>
                                                                <?= htmlspecialchars($fav['titulo']) ?>
                                                            </strong>

                                                        </div>
                                                    </td>

                                                    <!-- Precio -->
                                                    <td>
                                                        <strong>
                                                            <?= number_format((float)$fav['precio'], 2) ?> €
                                                        </strong>
                                                    </td>

                                                    <!-- Fecha favorito -->
                                                    <td>
                                                        <?= date('d/m/Y', strtotime($fav['fecha'])) ?>
                                                    </td>

                                                    <!-- Botones -->
                                                    <td class="text-end">

                                                        <!-- Ver detalle -->
                                                        <a
                                                            href="/UNRINCONDEPT/public/detalle.php?id=<?= $fav['id'] ?>"
                                                            class="btn btn-primary btn-sm">
                                                            Ver
                                                        </a>

                                                        <!-- 
                                   Añade el producto al carrito, lo elimina de favoritos y lo quita visualmente de la tabla.
                                                            -->
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-success"
                                                            onclick="gestionarSesion(<?= (int)$fav['id'] ?>, 'add_carrito')">
                                                            <i class="bi bi-cart-plus"></i>

                                                        </button>

                                                    </td>

                                                    </tr>

                                                <?php endforeach; ?>

                                        </tbody>

                                    </table>

                                </div>

                            <?php endif; ?>

                        </section>


                        <!-- SECCIÓN DATOS DE CUENTA 
                            
                             Muestra información personal registrada del usuario.
                             Los campos están deshabilitados, por lo que solo son
                             de consulta.-->
                        <section id="section-cuenta" class="panel-section">

                            <h2>
                                Datos de Cuenta
                            </h2>

                            <p>
                                Consulta tus datos personales asociados a la cuenta.
                            </p>

                            <form class="panel-form">

                                <div class="row g-3">

                                    <!-- Nombre -->
                                    <div class="col-12 col-md-6">

                                        <label class="form-label">
                                            Nombre
                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text">
                                                <i class="bi bi-person"></i>
                                            </span>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>"
                                                disabled>

                                        </div>

                                    </div>

                                    <!-- Apellidos -->
                                    <div class="col-12 col-md-6">

                                        <label class="form-label">
                                            Apellidos
                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text">
                                                <i class="bi bi-person-lines-fill"></i>
                                            </span>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?= htmlspecialchars($usuario['apellidos'] ?? '') ?>"
                                                disabled>

                                        </div>

                                    </div>

                                    <!-- Email -->
                                    <div class="col-12 col-md-6">

                                        <label class="form-label">
                                            Email
                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text">
                                                <i class="bi bi-envelope"></i>
                                            </span>

                                            <input
                                                type="email"
                                                class="form-control"
                                                value="<?= htmlspecialchars($usuario['email'] ?? '') ?>"
                                                disabled>

                                        </div>

                                    </div>

                                    <!-- Localidad -->
                                    <div class="col-12 col-md-6">

                                        <label class="form-label">
                                            Localidad
                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text">
                                                <i class="bi bi-geo-alt"></i>
                                            </span>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?= htmlspecialchars($usuario['localidad'] ?? 'No indicada') ?>"
                                                disabled>

                                        </div>

                                    </div>

                                    <!-- Código Postal -->
                                    <div class="col-12 col-md-6">

                                        <label class="form-label">
                                            Código Postal
                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text">
                                                <i class="bi bi-mailbox"></i>
                                            </span>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?= htmlspecialchars($usuario['cp'] ?? 'No indicado') ?>"
                                                disabled>

                                        </div>

                                    </div>

                                    <!-- Fecha de registro -->
                                    <div class="col-12 col-md-6">

                                        <label class="form-label">
                                            Fecha de registro
                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text">
                                                <i class="bi bi-calendar-check"></i>
                                            </span>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?= !empty($usuario['fecha_registro']) ? date('d/m/Y', strtotime($usuario['fecha_registro'])) : 'No disponible' ?>"
                                                disabled>

                                        </div>

                                    </div>

                                </div>

                                <div class="alert alert-info mt-4 mb-0">
                                    <i class="bi bi-info-circle"></i>
                                    Estos datos se recogen durante el registro para identificar la cuenta y conocer la procedencia de los usuarios.
                                </div>

                            </form>

                        </section>



                        <!-- SECCIÓN SEGURIDAD 
                           
                             Agrupa acciones relacionadas con la cuenta:
                             cambio de contraseña, último acceso y cierre de sesión.-->

                        <section
                            id="section-seguridad"
                            class="panel-section">

                            <h2>
                                Seguridad
                            </h2>

                            <p>
                                Gestiona la seguridad de tu cuenta.
                            </p>

                        
                            <!-- Último acceso -->
                            <div class="security-card">

                                <p class="mb-2">

                                    <strong>Último acceso:</strong><br>

                                    <?php if (!empty($usuario['ultimo_acceso'])): ?>

                                        <?= date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])) ?>

                                    <?php else: ?>

                                        No hay accesos anteriores registrados.

                                    <?php endif; ?>

                                    <?php if (!empty($usuario['ultimo_ip'])): ?>
                                        <br>
                                        <small class="text-muted">
                                            IP registrada: <?= htmlspecialchars($usuario['ultimo_ip']) ?>
                                        </small>
                                    <?php endif; ?>

                                    <?php if (!empty($usuario['acceso_actual'])): ?>
                                        <br>
                                        <small class="text-muted">
                                            Acceso actual: <?= date('d/m/Y H:i', strtotime($usuario['acceso_actual'])) ?>
                                        </small>
                                    <?php endif; ?>

                                </p>
                                <button
                                    type="button"
                                    class="btn btn-outline-danger"
                                    onclick="enviarAlertaSeguridad()">
                                    No reconozco este acceso
                                </button>
                            </div>


                            <!-- Cerrar sesión -->
                            <div class="security-card">

                                <h5>
                                    <i class="bi bi-box-arrow-right"></i>
                                    Cerrar sesión
                                </h5>

                                <p>
                                    Finaliza tu sesión de forma segura.
                                </p>

                                <a
                                    href="/UNRINCONDEPT/public/logout.php"
                                    class="btn btn-outline-danger">
                                    Cerrar sesión
                                </a>

                            </div>

                        </section>
                        <!-- SECCIÓN SOPORTE
                         Permite al usuario crear nuevas consultas y revisar
                             conversaciones anteriores.
                      -->
                        <section id="section-soporte" class="panel-section">

                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                                <div>
                                    <h2>Centro de Soporte</h2>
                                    <p class="text-muted mb-0">
                                        Consulta tus conversaciones o abre una nueva incidencia.
                                    </p>
                                </div>
                                <!-- Abre/cierra el formulario de nueva consulta -->
                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#nuevoSoporteBox">
                                    <i class="bi bi-plus-circle"></i>
                                    Nueva consulta
                                </button>
                            </div>
                            <!-- Formulario plegable para crear un nuevo ticket -->
                            <div class="collapse mb-4" id="nuevoSoporteBox">
                                <div class="panel-card-soft">

                                    <h4 class="mb-3">
                                        <i class="bi bi-chat-dots"></i>
                                        Nueva consulta de soporte
                                    </h4>

                                    <form id="formSoporte">

                                        <div class="mb-3">
                                            <label class="form-label">Asunto</label>
                                            <input
                                                type="text"
                                                name="asunto"
                                                class="form-control"
                                                placeholder="Ej: No puedo descargar un recurso"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Mensaje</label>
                                            <textarea
                                                name="mensaje"
                                                class="form-control"
                                                rows="5"
                                                placeholder="Cuéntanos qué ocurre..."
                                                required></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100">
                                            Enviar consulta
                                        </button>

                                    </form>
                                    <!-- Respuesta generada por AJAX tras enviar soporte -->
                                    <div id="soporteRespuesta" class="mt-3"></div>

                                </div>
                            </div>
                            <!-- Listado de consultas existentes del usuario -->
                            <div class="panel-card-soft">

                                <h4 class="mb-4">
                                    <i class="bi bi-life-preserver"></i>
                                    Mis consultas
                                </h4>

                                <?php if (empty($ticketsSoporte)): ?>

                                    <div class="panel-empty">
                                        No tienes consultas abiertas.
                                    </div>

                                <?php else: ?>

                                    <?php foreach ($ticketsSoporte as $ticket): ?>

                                        <div class="ticket-card-mini">

                                            <div>
                                                <strong>
                                                    <?= htmlspecialchars($ticket['asunto']) ?>
                                                </strong>

                                                <div class="small text-muted mt-1">
                                                    <?= date('d/m/Y H:i', strtotime($ticket['fecha'])) ?>
                                                    · <?= htmlspecialchars($ticket['estado']) ?>
                                                </div>
                                            </div>

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary btn-ver-ticket-usuario"
                                                data-ticket-id="<?= $ticket['id'] ?>"
                                                data-ticket-estado="<?= htmlspecialchars($ticket['estado']) ?>"
                                                data-asunto="<?= htmlspecialchars($ticket['asunto'], ENT_QUOTES) ?>">
                                                Ver conversación
                                            </button>

                                        </div>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                            </div>

                        </section>


                        <!-- SECCIÓN BUZÓN DE SUGERENCIAS 
                         Permite al usuario enviar ideas o propuestas.
                             No genera conversación como soporte.-->
                        <section id="section-sugerencias" class="panel-section">

                            <div class="sugerencias-layout">

                                <div class="sugerencias-form-card">

                                    <span class="sugerencias-pill">
                                        ✨ Estamos aquí para escucharte
                                    </span>

                                    <h2>
                                        Envíame un <span>mensaje mágico</span>
                                    </h2>

                                    <p>
                                        ¿Tienes alguna idea para mejorar la web, proponer un nuevo material
                                        o sugerir una ficha? Me encantará leerte.
                                    </p>
                                    <!-- Formulario AJAX de sugerencias -->
                                    <form id="formSugerencia">

                                        <div class="mb-3">
                                            <label class="form-label">Tu mensaje</label>

                                            <textarea
                                                name="mensaje"
                                                class="form-control"
                                                rows="6"
                                                placeholder="Escribe aquí tu duda, sugerencia o idea..."
                                                required></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-warning w-100">
                                            Enviar mensaje
                                            <i class="bi bi-send"></i>
                                        </button>

                                    </form>
                                    <!-- Respuesta generada por AJAX tras enviar sugerencia -->
                                    <div id="respuestaSugerencia" class="mt-3"></div>

                                </div>

                                <div class="sugerencias-info-card">

                                    <div class="sugerencias-avatar">
                                        <i class="bi bi-chat-heart"></i>
                                    </div>

                                    <h4>Buzón de ideas</h4>

                                    <p>
                                        Este buzón no genera una conversación. La autora leerá tus propuestas
                                        para mejorar contenidos y crear nuevos recursos.
                                    </p>

                                    <div class="sugerencias-mini-info">
                                        <i class="bi bi-lightbulb"></i>
                                        Sugerencias de materiales, mejoras o nuevas fichas.
                                    </div>

                                </div>

                            </div>

                        </section>

                    </div>

                </div>

            </div>

        </div>

    </section>


</main>


<!-- MODAL VER / RESPONDER CONVERSACIÓN DEL USUARIO 
  Permite ver el historial de mensajes de un ticket de soporte,
     responder y finalizar la consulta.-->
<div class="modal fade" id="modalTicketUsuario" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <!-- Cabecera del modal -->
            <div class="modal-header">

                <h5 id="modalTicketUsuarioTitulo" class="modal-title">
                    Conversación
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar">
                </button>

            </div>

            <!-- Cuerpo del modal -->
            <div class="modal-body">

                <!-- ID oculto del ticket seleccionado -->
                <input type="hidden" id="ticketIdUsuarioRespuesta">

                <!-- Historial de mensajes -->
                <div id="ticketMensajesUsuario" class="soporte-chat-admin mb-3">
                    Cargando mensajes...
                </div>

                <!-- Formulario para responder -->
                <form id="formResponderTicketUsuario">

                    <label class="form-label">
                        Responder
                    </label>

                    <textarea
                        id="mensajeRespuestaUsuario"
                        class="form-control"
                        rows="3"
                        placeholder="Escribe una respuesta si necesitas continuar la consulta..."
                        required></textarea>

                    <div class="d-flex gap-2 mt-3">

                        <button type="submit" class="btn btn-primary">
                            Enviar respuesta
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-danger"
                            id="btnFinalizarTicketUsuario">
                            Finalizar consulta
                        </button>

                    </div>

                </form>

                <!-- Respuesta AJAX -->
                <div id="respuestaTicketUsuario" class="mt-3"></div>

            </div>

        </div>

    </div>

</div>


<!--  MODAL: AÑADIR O EDITAR RESEÑA
     Permite al usuario valorar un recurso adquirido.-->

<div class="modal fade" id="modalResena" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="modalResenaTitulo">
                    Añadir reseña
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body">
                <!-- Formulario AJAX de reseña -->
                <form id="formResena">

                    <input type="hidden" id="resenaProductoId" name="producto_id">

                    <div class="mb-3">
                        <label class="form-label">Puntuación</label>

                        <select name="puntuacion" id="resenaPuntuacion" class="form-select" required>
                            <option value="">Selecciona puntuación</option>
                            <option value="5">★★★★★ Excelente</option>
                            <option value="4">★★★★ Muy bueno</option>
                            <option value="3">★★★ Correcto</option>
                            <option value="2">★★ Mejorable</option>
                            <option value="1">★ Malo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Comentario</label>

                        <textarea
                            name="comentario"
                            id="resenaComentario"
                            class="form-control"
                            rows="4"
                            placeholder="Escribe tu opinión sobre el recurso..."
                            required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Guardar reseña
                    </button>

                </form>
                <!-- Respuesta AJAX del guardado de reseña -->
                <div id="respuestaResena" class="mt-3"></div>

            </div>

        </div>

    </div>

</div>

<!--MODAL PARA VER FAVORITOS DEL USUARIO
Modal reutilizable para mostrar información adicional.
     En esta vista aparece definido, aunque su uso principal puede
     estar relacionado con scripts compartidos o funcionalidades admin.-->

<div class="modal fade" id="modalAdminUsuarios" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalAdminUsuariosTitulo"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="modalAdminUsuariosContenido">
            </div>

        </div>
    </div>
</div>
<!-- 
    MODAL DE ALERTA DE SEGURIDAD
    ---------------------------------------------------------
    Se muestra cuando el usuario pulsa:
    "No reconozco este acceso"

    Informa de que:
    - se registrará la IP como sospechosa
    - se cerrarán todas las sesiones
    - deberá cambiar la contraseña
-->
<div class="modal fade" id="modalAlertaSeguridad" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">

            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    Alerta de seguridad
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar">
                </button>
            </div>

            <div class="modal-body">

                <div id="contenidoModalAlertaSeguridad">

                    <p class="mb-2">
                        Has indicado que no reconoces este acceso.
                    </p>

                    <div class="alert alert-warning mb-3">
                        Se registrará la IP como sospechosa, se cerrarán todas las sesiones activas
                        y será necesario cambiar la contraseña para volver a acceder.
                    </div>

                    <p class="small text-muted mb-0">
                        Esta acción está pensada para proteger tu cuenta si crees que otra persona
                        ha podido acceder sin autorización.
                    </p>

                </div>

            </div>

            <div class="modal-footer border-0">

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button
                    type="button"
                    class="btn btn-danger"
                    id="btnConfirmarAlertaSeguridad"
                    onclick="enviarAlertaSeguridad()">
                    Registrar IP y cerrar sesiones
                </button>

            </div>

        </div>
    </div>
</div>

<!-- 
   SCRIPT ESPECÍFICO DEL PANEL DE USUARIO
     usuario.js gestiona:
     - navegación entre secciones;
     - creación de soporte;
     - lectura y respuesta de tickets;
     - finalización de tickets;
     - envío de sugerencias;
     - apertura y envío de reseñas.-->
<script src="/UNRINCONDEPT/static/js/usuario.js"></script>
<script src="/UNRINCONDEPT/static/js/tienda.js"></script>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>