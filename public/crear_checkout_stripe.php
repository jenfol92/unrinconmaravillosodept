<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/stripe.php';
require_once __DIR__ . '/../app/modelos/Producto.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: /UNRINCONDEPT/public/login.php');
    exit;
}

if (empty($_SESSION['carrito'])) {
    header('Location: /UNRINCONDEPT/public/carrito.php');
    exit;
}

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$productoModel = new Producto();

$ids = array_keys($_SESSION['carrito']);
$productos = $productoModel->obtenerProductosID($ids);

$line_items = [];

foreach ($productos as $p) {

    $productoId = (int)$p['id'];
    $cantidad = (int)($_SESSION['carrito'][$productoId] ?? 1);

    if ($cantidad <= 0) {
        continue;
    }

    $precio = (float)$p['precio'];

    // Stripe trabaja en céntimos
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
}

if (empty($line_items)) {
    header('Location: /UNRINCONDEPT/public/carrito.php');
    exit;
}

try {

    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'mode' => 'payment',
        'line_items' => $line_items,

        // Identificamos al usuario para luego poder relacionar el pago
        'client_reference_id' => $_SESSION['usuario_id'],

        'metadata' => [
            'usuario_id' => $_SESSION['usuario_id'],
        ],

        'success_url' => APP_URL . '/pago_exitoso.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => APP_URL . '/pago_cancelado.php',
    ]);

    header('Location: ' . $checkout_session->url);
    exit;

} catch (Exception $e) {

    echo "Error creando la sesión de pago: " . htmlspecialchars($e->getMessage());
    exit;
}