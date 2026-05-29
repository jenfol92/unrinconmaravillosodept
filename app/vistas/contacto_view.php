<?php

/**
 * Vista: contacto_view.php
 * ---------------------------------------------------------
 * Muestra la página pública de contacto de la web.
 *
 * Esta vista puede utilizarse de dos formas:
 *
 * 1. Contacto general:
 *    - El usuario accede directamente a la página de contacto.
 *    - No existe producto asociado.
 *
 * 2. Contacto sobre un producto:
 *    - El usuario llega desde la ficha de un producto.
 *    - El controlador carga la variable $producto.
 *    - La vista muestra una alerta indicando el material consultado.
 *    - Se añade un campo oculto producto_id al formulario.
 *
 * Variable recibida desde ContactoController:
 *
 * - $producto:
 *   Puede ser null o contener los datos del producto sobre el que
 *   el usuario desea hacer una consulta.
 *
 * Funcionalidades principales:
 *
 * - Mostrar formulario público de contacto.
 * - Mostrar información del producto si existe.
 * - Enviar nombre, email, asunto y mensaje.
 * - Enviar producto_id oculto cuando la consulta está asociada a un producto.
 * - Mostrar una zona lateral con imagen o mensaje visual.
 * - Cargar contacto.js para procesar el formulario mediante JavaScript/AJAX.
 *
 * Seguridad:
 *
 * - Se utiliza htmlspecialchars() al imprimir datos dinámicos en HTML.
 * - El producto_id se convierte a entero con (int).
 */
?>

<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<!--
     PÁGINA DE CONTACTO
     Contenedor principal de la vista pública de contacto.
 -->
<main class="contacto-page">

    <!--
         HERO DE CONTACTO
         Sección principal con el formulario a la izquierda
         y la información visual/contacto a la derecha.
    -->
    <section class="contacto-hero">

        <div class="container">

            <div class="row align-items-center g-5">

                <!-- 
                     COLUMNA IZQUIERDA: FORMULARIO DE CONTACTO
                     Contiene el formulario público que permite enviar
                     dudas, sugerencias o consultas sobre un producto.
                 -->
                <div class="col-12 col-lg-7">

                    <div class="contacto-form-wrap">

                        <!-- Etiqueta visual superior -->
                        <span class="contacto-pill">
                            ✨ Estamos aquí para ayudarte
                        </span>

                        <!-- Título principal de la sección -->
                        <h1>
                            Envíame un <span>mensaje mágico</span>
                        </h1>

                        <!-- Texto introductorio -->
                        <p class="contacto-intro">
                            ¿Tienes alguna duda sobre los materiales? ¿O quizás una sugerencia
                            para una nueva ficha? ¡Me encantará leerte!
                        </p>

                        <!-- 
                             ALERTA DE PRODUCTO ASOCIADO
                             Si $producto contiene datos, significa que el usuario
                             está realizando una consulta sobre un material concreto.
                       -->
                        <?php if (!empty($producto)): ?>
                            <div class="contacto-product-alert">
                                <strong>Consulta sobre el material:</strong>
                                <?= htmlspecialchars($producto['titulo']) ?>
                            </div>
                        <?php endif; ?>

                        <!-- 
                             FORMULARIO DE CONTACTO PÚBLICO
                             El formulario no tiene action directo porque se gestiona
                             desde contacto.js mediante JavaScript/AJAX.
                             
                             El atributo novalidate permite controlar la validación
                             desde nuestro propio JavaScript.
                        -->
                        <form id="formContactoPublico" class="contacto-form" novalidate>

                            <!--
                                 PRODUCTO ASOCIADO
                                 Si existe producto, enviamos su ID en un campo oculto
                                 para que el mensaje quede relacionado con ese recurso.
                           -->
                            <?php if (!empty($producto)): ?>
                                <input type="hidden" name="producto_id" value="<?= (int)$producto['id'] ?>">
                            <?php endif; ?>

                            <div class="row g-3">

                                <!-- Campo nombre -->
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

                                <!-- Campo email -->
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

                                <!-- 
                                     Campo asunto.
                                     Si hay producto asociado, se rellena automáticamente
                                     con "Consulta sobre: [título del producto]".
                                -->
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

                                <!-- Campo mensaje -->
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

                                <!-- Botón de envío -->
                                <div class="col-12">
                                    <button type="submit" class="btn contacto-btn">
                                        Enviar mensaje
                                        <i class="bi bi-send"></i>
                                    </button>
                                </div>

                            </div>

                        </form>

                        <!-- 
                             RESPUESTA DEL FORMULARIO
                             contacto.js utiliza este contenedor para mostrar
                             mensajes de éxito o error tras enviar el formulario.
                    -->
                        <div id="respuestaContactoPublico" class="mt-3"></div>

                    </div>

                </div>

                <!-- 
                     COLUMNA DERECHA: INFORMACIÓN VISUAL
                     Muestra imagen del producto consultado o una imagen
                     alternativa si no hay producto asociado.
                -->
                <div class="col-12 col-lg-5">

                    <div class="contacto-side">

                        <!-- Tarjeta visual -->
                        <div class="contacto-img-card">

                            <!-- 
                                 IMAGEN DEL PRODUCTO
                                 Si hay producto y tiene imagen, se muestra como apoyo
                                 visual a la consulta.
                             -->
                            <?php if (!empty($producto) && !empty($producto['imagen'])): ?>

                                <img
                                    src="<?= BASE_URL ?>static/images/img/<?= htmlspecialchars($producto['imagen']) ?>"
                                    alt="<?= htmlspecialchars($producto['titulo']) ?>">

                                <div class="contacto-img-caption">
                                    ¿Tienes dudas sobre este material? Te ayudo encantada.
                                </div>

                            <?php else: ?>

                                <!-- 
                                     IMAGEN / BLOQUE ALTERNATIVO
                                     Se muestra cuando la consulta no está asociada
                                     a ningún producto concreto.
                               -->
                                <div class="contacto-placeholder-img">
                                    <i class="bi bi-mortarboard"></i>
                                    <span>
                                        ¡Juntos haremos que aprender sea una aventura!
                                    </span>
                                </div>

                            <?php endif; ?>

                        </div>

                        <!-- 
                             TARJETA DE DATOS DE CONTACTO
                             Información estática de apoyo para el usuario.
                         -->
                        <div class="contacto-info-card">

                            <h3>Datos de Contacto</h3>

                            <!-- Correo electrónico -->
                            <div class="contacto-info-item">
                                <div class="contacto-info-icon">
                                    <i class="bi bi-envelope"></i>
                                </div>

                                <div>
                                    <strong>Correo Electrónico</strong>
                                    <p>
                                        unrinconmaravillosodept@gmail.com<br>
                                        <small>Te responderemos lo antes posible.</small>
                                    </p>
                                </div>
                            </div>

                            <!-- Información sobre dudas de materiales -->
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

                            <!-- Información sobre recursos educativos -->
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

<!-- 
     SCRIPT DE CONTACTO
     contacto.js gestiona el envío del formulario público.
     
     Elementos principales que utiliza:
     - formContactoPublico
     - respuestaContactoPublico
 -->

<script src="<?= BASE_URL ?>static/js/contacto.js"></script>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>