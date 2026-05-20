<?php

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
    private $adminModel;
    private $productoModel;
    private $usuarioModel;
    private $soporteModel;

    private $recursoGratuitoModel;

    public function __construct()
    {
        $this->adminModel = new Admin();
        $this->productoModel = new Producto();
        $this->usuarioModel = new Usuario();
        $this->soporteModel = new Soporte();
        $this->recursoGratuitoModel = new RecursoGratuito();
    }

    public function dashboard()
    {
        // Periodo seleccionado para ventas del dashboard
        $rangoVentas = $this->obtenerRangoVentasDashboard();

        // Estadísticas generales según periodo
        $stats = $this->adminModel->obtenerEstadisticas(
            $rangoVentas['inicio'],
            $rangoVentas['fin']
        );

        // Productos más vendidos según periodo
        $productos = $this->adminModel->obtenerProductosMasVendidos(
            $rangoVentas['inicio'],
            $rangoVentas['fin'],
            5
        );

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

        // Soporte, sugerencias y mensajes de contacto
        $tickets = $this->soporteModel->obtenerTicketsAdmin();
        $sugerencias = $this->soporteModel->obtenerSugerencias();
        $mensajesContacto = $this->soporteModel->obtenerMensajesContactoAdmin();

        //Categorias gratuitas y contenido gratuito
        $categoriasGratuitas = $this->recursoGratuitoModel->obtenerCategoriasGratuitas();

        // Rango seleccionado para clicks/descargas de contenido gratuito.
        $rangoMetricasGratuitas = $this->obtenerRangoMetricasGratuitas();

        // Si inicio/fin son null, el modelo devuelve los totales históricos.
        // Si tienen fechas, el modelo devuelve clicks/descargas solo de ese periodo.
        $recursosGratuitosAdmin = $this->recursoGratuitoModel->obtenerRecursosGratuitosAdmin(
            $rangoMetricasGratuitas['inicio'],
            $rangoMetricasGratuitas['fin']
        );

        // Cargar vista
        require_once __DIR__ . '/../vistas/admin_view.php';
    }

    public function subirArchivoProductoR2()
    {
        $rolUsuario = (int)($_SESSION['rol'] ?? 0);

        if (empty($_SESSION['usuario_id']) || !in_array($rolUsuario, [1, 2], true)) {
            header('Location: /UNRINCONDEPT/public/login.php');
            exit;
        }

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
            // Obtenemos el producto actual
            $productoActual = $this->productoModel->obtenerProductosID($producto_id);

            if (!$productoActual) {
                die('Producto no encontrado.');
            }

            // Subimos el archivo a Cloudflare R2
            $r2Service = new R2Service();

            $nuevaKey = $r2Service->subirArchivoProducto(
                $producto_id,
                $_FILES['archivo'],
                $productoActual['titulo'] ?? 'recurso'
            );

            if (!empty($nuevaKey)) {
                // Guardamos la key del archivo en la tabla productos
                $this->productoModel->actualizarArchivoR2($producto_id, $nuevaKey);

                // Si había un archivo anterior, lo eliminamos de R2
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
    public function guardarProducto()
    {
        header('Content-Type: application/json; charset=utf-8');

        $rolUsuario = (int)($_SESSION['rol'] ?? 0);

        if (empty($_SESSION['usuario_id']) || !in_array($rolUsuario, [1, 2], true)) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'No tienes permisos para guardar productos.'
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

        $productoActual = null;
        $imagen = 'default.png';

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
            if (!empty($_FILES['imagen']['name'])) {
                $nuevaImagen = $this->guardarImagenLocal($_FILES['imagen'], 'producto');

                if (!empty($nuevaImagen)) {
                    if ($productoActual && !empty($productoActual['imagen'])) {
                        $this->eliminarImagenLocal($productoActual['imagen']);
                    }

                    $imagen = $nuevaImagen;
                }
            }

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
    //Eliminar un producto / inactivarlo.
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
            /*
            1. Obtenemos el producto actual.
            obtenerProductosID($id) devuelve un producto único cuando recibe un solo ID.
        */
            $producto = $this->productoModel->obtenerProductosID($producto_id);

            if (!$producto) {
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'Producto no encontrado.'
                ]);
                exit;
            }

            /*
            2. Si tiene archivo en Cloudflare R2, lo eliminamos.
        */
            if (!empty($producto['archivo_s3_key'])) {
                $r2Service = new R2Service();
                $r2Service->eliminarArchivo($producto['archivo_s3_key']);
            }

            /*
            3. Si el producto tiene pedidos asociados, NO lo borramos físicamente.
            Lo marcamos como inactivo y quitamos la referencia al archivo.
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

            /*
            4. Si no tiene pedidos, podemos eliminarlo físicamente.
        */
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
                Mes actual completo:
                - Desde día 1
                - Hasta último día real del mes
                PHP calcula automáticamente si son 28, 29, 30 o 31 días.
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

        $productos = $this->productoModel->obtenerProductosAdminParaPdf(
            $busqueda,
            $categoria,
            $estado
        );

        $fecha = date('d/m/Y H:i');

        ob_start();

        require __DIR__ . '/../vistas/reporte_productos_pdf_view.php';

        $html = ob_get_clean();

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $dompdf->stream('reporte-recursos.pdf', [
            'Attachment' => true
        ]);

        exit;
    }
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

        // De momento puede venir vacío, porque el enlace lo generará Google Drive con OAuth.
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

            if ($id > 0) {
                $recursoActual = $this->recursoGratuitoModel->obtenerRecursoGratuitoAdminPorId($id);

                if (!$recursoActual) {
                    echo json_encode([
                        'ok' => false,
                        'mensaje' => 'Recurso gratuito no encontrado.'
                    ]);
                    exit;
                }

                // Si editas y no subes imagen nueva, conserva la anterior.
                $imagen = $recursoActual['imagen'] ?? 'default.png';

                // Si editas y todavía no subes nuevo archivo a Drive, conserva el enlace anterior.
                if ($urlDrive === '') {
                    $urlDrive = $recursoActual['url_drive'] ?? '';
                }
            }

            /*
            Imagen de portada del recurso gratuito.
            Si tienes creada la función guardarImagenLocal(), usa esta línea.
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
            Archivo PDF/ZIP para Google Drive.

            De momento NO obligamos a tener url_drive, porque lo vamos a generar
            automáticamente cuando montemos OAuth.

            Más adelante aquí irá algo así:

            if (!empty($_FILES['archivo_drive']['name'])) {
                $driveService = new GoogleDriveService();

                $resultadoDrive = $driveService->subirArchivo(
                    $_FILES['archivo_drive'],
                    $titulo
                );

                $urlDrive = $resultadoDrive['url'];
            }
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

        $carpeta = __DIR__ . '/../../static/images/recursos_gratuitos/';

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

            // Eliminar imagen local si no es la imagen por defecto
            if (!empty($recurso['imagen']) && $recurso['imagen'] !== 'default.png') {
                $rutaImagen = __DIR__ . '/../../static/images/img/' . $recurso['imagen'];

                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }

            // De momento no borramos de Google Drive hasta que montemos OAuth.
            // Cuando tengamos el drive_file_id, añadiremos aquí el borrado en Drive.

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
    //RANGO METRICAS PARA OBTENER LOS DATOS DE CLICKS Y DESCARGAS DE LOS RECURSOS GRATUITOS.
    private function obtenerRangoMetricasGratuitas()
    {
        /*
        Esta función transforma el periodo elegido en dos fechas:
        inicio y fin.

        Si el usuario elige "todos", devolvemos null/null.
        Eso indica que se deben mostrar los totales históricos.
    */

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
