<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/stripe.php';

\Stripe\Stripe::setApiKey(secret_key);

echo "Stripe cargado correctamente";