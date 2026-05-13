<?php

require_once __DIR__ . '/../modelos/Admin.php';
require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../modelos/Soporte.php';

class AdminController
{
    private $adminModel;
    private $productoModel;
    private $usuarioModel;
    private $soporteModel;

    public function __construct()
    {
        // Instanciamos una sola vez
        $this->adminModel = new Admin();
        $this->productoModel = new Producto();
        $this->usuarioModel = new Usuario();
        $this->soporteModel = new Soporte();
    }

    public function dashboard()
    {
        // Estadísticas generales
        $stats = $this->adminModel->obtenerEstadisticas();

        // Últimos productos
        $productos = $this->adminModel->obtenerUltimosProductos();

        // Tickets de soporte
        $tickets = $this->adminModel->obtenerTicketsPendientes();

        // Categorías y niveles
        $categorias = $this->productoModel->obtenerCategorias();
        $niveles = $this->productoModel->obtenerNiveles();

        // Usuarios registrados
        $usuarios = $this->usuarioModel->obtenerUsuariosClientes();
        // Productos completos para la sección Productos del panel admin
$resultadoProductosAdmin = $this->productoModel->obtenerProductosAdmin();

// Lista real de productos
$productosAdmin = $resultadoProductosAdmin['productos'];

// Total de páginas, por si luego quieres paginar
$totalPaginasProductosAdmin = $resultadoProductosAdmin['total_paginas'];

// Categorías y niveles para filtros y formulario
$categorias = $this->productoModel->obtenerCategorias();
$niveles = $this->productoModel->obtenerNiveles();

$tickets = $this->soporteModel->obtenerTicketsAdmin();


        // Cargar vista
        require_once __DIR__ . '/../vistas/admin_view.php';
    }
}