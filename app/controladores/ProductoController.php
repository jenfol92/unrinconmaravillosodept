<?php
//Incluimos los datos que producto.php extrae de la base de datos.
require_once __DIR__ . '/../modelos/Producto.php';

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

        // Obtener datos desde el modelo
        $categorias = $this->productModel->obtenerCategorias();
        $destacados = $this->productModel->obtenerRecursosDestacados();

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
    public function detalle() {

        // Obtener id desde la URL
        $id = $_GET['id'] ?? null;

        if(!$id){
            die("Producto no encontrado");
        }
        // Registrar visita al producto
$this->productModel->incrementarClicks($id);

        // Obtener producto
        $producto = $this->productModel->obtenerProductosID($id);

        if(!$producto){
            die("Producto no existe");
        }
// Incrementar contador de visitas
$this->productModel->incrementarClicks($id);

$relacionados = $this->productModel->obtenerProductosRelacionados(
    $producto['categoria_id'],
    $producto['id']
);
       
    //Obtener reseñas
$resenas = $this->productModel->obtenerResenasPorProducto($id);
        // Cargar vista
        require_once __DIR__ . '/../vistas/producto_detalle.php';
    }
    public function carrito() {
       $productos_carrito = []; 
        $totales = [
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0
    ];
    // 2. ¿Hay algo en la sesión?
    if (!empty($_SESSION['carrito'])) {
        // Obtenemos solo los IDs (las llaves del array de sesión)
        $ids = array_keys($_SESSION['carrito']);
        
        // Llamamos a tu función universal del modelo
        $productos_data = $this->productModel->obtenerProductosID($ids);

        // 3. Cruzamos datos: Información de BD + Cantidades de Sesión
        foreach ($productos_data as $p) {
            $id_actual = $p['id'];
            $cantidad = $_SESSION['carrito'][$id_actual];
            $precio_total = $p['precio'] * $cantidad;

            // Añadimos la cantidad al array del producto para la vista
            $p['cantidad'] = $cantidad;
            $p['total_fila'] = $precio_total;

            $productos_carrito[] = $p;
            
            // Sumamos al subtotal
            $totales['subtotal'] += $precio_total;
        }

// 4. Cálculos finales
$totales['iva'] = $totales['subtotal'] * 0.21;
$totales['total'] = $totales['subtotal'] + $totales['iva'];
}

// Variables simples para carrito_view.php
$subtotal = $totales['subtotal'];
$iva = $totales['iva'];
$total = $totales['total'];

// 5. Llamamos a la vista
require_once __DIR__ . '/../vistas/carrito_view.php';
}
}