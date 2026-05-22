<?php

/**
 * Controlador de administración.
 * ---------------------------------------------------------
 * Este controlador centraliza las acciones principales del
 * panel de administración de la aplicación:
 *
 * - Carga del dashboard.
 * - Gestión de productos de pago.
 * - Subida de archivos a Cloudflare R2.
 * - Eliminación lógica o física de productos.
 * - Exportación de productos a PDF.
 * - Gestión de recursos gratuitos.
 * - Gestión de imágenes locales.
 * - Cálculo de rangos de fechas para métricas.
 *
 * El controlador trabaja con distintos modelos:
 * - Admin
 * - Producto
 * - Usuario
 * - Soporte
 * - RecursoGratuito
 *
 * También utiliza servicios externos como:
 * - R2Service para gestionar archivos en Cloudflare R2.
 * - Dompdf para exportar listados en PDF.
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../modelos/Admin.php';
require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../modelos/Soporte.php';
require_once __DIR__ . '/../servicios/R2Service.php';
require_once __DIR__ . '/../modelos/recursosGratuitos.php';

class AdminController
{
    /**
     * Modelo de administración.
     *
     * Se utiliza para obtener estadísticas generales del dashboard,
     * productos más vendidos y datos globales del panel.
     */
    private $adminModel;

    /**
     * Modelo de productos.
     *
     * Se utiliza para crear, editar, eliminar, consultar productos,
     * categorías, niveles y datos necesarios para el panel admin.
     */
    private $productoModel;

    /**
     * Modelo de usuarios.
     *
     * Se utiliza para listar usuarios clientes y consultar información
     * asociada a sus compras, descargas y actividad.
     */
    private $usuarioModel;

    /**
     * Modelo de soporte.
     *
     * Se utiliza para obtener tickets, sugerencias y mensajes recibidos
     * desde el formulario público de contacto.
     */
    private $soporteModel;

    /**
     * Modelo de recursos gratuitos.
     *
     * Se utiliza para gestionar contenido gratuito, categorías gratuitas,
     * métricas de clicks y descargas.
     */
    private $recursoGratuitoModel;

    /**
     * Constructor del controlador.
     * ---------------------------------------------------------
     * Inicializa todos los modelos que necesita el panel de administración.
     *
     * Cada modelo se encarga de una parte concreta de la aplicación:
     * estadísticas, productos, usuarios, soporte y recursos gratuitos.
     */
    public function __construct()
    {
        $this->adminModel = new Admin();
        $this->productoModel = new Producto();
        $this->usuarioModel = new Usuario();
        $this->soporteModel = new Soporte();
        $this->recursoGratuitoModel = new RecursoGratuito();
    }

    /**
     * Carga el dashboard principal del panel de administración.
     * ---------------------------------------------------------
     * Esta función recopila toda la información necesaria para pintar
     * la vista admin_view.php:
     *
     * - Estadísticas generales de ventas.
     * - Productos más vendidos.
     * - Tickets de soporte.
     * - Categorías y niveles.
     * - Usuarios registrados.
     * - Productos para la sección admin.
     * - Sugerencias.
     * - Mensajes de contacto web.
     * - Categorías y recursos gratuitos.
     *
     * También calcula rangos de fechas para filtrar ventas y métricas.
     */
    public function dashboard()
    {
        // Periodo seleccionado para las ventas del dashboard.
        $rangoVentas = $this->obtenerRangoVentasDashboard();

        // Estadísticas generales según el periodo seleccionado.
        $stats = $this->adminModel->obtenerEstadisticas(
            $rangoVentas['inicio'],
            $rangoVentas['fin']
        );

        // Productos más vendidos según el periodo seleccionado.
        $productos = $this->adminModel->obtenerProductosMasVendidos(
            $rangoVentas['inicio'],
            $rangoVentas['fin'],
            5
        );

        // Tickets pendientes del modelo Admin.
        $tickets = $this->adminModel->obtenerTicketsPendientes();

        // Categorías y niveles de productos.
        $categorias = $this->productoModel->obtenerCategorias();
        $niveles = $this->productoModel->obtenerNiveles();

        // Usuarios clientes registrados.
        $usuarios = $this->usuarioModel->obtenerUsuariosClientes();

        // Productos completos para el listado del panel de administración.
        $resultadoProductosAdmin = $this->productoModel->obtenerProductosAdmin();

        // Lista real de productos.
        $productosAdmin = $resultadoProductosAdmin['productos'];

        // Total de páginas para la paginación del listado de productos.
        $totalPaginasProductosAdmin = $resultadoProductosAdmin['total_paginas'];

        // Categorías y niveles para filtros y formularios.
        $categorias = $this->productoModel->obtenerCategorias();
        $niveles = $this->productoModel->obtenerNiveles();

        // Soporte, sugerencias y mensajes de contacto web.
        $tickets = $this->soporteModel->obtenerTicketsAdmin();
        $sugerencias = $this->soporteModel->obtenerSugerencias();
        $mensajesContacto = $this->soporteModel->obtenerMensajesContactoAdmin();

        // Categorías de recursos gratuitos.
        $categoriasGratuitas = $this->recursoGratuitoModel->obtenerCategoriasGratuitas();

        // Rango seleccionado para métricas de clicks y descargas de recursos gratuitos.
        $rangoMetricasGratuitas = $this->obtenerRangoMetricasGratuitas();

        /*
         * Recursos gratuitos para el panel de administración.
         *
         * Si inicio y fin son null, el modelo devuelve totales históricos.
         * Si tienen fechas, devuelve datos solo del periodo seleccionado.
         */
        $recursosGratuitosAdmin = $this->recursoGratuitoModel->obtenerRecursosGratuitosAdmin(
            $rangoMetricasGratuitas['inicio'],
            $rangoMetricasGratuitas['fin']
        );

        // Carga de la vista principal del panel admin.
        require_once __DIR__ . '/../vistas/admin_view.php';
    }

    /**
     * Sube o sustituye el archivo descargable de un producto en Cloudflare R2.
     * ---------------------------------------------------------
     * Flujo:
     * 1. Comprueba que el usuario esté autenticado y tenga rol admin.
     * 2. Valida que la petición sea POST.
     * 3. Obtiene el producto.
     * 4. Sube el archivo nuevo a Cloudflare R2.
     * 5. Guarda la nueva key en base de datos.
     * 6. Si existía un archivo anterior, lo elimina de R2.
     *
     * Esta función no devuelve JSON, sino que redirige al panel admin.
     */
    public function subirArchivoProductoR2()
    {
        $rolUsuario = (int)($_SESSION['rol'] ?? 0);

        // Solo usuarios con rol 1 o 2 pueden subir archivos.
        if (empty($_SESSION['usuario_id']) || !in_array($rolUsuario, [1, 2], true)) {
            header('Location: /UNRINCONDEPT/public/login.php');
            exit;
        }

        // La subida solo se permite por POST.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /UNRINCONDEPT/public/admin.php');
            exit;
        }

        $producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;

        if ($producto_id <= 0) {
            die('Producto no válido.');
        }

        if (!isset($_FILES['archivo']) || empty($_FILES['archivo']['name'])) {
            die('No se ha seleccionado ningún archivo.');
        }

        try {
            // Obtenemos el producto actual.
            $productoActual = $this->productoModel->obtenerProductosID($producto_id);

            if (!$productoActual) {
                die('Producto no encontrado.');
            }

            // Instanciamos el servicio de Cloudflare R2.
            $r2Service = new R2Service();

            // Subimos el archivo y obtenemos la nueva key.
            $nuevaKey = $r2Service->subirArchivoProducto(
                $producto_id,
                $_FILES['archivo'],
                $productoActual['titulo'] ?? 'recurso'
            );

            if (!empty($nuevaKey)) {
                // Guardamos la key del archivo nuevo en la tabla productos.
                $this->productoModel->actualizarArchivoR2($producto_id, $nuevaKey);

                // Si había un archivo anterior, lo eliminamos de R2.
                if (!empty($productoActual['archivo_s3_key'])) {
                    $r2Service->eliminarArchivo($productoActual['archivo_s3_key']);
                }
            }

            header('Location: /UNRINCONDEPT/public/admin.php?archivo=subido');
            exit;

        } catch (Exception $e) {
            die('Error subiendo archivo: ' . htmlspecialchars($e->getMessage()));
        }
    }

    /**
     * Crea o actualiza un producto desde el panel admin mediante AJAX.
     * ---------------------------------------------------------
     * Esta función devuelve siempre una respuesta JSON.
     *
     * Permite:
     * - Crear un producto nuevo.
     * - Editar un producto existente.
     * - Subir o sustituir la imagen local del producto.
     * - Validar título, categoría y nivel.
     *
     * El archivo descargable no se sube aquí, sino en subirArchivoProductoR2().
     */
    public function guardarProducto()
    {
        header('Content-Type: application/json; charset=utf-8');

        $rolUsuario = (int)($_SESSION['rol'] ?? 0);

        // Control de permisos.
        if (empty($_SESSION['usuario_id']) || !in_array($rolUsuario, [1, 2], true)) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'No tienes permisos para guardar productos.'
            ]);
            exit;
        }

        // Solo se permite POST.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Método no permitido.'
            ]);
            exit;
        }

        $producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;

        $productoActual = null;
        $imagen = 'default.png';

        /*
         * Si producto_id es mayor que 0, estamos editando.
         * En ese caso buscamos el producto actual para conservar datos previos.
         */
        if ($producto_id > 0) {
            $productoActual = $this->productoModel->obtenerProductosID($producto_id);

            if (!$productoActual) {
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'Producto no encontrado.'
                ]);
                exit;
            }

            $imagen = $productoActual['imagen'] ?? 'default.png';
        }

        try {
            /*
             * Gestión de imagen local.
             * Si se sube una imagen nueva, se guarda y se elimina la anterior.
             */
            if (!empty($_FILES['imagen']['name'])) {
                $nuevaImagen = $this->guardarImagenLocal($_FILES['imagen'], 'producto');

                if (!empty($nuevaImagen)) {
                    if ($productoActual && !empty($productoActual['imagen'])) {
                        $this->eliminarImagenLocal($productoActual['imagen']);
                    }

                    $imagen = $nuevaImagen;
                }
            }

            // Datos principales del producto.
            $datos = [
                'titulo' => trim($_POST['titulo'] ?? ''),
                'precio' => (float)($_POST['precio'] ?? 0),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'contenido' => trim($_POST['contenido'] ?? ''),
                'categoria_id' => (int)($_POST['categoria_id'] ?? 0),
                'nivel_id' => (int)($_POST['nivel_id'] ?? 0),
                'estado' => $_POST['estado'] ?? 'activo',
                'imagen' => $imagen
            ];

            // Validaciones mínimas en servidor.
            if ($datos['titulo'] === '') {
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'El título es obligatorio.'
                ]);
                exit;
            }

            if ($datos['categoria_id'] <= 0) {
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'Debes seleccionar una categoría.'
                ]);
                exit;
            }

            if ($datos['nivel_id'] <= 0) {
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'Debes seleccionar un nivel.'
                ]);
                exit;
            }

            /*
             * Si producto_id > 0, actualizamos.
             * Si no, creamos un producto nuevo.
             */
            if ($producto_id > 0) {
                $this->productoModel->actualizarProductoAdmin($producto_id, $datos);

                echo json_encode([
                    'ok' => true,
                    'mensaje' => 'Producto actualizado correctamente. Puedes asociar o sustituir el archivo descargable.',
                    'producto_id' => $producto_id,
                    'modo' => 'editar'
                ]);
                exit;
            }

            $nuevo_id = $this->productoModel->crearProductoAdmin($datos);

            echo json_encode([
                'ok' => true,
                'mensaje' => 'Producto creado correctamente. Ahora puedes asociar un archivo a este recurso.',
                'producto_id' => $nuevo_id,
                'modo' => 'crear'
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Error guardando producto: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Elimina un producto o lo desactiva si tiene pedidos asociados.
     * ---------------------------------------------------------
     * Esta función devuelve JSON y se utiliza desde el panel admin.
     *
     * Flujo:
     * 1. Comprueba permisos.
     * 2. Valida producto_id.
     * 3. Obtiene el producto.
     * 4. Elimina archivo de Cloudflare R2 si existe.
     * 5. Si el producto tiene pedidos, lo desactiva.
     * 6. Si no tiene pedidos, lo elimina físicamente.
     */
    public function eliminarProducto()
    {
        header('Content-Type: application/json; charset=utf-8');

        $rolUsuario = (int)($_SESSION['rol'] ?? 0);

        if (empty($_SESSION['usuario_id']) || !in_array($rolUsuario, [1, 2], true)) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'No tienes permisos para eliminar productos.'
            ]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Método no permitido.'
            ]);
            exit;
        }

        $producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;

        if ($producto_id <= 0) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Producto no válido.'
            ]);
            exit;
        }

        try {
            // Obtenemos el producto actual.
            $producto = $this->productoModel->obtenerProductosID($producto_id);

            if (!$producto) {
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'Producto no encontrado.'
                ]);
                exit;
            }

            // Si tiene archivo en Cloudflare R2, lo eliminamos.
            if (!empty($producto['archivo_s3_key'])) {
                $r2Service = new R2Service();
                $r2Service->eliminarArchivo($producto['archivo_s3_key']);
            }

            /*
             * Si el producto tiene pedidos asociados, no lo borramos físicamente.
             * Se desactiva para mantener la integridad histórica de las compras.
             */
            if ($this->productoModel->productoTienePedidos($producto_id)) {
                $this->productoModel->desactivarProductoAdmin($producto_id);

                echo json_encode([
                    'ok' => true,
                    'mensaje' => 'El recurso tenía pedidos asociados. Se ha ocultado de la tienda y se ha eliminado su archivo de Cloudflare.',
                    'modo' => 'desactivado'
                ]);
                exit;
            }

            // Si no tiene pedidos, se elimina físicamente.
            $this->productoModel->eliminarProductoFisicoAdmin($producto_id);

            echo json_encode([
                'ok' => true,
                'mensaje' => 'Recurso eliminado correctamente junto con su archivo de Cloudflare.',
                'modo' => 'eliminado'
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Error eliminando producto: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Obtiene el rango de fechas usado para las ventas del dashboard.
     * ---------------------------------------------------------
     * Lee el parámetro GET periodo_ventas y lo convierte en fecha de inicio
     * y fecha de fin.
     *
     * Periodos permitidos:
     * - hoy
     * - ultimos_7
     * - ultimos_30
     * - mes_anterior
     * - anio_actual
     * - personalizado
     * - mes_actual
     *
     * @return array Devuelve periodo, inicio y fin en formato Y-m-d.
     */
    private function obtenerRangoVentasDashboard()
    {
        $periodo = $_GET['periodo_ventas'] ?? 'mes_actual';

        $hoy = new DateTime();

        switch ($periodo) {
            case 'hoy':
                $inicio = clone $hoy;
                $fin = clone $hoy;
                break;

            case 'ultimos_7':
                $inicio = (clone $hoy)->modify('-6 days');
                $fin = clone $hoy;
                break;

            case 'ultimos_30':
                $inicio = (clone $hoy)->modify('-29 days');
                $fin = clone $hoy;
                break;

            case 'mes_anterior':
                $inicio = new DateTime('first day of last month');
                $fin = new DateTime('last day of last month');
                break;

            case 'anio_actual':
                $inicio = new DateTime(date('Y') . '-01-01');
                $fin = new DateTime(date('Y') . '-12-31');
                break;

            case 'personalizado':
                $desde = $_GET['fecha_desde'] ?? null;
                $hasta = $_GET['fecha_hasta'] ?? null;

                if ($desde && $hasta) {
                    $inicio = new DateTime($desde);
                    $fin = new DateTime($hasta);
                } else {
                    $inicio = new DateTime('first day of this month');
                    $fin = new DateTime('last day of this month');
                    $periodo = 'mes_actual';
                }
                break;

            case 'mes_actual':
            default:
                /*
                 * Mes actual completo:
                 * - Desde el día 1.
                 * - Hasta el último día real del mes.
                 */
                $inicio = new DateTime('first day of this month');
                $fin = new DateTime('last day of this month');
                $periodo = 'mes_actual';
                break;
        }

        return [
            'periodo' => $periodo,
            'inicio' => $inicio->format('Y-m-d'),
            'fin' => $fin->format('Y-m-d'),
        ];
    }

    /**
     * Exporta el listado de productos a PDF.
     * ---------------------------------------------------------
     * Utiliza Dompdf para generar un archivo PDF con productos filtrados.
     *
     * Permite filtrar por:
     * - búsqueda
     * - categoría
     * - estado
     *
     * La vista HTML del reporte se encuentra en:
     * app/vistas/reporte_productos_pdf_view.php
     */
    public function exportarProductosPdf()
    {
        $rolUsuario = (int)($_SESSION['rol'] ?? 0);

        if (empty($_SESSION['usuario_id']) || !in_array($rolUsuario, [1, 2], true)) {
            header('Location: /UNRINCONDEPT/public/login.php');
            exit;
        }

        $busqueda = $_GET['busqueda'] ?? '';
        $categoria = $_GET['categoria'] ?? '';
        $estado = $_GET['estado'] ?? '';

        // Obtenemos los productos filtrados para el PDF.
        $productos = $this->productoModel->obtenerProductosAdminParaPdf(
            $busqueda,
            $categoria,
            $estado
        );

        $fecha = date('d/m/Y H:i');

        /*
         * Capturamos la vista HTML en buffer para convertirla posteriormente a PDF.
         */
        ob_start();
        require __DIR__ . '/../vistas/reporte_productos_pdf_view.php';
        $html = ob_get_clean();

        // Configuración de Dompdf.
        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Descarga del archivo generado.
        $dompdf->stream('reporte-recursos.pdf', [
            'Attachment' => true
        ]);

        exit;
    }

    /**
     * Crea o actualiza un recurso gratuito mediante AJAX.
     * ---------------------------------------------------------
     * Esta función gestiona recursos gratuitos del panel admin.
     *
     * Permite:
     * - Crear recurso gratuito.
     * - Editar recurso gratuito.
     * - Subir o conservar imagen.
     * - Asociar URL de Google Drive.
     * - Guardar formato y estado.
     *
     * Actualmente la subida a Google Drive queda preparada para OAuth futuro.
     */
    public function guardarRecursoGratuitoAjax()
    {
        header('Content-Type: application/json; charset=utf-8');

        $rolUsuario = (int)($_SESSION['rol'] ?? 0);

        if (empty($_SESSION['usuario_id']) || !in_array($rolUsuario, [1, 2], true)) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'No tienes permisos.'
            ]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Método no permitido.'
            ]);
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $titulo = trim($_POST['titulo'] ?? '');

        $categoriaId = (int)(
            $_POST['categoria_id']
            ?? $_POST['categoria_gratuita_id']
            ?? 0
        );

        /*
         * De momento puede venir vacío, porque más adelante el enlace
         * podrá generarse automáticamente con Google Drive y OAuth.
         */
        $urlDrive = trim(
            $_POST['url_drive']
                ?? $_POST['drive_url']
                ?? ''
        );

        $estado = $_POST['estado'] ?? 'activo';

        if ($titulo === '') {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'El título es obligatorio.'
            ]);
            exit;
        }

        if ($categoriaId <= 0) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Debes seleccionar una categoría.'
            ]);
            exit;
        }

        try {
            $recursoActual = null;
            $imagen = 'default.png';

            /*
             * Si id > 0, estamos editando un recurso existente.
             */
            if ($id > 0) {
                $recursoActual = $this->recursoGratuitoModel->obtenerRecursoGratuitoAdminPorId($id);

                if (!$recursoActual) {
                    echo json_encode([
                        'ok' => false,
                        'mensaje' => 'Recurso gratuito no encontrado.'
                    ]);
                    exit;
                }

                // Conserva imagen anterior si no se sube una nueva.
                $imagen = $recursoActual['imagen'] ?? 'default.png';

                // Conserva URL Drive anterior si no se envía una nueva.
                if ($urlDrive === '') {
                    $urlDrive = $recursoActual['url_drive'] ?? '';
                }
            }

            /*
             * Imagen de portada del recurso gratuito.
             */
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
                $nuevaImagen = $this->guardarImagenRecursoGratuito($_FILES['imagen']);

                if (!empty($nuevaImagen)) {
                    if ($recursoActual && !empty($recursoActual['imagen'])) {
                        $this->eliminarImagenLocal($recursoActual['imagen']);
                    }

                    $imagen = $nuevaImagen;
                }
            }

            /*
             * Archivo PDF/ZIP para Google Drive.
             *
             * De momento no obligamos a tener url_drive porque se prevé generar
             * el enlace automáticamente cuando se implemente OAuth.
             */
            $datos = [
                'id' => $id,
                'titulo' => $titulo,
                'imagen' => $imagen,
                'categoria_id' => $categoriaId,
                'url_drive' => $urlDrive,
                'formato' => $_POST['formato'] ?? 'PDF',
                'estado' => $estado
            ];

            $resultado = $this->recursoGratuitoModel->guardarRecursoGratuitoAdmin($datos);

            echo json_encode([
                'ok' => true,
                'mensaje' => $id > 0
                    ? 'Recurso gratuito actualizado correctamente.'
                    : 'Recurso gratuito creado correctamente.',
                'id' => $id > 0 ? $id : $resultado
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Error guardando recurso gratuito: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Guarda la imagen de portada de un recurso gratuito en local.
     * ---------------------------------------------------------
     * Valida:
     * - Que no haya error de subida.
     * - Que la extensión sea jpg, jpeg, png o webp.
     *
     * @param array $archivo Archivo recibido desde $_FILES.
     * @return string Nombre del archivo guardado.
     * @throws Exception Si la imagen no es válida o no se puede guardar.
     */
    private function guardarImagenRecursoGratuito($archivo)
    {
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir la imagen.');
        }

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $permitidas, true)) {
            throw new Exception('La imagen debe ser JPG, PNG o WEBP.');
        }

        $carpeta = __DIR__ . '/../../static/images/img/';

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $nombreArchivo = 'gratuito-' . time() . '-' . rand(1000, 9999) . '.' . $extension;
        $destino = $carpeta . $nombreArchivo;

        if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
            throw new Exception('No se ha podido guardar la imagen.');
        }

        return $nombreArchivo;
    }

    /**
     * Crea una categoría de recursos gratuitos mediante AJAX.
     * ---------------------------------------------------------
     * Permite añadir categorías desde el panel admin sin recargar la página.
     */
    public function crearCategoriaGratuitaAjax()
    {
        header('Content-Type: application/json; charset=utf-8');

        $rolUsuario = (int)($_SESSION['rol'] ?? 0);

        if (empty($_SESSION['usuario_id']) || !in_array($rolUsuario, [1, 2], true)) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'No tienes permisos.'
            ]);
            exit;
        }

        $nombre = trim($_POST['nombre'] ?? '');

        if ($nombre === '') {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'El nombre de la categoría es obligatorio.'
            ]);
            exit;
        }

        try {
            $id = $this->recursoGratuitoModel->crearCategoriaGratuita($nombre);

            echo json_encode([
                'ok' => true,
                'mensaje' => 'Categoría creada correctamente.',
                'categoria' => [
                    'id' => $id,
                    'nombre' => $nombre
                ]
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Error creando categoría: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Elimina un recurso gratuito mediante AJAX.
     * ---------------------------------------------------------
     * Flujo:
     * 1. Comprueba permisos.
     * 2. Valida ID.
     * 3. Obtiene el recurso.
     * 4. Elimina imagen local si procede.
     * 5. Elimina el registro de la base de datos.
     *
     * De momento no elimina archivo en Google Drive porque la integración OAuth
     * se deja como mejora futura.
     */
    public function eliminarRecursoGratuitoAjax()
    {
        header('Content-Type: application/json; charset=utf-8');

        $rolUsuario = (int)($_SESSION['rol'] ?? 0);

        if (empty($_SESSION['usuario_id']) || !in_array($rolUsuario, [1, 2], true)) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'No tienes permisos para eliminar recursos gratuitos.'
            ]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Método no permitido.'
            ]);
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($id <= 0) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Recurso gratuito no válido.'
            ]);
            exit;
        }

        try {
            $recurso = $this->recursoGratuitoModel->obtenerRecursoGratuitoAdminPorId($id);

            if (!$recurso) {
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'Recurso gratuito no encontrado.'
                ]);
                exit;
            }

            // Eliminamos la imagen local si existe y no es la imagen por defecto.
            if (!empty($recurso['imagen']) && $recurso['imagen'] !== 'default.png') {
                $rutaImagen = __DIR__ . '/../../static/images/img/' . $recurso['imagen'];

                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }

            /*
             * De momento no se borra de Google Drive.
             * Cuando se implemente OAuth y drive_file_id, se añadirá aquí.
             */
            $this->recursoGratuitoModel->eliminarRecursoGratuitoAdmin($id);

            echo json_encode([
                'ok' => true,
                'mensaje' => 'Recurso gratuito eliminado correctamente.'
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Error eliminando recurso gratuito: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Guarda una imagen local de producto o recurso.
     * ---------------------------------------------------------
     * Valida:
     * - Que el archivo exista.
     * - Que no haya error de subida.
     * - Que la extensión sea permitida.
     * - Que no supere 5 MB.
     *
     * @param array $archivo Archivo recibido desde $_FILES.
     * @param string $prefijo Prefijo del nombre generado.
     * @return string|null Nombre del archivo guardado o null si no hay archivo.
     * @throws Exception Si la imagen no es válida.
     */
    private function guardarImagenLocal($archivo, $prefijo = 'recurso')
    {
        if (empty($archivo['name'])) {
            return null;
        }

        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir la imagen.');
        }

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $permitidas, true)) {
            throw new Exception('La imagen debe ser JPG, JPEG, PNG o WEBP.');
        }

        $maxBytes = 5 * 1024 * 1024;

        if ($archivo['size'] > $maxBytes) {
            throw new Exception('La imagen no puede superar los 5 MB.');
        }

        $carpeta = __DIR__ . '/../../static/images/img/';

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $nombreArchivo = $prefijo . '-' . time() . '-' . rand(1000, 9999) . '.' . $extension;

        $destino = $carpeta . $nombreArchivo;

        if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
            throw new Exception('No se ha podido guardar la imagen.');
        }

        return $nombreArchivo;
    }

    /**
     * Elimina una imagen local si existe.
     * ---------------------------------------------------------
     * No elimina imágenes vacías ni la imagen por defecto.
     *
     * @param string $nombreImagen Nombre del archivo de imagen.
     * @return bool True si se elimina, false en caso contrario.
     */
    private function eliminarImagenLocal($nombreImagen)
    {
        if (empty($nombreImagen) || $nombreImagen === 'default.png') {
            return false;
        }

        $ruta = __DIR__ . '/../../static/images/img/' . $nombreImagen;

        if (file_exists($ruta)) {
            return unlink($ruta);
        }

        return false;
    }

    /**
     * Obtiene el rango de fechas para métricas de recursos gratuitos.
     * ---------------------------------------------------------
     * Esta función transforma el periodo elegido en dos fechas:
     * inicio y fin.
     *
     * Si el usuario elige "todos", devuelve null/null para que el modelo
     * muestre totales históricos.
     *
     * Periodos permitidos:
     * - todos
     * - ultimos_7
     * - ultimos_30
     * - mes_anterior
     * - personalizado
     *
     * @return array Devuelve periodo, inicio y fin.
     */
    private function obtenerRangoMetricasGratuitas()
    {
        $periodo = $_GET['periodo_gratis'] ?? 'todos';

        $hoy = new DateTime();

        switch ($periodo) {

            case 'ultimos_7':
                // Incluye hoy y los 6 días anteriores.
                $inicio = (clone $hoy)->modify('-6 days');
                $fin = clone $hoy;
                break;

            case 'ultimos_30':
                // Incluye hoy y los 29 días anteriores.
                $inicio = (clone $hoy)->modify('-29 days');
                $fin = clone $hoy;
                break;

            case 'mes_anterior':
                // Primer y último día del mes anterior.
                $inicio = new DateTime('first day of last month');
                $fin = new DateTime('last day of last month');
                break;

            case 'personalizado':
                $desde = $_GET['gratis_desde'] ?? null;
                $hasta = $_GET['gratis_hasta'] ?? null;

                if ($desde && $hasta) {
                    $inicio = new DateTime($desde);
                    $fin = new DateTime($hasta);
                } else {
                    $periodo = 'todos';
                    $inicio = null;
                    $fin = null;
                }
                break;

            case 'todos':
            default:
                $periodo = 'todos';
                $inicio = null;
                $fin = null;
                break;
        }

        return [
            'periodo' => $periodo,
            'inicio' => $inicio ? $inicio->format('Y-m-d') : null,
            'fin' => $fin ? $fin->format('Y-m-d') : null,
        ];
    }
}