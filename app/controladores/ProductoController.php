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
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/productos_visitados_cookies.php';
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
 * 4. Guarda el ID del producto en la cookie productos_recientes.
 * 5. Registra un click o visita en base de datos.
 * 6. Obtiene productos relacionados.
 * 7. Obtiene reseñas del producto.
 * 8. Prepara las rutas de imagen y vídeo.
 * 9. Carga la vista producto_detalle.php.
 *
 * @return void
 */
public function detalle()
{
    /*
        Obtenemos el ID del producto desde la URL.

        Ejemplo:
        producto_detalle.php?id=5
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

    $resumenResenas = $this->productModel->obtenerResumenResenasProducto($id);

$media_resenas = (float)($resumenResenas['media_resenas'] ?? 0);
$total_resenas = (int)($resumenResenas['total_resenas'] ?? 0);

    /*
        Guardamos el producto actual en la cookie de productos recientes.
        ---------------------------------------------------------
        Esta línea es la que cumple el requisito de trabajar cookies
        desde PHP.

        Es importante colocarla aquí:
        - Después de comprobar que el producto existe.
        - Antes de cargar la vista.

        Motivo:
        setcookie() necesita enviar cabeceras HTTP antes de imprimir HTML.
    */
    guardarProductoRecienteCookie((int)$producto['id']);

    /*
        Registramos una visita o click al producto.

        Esto sirve para estadísticas internas o métricas del panel admin.
        No sustituye a la cookie.

        Diferencia:
        - incrementarClicks(): guarda datos en base de datos.
        - guardarProductoRecienteCookie(): guarda los últimos vistos
          en el navegador del usuario.
    */
    $this->productModel->incrementarClicks($id);

    /*
        Obtenemos productos relacionados.

        Se buscan productos de la misma categoría, excluyendo el producto actual.
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
        Preparamos las rutas de imagen y vídeo del producto.
        La vista usará esta variable para mostrar los recursos multimedia.
    */
    $mediaProducto = [
        /*
            Ruta de imagen del producto.

            Si el producto tiene imagen, generamos la ruta completa.
            Si no tiene imagen, dejamos null.
        */
        'ruta_imagen' => !empty($producto['imagen'])
            ? BASE_URL . 'static/images/img/' . rawurlencode($producto['imagen'])
            : null,

        /*
            Indica si el producto tiene vídeo asociado.
        */
        'tiene_video' => !empty($producto['video_url']),

        /*
            Ruta del vídeo si existe.
        */
        'ruta_video' => !empty($producto['video_url'])
            ? BASE_URL . 'static/videos/' . rawurlencode($producto['video_url'])
            : null,

        /*
            Tipo MIME del vídeo.
        */
        'tipo_video' => 'video/mp4'
    ];

    /*
        Cargamos la vista de detalle.

        La vista tendrá disponibles:
        - $producto
        - $relacionados
        - $resenas
        - $mediaProducto
    */
    require_once __DIR__ . '/../vistas/producto_detalle.php';
}

/**
 * Devuelve recursos filtrados para la tienda pública mediante AJAX.
 * ---------------------------------------------------------
 * Este método se usa desde JavaScript para actualizar el listado
 * de productos sin recargar la página.
 *
 * Recibe filtros por GET:
 * - categorias
 * - niveles
 * - busqueda
 * - pagina
 *
 * También comprueba si el usuario está logueado para marcar
 * qué productos están en favoritos.
 *
 * Devuelve siempre una respuesta JSON.
 *
 * @return void
 */
