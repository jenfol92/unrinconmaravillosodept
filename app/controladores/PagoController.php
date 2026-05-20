<?php

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/stripe.php';
require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/Pedido.php';

class PagoController
{
    public function crearCheckout()
    {
        if (empty($_SESSION['usuario_id'])) {
            header('Location: /UNRINCONDEPT/public/login.php');
            exit;
        }

        if (empty($_SESSION['carrito'])) {
            header('Location: /UNRINCONDEPT/public/carrito.php');
            exit;
        }

        try {
            \Stripe\Stripe::setApiKey(secret_key);

            $productoModel = new Producto();
            $pedidoModel = new Pedido();

            $ids = array_keys($_SESSION['carrito']);
            $ids = array_filter(array_map('intval', $ids), function ($id) {
                return $id > 0;
            });

            if (empty($ids)) {
                header('Location: /UNRINCONDEPT/public/carrito.php');
                exit;
            }

            $productos = $productoModel->obtenerProductosID($ids);

            $line_items = [];
            $productosPedido = [];

            foreach ($productos as $p) {
                $productoId = (int)$p['id'];

                $entradaCarrito = $_SESSION['carrito'][$productoId] ?? 1;

                if (is_array($entradaCarrito)) {
                    $cantidad = (int)($entradaCarrito['cantidad'] ?? 1);
                } else {
                    $cantidad = (int)$entradaCarrito;
                }

                if ($cantidad <= 0) {
                    continue;
                }

                $precio = (float)$p['precio'];
                $precioCentimos = (int)round($precio * 100);

                if ($precioCentimos <= 0) {
                    continue;
                }

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

                $p['cantidad'] = $cantidad;
                $productosPedido[] = $p;
            }

            if (empty($line_items) || empty($productosPedido)) {
                header('Location: /UNRINCONDEPT/public/carrito.php');
                exit;
            }

            $pedido_id = $pedidoModel->crearPedidoPendiente(
                $_SESSION['usuario_id'],
                $productosPedido
            );

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

            $pedidoModel->guardarReferenciaPago($pedido_id, $checkout_session->id);

            header('Location: ' . $checkout_session->url);
            exit;

        } catch (Exception $e) {
            echo "Error creando la sesión de pago: " . htmlspecialchars($e->getMessage());
            exit;
        }
    }

    public function pagoExitoso()
    {
        $session_id = $_GET['session_id'] ?? null;

        if (!$session_id) {
            die('No se ha recibido la referencia del pago.');
        }

        try {
            \Stripe\Stripe::setApiKey(secret_key);

            $session = \Stripe\Checkout\Session::retrieve($session_id);

            if ($session->payment_status !== 'paid') {
                die('El pago todavía no aparece como completado.');
            }

            $pedidoModel = new Pedido();
            $pedido_id = $pedidoModel->marcarComoPagadoPorReferencia($session_id);

            $_SESSION['carrito'] = [];

            require_once __DIR__ . '/../vistas/pago_exitoso_view.php';

        } catch (Exception $e) {
            die('Error confirmando el pago: ' . htmlspecialchars($e->getMessage()));
        }
    }

    public function pagoCancelado()
    {
        $pedido_id = isset($_GET['pedido_id']) ? (int)$_GET['pedido_id'] : 0;

        if ($pedido_id > 0) {
            try {
                $pedidoModel = new Pedido();
                $pedidoModel->marcarComoCancelado($pedido_id);
            } catch (Exception $e) {
                // No paramos la página si falla esto.
            }
        }

        require_once __DIR__ . '/../vistas/pagos/pago_cancelado_view.php';
    }
    
}