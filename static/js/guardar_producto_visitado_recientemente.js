/**
 * PRODUCTO RECIENTE - GUARDAR COOKIE
 * ---------------------------------------------------------
 * Este archivo se carga solo en la página de detalle.
 *
 * Finalidad:
 * - Guardar en una cookie funcional el ID del producto visitado.
 * - La cookie se usará después en la tienda para mostrar
 *   "Últimos recursos vistos".
 *
 * Privacidad:
 * - Solo guarda IDs de productos.
 * - No guarda nombre, email, IP ni datos personales.
 */

document.addEventListener("DOMContentLoaded", function () {

    const detalle = document.getElementById("productoDetalleActual");

    if (!detalle) return;

    const productoId = String(detalle.dataset.productoId || "");

    if (!productoId) return;

    guardarProductoReciente(productoId);
});


function guardarProductoReciente(productoId) {

    const nombreCookie = "productos_recientes";
    const limite = 5;

    let productos = [];

    try {
        productos = JSON.parse(leerCookie(nombreCookie)) || [];
    } catch (e) {
        productos = [];
    }

    productos = productos.map(String);

    productos = productos.filter(id => id !== productoId);

    productos.unshift(productoId);

    productos = productos.slice(0, limite);

    crearCookie(nombreCookie, JSON.stringify(productos), 30);
}


function leerCookie(nombre) {

    const nombreBuscado = nombre + "=";
    const cookies = document.cookie.split(";");

    for (let cookie of cookies) {
        cookie = cookie.trim();

        if (cookie.indexOf(nombreBuscado) === 0) {
            return decodeURIComponent(cookie.substring(nombreBuscado.length));
        }
    }

    return null;
}


function crearCookie(nombre, valor, dias) {

    const fecha = new Date();

    fecha.setTime(fecha.getTime() + (dias * 24 * 60 * 60 * 1000));

    document.cookie =
        nombre + "=" + encodeURIComponent(valor) +
        "; expires=" + fecha.toUTCString() +
        "; path=/UNRINCONDEPT" +
        "; SameSite=Lax";
}