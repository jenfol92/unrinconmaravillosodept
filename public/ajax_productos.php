<?php

require_once __DIR__ . '/../app/modelos/Producto.php';
require_once __DIR__ . "/../includes/session.php";

// Indicamos que la respuesta será JSON
header('Content-Type: application/json');

$model = new Producto();

// Recoger filtros desde GET
$categorias = $_GET['categorias'] ?? '';
$niveles = $_GET['niveles'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';
$pagina = $_GET['pagina'] ?? 1;

// Configuración de paginación
$limite = 9;
$offset = ($pagina - 1) * $limite;

// Convertir filtros JSON a array
$categorias = $categorias ? json_decode($categorias, true) : [];
$niveles = $niveles ? json_decode($niveles, true) : [];

// Obtener productos filtrados desde el modelo
$resultado = $model->RecursosFiltrados(
    $categorias,
    $niveles,
    $busqueda,
    $limite,
    $offset
);

// Array vacío por defecto para favoritos
$favoritos = [];

// Si el usuario está logueado, obtenemos sus favoritos
if (isset($_SESSION['usuario_id'])) {
    $favoritos = $model->obtenerFavoritosUsuario($_SESSION['usuario_id']);
}

// Comprobamos que exista la clave productos antes de recorrerla
if (isset($resultado['productos']) && is_array($resultado['productos'])) {

    // Añadimos a cada producto si es favorito o no
    foreach ($resultado['productos'] as &$producto) {

        // Si el ID del producto está en favoritos, será true
        $producto['es_favorito'] = in_array($producto['id'], $favoritos);
    }
} else {

    // Si por cualquier motivo no viene productos, evitamos romper el JSON
    $resultado['productos'] = [];
}

// Devolver JSON limpio
echo json_encode($resultado);
exit;
