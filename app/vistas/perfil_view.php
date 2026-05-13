<?php
// Vista del perfil de usuario.
// Variables que deberían venir desde UsuarioController:
// $usuario   → datos del usuario
// $favoritos → productos favoritos del usuario
?>

<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<main class="perfil-page">

    <!-- =============================== -->
    <!-- CABECERA DEL PERFIL -->
    <!-- =============================== -->
    <section class="perfil-hero">

        <div class="container">

            <div class="d-flex align-items-center gap-4 flex-wrap">

                <!-- Datos principales -->
                <div>
                    <h1>
                        ¡Hola, <?= htmlspecialchars($usuario['nombre'] ?? 'Usuario') ?>!
                    </h1>

                    <p class="mb-0">
                        Miembro desde
                    </p>

                    <small>
                        Bienvenido a tu panel personal
                    </small>
                </div>

            </div>

        </div>

    </section>


    <!-- SIDEBAR PERFIL USUARIO-->

    <section class="user-panel">

        <div class="container">

            <div class="row g-4">

                <!-- =============================== -->
                <!-- SIDEBAR IZQUIERDO -->
                <!-- =============================== -->
                <div class="col-12 col-lg-3">

                    <aside class="panel-sidebar">

                        <!-- Menú del panel -->
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

                        <!-- Bloque de ayuda -->
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
                                class="btn btn-success w-100"
                                data-bs-toggle="modal"
                                data-bs-target="#modalSoporte">
                                Soporte Técnico
                            </button>

                        </div>
                        <?php if (empty($ticketsSoporte)): ?>
                            <p>No tienes consultas de soporte.</p>
                        <?php else: ?>
                            <?php foreach ($ticketsSoporte as $ticket): ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <strong><?= htmlspecialchars($ticket['asunto']) ?></strong>
                                        <span class="badge bg-info"><?= htmlspecialchars($ticket['estado']) ?></span>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-primary btn-ver-ticket-usuario"
                                            data-ticket-id="<?= $ticket['id'] ?>"
                                            data-asunto="<?= htmlspecialchars($ticket['asunto'], ENT_QUOTES) ?>">
                                            Ver conversación
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </aside>
                    <section id="section-soporte" class="panel-section">

                        <h2>Mis consultas de soporte</h2>

                        <?php if (empty($ticketsSoporte)): ?>

                            <div class="panel-empty">
                                No tienes consultas de soporte todavía.
                            </div>

                        <?php else: ?>

                            <?php foreach ($ticketsSoporte as $ticket): ?>

                                <div class="card mb-3">

                                    <div class="card-body d-flex justify-content-between align-items-center">

                                        <div>
                                            <strong>
                                                <?= htmlspecialchars($ticket['asunto']) ?>
                                            </strong>

                                            <div class="text-muted small">
                                                <?= date('d/m/Y H:i', strtotime($ticket['fecha'])) ?>
                                                · <?= htmlspecialchars($ticket['estado']) ?>
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-primary btn-ver-ticket-usuario"
                                            data-ticket-id="<?= $ticket['id'] ?>"
                                            data-asunto="<?= htmlspecialchars($ticket['asunto'], ENT_QUOTES) ?>">
                                            Ver conversación
                                        </button>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </section>

                </div>


                <!-- =============================== -->
                <!-- CONTENIDO DERECHO -->
                <!-- =============================== -->
                <div class="col-12 col-lg-9">

                    <div class="panel-content">

                        <!-- =============================== -->
                        <!-- SECCIÓN MIS DESCARGAS -->
                        <!-- =============================== -->
                        <section
                            id="section-descargas"
                            class="panel-section active">

                            <h2>
                                Historial de Descargas
                            </h2>

                            <p>
                                Aquí tienes todos los recursos que has adquirido.
                                Puedes volver a descargarlos cuando quieras.
                            </p>

                            <!-- De momento placeholder -->
                            <div class="panel-empty">
                                Próximamente cargaremos aquí tus descargas.
                            </div>

                        </section>


                        <!-- =============================== -->
                        <!-- SECCIÓN FAVORITOS -->
                        <!-- =============================== -->
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

                                                <tr>

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

                                                        <!-- Añadir al carrito -->
                                                        <button
                                                            type="button"
                                                            class="btn btn-outline-success btn-sm"
                                                            onclick="gestionarSesion(<?= $fav['id'] ?>, 'add_carrito')"
                                                            title="Añadir al carrito">
                                                            <i class="bi bi-cart"></i>
                                                        </button>

                                                    </td>

                                                </tr>

                                            <?php endforeach; ?>

                                        </tbody>

                                    </table>

                                </div>

                            <?php endif; ?>

                        </section>


                        <!-- =============================== -->
                        <!-- SECCIÓN DATOS DE CUENTA -->
                        <!-- =============================== -->
                        <section
                            id="section-cuenta"
                            class="panel-section">

                            <h2>
                                Datos de Cuenta
                            </h2>

                            <p>
                                Consulta y modifica tus datos personales.
                            </p>

                            <form class="panel-form">

                                <div class="row g-3">

                                    <!-- Nombre -->
                                    <div class="col-12 col-md-6">

                                        <label class="form-label">
                                            Nombre
                                        </label>

                                        <div class="input-group">

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>"
                                                disabled>

                                            <button
                                                class="btn btn-outline-secondary"
                                                type="button"
                                                title="Editar nombre">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                        </div>

                                    </div>

                                    <!-- Email -->
                                    <div class="col-12 col-md-6">

                                        <label class="form-label">
                                            Email
                                        </label>

                                        <div class="input-group">

                                            <input
                                                type="email"
                                                class="form-control"
                                                value="<?= htmlspecialchars($usuario['email'] ?? '') ?>"
                                                disabled>

                                            <button
                                                class="btn btn-outline-secondary"
                                                type="button"
                                                title="Editar email">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </form>

                        </section>


                        <!-- =============================== -->
                        <!-- SECCIÓN SEGURIDAD -->
                        <!-- =============================== -->
                        <section
                            id="section-seguridad"
                            class="panel-section">

                            <h2>
                                Seguridad
                            </h2>

                            <p>
                                Gestiona la seguridad de tu cuenta.
                            </p>

                            <!-- Cambiar contraseña -->
                            <div class="security-card">

                                <h5>
                                    <i class="bi bi-key"></i>
                                    Cambiar contraseña
                                </h5>

                                <p>
                                    Puedes actualizar tu contraseña para mantener tu cuenta protegida.
                                </p>

                                <button class="btn btn-primary">
                                    Cambiar contraseña
                                </button>

                            </div>

                            <!-- Último acceso -->
                            <div class="security-card">

                                <h5>
                                    <i class="bi bi-clock-history"></i>
                                    Último acceso
                                </h5>

                                <p class="mb-0">
                                    Aquí podrás mostrar la última fecha de inicio de sesión del usuario.
                                </p>

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

                    </div>

                </div>

            </div>

        </div>

    </section>

