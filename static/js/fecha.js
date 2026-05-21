/**
 * Archivo: fecha.js
 * ---------------------------------------------------------
 * Este archivo utiliza el objeto Date de JavaScript para:
 * - Mostrar automáticamente el año actual en el footer.
 * - Mostrar la fecha actual en formato español.
 * - Mostrar la última visita del usuario usando localStorage.
 * - Mostrar la última actualización de la tienda.
 *
 * También utiliza DOMContentLoaded para ejecutar el código
 * cuando el documento HTML ya se ha cargado.
 */

document.addEventListener("DOMContentLoaded", function () {
    const fechaActual = new Date();

    insertarAnioActual(fechaActual);
    insertarFechaActual(fechaActual);
    gestionarUltimaVisita(fechaActual);
    insertarUltimaActualizacionTienda();
});


/**
 * Inserta el año actual dentro del elemento con id footer-anio.
 *
 * @param {Date} fecha Objeto Date con la fecha actual.
 */
function insertarAnioActual(fecha) {
    const elementoAnio = document.getElementById("footer-anio");

    if (elementoAnio) {
        elementoAnio.textContent = fecha.getFullYear();
    }
}


/**
 * Inserta la fecha actual dentro del elemento con id footer-fecha-actual.
 *
 * @param {Date} fecha Objeto Date con la fecha actual.
 */
function insertarFechaActual(fecha) {
    const elementoFecha = document.getElementById("footer-fecha-actual");

    if (elementoFecha) {
        elementoFecha.textContent = formatearFechaCompleta(fecha);
    }
}


/**
 * Muestra la última visita del usuario.
 *
 * Para ello se utiliza localStorage:
 * - Si existe una visita anterior, se muestra en el footer.
 * - Si no existe, se muestra un mensaje de bienvenida.
 * - Después se guarda la visita actual para la próxima vez.
 *
 * @param {Date} fechaActual Objeto Date con la fecha y hora actual.
 */
function gestionarUltimaVisita(fechaActual) {
    const elementoUltimaVisita = document.getElementById("footer-ultima-visita");

    if (!elementoUltimaVisita) {
        return;
    }

    const ultimaVisitaGuardada = localStorage.getItem("ultimaVisita");

    if (ultimaVisitaGuardada) {
        const fechaUltimaVisita = new Date(ultimaVisitaGuardada);
        elementoUltimaVisita.textContent = formatearFechaConHora(fechaUltimaVisita);
    } else {
        elementoUltimaVisita.textContent = "es tu primera visita";
    }

    localStorage.setItem("ultimaVisita", fechaActual.toISOString());
}


/**
 * Muestra la última actualización de la tienda.
 *
 * La fecha se recoge desde el atributo data-fecha-actualizacion
 * del elemento HTML con id footer-ultima-actualizacion.
 */
function insertarUltimaActualizacionTienda() {
    const elementoActualizacion = document.getElementById("footer-ultima-actualizacion");

    if (!elementoActualizacion) {
        return;
    }

    const fechaActualizacion = elementoActualizacion.dataset.fechaActualizacion;

    if (!fechaActualizacion) {
        elementoActualizacion.textContent = "pendiente de actualizar";
        return;
    }

    const fecha = new Date(fechaActualizacion);

    if (isNaN(fecha.getTime())) {
        elementoActualizacion.textContent = "fecha no disponible";
        return;
    }

    elementoActualizacion.textContent = formatearFechaCompleta(fecha);
}


/**
 * Formatea una fecha en español.
 *
 * Ejemplo:
 * jueves, 21 de mayo de 2026
 *
 * @param {Date} fecha Fecha que se desea formatear.
 * @returns {string} Fecha formateada.
 */
function formatearFechaCompleta(fecha) {
    const opcionesFecha = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric"
    };

    return fecha.toLocaleDateString("es-ES", opcionesFecha);
}


/**
 * Formatea una fecha incluyendo hora y minutos.
 *
 * Ejemplo:
 * jueves, 21 de mayo de 2026, 10:42
 *
 * @param {Date} fecha Fecha que se desea formatear.
 * @returns {string} Fecha y hora formateadas.
 */
function formatearFechaConHora(fecha) {
    const opcionesFechaHora = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit"
    };

    return fecha.toLocaleDateString("es-ES", opcionesFechaHora);
}