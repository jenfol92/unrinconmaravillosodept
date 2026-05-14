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


                <!-- CONTENIDO DERECHO -->

                <div class="col-12 col-lg-9">

                    <div class="panel-content">


                        <!-- SECCIÓN MIS RECURSOS ADQUIRIDOS -->

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

                                <!-- Tabla responsive -->
                                <div class="table-responsive">

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

                                            <?php foreach ($productosComprados as $producto): ?>

                                                <tr>
                                                    <td>
                                                        <?= date('d/m/Y', strtotime($producto['fecha_compra'])) ?>
                                                    </td>
                                                    <!-- Imagen + título -->
                                                    <td>
                                                        <div class="d-flex align-items-center gap-3">

                                                            <img
                                                                src="/UNRINCONDEPT/static/images/img/<?= htmlspecialchars($producto['imagen']) ?>"
                                                                class="panel-product-img">

                                                            <div>
                                                                <strong>
                                                                    <?= htmlspecialchars($producto['titulo']) ?>
                                                                </strong>

                                                                <div class="text-muted small">
                                                                   Recurso adquirido #<?= $producto['descarga_id'] ?>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </td>

                                                    <!-- Precio unitario -->
                                                    <td>
                                                        <strong>
                                                            <?= number_format((float)$producto['precio'], 2) ?> €
                                                        </strong>
                                                    </td>

                                                    <td>
                                                        <?= (int)($producto['numero_descargas'] ?? 0) ?> /
                                                        <?= (int)($producto['max_descargas'] ?? 0) ?>
                                                    </td>

                                                    <!-- Acciones -->
                                                    <td class="text-end">

                                                        <!-- Descargar archivo -->
                                                        <a
                                                            href="/UNRINCONDEPT/public/descargar.php?id=<?= $producto['descarga_id'] ?>"
                                                            class="btn btn-sm btn-success"
                                                            title="Descargar recurso">
                                                            <i class="bi bi-download"></i>
                                                            Descargar
                                                        </a>

                                                        <!-- Ver detalle -->
                                                        <a
                                                            href="/UNRINCONDEPT/public/detalle.php?id=<?= $producto['id'] ?>"
                                                            class="btn btn-sm btn-outline-primary"
                                                            title="Ver detalle">
                                                            <i class="bi bi-eye"></i>
                                                        </a>

                                                        <!-- Añadir reseña -->
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-warning btn-abrir-resena"
                                                            data-producto-id="<?= (int)$producto['id'] ?>"
                                                            data-producto-titulo="<?= htmlspecialchars($producto['titulo'], ENT_QUOTES) ?>"
                                                            title="Añadir o editar reseña">
                                                            <i class="bi bi-star"></i>
                                                            Reseña
                                                        </button>

                                                    </td>

                                                </tr>

                                            <?php endforeach; ?>

                                        </tbody>

                                    </table>

                                </div>

                            <?php endif; ?>

                        </section>



                        <!-- SECCIÓN FAVORITOS -->

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



                        <!-- SECCIÓN DATOS DE CUENTA -->

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



                        <!-- SECCIÓN SEGURIDAD -->

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
                        <!-- SECCIÓN SOPORTE -->
                        <section id="section-soporte" class="panel-section">

                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                                <div>
                                    <h2>Centro de Soporte</h2>
                                    <p class="text-muted mb-0">
                                        Consulta tus conversaciones o abre una nueva incidencia.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#nuevoSoporteBox">
                                    <i class="bi bi-plus-circle"></i>
                                    Nueva consulta
                                </button>
                            </div>

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

                                    <div id="soporteRespuesta" class="mt-3"></div>

                                </div>
                            </div>

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


                        <!-- SECCIÓN BUZÓN DE SUGERENCIAS -->
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


<!-- MODAL VER / RESPONDER CONVERSACIÓN DEL USUARIO -->
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


<!-- Modal añadir reseña -->

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

                <div id="respuestaResena" class="mt-3"></div>

            </div>

        </div>

    </div>

</div>

<!-- JS específico del panel de usuario -->
<script src="/UNRINCONDEPT/static/js/usuario.js"></script>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>