<?php
require_once __DIR__ . "/../../templates/header.php";
?>

<div class="col-md-8">
    <h5><?= count($productos_carrito) ?> PRODUCTOS SELECCIONADOS</h5>
    
    <?php foreach ($productos_carrito as $p): ?>
    <div class="card mb-3 p-3 shadow-sm border-0">
        <div class="row align-items-center">
            <div class="col-2">
                <img src="/UNRINCONDEPT/static/images/img/<?= $p['imagen'] ?>" class="img-fluid rounded">
            </div>
            <div class="col-5">
                <small class="text-primary">Matemáticas</small>
                <h6><?= $p['titulo'] ?></h6>
            </div>
            <div class="col-3 d-flex align-items-center">
               <button 
    type="button"
    class="btn btn-sm btn-outline-secondary btn-carrito-accion"
    data-id="<?= $p['id'] ?>"
    data-accion="restar_carrito"
>
    -
</button>
                <span class="mx-2"><?= $p['cantidad'] ?></span>
             <button 
    type="button"
    class="btn btn-sm btn-outline-secondary btn-carrito-accion"
    data-id="<?= $p['id'] ?>"
    data-accion="add_carrito"
>
    +
</button>
            </div>
            <div class="col-2 text-end">
                <span class="fw-bold"><?= number_format($p['precio'] * $p['cantidad'], 2) ?>€</span>
                
              <button 
    type="button"
    class="btn btn-sm btn-outline-danger btn-carrito-accion ms-2"
    data-id="<?= $p['id'] ?>"
    data-accion="eliminar_carrito"
>
    <i class="bi bi-trash"></i>
</button>
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
<script src="/UNRINCONDEPT/static/js/carrito.js"></script>
    <?php
    require_once __DIR__ . '/../../templates/footer.php';
    ?>