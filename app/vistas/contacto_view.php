<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<main class="contacto-page">

    <section class="contacto-hero">

        <div class="container">

            <div class="row align-items-center g-5">

                <!-- IZQUIERDA: FORMULARIO -->
                <div class="col-12 col-lg-7">

                    <div class="contacto-form-wrap">

                        <span class="contacto-pill">
                            ✨ Estamos aquí para ayudarte
                        </span>

                        <h1>
                            Envíame un <span>mensaje mágico</span>
                        </h1>

                        <p class="contacto-intro">
                            ¿Tienes alguna duda sobre los materiales? ¿O quizás una sugerencia
                            para una nueva ficha? ¡Me encantará leerte!
                        </p>

                        <?php if (!empty($producto)): ?>
                            <div class="contacto-product-alert">
                                <strong>Consulta sobre el material:</strong>
                                <?= htmlspecialchars($producto['titulo']) ?>
                            </div>
                        <?php endif; ?>

                        <form id="formContactoPublico" class="contacto-form" novalidate>

                            <?php if (!empty($producto)): ?>
                                <input type="hidden" name="producto_id" value="<?= (int)$producto['id'] ?>">
                            <?php endif; ?>

                            <div class="row g-3">

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Tu nombre</label>
                                    <div class="contacto-input-icon">
                                        <i class="bi bi-person"></i>
                                        <input
                                            type="text"
                                            name="nombre"
                                            class="form-control"
                                            placeholder="Ej. María García"
                                            required>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Tu email</label>
                                    <div class="contacto-input-icon">
                                        <i class="bi bi-envelope"></i>
                                        <input
                                            type="email"
                                            name="email"
                                            class="form-control"
                                            placeholder="tu@email.com"
                                            required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Asunto</label>
                                    <input
                                        type="text"
                                        name="asunto"
                                        class="form-control"
                                        value="<?= !empty($producto) ? 'Consulta sobre: ' . htmlspecialchars($producto['titulo']) : '' ?>"
                                        placeholder="¿De qué trata tu mensaje?"
                                        required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Tu mensaje</label>
                                    <div class="contacto-textarea-icon">
                                        <i class="bi bi-chat-left"></i>
                                        <textarea
                                            name="mensaje"
                                            class="form-control"
                                            rows="6"
                                            placeholder="Escribe aquí tu duda, sugerencia o saludo..."
                                            required></textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn contacto-btn">
                                        Enviar mensaje
                                        <i class="bi bi-send"></i>
                                    </button>
                                </div>

                            </div>

                        </form>

                        <div id="respuestaContactoPublico" class="mt-3"></div>

                    </div>

                </div>

                <!-- DERECHA: INFO -->
                <div class="col-12 col-lg-5">

                    <div class="contacto-side">

                        <div class="contacto-img-card">

                            <?php if (!empty($producto) && !empty($producto['imagen'])): ?>

                                <img
                                    src="/UNRINCONDEPT/static/images/img/<?= htmlspecialchars($producto['imagen']) ?>"
                                    alt="<?= htmlspecialchars($producto['titulo']) ?>">

                                <div class="contacto-img-caption">
                                    ¿Tienes dudas sobre este material? Te ayudo encantada.
                                </div>

                            <?php else: ?>

                                <div class="contacto-placeholder-img">
                                    <i class="bi bi-mortarboard"></i>
                                    <span>
                                        ¡Juntos haremos que aprender sea una aventura!
                                    </span>
                                </div>

                            <?php endif; ?>

                        </div>

                        <div class="contacto-info-card">

                            <h3>Datos de Contacto</h3>

                            <div class="contacto-info-item">
                                <div class="contacto-info-icon">
                                    <i class="bi bi-envelope"></i>
                                </div>

                                <div>
                                    <strong>Correo Electrónico</strong>
                                    <p>
                                        hola@unrinconmaravilloso.com<br>
                                        <small>Te responderemos lo antes posible.</small>
                                    </p>
                                </div>
                            </div>

                            <div class="contacto-info-item">
                                <div class="contacto-info-icon">
                                    <i class="bi bi-chat-heart"></i>
                                </div>

                                <div>
                                    <strong>Dudas sobre materiales</strong>
                                    <p>
                                        Consultas, mejoras o sugerencias.
                                    </p>
                                </div>
                            </div>

                            <div class="contacto-info-item">
                                <div class="contacto-info-icon">
                                    <i class="bi bi-stars"></i>
                                </div>

                                <div>
                                    <strong>Recursos educativos</strong>
                                    <p>
                                        Materiales pensados para aprender jugando.
                                    </p>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</main>

<script src="/UNRINCONDEPT/static/js/contacto.js"></script>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>