<?php

/**
 * PagoController
 * ---------------------------------------------------------
 * Controlador encargado de gestionar el proceso de pago de la tienda.
 *
 * Funciones principales:
 *
 * 1. Crear una sesión de pago con Stripe Checkout.
 * 2. Crear un pedido pendiente antes de enviar al usuario a Stripe.
 * 3. Redirigir al usuario a la pasarela de pago.
 * 4. Confirmar un pago correcto cuando Stripe devuelve al usuario.
 * 5. Marcar el pedido como pagado.
 * 6. Vaciar el carrito tras el pago.
 * 7. Marcar un pedido como cancelado si el usuario cancela el pago.
 *
 * Este controlador utiliza:
 *
 * - Sesiones PHP para identificar al usuario y leer el carrito.
 * - Stripe Checkout para procesar el pago.
 * - Producto.php para obtener los productos del carrito.
 * - Pedido.php para crear y actualizar pedidos.
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/stripe.php';
require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/Pedido.php';

class PagoController
{
    /**
     * Crea una sesión de pago en Stripe Checkout.
     * ---------------------------------------------------------
     * Esta función se ejecuta cuando el usuario pulsa el botón
     * de pagar desde el carrito.
     *
     * Flujo de funcionamiento:
     *
     * 1. Comprueba que el usuario esté logueado.
     * 2. Comprueba que el carrito no esté vacío.
     * 3. Configura la clave secreta de Stripe.
     * 4. Obtiene los productos reales desde la base de datos.
     * 5. Construye los line_items que necesita Stripe.
     * 6. Crea un pedido pendiente en la base de datos.
     * 7. Crea la sesión de Checkout en Stripe.
     * 8. Guarda la referencia de pago de Stripe en el pedido.
     * 9. Redirige al usuario a la URL de pago de Stripe.
     *
     * @return void
     */
    public function crearCheckout()
    {
        /*
            Comprobamos que el usuario haya iniciado sesión.

            Si no existe usuario_id en sesión, significa que el usuario
            no está autenticado. En ese caso, lo mandamos al login.
        */
        if (empty($_SESSION['usuario_id'])) {
            header('Location: /UNRINCONDEPT/public/login.php');
            exit;
        }

        /*
            Comprobamos que el carrito no esté vacío.

            Si no hay productos en el carrito, no tiene sentido crear
            una sesión de pago, así que redirigimos al carrito.
        */
        if (empty($_SESSION['carrito'])) {
            header('Location: /UNRINCONDEPT/public/carrito.php');
            exit;
        }

        try {
            /*
                Configuramos Stripe con la clave secreta.

                La constante secret_key viene definida en:
                config/stripe.php

                Este archivo debe estar protegido y no subirse a GitHub
                si contiene claves reales.
            */
            \Stripe\Stripe::setApiKey(secret_key);

            /*
                Instanciamos los modelos necesarios.

                Producto:
                - Obtiene la información real de los productos.

                Pedido:
                - Crea el pedido pendiente.
                - Guarda la referencia de Stripe.
                - Marca el pedido como pagado o cancelado.
            */
            $productoModel = new Producto();
            $pedidoModel = new Pedido();

            /*
                Obtenemos los IDs de productos guardados en el carrito.

                $_SESSION['carrito'] almacena los productos seleccionados
                por el usuario. Sus claves son los IDs de producto.
            */
            $ids = array_keys($_SESSION['carrito']);

            /*
                Convertimos los IDs a enteros y eliminamos cualquier
                valor inválido o menor que 1.
            */
            $ids = array_filter(array_map('intval', $ids), function ($id) {
                return $id > 0;
            });

            /*
                Si después de filtrar no queda ningún ID válido,
                devolvemos al usuario al carrito.
            */
            if (empty($ids)) {
                header('Location: /UNRINCONDEPT/public/carrito.php');
                exit;
            }

            /*
                Obtenemos los productos desde la base de datos.

                Es importante no confiar solo en los datos de sesión,
                porque el precio y el título deben obtenerse de la base
                de datos para evitar manipulaciones.
            */
            $productos = $productoModel->obtenerProductosID($ids);

            /*
                line_items:
                Array que Stripe necesita para saber qué productos
                se van a cobrar.

                productosPedido:
                Array propio que se usará para crear el pedido pendiente
                en la base de datos.
            */
            $line_items = [];
            $productosPedido = [];

            /*
                Recorremos los productos reales obtenidos de la base de datos.
            */
            foreach ($productos as $p) {
                $productoId = (int)$p['id'];

                /*
                    Obtenemos la entrada del carrito correspondiente
                    a este producto.

                    Puede venir como:
                    - número simple: cantidad
                    - array: ['cantidad' => X]
                */
                $entradaCarrito = $_SESSION['carrito'][$productoId] ?? 1;

                if (is_array($entradaCarrito)) {
                    $cantidad = (int)($entradaCarrito['cantidad'] ?? 1);
                } else {
                    $cantidad = (int)$entradaCarrito;
                }

                /*
                    Si la cantidad no es válida, ignoramos ese producto.
                */
                if ($cantidad <= 0) {
                    continue;
                }

                /*
                    Stripe trabaja los importes en céntimos.

                    Ejemplo:
                    9.99 € -> 999 céntimos
                */
                $precio = (float)$p['precio'];
                $precioCentimos = (int)round($precio * 100);

                /*
                    Si el precio no es válido, no se añade al pago.
                */
                if ($precioCentimos <= 0) {
                    continue;
                }

                /*
                    Añadimos el producto al array line_items que enviamos a Stripe.
                */
                $line_items[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $p['titulo'],
                        ],
                        'unit_amount' => $precioCentimos,
                    ],
                    'quantity' => $cantidad,
                ];

                /*
                    Guardamos también la cantidad dentro del producto para poder
                    crear correctamente el pedido en nuestra base de datos.
                */
                $p['cantidad'] = $cantidad;
                $productosPedido[] = $p;
            }

            /*
                Si por cualquier motivo no hay productos válidos para cobrar,
                devolvemos al usuario al carrito.
            */
            if (empty($line_items) || empty($productosPedido)) {
                header('Location: /UNRINCONDEPT/public/carrito.php');
                exit;
            }

            /*
                Creamos un pedido pendiente en la base de datos.

                Este pedido todavía no está pagado. Se crea antes de ir a Stripe
                para poder relacionar la sesión de pago con un pedido interno.
            */
            $pedido_id = $pedidoModel->crearPedidoPendiente(
                $_SESSION['usuario_id'],
                $productosPedido
            );

            /*
                Creamos la sesión de Stripe Checkout.

                Campos importantes:

                payment_method_types:
                - Métodos de pago permitidos. En este caso, tarjeta.

                mode:
                - payment indica que es un pago único.

                line_items:
                - Productos que se van a cobrar.

                client_reference_id:
                - Referencia interna del pedido.

                metadata:
                - Datos propios para poder identificar usuario y pedido.

                success_url:
                - URL a la que Stripe enviará al usuario si el pago finaliza bien.

                cancel_url:
                - URL a la que Stripe enviará al usuario si cancela el pago.
            */
            $checkout_session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'line_items' => $line_items,

                'client_reference_id' => $pedido_id,

                'metadata' => [
                    'usuario_id' => $_SESSION['usuario_id'],
                    'pedido_id' => $pedido_id,
                ],

                'success_url' => APP_URL . '/pago_exitoso.php?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => APP_URL . '/pago_cancelado.php?pedido_id=' . $pedido_id,
            ]);

            /*
                Guardamos en la base de datos la referencia de Stripe.

                Esto permite localizar el pedido cuando Stripe redirige
                al usuario de vuelta a la web.
            */
            $pedidoModel->guardarReferenciaPago($pedido_id, $checkout_session->id);

            /*
                Redirigimos al usuario a la página de pago segura de Stripe.
            */
            header('Location: ' . $checkout_session->url);
            exit;

        } catch (Exception $e) {
            /*
                Si ocurre cualquier error al crear la sesión de pago,
                se muestra un mensaje controlado.
            */
            echo "Error creando la sesión de pago: " . htmlspecialchars($e->getMessage());
            exit;
        }
    }

    /**
     * Confirma un pago correcto.
     * ---------------------------------------------------------
     * Esta función se ejecuta cuando Stripe redirige al usuario
     * a pago_exitoso.php después de completar el pago.
     *
     * Flujo de funcionamiento:
     *
     * 1. Recibe el session_id enviado por Stripe.
     * 2. Recupera la sesión de Checkout desde Stripe.
     * 3. Comprueba que el estado del pago sea "paid".
     * 4. Marca el pedido como pagado en la base de datos.
     * 5. Vacía el carrito.
     * 6. Carga la vista de pago exitoso.
     *
     * @return void
     */
    public function pagoExitoso()
    {
        /*
            Stripe devuelve el identificador de la sesión por GET.

            Ejemplo:
            pago_exitoso.php?session_id=cs_test_...
        */
        $session_id = $_GET['session_id'] ?? null;

        /*
            Si no recibimos session_id, no podemos confirmar el pago.
        */
        if (!$session_id) {
            die('No se ha recibido la referencia del pago.');
        }

        try {
            /*
                Configuramos Stripe con la clave secreta.
            */
            \Stripe\Stripe::setApiKey(secret_key);

            /*
                Recuperamos desde Stripe la sesión de Checkout.
            */
            $session = \Stripe\Checkout\Session::retrieve($session_id);

            /*
                Comprobamos que el pago aparece como completado.

                Si payment_status no es "paid", no marcamos el pedido
                como pagado.
            */
            if ($session->payment_status !== 'paid') {
                die('El pago todavía no aparece como completado.');
            }

            /*
                Marcamos el pedido como pagado en nuestra base de datos
                usando la referencia de Stripe.
            */
            $pedidoModel = new Pedido();
            $pedido_id = $pedidoModel->marcarComoPagadoPorReferencia($session_id);

            /*
                Vaciamos el carrito porque la compra ya se ha completado.
            */
            $_SESSION['carrito'] = [];

            /*
                Cargamos la vista de confirmación de pago.
                La vista puede usar $pedido_id para mostrar información
                relacionada con la compra.
            */
            require_once __DIR__ . '/../vistas/pago_exitoso_view.php';

        } catch (Exception $e) {
            /*
                Si ocurre un error al consultar Stripe o al actualizar
                el pedido, se muestra un mensaje controlado.
            */
            die('Error confirmando el pago: ' . htmlspecialchars($e->getMessage()));
        }
    }

    /**
     * Gestiona un pago cancelado.
     * ---------------------------------------------------------
     * Esta función se ejecuta cuando el usuario cancela el pago
     * desde Stripe Checkout.
     *
     * Flujo de funcionamiento:
     *
     * 1. Recibe el pedido_id por GET.
     * 2. Si el pedido existe, intenta marcarlo como cancelado.
     * 3. Si falla el marcado, no se detiene la página.
     * 4. Carga la vista de pago cancelado.
     *
     * @return void
     */
    public function pagoCancelado()
    {
        /*
            Obtenemos el pedido_id desde la URL.

            Ejemplo:
            pago_cancelado.php?pedido_id=10
        */
        $pedido_id = isset($_GET['pedido_id']) ? (int)$_GET['pedido_id'] : 0;

        /*
            Si hay un pedido_id válido, intentamos marcar el pedido
            como cancelado.
        */
        if ($pedido_id > 0) {
            try {
                $pedidoModel = new Pedido();
                $pedidoModel->marcarComoCancelado($pedido_id);
            } catch (Exception $e) {
                /*
                    No detenemos la página si falla esta operación.

                    Aunque no se pueda marcar como cancelado, el usuario
                    debe ver igualmente la pantalla de pago cancelado.
                */
            }
        }

        /*
            Cargamos la vista informativa de pago cancelado.
        */
        require_once __DIR__ . '/../vistas/pagos/pago_cancelado_view.php';
    }
}