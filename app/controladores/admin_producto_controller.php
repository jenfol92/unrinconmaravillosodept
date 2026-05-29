<?php

/**
 * Controlador AdminProductoController
 * ---------------------------------------------------------
 * Controlador encargado de gestionar acciones AJAX relacionadas
 * con productos desde el panel de administración.
 *
 * Este controlador NO carga vistas.
 * Devuelve respuestas JSON porque sus métodos son llamados
 * desde JavaScript mediante fetch/AJAX.
 *
 * Responsabilidades:
 * - Comprobar permisos de administrador/gestor.
 * - Validar método HTTP.
 * - Validar datos recibidos.
 * - Coordinar modelo Producto y servicio R2Service.
 * - Devolver respuestas JSON.
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../servicios/R2Service.php';

class AdminProductoController
{
    /**
     * Sube o reemplaza el archivo descargable de un producto en Cloudflare R2.
     * ---------------------------------------------------------
     * Esta acción se usa desde el panel de administración mediante AJAX.
     *
     * Archivo público que llama a este método:
     * - public/ajax_subir_archivo_producto.php
     *
     * Entrada esperada por POST:
     * - producto_id
     *
     * Entrada esperada por FILES:
     * - archivo_recurso
     * - archivo
     *
     * Respuesta correcta:
     * {
     *   "ok": true,
     *   "mensaje": "Archivo subido correctamente.",
     *   "archivo_s3_key": "..."
     * }
     *
     * @return void
     */
    public function subirArchivoProductoR2Ajax()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            /**
             * Comprobación de permisos.
             * -------------------------------------------------
             * usuarioEsAdminOGestor() debe estar definido en
             * includes/session.php.
             */
            if (!usuarioEsAdminOGestor()) {
                responderNoAutorizado();
            }

            /**
             * Validación del método HTTP.
             */
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'Método no permitido.'
                ]);
                exit;
            }

            /**
             * Recogemos y validamos el ID del producto.
             */
            $producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;

            if ($producto_id <= 0) {
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'Producto no válido.'
                ]);
                exit;
            }

            /**
             * Recogida del archivo enviado.
             * -------------------------------------------------
             * Se mantienen los dos posibles nombres que ya usabas:
             * - archivo_recurso
             * - archivo
             */
            $archivo = null;

            if (isset($_FILES['archivo_recurso'])) {
                $archivo = $_FILES['archivo_recurso'];
            } elseif (isset($_FILES['archivo'])) {
                $archivo = $_FILES['archivo'];
            }

            if (!$archivo || empty($archivo['name'])) {
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'No se ha seleccionado ningún archivo.'
                ]);
                exit;
            }

            /**
             * Instanciamos el modelo Producto.
             */
            $productoModel = new Producto();

            /**
             * Obtenemos los datos actuales del producto.
             */
            $productoActual = $productoModel->obtenerProductosID($producto_id);

            if (!$productoActual) {
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'Producto no encontrado.'
                ]);
                exit;
            }

            /**
             * Instanciamos el servicio R2.
             */
            $r2Service = new R2Service();

            /**
             * Subimos el nuevo archivo a Cloudflare R2.
             */
            $nuevaKey = $r2Service->subirArchivoProducto(
                $producto_id,
                $archivo,
                $productoActual['titulo'] ?? 'recurso'
            );

            if (empty($nuevaKey)) {
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'No se ha podido generar la ruta del archivo.'
                ]);
                exit;
            }

            /**
             * Guardamos la nueva key en base de datos.
             */
            $productoModel->actualizarArchivoR2($producto_id, $nuevaKey);

            /**
             * Eliminamos el archivo anterior de R2 si existía.
             */
            if (!empty($productoActual['archivo_s3_key'])) {
                $r2Service->eliminarArchivo($productoActual['archivo_s3_key']);
            }

            /**
             * Respuesta correcta.
             */
            echo json_encode([
                'ok' => true,
                'mensaje' => 'Archivo subido correctamente.',
                'archivo_s3_key' => $nuevaKey
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Error subiendo archivo: ' . $e->getMessage()
            ]);
            exit;
        }
    }
    /**
 * Carga productos del panel de administración mediante AJAX.
 * ---------------------------------------------------------
 * Esta acción devuelve un listado paginado de productos para
 * el panel admin, aplicando filtros de búsqueda, estado,
 * categoría y periodo de datos.
 *
 * Este método NO carga ninguna vista.
 * Devuelve JSON porque es llamado desde JavaScript mediante fetch/AJAX.
 *
 * Entrada esperada por GET:
 * - pagina: página actual del listado.
 * - busqueda: texto de búsqueda.
 * - estado: estado del producto.
 * - categoria: ID de categoría.
 * - periodo_datos: periodo de fechas.
 * - datos_desde: fecha inicial personalizada.
 * - datos_hasta: fecha final personalizada.
 *
 * Respuesta correcta:
 * {
 *   "ok": true,
 *   "productos": [...],
 *   "total_paginas": 3,
 *   "pagina_actual": 1,
 *   "periodo_datos": {
 *      "periodo": "ultimos_30",
 *      "inicio": "2026-05-01",
 *      "fin": "2026-05-30"
 *   }
 * }
 *
 * @return void
 */