public function filtrarRecursosAjax()
{
    /*
        Indicamos que la respuesta será JSON.

        Esto es importante porque este método se llama desde JavaScript
        usando fetch(), AJAX o una petición similar.
    */
    header('Content-Type: application/json; charset=utf-8');

    /*
        Recogemos filtros enviados por GET.

        categorias y niveles pueden llegar como JSON desde JavaScript.
        Por ejemplo:
        ["Primaria", "Infantil"]
    */
    $categoriasJson = $_GET['categorias'] ?? '';
    $nivelesJson = $_GET['niveles'] ?? '';
    $busqueda = trim($_GET['busqueda'] ?? '');

    /*
        Recogemos la página actual.

        La convertimos a entero para evitar trabajar con texto recibido
        directamente desde la URL.
    */
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

    /*
        Si la página es menor que 1, la corregimos a 1.
    */
    if ($pagina < 1) {
        $pagina = 1;
    }

    /*
        Configuración de paginación.

        En este caso se muestran 9 productos por página.
    */
    $limite = 9;
    $offset = ($pagina - 1) * $limite;

    /*
        Convertimos los filtros JSON a arrays PHP.

        Si no llega nada, usamos arrays vacíos.
    */
    $categorias = $categoriasJson ? json_decode($categoriasJson, true) : [];
    $niveles = $nivelesJson ? json_decode($nivelesJson, true) : [];

    /*
        Si json_decode falla o no devuelve un array,
        dejamos el filtro como array vacío para evitar errores.
    */
    if (!is_array($categorias)) {
        $categorias = [];
    }

    if (!is_array($niveles)) {
        $niveles = [];
    }

    /*
        Obtenemos los productos filtrados desde el modelo.

        El modelo Producto es quien debe encargarse de consultar
        la base de datos.
    */
    $resultado = $this->productModel->RecursosFiltrados(
        $categorias,
        $niveles,
        $busqueda,
        $limite,
        $offset
    );

    /*
        Array vacío por defecto para favoritos.

        Si el usuario no está logueado, ningún producto se marcará
        como favorito.
    */
    $favoritos = [];

    /*
        Si el usuario está logueado, obtenemos sus productos favoritos.
    */
    if (isset($_SESSION['usuario_id'])) {
        $favoritos = $this->productModel->obtenerFavoritosUsuario(
            (int)$_SESSION['usuario_id']
        );
    }

    /*
        Comprobamos que el resultado tenga la clave productos
        y que sea un array.
    */
    if (isset($resultado['productos']) && is_array($resultado['productos'])) {

        /*
            Recorremos cada producto para añadir una clave extra:
            es_favorito.
        */
        foreach ($resultado['productos'] as &$producto) {

            /*
                Si el ID del producto está dentro del array de favoritos,
                marcamos es_favorito como true.
            */
            $producto['es_favorito'] = in_array(
                $producto['id'],
                $favoritos
            );
        }

        /*
            Rompemos la referencia creada por foreach con &.
            Es una buena práctica para evitar efectos raros después.
        */
        unset($producto);

    } else {

        /*
            Si por cualquier motivo no viene la clave productos,
            dejamos un array vacío para que el JSON no rompa en JavaScript.
        */
        $resultado['productos'] = [];
    }

    /*
        Devolvemos JSON limpio.
    */
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    exit;
}
/**
 * Devuelve los últimos productos visitados en formato JSON.
 * ---------------------------------------------------------
 * Este método se usa desde tienda.js para pintar el widget
 * "Últimos vistos" o "Últimos recursos vistos".
 *
 * Flujo:
 * 1. Lee la cookie productos_recientes mediante PHP.
 * 2. Si no hay productos recientes, devuelve un array vacío.
 * 3. Consulta los productos en base de datos.
 * 4. Devuelve una respuesta JSON para JavaScript.
 *
 * Archivo público que llama a este método:
 * - public/ajax_ultimos_productos_visitados.php
 *
 * @return void
 */
public function ultimosVisitadosAjax()
{
    /*
        Indicamos que la respuesta será JSON.
    */
    header('Content-Type: application/json; charset=utf-8');

    try {
        /*
            Leemos los IDs guardados en la cookie productos_recientes.
            Esta función está en includes/productos_visitados_cookies.php.
        */
        $ids = leerProductosRecientesCookie();

        /*
            Si no hay productos recientes, devolvemos una respuesta correcta
            pero con array vacío.
        */
        if (empty($ids)) {
            echo json_encode([
                'ok' => true,
                'productos' => [],
                'total' => 0
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        /*
            Consultamos en base de datos los productos correspondientes
            a los IDs guardados en la cookie.
        */
        $productos = $this->productModel->obtenerProductosPorIds($ids);

        /*
            Devolvemos los productos encontrados.
        */
        echo json_encode([
            'ok' => true,
            'productos' => $productos,
            'total' => count($productos)
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Throwable $e) {
        /*
            En caso de error, devolvemos JSON controlado.
        */
        echo json_encode([
            'ok' => false,
            'error' => 'No se pudieron cargar los últimos productos vistos.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
}