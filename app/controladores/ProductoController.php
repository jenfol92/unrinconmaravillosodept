<?php
//Incluimos los datos que producto.php extrae de la base de datos.
require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/recursosGratuitos.php';

//Creamos la clase ProductoController
class ProductoController{

private $productModel;

public function __construct(){
    //Instanciamos el modelo ProductController
    $this->productModel = new Producto();
}
/* Function HOME (index).
Obtenemos datos con el controlador del modelo Producto.php para cargar los recursos en home (index) 
obteniendo los datos de la base de datos y cargar la vista index.view.php */
public function home(){
     $recursosGratuitosModel = new RecursoGratuito();

        // Obtener datos desde el modelo
        $categorias = $this->productModel->obtenerCategorias();
        $destacados = $this->productModel->obtenerRecursosDestacados();
        $gratuitosHome = $recursosGratuitosModel->obtenerRecursosGratuitosHome(3);

        // Cargar vista
        require_once __DIR__ . '/../vistas/index_view.php';
    }

    // TIENDA (con filtros)
    public function tienda() {

        // Recoger filtros (si existen)
        $pagina=$_GET['pagina'] ?? 1;
        $limite = 9;
        $offset = ($pagina - 1) * $limite;
        $busqueda=$_GET['busqueda'] ?? '';
        $categoria = $_GET['categoria'] ?? null;
        $nivel = $_GET['nivel'] ?? null;

        // Obtener datos filtrados
       $productos = $this->productModel->RecursosFiltrados($categoria, $nivel,$busqueda,$limite, $offset,);
       //obtengo productos:
       $resultado_productos=$productos['productos'];
       //Paginas
       $paginas=$productos['total_paginas'];
       //Obtener filtros
        $niveles = $this->productModel->obtenerNiveles();
        $categorias = $this->productModel->obtenerCategorias();
     // Cargar vista
        require_once __DIR__ . '/../vistas/tienda_view.php';
    }

    //DETALLE DE PRODUCTO
    public function detalle()
{
    $id = $_GET['id'] ?? null;

    if (!$id) {
        die("Producto no encontrado");
    }

    $producto = $this->productModel->obtenerProductosID($id);

    if (!$producto) {
        die("Producto no existe");
    }

    // Registrar una sola visita/clic al producto
    $this->productModel->incrementarClicks($id);

    $relacionados = $this->productModel->obtenerProductosRelacionados(
        $producto['categoria_id'],
        $producto['id']
    );

    $resenas = $this->productModel->obtenerResenasPorProducto($id);

    require_once __DIR__ . '/../vistas/producto_detalle.php';
}
public function carrito() 
{
    $productos_carrito = []; 

    $totales = [
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0
    ];

    // ¿Hay algo en la sesión?
    if (!empty($_SESSION['carrito'])) {

        // Obtenemos solo los IDs del carrito
        $ids = array_keys($_SESSION['carrito']);

        // Obtenemos los productos desde la BD
        $productos_data = $this->productModel->obtenerProductosID($ids);

        // Cruzamos datos de BD + cantidades de sesión
        foreach ($productos_data as $p) {

            $id_actual = $p['id'];

            $cantidad = (int)($_SESSION['carrito'][$id_actual] ?? 1);
            $precio = (float)($p['precio'] ?? 0);

            $precio_total = $precio * $cantidad;

            // Añadimos datos extra para la vista
            $p['cantidad'] = $cantidad;
            $p['total_fila'] = $precio_total;

            $productos_carrito[] = $p;

            // Sumamos al subtotal
            $totales['subtotal'] += $precio_total;
        }
    }

    // Como no hay IVA ni gastos añadidos, el total es igual al subtotal
    $totales['total'] = $totales['subtotal'];

    // Variables simples para carrito_view.php
    $subtotal = $totales['subtotal'];
    $iva = $totales['iva'];
    $total = $totales['total'];

    // Cargamos la vista
    require_once __DIR__ . '/../vistas/carrito_view.php';
}
}