public function cargarProductosAdminAjax()
{
    /**
     * Indicamos que la respuesta será JSON en UTF-8.
     */
    header('Content-Type: application/json; charset=utf-8');

    /**
     * Comprobación de permisos.
     * -----------------------------------------------------
     * Solo administradores o gestores pueden cargar el listado
     * de productos del panel admin.
     */
    if (!usuarioEsAdminOGestor()) {
        responderNoAutorizado();
    }

    try {
        /**
         * Instanciamos el modelo Producto.
         * -------------------------------------------------
         * Este modelo contiene el método obtenerProductosAdmin(),
         * que realiza la consulta real a base de datos.
         */
        $model = new Producto();

        
        // PAGINACIÓN
     

        /**
         * Página actual.
         * -------------------------------------------------
         * Se obtiene desde GET y se convierte a entero.
         * max(1, ...) evita páginas menores que 1.
         */
        $pagina = max(1, (int)($_GET['pagina'] ?? 1));

        /**
         * Número máximo de productos por página.
         */
        $limite = 10;

        /**
         * Offset para la consulta SQL.
         * -------------------------------------------------
         * página 1 → offset 0
         * página 2 → offset 10
         * página 3 → offset 20
         */
        $offset = ($pagina - 1) * $limite;

        
        // FILTROS BÁSICOS
     

        /**
         * Texto de búsqueda.
         */
        $busqueda = trim($_GET['busqueda'] ?? '');

        /**
         * Estado del producto.
         */
        $estado = trim($_GET['estado'] ?? '');

        /**
         * Categoría seleccionada.
         */
        $categoria = $_GET['categoria'] ?? '';

        /**
         * El modelo espera un array de categorías.
         * Si llega una categoría, se convierte a entero y se mete
         * dentro de un array. Si no llega, se envía array vacío.
         */
        $categorias = $categoria !== '' ? [(int)$categoria] : [];

        /**
         * Filtro de niveles.
         * De momento queda vacío, pero se mantiene por compatibilidad
         * con la firma de Producto::obtenerProductosAdmin().
         */
        $niveles = [];

        
        // FILTRO DE PERIODO
   

        /**
         * Periodo seleccionado para filtrar datos.
         *
         * Valores esperados:
         * - todos
         * - ultimos_7
         * - ultimos_30
         * - mes_anterior
         * - personalizado
         */
        $periodoDatos = $_GET['periodo_datos'] ?? 'todos';

        /**
         * Fechas personalizadas.
         * Solo se usan cuando periodo_datos = personalizado.
         */
        $datosDesde = $_GET['datos_desde'] ?? '';
        $datosHasta = $_GET['datos_hasta'] ?? '';

        /**
         * Fechas finales que se enviarán al modelo.
         * null significa que no se aplica filtro de fechas.
         */
        $fechaInicio = null;
        $fechaFin = null;

        /**
         * Fecha actual para calcular rangos relativos.
         */
        $hoy = new DateTime();

        /**
         * Cálculo del periodo.
         */
        switch ($periodoDatos) {
            case 'ultimos_7':
                $fechaInicio = (clone $hoy)->modify('-6 days')->format('Y-m-d');
                $fechaFin = $hoy->format('Y-m-d');
                break;

            case 'ultimos_30':
                $fechaInicio = (clone $hoy)->modify('-29 days')->format('Y-m-d');
                $fechaFin = $hoy->format('Y-m-d');
                break;

            case 'mes_anterior':
                $fechaInicio = (new DateTime('first day of last month'))->format('Y-m-d');
                $fechaFin = (new DateTime('last day of last month'))->format('Y-m-d');
                break;

            case 'personalizado':
                if ($datosDesde !== '' && $datosHasta !== '') {
                    $fechaInicio = $datosDesde;
                    $fechaFin = $datosHasta;
                }
                break;

            case 'todos':
            default:
                $fechaInicio = null;
                $fechaFin = null;
                break;
        }

        // CONSULTA AL MODELO
       

        /**
         * Obtenemos productos desde el modelo.
         */
        $resultado = $model->obtenerProductosAdmin(
            $categorias,
            $niveles,
            $busqueda,
            $estado,
            $limite,
            $offset,
            $fechaInicio,
            $fechaFin
        );

        /**
         * Respuesta correcta.
         */
        echo json_encode([
            'ok' => true,
            'productos' => $resultado['productos'],
            'total_paginas' => $resultado['total_paginas'],
            'pagina_actual' => $pagina,
            'periodo_datos' => [
                'periodo' => $periodoDatos,
                'inicio' => $fechaInicio,
                'fin' => $fechaFin
            ]
        ]);
        exit;

    } catch (Exception $e) {
        /**
         * Respuesta en caso de error.
         *
         * En producción sería mejor no mostrar $e->getMessage()
         * para no exponer detalles internos.
         */
        echo json_encode([
            'ok' => false,
            'error' => 'Error cargando productos: ' . $e->getMessage()
        ]);
        exit;
    }
}
/**
 * Crea una categoría de producto mediante AJAX.
 * ---------------------------------------------------------
 * Esta acción permite crear una nueva categoría de productos
 * desde el panel de administración, normalmente desde un modal
 * o formulario sin recargar la página.
 *
 * Este método NO carga vistas.
 * Devuelve JSON porque es llamado desde JavaScript mediante fetch/AJAX.
 *
 * Archivo público que llama a este método:
 * - public/ajax_crear_categoria.php
 *   o el nombre real que ya esté usando tu JS.
 *
 * Entrada esperada por POST:
 * - nombre: nombre de la nueva categoría.
 *
 * Respuesta correcta:
 * {
 *   "ok": true,
 *   "categoria": {...}
 * }
 *
 * Respuesta con error:
 * {
 *   "ok": false,
 *   "error": "Mensaje de error"
 * }
 *
 * @return void
 */
