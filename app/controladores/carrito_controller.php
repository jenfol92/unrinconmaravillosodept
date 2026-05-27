<?php

/**
 * CarritoController
 * ---------------------------------------------------------
 * Controlador encargado de gestionar toda la lógica relacionada
 * con el carrito de compra.
 *
 * Responsabilidades:
 * - Mostrar la vista del carrito.
 * - Añadir productos al carrito.
 * - Restar productos del carrito.
 * - Eliminar productos del carrito.
 * - Calcular totales del carrito.
 * - Devolver respuestas JSON para operaciones AJAX.
 * - Eliminar un producto de favoritos cuando se añade al carrito.
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../modelos/Producto.php';

class CarritoController
{
    /**
     * Modelo de productos.
     *
     * Lo usamos para:
     * - Obtener los datos reales de los productos del carrito.
     * - Eliminar favoritos del usuario si añade un producto al carrito.
     */
    private $productModel;

    /**
     * Constructor del controlador.
     * ---------------------------------------------------------
     * Se ejecuta automáticamente al crear el controlador.
     *
     * Inicializa:
     * - El modelo Producto.
     * - El array de carrito en sesión si todavía no existe.
     */
    public function __construct()
    {
        $this->productModel = new Producto();

        /*
            Si el carrito no existe todavía en sesión,
            lo inicializamos como array vacío.

            La estructura será:
            $_SESSION['carrito'] = [
                producto_id => cantidad,
                producto_id => cantidad
            ];
        */
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
    }

    /**
     * Muestra la página del carrito.
     * ---------------------------------------------------------
     * Esta función antes estaba dentro de ProductoController.
     *
     * La movemos aquí porque realmente no muestra un producto concreto,
     * sino el contenido completo del carrito.
     *
     * @return void
     */
    public function index()
    {
        /*
            Array donde guardaremos los productos preparados
            para mostrarlos en la vista carrito_view.php.
        */
        $productos_carrito = [];

        /*
            Inicializamos los totales del carrito.

            Actualmente no se aplica IVA ni gastos adicionales,
            por lo que el total será igual al subtotal.
        */
        $totales = [
            'subtotal' => 0,
            'iva' => 0,
            'total' => 0
        ];

        /*
            Comprobamos si existe carrito en sesión.

            $_SESSION['carrito'] contiene:
            - ID del producto.
            - Cantidad seleccionada.
        */
        if (!empty($_SESSION['carrito'])) {

            /*
                Obtenemos solo los IDs de productos que hay en el carrito.
            */
            $ids = array_keys($_SESSION['carrito']);

            /*
                Obtenemos los datos reales de esos productos desde la base de datos.

                Esto es importante porque nunca debemos fiarnos del precio,
                título o datos enviados desde el cliente.
            */
            $productos_data = $this->productModel->obtenerProductosID($ids);

            /*
                Recorremos los productos obtenidos desde base de datos.
            */
            foreach ($productos_data as $p) {

                /*
                    ID real del producto actual.
                */
                $id_actual = $p['id'];

                /*
                    Cantidad del producto en el carrito.

                    Si por algún motivo no existe cantidad, usamos 1.
                */
                $cantidad = (int)($_SESSION['carrito'][$id_actual] ?? 1);

                /*
                    Precio unitario del producto.
                */
                $precio = (float)($p['precio'] ?? 0);

                /*
                    Total de esa línea:
                    precio unitario x cantidad.
                */
                $precio_total = $precio * $cantidad;

                /*
                    Añadimos información calculada al producto.

                    Así la vista puede mostrar:
                    - cantidad
                    - total de la fila
                */
                $p['cantidad'] = $cantidad;
                $p['total_fila'] = $precio_total;

                /*
                    Añadimos el producto preparado al array final.
                */
                $productos_carrito[] = $p;

                /*
                    Sumamos el total de esta fila al subtotal general.
                */
                $totales['subtotal'] += $precio_total;
            }
        }

        /*
            Como actualmente no hay IVA ni gastos añadidos,
            el total es igual al subtotal.
        */
        $totales['total'] = $totales['subtotal'];

        /*
            Variables simples para que carrito_view.php pueda usarlas
            directamente sin acceder al array $totales.
        */
        $subtotal = $totales['subtotal'];
        $iva = $totales['iva'];
        $total = $totales['total'];

        /*
            Cargamos la vista del carrito.

            La vista tendrá disponibles:
            - $productos_carrito
            - $subtotal
            - $iva
            - $total
        */
        require_once __DIR__ . '/../vistas/carrito_view.php';
    }

    /**
     * Gestiona operaciones AJAX del carrito.
     * ---------------------------------------------------------
     * Este método sustituye al archivo operaciones_carrito.php
     * lleno de lógica directa.
     *
     * Operaciones permitidas:
     * - add_carrito
     * - restar_carrito
     * - eliminar_carrito
     *
     * Devuelve siempre JSON.
     *
     * @return void
     */
    public function operacionesAjax()
    {
        /*
            Indicamos que la respuesta será JSON.
        */
        header('Content-Type: application/json; charset=utf-8');

        /*
            Recogemos los datos enviados por POST.
        */
        $id = $_POST['id'] ?? null;
        $accion = $_POST['accion'] ?? null;

        /*
            Convertimos el ID a entero.

            Esto evita trabajar con valores manipulados como texto.
        */
        $id = (int)$id;

        /*
            Variable que indica si se ha eliminado el producto de favoritos.

            La inicializamos aquí para que exista siempre,
            aunque la acción no sea add_carrito.
        */
        $favoritoEliminado = false;

        /*
            Validamos que llegue un ID válido y una acción.
        */
        if ($id <= 0 || empty($accion)) {
            $this->responderJson([
                'status' => 'error',
                'message' => 'Datos incompletos',
                'contador_carrito' => $this->obtenerContadorCarrito(),
                'usuario_logueado' => $this->usuarioLogueado(),
                'favorito_eliminado' => false,
                'producto_id' => $id
            ]);
        }

        /*
            Mensaje por defecto.
        */
        $mensaje = 'Operación realizada correctamente';

        /*
            Procesamos la acción recibida.
        */
        switch ($accion) {

            case 'add_carrito':

                /*
                    Añadimos una unidad del producto al carrito.

                    Si el producto ya existe, sumamos 1.
                    Si no existe, empieza con cantidad 1.
                */
                $_SESSION['carrito'][$id] = ($_SESSION['carrito'][$id] ?? 0) + 1;

                $mensaje = 'Producto añadido al carrito';

                /*
                    Si el usuario está logueado, eliminamos el producto
                    de favoritos al añadirlo al carrito.
                */
                if ($this->usuarioLogueado()) {
                    $favoritoEliminado = $this->productModel->eliminarFavoritoUsuario(
                        (int)$_SESSION['usuario_id'],
                        $id
                    );
                }

                /*
                    IMPORTANTE:
                    Este break debe estar fuera del if del usuario logueado.

                    Si estuviera dentro, cuando el usuario no está logueado,
                    el switch seguiría ejecutando el siguiente case.
                */
                break;

            case 'restar_carrito':

                /*
                    Restamos una unidad del producto si existe en el carrito.
                */
                if (isset($_SESSION['carrito'][$id])) {
                    $_SESSION['carrito'][$id]--;

                    /*
                        Si la cantidad queda en 0 o menos,
                        eliminamos el producto completamente del carrito.
                    */
                    if ($_SESSION['carrito'][$id] <= 0) {
                        unset($_SESSION['carrito'][$id]);
                    }
                }

                $mensaje = 'Producto actualizado';
                break;

            case 'eliminar_carrito':

                /*
                    Eliminamos el producto completo del carrito,
                    independientemente de la cantidad que tuviera.
                */
                unset($_SESSION['carrito'][$id]);

                $mensaje = 'Producto eliminado del carrito';
                break;

            default:

                /*
                    Si llega una acción no reconocida,
                    devolvemos error.
                */
                $this->responderJson([
                    'status' => 'error',
                    'message' => 'Acción no válida',
                    'contador_carrito' => $this->obtenerContadorCarrito(),
                    'usuario_logueado' => $this->usuarioLogueado(),
                    'favorito_eliminado' => false,
                    'producto_id' => $id
                ]);
        }

        /*
            Devolvemos respuesta correcta.
        */
        $this->responderJson([
            'status' => 'success',
            'message' => $mensaje,
            'contador_carrito' => $this->obtenerContadorCarrito(),
            'usuario_logueado' => $this->usuarioLogueado(),
            'favorito_eliminado' => $favoritoEliminado,
            'producto_id' => $id
        ]);
    }

    /**
     * Devuelve el número total de productos del carrito.
     * ---------------------------------------------------------
     * Suma todas las cantidades guardadas en sesión.
     *
     * @return int
     */
    private function obtenerContadorCarrito()
    {
        return array_sum($_SESSION['carrito'] ?? []);
    }

    /**
     * Comprueba si el usuario está logueado.
     * ---------------------------------------------------------
     *
     * @return bool
     */
    private function usuarioLogueado()
    {
        return !empty($_SESSION['usuario_id']);
    }

    /**
     * Devuelve una respuesta JSON y detiene la ejecución.
     * ---------------------------------------------------------
     * Centralizamos la salida JSON para no repetir:
     *
     * echo json_encode(...);
     * exit;
     *
     * @param array $data
     * @return void
     */
    private function responderJson(array $data)
    {
        echo json_encode($data);
        exit;
    }
}