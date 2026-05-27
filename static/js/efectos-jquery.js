/**
 * Archivo: efectos-jquery.js
 * ---------------------------------------------------------
 * Funcionalidad:
 * - Mostrar y ocultar filtros del panel admin usando jQuery.
 * - Animar mensajes de éxito, error, aviso e información.
 * - Mejorar visualmente el chat de soporte del administrador.
 *
 * Requisitos DWEC cubiertos:
 * - Uso de jQuery.
 * - Uso de efectos slideToggle, fadeIn, fadeOut y animate.
 * - Mostrar y ocultar partes de una página tras cargar.
 * - Uso de eventos para definir comportamientos.
 * - Manipulación del DOM mediante jQuery.
 */

$(document).ready(function () {
    inicializarFiltrosAdminJquery();
    inicializarAnimacionMensajes();
    inicializarChatSoporteAdmin();
});


/* 
   1. FILTROS ADMIN CON JQUERY

 * Inicializa los botones de filtros del panel admin.
 
 * En tu HTML ya tienes botones con:
 * - class="admin-filtros-toggle"
 * - data-filter-target="idDelPanel"
 
 * Ejemplo real:
 * data-filter-target="filtrosProductosBody"
 * data-filter-target="filtrosGratuitosBody"
 */
function inicializarFiltrosAdminJquery() {

    $(".admin-filtros-toggle").each(function () {

        const boton = $(this);
        const targetId = boton.data("filter-target");
        const panel = $("#" + targetId);

        if (panel.length === 0) {
            return;
        }

        /*
            En escritorio dejamos los filtros visibles.
            En móvil los ocultamos para que se puedan desplegar.
        */
        if ($(window).width() < 768) {
            panel.hide().removeClass("is-open");
            boton.removeClass("is-open");
            boton.attr("aria-expanded", "false");
        } else {
            panel.show().addClass("is-open");
            boton.addClass("is-open");
            boton.attr("aria-expanded", "true");
        }
    });


    $(".admin-filtros-toggle").on("click", function () {

        const boton = $(this);
        const targetId = boton.data("filter-target");
        const panel = $("#" + targetId);

        if (panel.length === 0) {
            return;
        }

        panel.stop(true, true).slideToggle(300, function () {

            const estaVisible = panel.is(":visible");

            panel.toggleClass("is-open", estaVisible);
            boton.toggleClass("is-open", estaVisible);
            boton.attr("aria-expanded", estaVisible ? "true" : "false");

            actualizarTextoAccesibleFiltros(boton, estaVisible);
        });

    });

}


/**
 * Cambia el title del botón según el estado del panel.
 *
 * @param {jQuery} boton Botón pulsado.
 * @param {boolean} estaVisible Indica si el panel está visible.
 */
function actualizarTextoAccesibleFiltros(boton, estaVisible) {

    if (estaVisible) {
        boton.attr("title", "Ocultar filtros");
    } else {
        boton.attr("title", "Mostrar filtros");
    }

}


/* 
   2. MENSAJES ANIMADOS DE ÉXITO / ERROR

 * Inicializa la animación de mensajes.
 *
 * Como tu admin.js usa fetch() y escribe respuestas con innerHTML,
 * aquí usamos MutationObserver para detectar automáticamente
 * nuevos mensajes .alert que aparezcan en la página.
 */
function inicializarAnimacionMensajes() {

    animarAlertasExistentes();

    const observador = new MutationObserver(function (mutaciones) {

        mutaciones.forEach(function (mutacion) {

            $(mutacion.addedNodes).each(function () {

                const nodo = $(this);

                if (nodo.hasClass("alert")) {
                    animarAlerta(nodo);
                }

                nodo.find(".alert").each(function () {
                    animarAlerta($(this));
                });

            });

        });

    });

    observador.observe(document.body, {
        childList: true,
        subtree: true
    });

}


/**
 * Anima las alertas que ya existen cuando carga la página.
 */
