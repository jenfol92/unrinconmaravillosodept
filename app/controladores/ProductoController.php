<?php

/**
 * ProductoController
 * ---------------------------------------------------------
 * Controlador encargado de gestionar las páginas públicas
 * relacionadas con los productos y recursos de la tienda.
 *
 * Funciones principales:
 *
 * - Cargar la página de inicio.
 * - Cargar la tienda con filtros y paginación.
 * - Mostrar el detalle de un producto.
 * - Registrar visitas/clicks en productos.
 * - Obtener productos relacionados.
 * - Mostrar reseñas de productos.
 * - Cargar el carrito con productos guardados en sesión.
 *
 * Este controlador utiliza:
 *
 * - Producto.php para obtener productos, categorías, niveles,
 *   recursos destacados, reseñas y productos relacionados.
 *
 * - recursosGratuitos.php para cargar recursos gratuitos en
 *   la página principal.
 */

require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/recursosGratuitos.php';

class ProductoController
{
    /**
     * Modelo de productos.
     *
     * Se utiliza para consultar productos, categorías, niveles,
     * recursos destacados, reseñas, productos relacionados y
     * datos necesarios para tienda, detalle y carrito.
     */
    private $productModel;

    /**
     * Constructor del controlador.
     * ---------------------------------------------------------
     * Instancia el modelo Producto para poder utilizarlo en
     * todas las funciones del controlador.
     */
    public function __construct()
    {
        $this->productModel = new Producto();
    }

    /**
     * Carga la página de inicio de la web.
     * ---------------------------------------------------------
     * Esta función obtiene los datos necesarios para mostrar
     * la home o página principal:
     *
     * - Categorías de productos.
     * - Recursos destacados.
     * - Recursos gratuitos destacados.
     *
     * Después carga la vista index_view.php.
     *
     * @return void
     */
    public function home()
    {
        /*
            Instanciamos el modelo de recursos gratuitos.

            Se utiliza únicamente en esta función para cargar
            algunos recursos gratuitos destacados en la página principal.
        */
        $recursosGratuitosModel = new RecursoGratuito();

        /*
            Obtenemos las categorías de productos.

            Estas categorías pueden utilizarse en la home para mostrar
            accesos rápidos o filtros visuales.
        */
        $categorias = $this->productModel->obtenerCategorias();

        /*
            Obtenemos recursos destacados.

            Normalmente son productos seleccionados para aparecer
            en la página principal.
        */
        $destacados = $this->productModel->obtenerRecursosDestacados();

        /*
            Obtenemos recursos gratuitos para mostrar en la home.

            El número 3 indica que queremos cargar como máximo
            tres recursos gratuitos destacados.
        */
        $gratuitosHome = $recursosGratuitosModel->obtenerRecursosGratuitosHome(3);

        /*
            Cargamos la vista principal.

            Las variables $categorias, $destacados y $gratuitosHome
            quedan disponibles dentro de index_view.php.
        */
        require_once __DIR__ . '/../vistas/index_view.php';
    }

    /**
     * Carga la tienda de productos con filtros y paginación.
     * ---------------------------------------------------------
     * Esta función permite mostrar productos filtrados por:
     *
     * - Búsqueda por texto.
     * - Categoría.
     * - Nivel.
     *
     * También aplica paginación para no mostrar todos los productos
     * de golpe.
     *
     * @return void
     */
    public function tienda()
    {
        /*
            Recogemos la página actual desde la URL.

            Si no viene el parámetro pagina, se usa la página 1.
        */
        $pagina = $_GET['pagina'] ?? 1;

        /*
            Número máximo de productos por página.
        */
        $limite = 9;

        /*
            Calculamos el offset para la consulta SQL.

            Ejemplo:
            Página 1 -> offset 0
            Página 2 -> offset 9
            Página 3 -> offset 18
        */
        $offset = ($pagina - 1) * $limite;

        /*
            Recogemos filtros enviados por GET.

            Si no existen, se dejan vacíos o en null.
        */
        $busqueda = $_GET['busqueda'] ?? '';
        $categoria = $_GET['categoria'] ?? null;
        $nivel = $_GET['nivel'] ?? null;

        /*
            Obtenemos productos filtrados desde el modelo.

            El modelo devuelve:
            - productos encontrados.
            - total de páginas.
        */
        $productos = $this->productModel->RecursosFiltrados(
            $categoria,
            $nivel,
            $busqueda,
            $limite,
            $offset
        );

        /*
            Lista de productos que se mostrará en la vista.
        */
        $resultado_productos = $productos['productos'];

        /*
            Total de páginas para la paginación.
        */
        $paginas = $productos['total_paginas'];

        /*
            Obtenemos niveles y categorías para pintar los filtros
            de la tienda.
        */
        $niveles = $this->productModel->obtenerNiveles();
        $categorias = $this->productModel->obtenerCategorias();

        /*
            Cargamos la vista de tienda.

            La vista recibirá:
            - $resultado_productos
            - $paginas
            - $niveles
            - $categorias
            - filtros actuales
        */
        require_once __DIR__ . '/../vistas/tienda_view.php';
    }

