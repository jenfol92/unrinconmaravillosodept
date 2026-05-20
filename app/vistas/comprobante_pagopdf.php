<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #333;
            font-size: 12px;
        }

        .header {
            border-bottom: 2px solid #56B3AD;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        h1 {
            color: #56B3AD;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        th, td {
            border-bottom: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .total {
            margin-top: 20px;
            text-align: right;
            font-size: 16px;
            font-weight: bold;
        }

        .small {
            color: #777;
            font-size: 11px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Comprobante de pago</h1>
    <p>UN RINCÓN DE PT</p>
</div>

<p><strong>Pedido nº:</strong> <?= htmlspecialchars($pedido['pedido_id']) ?></p>
<p><strong>Fecha del pedido:</strong> <?= htmlspecialchars($pedido['fecha_pedido']) ?></p>
<p><strong>Fecha de pago:</strong> <?= htmlspecialchars($pedido['fecha_pago'] ?? '') ?></p>
<p><strong>Método de pago:</strong> <?= htmlspecialchars($pedido['metodo_pago']) ?></p>
<p><strong>Estado:</strong> Pagado</p>
<p><strong>Referencia:</strong> <?= htmlspecialchars($pedido['referencia_pago'] ?? '') ?></p>

<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio unitario</th>
            <th>Total línea</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lineas as $linea): ?>
            <tr>
                <td><?= htmlspecialchars($linea['titulo']) ?></td>
                <td><?= (int)$linea['cantidad'] ?></td>
                <td><?= number_format((float)$linea['precio_unitario'], 2) ?> €</td>
                <td><?= number_format((float)$linea['precio_unitario'] * (int)$linea['cantidad'], 2) ?> €</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="total">
    Total pagado: <?= number_format((float)$pedido['total'], 2) ?> €
</div>

<p class="small">
    Este documento acredita el pago realizado a través de Stripe. 
    No sustituye a una factura formal salvo que se emita conforme a la normativa fiscal aplicable.
</p>

</body>
</html>