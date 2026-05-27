<?php

/**
 * ContactoController
 * ---------------------------------------------------------
 * Controlador encargado de gestionar la página pública de contacto
 * y el envío de mensajes desde el formulario.
 *
 * Este controlador tiene dos responsabilidades principales:
 *
 * 1. Mostrar la página de contacto:
 *    - Contacto general.
 *    - Contacto relacionado con un producto concreto.
 *
 * 2. Guardar mensajes de contacto mediante AJAX:
 *    - Recibe datos por POST.
 *    - Valida campos obligatorios.
 *    - Valida el email.
 *    - Normaliza el producto_id.
 *    - Guarda el mensaje usando el modelo Contacto.
 *    - Devuelve una respuesta JSON.
 */

require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/Contacto.php';

class ContactoController
{
    /**
     * Muestra la página pública de contacto.
     * ---------------------------------------------------------
     * Esta función carga la vista contacto_view.php.
     *
     * Puede funcionar de dos formas:
     *
     * 1. Contacto general:
     *    - El usuario entra directamente a contacto.php.
     *    - No existe producto asociado.
     *
     * 2. Contacto sobre un producto:
     *    - El usuario entra desde la ficha de un producto.
     *    - La URL incluye un producto_id.
     *
     * Ejemplo:
     *
     * contacto.php?producto_id=5
     *
     * Si el producto existe, se carga y queda disponible en la vista.
     * Si el producto no existe, se muestra igualmente el formulario general.
     *
     * @return void
     */
    public function index()
    {
        /*
            Inicializamos $producto como null.

            De esta forma, la vista contacto_view.php siempre tendrá
            disponible esta variable y podrá comprobar si hay producto
            relacionado o no.
        */
        $producto = null;

        /*
            Comprobamos si llega un producto_id por GET.

            Este parámetro se usará cuando el contacto venga desde
            la ficha de un producto concreto.
        */
        if (!empty($_GET['producto_id'])) {

            /*
                Convertimos el producto_id a entero.

                Esto evita trabajar directamente con el valor recibido
                por URL y reduce riesgos de errores o manipulaciones.
            */
            $productoId = (int) $_GET['producto_id'];

            /*
                Solo intentamos buscar el producto si el ID es mayor que 0.
            */
            if ($productoId > 0) {

                /*
                    Instanciamos el modelo Producto para consultar
                    la información del producto en la base de datos.
                */
                $productoModel = new Producto();

                /*
                    Buscamos el producto por su ID.

                    Este método debe devolver:
                    - Los datos del producto si existe.
                    - false/null si no existe.
                */
                $producto = $productoModel->obtenerProductosID($productoId);

                /*
                    Si no se encuentra el producto, dejamos $producto en null.

                    No detenemos la página porque el formulario de contacto
                    puede seguir funcionando como contacto general.
                */
                if (!$producto) {
                    $producto = null;
                }
            }
        }

        /*
            Cargamos la vista de contacto.

            La vista podrá usar la variable $producto para:
            - Mostrar información del producto consultado.
            - Rellenar un campo oculto producto_id.
            - Mostrar un formulario general si no hay producto.
        */
        require_once __DIR__ . '/../vistas/contacto_view.php';
    }

    /**
     * Guarda un mensaje de contacto mediante AJAX.
     * ---------------------------------------------------------
     * Este método se ejecuta cuando el usuario envía el formulario
     * público de contacto.
     *
     * Flujo:
     * 1. Indica que la respuesta será JSON.
     * 2. Recoge los datos enviados por POST.
     * 3. Limpia los datos recibidos.
     * 4. Valida campos obligatorios.
     * 5. Valida el email.
     * 6. Normaliza producto_id.
     * 7. Guarda el mensaje usando el modelo Contacto.
     * 8. Devuelve respuesta JSON.
     *
     * @return void
     */
    public function guardarMensajeAjax()
    {
        /*
            Indicamos que la respuesta será JSON.

            Esto es importante porque este método será llamado desde JavaScript,
            normalmente mediante fetch() o AJAX.
        */
        header('Content-Type: application/json; charset=utf-8');

        /*
            Recogida y limpieza de datos.

            trim() elimina espacios al principio y al final.
            El operador ?? evita errores si algún campo no llega por POST.
        */
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $asunto = trim($_POST['asunto'] ?? '');
        $mensaje = trim($_POST['mensaje'] ?? '');

        /*
            producto_id es opcional.

            Puede venir si el usuario está preguntando por un producto concreto.
            Si no llega, si llega vacío o si no es válido, se guarda como null.
        */
        $productoId = isset($_POST['producto_id'])
            ? (int) $_POST['producto_id']
            : 0;

        $productoId = $productoId > 0 ? $productoId : null;

        /*
            Validamos que los campos obligatorios no estén vacíos.
        */
        if ($nombre === '' || $email === '' || $asunto === '' || $mensaje === '') {
            echo json_encode([
                'ok' => false,
                'error' => 'Completa todos los campos.'
            ]);
            exit;
        }

        /*
            Validamos que el email tenga un formato correcto.
        */
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'ok' => false,
                'error' => 'Introduce un email válido.'
            ]);
            exit;
        }

        try {
            /*
                Instanciamos el modelo Contacto.
            */
            $contactoModel = new Contacto();

            /*
                Guardamos el mensaje en base de datos.

                El modelo Contacto se encarga de ejecutar el INSERT.
            */
            $contactoModel->guardarMensaje(
                $productoId,
                $nombre,
                $email,
                $asunto,
                $mensaje
            );

            /*
                Respuesta correcta para JavaScript.
            */
            echo json_encode([
                'ok' => true,
                'mensaje' => 'Tu mensaje se ha enviado correctamente. Si la consulta lo requiere, recibirás respuesta por email.'
            ]);
            exit;

        } catch (Exception $e) {

            /*
                Respuesta en caso de error.

                En producción no conviene mostrar detalles técnicos al usuario.
                El error real se podría registrar en un log interno más adelante.
            */
            echo json_encode([
                'ok' => false,
                'error' => 'No se pudo enviar el mensaje. Inténtalo de nuevo más tarde.'
            ]);
            exit;
        }
    }
}