function animarAlertasExistentes() {

    $(".alert").each(function () {
        animarAlerta($(this));
    });

}


/**
 * Aplica efecto visual a una alerta concreta.
 *
 * @param {jQuery} alerta Elemento alert de Bootstrap.
 */
function animarAlerta(alerta) {

    if (alerta.data("animada") === true) {
        return;
    }

    alerta.data("animada", true);

    alerta.hide().fadeIn(350);

    /*
        Las alertas informativas o de éxito desaparecen solas.
        Las de error se quedan visibles para que el usuario pueda leerlas.
    */
    if (alerta.hasClass("alert-success") || alerta.hasClass("alert-info")) {
        alerta.delay(3500).fadeOut(600);
    }

}


/**
 * Función global opcional.
 *
 * Sirve por si desde otro JS quieres lanzar un mensaje animado.
 *
 * Ejemplo:
 * mostrarMensajeJquery("#respuestaGuardarProducto", "success", "Producto guardado.");
 */
window.mostrarMensajeJquery = function (selector, tipo, texto) {

    const contenedor = $(selector);

    if (contenedor.length === 0) {
        return;
    }

    const alerta = $(`
        <div class="alert alert-${tipo} mensaje-flash">
            ${texto}
        </div>
    `);

    contenedor.html(alerta);
    animarAlerta(alerta);

};


/* 
   3. CHAT DE SOPORTE ADMIN

 * Mejora visualmente el soporte admin.
 *
 * Tu panel ya tiene:
 * - modalResponderTicket
 * - ticketMensajes
 * - formResponderTicket
 * - mensajeRespuestaTicket
 */
function inicializarChatSoporteAdmin() {

    const modalSoporte = $("#modalResponderTicket");

    if (modalSoporte.length === 0) {
        return;
    }

    /*
        Cuando se abre el modal, se enfoca el textarea
        y se baja al último mensaje.
    */
    modalSoporte.on("shown.bs.modal", function () {

        $("#mensajeRespuestaTicket").trigger("focus");

        setTimeout(function () {
            bajarAlUltimoMensajeAdmin();
        }, 250);

    });


    /*
        Observamos cambios dentro del contenedor de mensajes.
        Cuando admin.js carga mensajes por AJAX, los animamos.
    */
    const contenedorMensajes = document.getElementById("ticketMensajes");

    if (contenedorMensajes) {

        const observadorChat = new MutationObserver(function () {
            animarMensajesChatAdmin();
            bajarAlUltimoMensajeAdmin();
        });

        observadorChat.observe(contenedorMensajes, {
            childList: true,
            subtree: true
        });

    }


    /*
        Al enviar respuesta, mostramos una pequeña animación visual
        en el botón para dar sensación de acción.
    */
    $("#formResponderTicket").on("submit", function () {

        const botonSubmit = $(this).find("button[type='submit']");

        if (botonSubmit.length > 0) {
            botonSubmit
                .prop("disabled", true)
                .addClass("opacity-75")
                .html('<span class="spinner-border spinner-border-sm me-2"></span>Enviando...');
        }

        setTimeout(function () {
            botonSubmit
                .prop("disabled", false)
                .removeClass("opacity-75")
                .html("Enviar respuesta");
        }, 1200);

    });

}


/**
 * Anima los mensajes del chat del admin.
 */
function animarMensajesChatAdmin() {

    $("#ticketMensajes .soporte-msg").each(function () {

        const mensaje = $(this);

        if (mensaje.data("animado") === true) {
            return;
        }

        mensaje.data("animado", true);
        mensaje.hide().fadeIn(300);

    });

}


/**
 * Baja automáticamente al último mensaje del chat.
 */
function bajarAlUltimoMensajeAdmin() {

    const contenedor = $("#ticketMensajes");

    if (contenedor.length === 0) {
        return;
    }

    contenedor.stop(true).animate({
        scrollTop: contenedor.prop("scrollHeight")
    }, 350);

}