</main>


<!-- =============================== -->
<!-- MODAL SOPORTE TÉCNICO -->
<!-- =============================== -->
<div class="modal fade" id="modalSoporte" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content soporte-modal">

            <!-- Cabecera modal -->
            <div class="modal-header">

                <h5 class="modal-title">
                    <i class="bi bi-chat-dots"></i>
                    Soporte Técnico
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar"></button>

            </div>

            <!-- Cuerpo modal -->
            <div class="modal-body">

                <!-- Formulario para crear ticket -->
                <form id="formSoporte">

                    <!-- Asunto -->
                    <div class="mb-3">

                        <label class="form-label">
                            Asunto
                        </label>

                        <input
                            type="text"
                            name="asunto"
                            class="form-control"
                            placeholder="Ej: No puedo descargar un recurso"
                            required>

                    </div>

                    <!-- Mensaje -->
                    <div class="mb-3">

                        <label class="form-label">
                            Mensaje
                        </label>

                        <textarea
                            name="mensaje"
                            class="form-control"
                            rows="4"
                            placeholder="Explícanos qué problema tienes..."
                            required></textarea>

                    </div>

                    <!-- Enviar -->
                    <button
                        type="submit"
                        class="btn btn-primary">
                        Enviar mensaje
                    </button>

                </form>

                <hr>

                <!-- Respuesta AJAX -->
                <div id="soporteRespuesta"></div>

            </div>

        </div>

    </div>

</div>
<div class="modal fade" id="modalTicketUsuario" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <div class="modal-header">
                <h5 id="modalTicketUsuarioTitulo" class="modal-title">
                    Conversación
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div id="ticketMensajesUsuario" class="soporte-chat-admin">
                    Cargando mensajes...
                </div>

            </div>

        </div>

    </div>

</div>

<!-- JS específico del panel de usuario -->
<script src="/UNRINCONDEPT/static/js/usuario.js"></script>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>