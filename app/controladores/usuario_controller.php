<?php
require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/Soporte.php';

class UsuarioController
{
    public function perfil()
    {
        $soporteModel = new Soporte();
$ticketsSoporte = $soporteModel->obtenerTicketsUsuario($_SESSION['usuario_id']);
        // Comprobamos que el usuario esté logueado
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: login.php");
            exit();
        }

        // Comprobamos que sea usuario 3 - cliente
        if ($_SESSION['rol'] != 3) {
            header("Location: index.php");
            exit();
        }

        // Cargamos datos del usuario
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->obtenerPorId($_SESSION['usuario_id']);

        // Cargamos favoritos del usuario
        $productoModel = new Producto();
        $favoritos = $productoModel->obtenerProductosFavoritos($_SESSION['usuario_id']);
        $productosComprados = $usuarioModel->obtenerRecursosAdquiridosUsuario($_SESSION['usuario_id']);
        // Mostramos la vista del perfil
        require_once __DIR__ . '/../vistas/perfil_view.php';
    }
}