    /**
     * Muestra el detalle de un producto.
     * ---------------------------------------------------------
     * Esta función se ejecuta cuando el usuario entra en la ficha
     * de un producto concreto.
     *
     * Flujo:
     *
     * 1. Recibe el ID del producto por GET.
     * 2. Comprueba que exista.
     * 3. Obtiene el producto desde la base de datos.
     * 4. Registra un click o visita al producto.
     * 5. Obtiene productos relacionados.
     * 6. Obtiene reseñas del producto.
     * 7. Carga la vista producto_detalle.php.
     *
     * @return void
     */
    public function detalle()
    {
        /*
            Obtenemos el ID del producto desde la URL.

            Ejemplo:
            producto.php?id=5
        */
        $id = $_GET['id'] ?? null;

        /*
            Si no llega ningún ID, no podemos cargar el producto.
        */
        if (!$id) {
            die("Producto no encontrado");
        }

        /*
            Buscamos el producto en base de datos.
        */
        $producto = $this->productModel->obtenerProductosID($id);

        /*
            Si el producto no existe, detenemos la ejecución.
        */
        if (!$producto) {
            die("Producto no existe");
        }

        /*
            Registramos una visita o click al producto.

            Esto permite obtener métricas de interés en el panel admin.
        */
        $this->productModel->incrementarClicks($id);

        /*
            Obtenemos productos relacionados.

            Se buscan productos de la misma categoría, excluyendo
            el producto actual.
        */
        $relacionados = $this->productModel->obtenerProductosRelacionados(
            $producto['categoria_id'],
            $producto['id']
        );

        /*
            Obtenemos las reseñas asociadas al producto.
        */
        $resenas = $this->productModel->obtenerResenasPorProducto($id);

        /*
            Cargamos la vista de detalle.

            La vista tendrá disponibles:
            - $producto
            - $relacionados
            - $resenas
        */
        require_once __DIR__ . '/../vistas/producto_detalle.php';
    }

    /**
     * Carga la vista del carrito.
     * ---------------------------------------------------------
     * Esta función construye la información necesaria para mostrar
     * los productos añadidos al carrito.
     *
     * El carrito se guarda en $_SESSION['carrito'].
     *
     * Flujo:
     *
     * 1. Inicializa el array de productos del carrito.
     * 2. Inicializa los totales.
     * 3. Si hay productos en sesión, obtiene sus datos reales desde BD.
     * 4. Cruza los datos de base de datos con las cantidades de sesión.
     * 5. Calcula subtotal y total.
     * 6. Carga la vista carrito_view.php.
     *
     * @return void
     */
    public function carrito()
    {
        /*
            Array donde guardaremos los productos preparados
            para mostrarlos en la vista.
        */
        $productos_carrito = [];

        /*
            Inicializamos los totales del carrito.

            Actualmente no se aplica IVA ni gastos adicionales,
            por lo que el total será igual al subtotal.
        */
        $totales = [
            'subtotal' => 0,
            'iva' => 0,
            'total' => 0
        ];

        /*
            Comprobamos si existe carrito en sesión.

            $_SESSION['carrito'] debe contener los IDs de productos
            y sus cantidades.
        */
        if (!empty($_SESSION['carrito'])) {

            /*
                Obtenemos solo los IDs de productos que hay en el carrito.
            */
            $ids = array_keys($_SESSION['carrito']);

            /*
                Obtenemos los datos reales de esos productos desde la base de datos.

                Esto evita confiar en datos modificables del cliente.
            */
            $productos_data = $this->productModel->obtenerProductosID($ids);

            /*
                Recorremos los productos obtenidos y cruzamos:
                - Datos reales del producto desde BD.
                - Cantidad guardada en sesión.
            */
            foreach ($productos_data as $p) {

                /*
                    ID del producto actual.
                */
                $id_actual = $p['id'];

                /*
                    Cantidad del producto en el carrito.

                    Si no existe cantidad, se usa 1 por defecto.
                */
                $cantidad = (int)($_SESSION['carrito'][$id_actual] ?? 1);

                /*
                    Precio unitario del producto.
                */
                $precio = (float)($p['precio'] ?? 0);

                /*
                    Total de esa fila:
                    precio unitario x cantidad.
                */
                $precio_total = $precio * $cantidad;

                /*
                    Añadimos datos extra al producto para que la vista
                    pueda mostrar cantidad y total de línea.
                */
                $p['cantidad'] = $cantidad;
                $p['total_fila'] = $precio_total;

                /*
                    Añadimos el producto preparado al array del carrito.
                */
                $productos_carrito[] = $p;

                /*
                    Sumamos el total de la fila al subtotal general.
                */
                $totales['subtotal'] += $precio_total;
            }
        }

        /*
            Como actualmente no hay IVA ni gastos añadidos,
            el total es igual al subtotal.
        */
        $totales['total'] = $totales['subtotal'];

        /*
            Variables simples para facilitar su uso en carrito_view.php.
        */
        $subtotal = $totales['subtotal'];
        $iva = $totales['iva'];
        $total = $totales['total'];

        /*
            Cargamos la vista del carrito.

            La vista tendrá disponibles:
            - $productos_carrito
            - $subtotal
            - $iva
            - $total
        */
        require_once __DIR__ . '/../vistas/carrito_view.php';
    }
}