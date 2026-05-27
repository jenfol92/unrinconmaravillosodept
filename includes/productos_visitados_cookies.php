<?php

/**
 * PRODUCTOS VISITADOS - COOKIES EN PHP
 * ---------------------------------------------------------
 * Este archivo centraliza la gestión de la cookie funcional
 * que guarda los últimos productos visitados por el usuario.
 *
 * Finalidad:
 * - Guardar los IDs de los últimos productos visitados.
 * - Leer posteriormente esos IDs desde PHP.
 * - Permitir mostrar en la tienda el widget "Últimos vistos".
 *
 * Privacidad:
 * - Solo se guardan IDs de productos.
 * - No se guarda nombre, email, IP, sesión ni datos personales.
 *
 * IMPORTANTE:
 * - La cookie se crea desde PHP con setcookie().
 * - setcookie() debe ejecutarse antes de imprimir HTML.
 * - Por eso la función de guardado debe llamarse desde el controller,
 *   antes de cargar la vista.
 */


/**
 * Guarda un producto como visitado recientemente.
 * ---------------------------------------------------------
 * Esta función:
 *
 * 1. Lee la cookie productos_recientes si existe.
 * 2. Convierte su contenido JSON en un array PHP.
 * 3. Elimina el producto actual si ya estaba guardado.
 * 4. Añade el producto actual al principio del array.
 * 5. Limita el listado a un número máximo de productos.
 * 6. Vuelve a guardar la cookie actualizada.
 *
 * Ejemplo de cookie guardada:
 *
 * [15, 8, 3, 2]
 *
 * @param int $productoId ID del producto visitado.
 * @param int $limite Número máximo de productos recientes a guardar.
 *
 * @return void
 */
function guardarProductoRecienteCookie(int $productoId, int $limite = 5): void
{
    /*
        Nombre de la cookie donde se almacenarán los productos recientes.
    */
    $nombreCookie = 'productos_recientes';

    /*
        Array inicial de productos visitados.
        Si no existe cookie, se queda vacío.
    */
    $productos = [];

    /*
        Si existe la cookie, intentamos leerla.
    */
    if (!empty($_COOKIE[$nombreCookie])) {

        /*
            La cookie guarda un JSON.
            Lo convertimos a array asociativo PHP.
        */
        $productosCookie = json_decode($_COOKIE[$nombreCookie], true);

        /*
            Solo aceptamos el valor si realmente es un array.
            Si la cookie estuviera manipulada o corrupta, se ignora.
        */
        if (is_array($productosCookie)) {
            $productos = $productosCookie;
        }
    }

    /*
        Convertimos todos los valores a enteros.
        Esto evita trabajar con texto recibido desde una cookie manipulable.
    */
    $productos = array_map('intval', $productos);

    /*
        Eliminamos IDs inválidos o menores que 1.
    */
    $productos = array_filter($productos, function ($id) {
        return $id > 0;
    });

    /*
        Eliminamos el producto actual si ya estaba dentro del array.
        Así evitamos duplicados.
    */
    $productos = array_filter($productos, function ($id) use ($productoId) {
        return $id !== $productoId;
    });

    /*
        Añadimos el producto actual al principio.
        De esta forma, el último producto visto aparece el primero.
    */
    array_unshift($productos, $productoId);

    /*
        Limitamos la cookie al número máximo de productos recientes.
    */
    $productos = array_slice($productos, 0, $limite);

    /*
        Reindexamos el array para que el JSON salga limpio:
        [15,8,3] en lugar de {"0":15,"2":8}
    */
    $productos = array_values($productos);

    /*
        Convertimos el array a JSON para guardarlo en la cookie.
    */
    $valorCookie = json_encode($productos);

    /*
        Creamos o actualizamos la cookie.
        
        expires:
        - Duración de 30 días.

        path:
        - "/" hace que la cookie esté disponible en toda la web.

        secure:
        - true solo cuando la web usa HTTPS.
        - En localhost normalmente será false.

        httponly:
        - true impide que JavaScript lea la cookie.
        - Esto es correcto porque el requisito era trabajar cookies con PHP.

        samesite:
        - Lax es una opción equilibrada para cookies funcionales.
    */
    setcookie($nombreCookie, $valorCookie, [
        'expires' => time() + (30 * 24 * 60 * 60),
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    /*
        Actualizamos también $_COOKIE en la petición actual.

        Normalmente, una cookie creada con setcookie() estará disponible
        en la siguiente carga de página. Esta línea permite que PHP pueda
        leer el valor actualizado durante esta misma petición si hiciera falta.
    */
    $_COOKIE[$nombreCookie] = $valorCookie;
}


/**
 * Lee la cookie de productos recientes.
 * ---------------------------------------------------------
 * Devuelve un array con los IDs guardados en productos_recientes.
 *
 * Si la cookie no existe, está vacía o tiene un formato incorrecto,
 * devuelve un array vacío.
 *
 * @return array
 */
function leerProductosRecientesCookie(): array
{
    /*
        Nombre de la cookie que queremos leer.
    */
    $nombreCookie = 'productos_recientes';

    /*
        Si no existe la cookie, no hay productos recientes.
    */
    if (empty($_COOKIE[$nombreCookie])) {
        return [];
    }

    /*
        Convertimos el JSON de la cookie en array PHP.
    */
    $productos = json_decode($_COOKIE[$nombreCookie], true);

    /*
        Si el resultado no es un array, devolvemos vacío.
    */
    if (!is_array($productos)) {
        return [];
    }

    /*
        Convertimos todos los IDs a enteros.
    */
    $productos = array_map('intval', $productos);

    /*
        Eliminamos IDs inválidos.
    */
    $productos = array_filter($productos, function ($id) {
        return $id > 0;
    });

    /*
        Reindexamos el array antes de devolverlo.
    */
    return array_values($productos);
}