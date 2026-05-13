<?php
require_once __DIR__ . "/../../templates/header.php";
?>

<div class="col-md-8">
    <h5><?= count($productos_carrito) ?> PRODUCTOS SELECCIONADOS</h5>
    
    <?php foreach ($productos_carrito as $p): ?>
    <div class="card mb-3 p-3 shadow-sm border-0">
        <div class="row align-items-center">
            <div class="col-2">
                <img src="/static/images/img/<?= $p['imagen'] ?>" class="img-fluid rounded">
            </div>
            <div class="col-5">
                <small class="text-primary">Matemáticas</small>
                <h6><?= $p['titulo'] ?></h6>
            </div>
            <div class="col-3 d-flex align-items-center">
                <button class="btn btn-sm btn-light">-</button>
                <span class="mx-2"><?= $p['cantidad'] ?></span>
                <button class="btn btn-sm btn-light">+</button>
            </div>
            <div class="col-2 text-end">
                <span class="fw-bold"><?= number_format($p['precio'] * $p['cantidad'], 2) ?>€</span>
                <br>
                <small class="text-danger" style="cursor:pointer">Eliminar</small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<div class="col-md-4">
    <div class="card p-4 bg-light border-0" style="border-radius: 20px;">
        <h4>Resumen del pedido</h4>
        <div class="d-flex justify-content-between mt-3">
            <span>Subtotal</span>
            <span><?= number_format($subtotal, 2) ?>€</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>Impuestos (IVA 21%)</span>
            <span><?= number_format($iva, 2) ?>€</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between fw-bold fs-4">
            <span>Total</span>
            <span><?= number_format($total, 2) ?>€</span>
        </div>
        <button class="btn btn-warning w-100 mt-4 fw-bold py-3" style="border-radius: 10px;">
            Finalizar compra
        </button>
    </div>
</div>