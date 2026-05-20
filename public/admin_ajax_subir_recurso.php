<?php
//
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Producto.php';
require_once __DIR__ . '/../app/servicios/R2Service.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $rolUsuario = (int)($_SESSION['rol'] ?? 0);

    if (empty($_SESSION['usuario_id']) || !in_array($rolUsuario, [1, 2], true)) {
        echo json_encode([
            'ok' => false,
            'mensaje' => 'No tienes permisos para subir archivos.'
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

    /*
        Aceptamos dos posibles nombres por si tu input ya existe con otro name:
        - archivo_recurso
        - archivo
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

    $productoModel = new Producto();

    $productoActual = $productoModel->obtenerProductosID($producto_id);

    if (!$productoActual) {
        echo json_encode([
            'ok' => false,
            'mensaje' => 'Producto no encontrado.'
        ]);
        exit;
    }

    $r2Service = new R2Service();

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

    $productoModel->actualizarArchivoR2($producto_id, $nuevaKey);

    // Si existía un archivo anterior, lo eliminamos de R2
    if (!empty($productoActual['archivo_s3_key'])) {
        $r2Service->eliminarArchivo($productoActual['archivo_s3_key']);
    }

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