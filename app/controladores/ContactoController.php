<?php

/**
 * ContactoController
 * ---------------------------------------------------------
 * Controlador encargado de mostrar la página pública de contacto.
 *
 * Esta página puede funcionar de dos formas:
 *
 * 1. Contacto general:
 *    - El usuario accede directamente a contacto.php.
 *    - No se carga ningún producto concreto.
 *
 * 2. Contacto relacionado con un producto:
 *    - El usuario llega desde la ficha de un producto.
 *    - Se envía por GET el parámetro producto_id.
 *    - El controlador intenta cargar ese producto para mostrarlo
 *      en el formulario de contacto.
 *
 * Ejemplo de URL con producto asociado:
 *
 * contacto.php?producto_id=5
 *
 * Si el producto no existe, no se interrumpe la página.
 * Simplemente se deja $producto como null y se muestra el formulario
 * de contacto normal.
 */

require_once __DIR__ . '/../modelos/Producto.php';

class ContactoController
{
    /**
     * Muestra la página de contacto.
     * ---------------------------------------------------------
     * Esta función carga la vista contacto_view.php.
     *
     * Antes de cargar la vista, comprueba si se ha recibido un
     * producto_id por la URL.
     *
     * Si existe producto_id:
     * - Se crea una instancia del modelo Producto.
     * - Se busca el producto en base de datos.
     * - Si se encuentra, se guarda en la variable $producto.
     * - Si no se encuentra, $producto queda como null.
     *
     * La variable $producto queda disponible en la vista para poder
     * mostrar información del recurso sobre el que se consulta.
     *
     * @return void
     */
    public function index()
    {
        /*
            Inicializamos la variable $producto como null.

            Esto permite que la vista contacto_view.php pueda comprobar
            si hay un producto asociado o si se trata de una consulta general.
        */
        $producto = null;

        /*
            Comprobamos si la URL incluye el parámetro producto_id.

            Ejemplo:
            contacto.php?producto_id=3

            Este caso se utiliza cuando el usuario pulsa una opción de contacto
            desde la ficha de un producto concreto.
        */
        if (!empty($_GET['producto_id'])) {

            /*
                Creamos una instancia del modelo Producto para poder consultar
                la base de datos.
            */
            $productoModel = new Producto();

            /*
                Buscamos el producto por su ID.

                obtenerProductosID() debe devolver los datos del producto
                si existe, o false/null si no se encuentra.
            */
            $producto = $productoModel->obtenerProductosID($_GET['producto_id']);

            /*
                Si el producto no existe, dejamos $producto como null.

                No se muestra error ni se detiene la ejecución, porque el
                formulario de contacto puede seguir funcionando como contacto
                general.
            */
            if (!$producto) {
                $producto = null;
            }
        }

        /*
            Cargamos la vista de contacto.

            La vista podrá utilizar la variable $producto para:
            - Mostrar el producto relacionado.
            - Rellenar un campo oculto con el producto_id.
            - Mostrar un mensaje tipo "Consulta sobre este recurso".
            - O mostrar simplemente el formulario general si $producto es null.
        */
        require_once __DIR__ . '/../vistas/contacto_view.php';
    }
}