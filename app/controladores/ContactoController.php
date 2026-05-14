<?php

require_once __DIR__ . '/../modelos/Producto.php';

class ContactoController
{
    public function index()
    {
        $producto = null;

        // Si viene producto_id, cargamos el producto
        if (!empty($_GET['producto_id'])) {

            $productoModel = new Producto();
            $producto = $productoModel->obtenerProductosID($_GET['producto_id']);

            // Si no existe, dejamos null en lugar de abortar
            if (!$producto) {
                $producto = null;
            }
        }

        require_once __DIR__ . '/../vistas/contacto_view.php';
    }
}