public function crearCategoriaProductoAjax()
{
    /**
     * Indicamos que la respuesta será JSON en UTF-8.
     */
    header('Content-Type: application/json; charset=utf-8');

    /**
     * Comprobación de permisos.
     * -----------------------------------------------------
     * Solo administradores o gestores pueden crear categorías
     * de productos desde el panel admin.
     */
    if (!usuarioEsAdminOGestor()) {
        responderNoAutorizado();
    }

    /**
     * Recogida del nombre de la categoría.
     * -----------------------------------------------------
     * Se obtiene desde POST y se aplica trim() para eliminar
     * espacios innecesarios al principio y al final.
     */
    $nombre = trim($_POST['nombre'] ?? '');

    /**
     * Validación del nombre.
     * -----------------------------------------------------
     * La categoría no puede crearse sin nombre.
     */
    if ($nombre === '') {
        echo json_encode([
            'ok' => false,
            'error' => 'El nombre de la categoría es obligatorio'
        ]);
        exit;
    }

    try {
        /**
         * Instanciamos el modelo Producto.
         * -------------------------------------------------
         * Este modelo contiene la lógica relacionada con
         * productos, categorías, reseñas, favoritos, etc.
         */
        $model = new Producto();

        /**
         * Creamos la categoría.
         * -------------------------------------------------
         * El método crearCategoria($nombre) debe insertar la
         * nueva categoría en la base de datos y devolver los
         * datos de la categoría creada.
         */
        $categoria = $model->crearCategoria($nombre);

        /**
         * Respuesta correcta.
         */
        echo json_encode([
            'ok' => true,
            'categoria' => $categoria
        ]);
        exit;

} catch (Throwable $e) {
    echo json_encode([
        'ok' => false,
        'error' => 'Error creando la categoría.',
        'debug' => $e->getMessage()
    ]);
    exit;
}
}
}