<?php
//Incluimos la funcion de conectar con la base de datos para llamarla en la clase producto.
require_once __DIR__ . '/../../config/conexion.php';
/*Definimos la clase producto. Contendrá la variable conexion para poder integrarla en todas sus funciones. 
Esta clase se utilizará para interactuar con la base de datos
*/
class Producto
{
    private $conexion;
    //Constructor que ejecuta la conexion con la base de datos.
    public function __construct()
    {
        $this->conexion = conectarBD();
    }
    //Funcion obtener categorias BD
    public function obtenercategorias()
    {
        $sql = "SELECT * FROM categorias";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    //Funcion Obtener niveles BD
    public function obtenerNiveles()
    {
        $sql = "SELECT * FROM niveles";
        return $this->conexion->query($sql)->fetchAll();
    }
    /*Función para obtener los siguientes datos de hasta 3 recursos según número de descargas de mayor a menor ordenador por total de descargas y donde el producto esté activo.
El parámetro límite será 3 por defecto.
Datos de obtención: id, titulo, descripcion, precio, imagen, categoria(nombre), nivel (nivel).
*/
    function obtenerRecursosDestacados($limite = 3)
    {
        $consulta = " SELECT 
            p.id,
            p.titulo,
            p.descripcion,
            p.precio,
            p.imagen,
            p.estado,
            c.nombre AS categoria,
            n.nombre AS nivel,
            SUM(d.numero_descargas) AS total_descargas
        FROM productos p
        LEFT JOIN categorias c ON c.id = p.categoria_id
        LEFT JOIN niveles n ON n.id = p.nivel_id
        LEFT JOIN descargas d ON d.producto_id = p.id
        WHERE p.estado = 'activo'
        GROUP BY p.id
        ORDER BY total_descargas DESC
        LIMIT :limite ";
        $stmt = $this->conexion->prepare($consulta);
        $stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /*
function obtenerRecursosFiltrados($categoria=null){
    $consulta="SELECT p.id,p.titulo,p.imagen,p.precio,p.descripcion,p.contenido FROM productos p where p.estado='activo'";
    if($categoria){
    $consulta.=" AND (categoria_id=:categoria)";
    }
    $stmt= $this->conexion->prepare($consulta);
    if($categoria){
        $stmt->bindParam(":categoria", $categoria);
    }

    $stmt->execute();
     return $stmt->fetchAll();
*/


    //Obtener productos según filtro checkbox dinámico y añadiendo paginacion con limite de 3 recursos por fila y 9 por página.
    public function RecursosFiltrados($categorias, $niveles, $busqueda, $limite, $offset)
    {
        // Base de la consulta
        $sql = "SELECT p.* , n.nombre AS nivel_nombre,c.nombre AS categoria_nombre FROM productos p inner join niveles n on p.nivel_id=n.id inner join categorias c on p.categoria_id=c.id WHERE estado='activo'";
        $params = [];

        //Filtro por categorias protegido contra inyeccion sql.
        if (!empty($categorias)) {
            // Creamos placeholders, un array definido con posiciones (?, ?, ?) para protegerlo de inyeccion sql.
            $placeholders = implode(',', array_fill(0, count($categorias), '?'));
            $sql .= " AND p.categoria_id IN ($placeholders)";
            //array_merge (unimos arrays)
            $params = array_merge($params, $categorias);
        }

        // Filtro por niveles (checkbox)
        if (!empty($niveles)) {
            $placeholders = implode(',', array_fill(0, count($niveles), '?'));
            $sql .= " AND p.nivel_id IN ($placeholders)";
            $params = array_merge($params, $niveles);
        }

        // Filtro por búsqueda (input texto)
        if (!empty($busqueda)) {
            $sql .= " AND p.titulo LIKE ?";
            $params[] = "%$busqueda%";
        }
        //  Paginación (máx 9 productos por página)
        $sql .= " LIMIT $limite OFFSET $offset";

        // Ejecutar consulta
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);


        // Paginación total productos según logica de filtros.
        $sql_total = "SELECT COUNT(*) FROM productos p WHERE p.estado='activo'";
        $params_total = [];
        if (!empty($categorias)) {
            $placeholders = implode(',', array_fill(0, count($categorias), '?'));
            $sql_total .= " AND p.categoria_id IN ($placeholders)";
            $params_total = array_merge($params_total, $categorias);
        }

        if (!empty($niveles)) {
            $placeholders = implode(',', array_fill(0, count($niveles), '?'));
            $sql_total .= " AND p.nivel_id IN ($placeholders)";
            $params_total = array_merge($params_total, $niveles);
        }

        if (!empty($busqueda)) {
            $sql_total .= " AND p.titulo LIKE ?";
            $params_total[] = "%$busqueda%";
        }
        // Ejecutar COUNT con filtros
        $stmt_total = $this->conexion->prepare($sql_total);
        $stmt_total->execute($params_total);
        $total = $stmt_total->fetchColumn();
        // Calcular total de páginas (ej: 23 productos / 9 = 3 páginas)
        $total_paginas = ceil($total / $limite);

        return [
            "productos" => $productos,
            "total_paginas" => $total_paginas
        ];
    }
    //Obtener datos del producto por id del producto para mostrar vista detalle y también listado en el carrito.
    public function obtenerProductosID($ids)
    {
        // 1. Normalizamos la entrada: si es un solo ID, lo metemos en un array
        $esArray = is_array($ids);
        $listaIds = $esArray ? $ids : [$ids];

        if (empty($listaIds)) return [];

        // 2. Creamos los placeholders (?,?,?) según la cantidad de IDs
        $placeholders = implode(',', array_fill(0, count($listaIds), '?'));

        $sql = "SELECT p.*, c.nombre as categoria_nombre, n.nombre as nivel_nombre
            FROM productos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            LEFT JOIN niveles n ON p.nivel_id = n.id 
            WHERE p.id IN ($placeholders)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($listaIds);

        // 3. Retornamos según el caso
        if ($esArray) {
            // Para el carrito: devolvemos todos los encontrados
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Para el detalle: devolvemos solo la primera fila (el producto único)
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    //Funcion que obtiene recursos relacionados con el que se está viendo.
    public function obtenerProductosRelacionados($categoria_id, $producto_actual_id, $limite = 4)
    {

        $sql = "SELECT 
                p.*,
                c.nombre AS categoria_nombre
            FROM productos p
            LEFT JOIN categorias c 
                ON p.categoria_id = c.id
            WHERE p.estado = 'activo'
            AND p.categoria_id = ?
            AND p.id != ?
            LIMIT ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $categoria_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $producto_actual_id, PDO::PARAM_INT);
        $stmt->bindValue(3, $limite, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Funcion para obtener todas las reseñas de un producto.
    public function obtenerResenasPorProducto($producto_id)
    {
        $sql = "SELECT 
                r.id,
                r.usuario_id,
                r.producto_id,
                r.comentario,
                r.puntuacion,
                r.fecha,
                u.nombre AS usuario_nombre
            FROM reseñas r
            LEFT JOIN usuarios u ON r.usuario_id = u.id
            WHERE r.producto_id = ?
            ORDER BY r.fecha DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$producto_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
// Añade o elimina un favorito
public function toggleFavorito($usuario_id, $producto_id)
{
    // Comprobamos si ya existe ese favorito
    $sql = "SELECT id FROM favoritos 
            WHERE usuario_id = ? AND producto_id = ?";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$usuario_id, $producto_id]);

    $favorito = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si existe, lo quitamos
    if ($favorito) {
        $sql = "DELETE FROM favoritos 
                WHERE usuario_id = ? AND producto_id = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id, $producto_id]);

        return 'eliminado';
    }

    // Si no existe, lo guardamos
    $sql = "INSERT INTO favoritos (usuario_id, producto_id) 
            VALUES (?, ?)";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$usuario_id, $producto_id]);

    return 'guardado';
}


// Devuelve solo los IDs favoritos del usuario
public function obtenerFavoritosUsuario($usuario_id)
{
    $sql = "SELECT producto_id 
            FROM favoritos 
            WHERE usuario_id = ?";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$usuario_id]);

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}


// Devuelve todos los  productos favoritos para el panel
public function obtenerProductosFavoritos($usuario_id)
{
    // Consulta favoritos con datos del producto
    $sql = "SELECT 
                p.id,
                p.titulo,
                p.precio,
                p.imagen,
                c.nombre AS categoria_nombre,
                n.nombre AS nivel_nombre,
                f.fecha
            FROM favoritos f
            INNER JOIN productos p ON p.id = f.producto_id
            LEFT JOIN categorias c ON c.id = p.categoria_id
            LEFT JOIN niveles n ON n.id = p.nivel_id
            WHERE f.usuario_id = ?
            ORDER BY f.fecha DESC";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$usuario_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Obtiene productos para el panel de administración con filtros
public function obtenerProductosAdmin($categorias = [], $niveles = [], $busqueda = '', $estado = '', $limite = 10, $offset = 0)
{
    // Consulta base: no filtramos solo activos porque en admin queremos ver todos
$sql = "SELECT 
            p.*,
            n.nombre AS nivel_nombre,
            c.nombre AS categoria_nombre,
            COALESCE(SUM(d.numero_descargas), 0) AS total_descargas,
            COUNT(DISTINCT r.id) AS total_resenas,
            COALESCE(p.clicks, 0) AS total_clicks
        FROM productos p
        LEFT JOIN niveles n ON p.nivel_id = n.id
        LEFT JOIN categorias c ON p.categoria_id = c.id
        LEFT JOIN descargas d ON d.producto_id = p.id
        LEFT JOIN reseñas r ON r.producto_id = p.id
        WHERE 1=1";

    $params = [];

    // Filtro por estado
    if (!empty($estado)) {
        $sql .= " AND p.estado = ?";
        $params[] = $estado;
    }

    // Filtro por categorías
    if (!empty($categorias)) {
        $placeholders = implode(',', array_fill(0, count($categorias), '?'));
        $sql .= " AND p.categoria_id IN ($placeholders)";
        $params = array_merge($params, $categorias);
    }

    // Filtro por niveles
    if (!empty($niveles)) {
        $placeholders = implode(',', array_fill(0, count($niveles), '?'));
        $sql .= " AND p.nivel_id IN ($placeholders)";
        $params = array_merge($params, $niveles);
    }

    // Filtro por búsqueda
    if (!empty($busqueda)) {
        $sql .= " AND p.titulo LIKE ?";
        $params[] = "%$busqueda%";
    }

    // Orden y paginación
   $sql .= " GROUP BY p.id ORDER BY p.id DESC LIMIT $limite OFFSET $offset";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute($params);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta total
    $sql_total = "SELECT COUNT(*) FROM productos p WHERE 1=1";
    $params_total = [];

    if (!empty($estado)) {
        $sql_total .= " AND p.estado = ?";
        $params_total[] = $estado;
    }

    if (!empty($categorias)) {
        $placeholders = implode(',', array_fill(0, count($categorias), '?'));
        $sql_total .= " AND p.categoria_id IN ($placeholders)";
        $params_total = array_merge($params_total, $categorias);
    }

    if (!empty($niveles)) {
        $placeholders = implode(',', array_fill(0, count($niveles), '?'));
        $sql_total .= " AND p.nivel_id IN ($placeholders)";
        $params_total = array_merge($params_total, $niveles);
    }

    if (!empty($busqueda)) {
        $sql_total .= " AND p.titulo LIKE ?";
        $params_total[] = "%$busqueda%";
    }

    $stmt_total = $this->conexion->prepare($sql_total);
    $stmt_total->execute($params_total);
    $total = $stmt_total->fetchColumn();

    return [
        'productos' => $productos,
        'total_paginas' => ceil($total / $limite)
    ];
}
// Crear nueva categoría
public function crearCategoria($nombre)
{
    $sql = "INSERT INTO categorias (nombre) VALUES (?)";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$nombre]);

    return [
        'id' => $this->conexion->lastInsertId(),
        'nombre' => $nombre
    ];
}
// Incrementar contador de clics del producto
public function incrementarClicks($producto_id)
{
    $sql = "UPDATE productos
            SET clicks = clicks + 1
            WHERE id = ?";

    $stmt = $this->conexion->prepare($sql);
    return $stmt->execute([$producto_id]);
}
// Crear o actualizar reseña de un usuario sobre un producto comprado
public function guardarResena($usuario_id, $producto_id, $puntuacion, $comentario)
{
    // Comprobamos si ya existe una reseña de ese usuario para ese producto
    $sql = "SELECT id 
            FROM reseñas 
            WHERE usuario_id = ? AND producto_id = ?";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$usuario_id, $producto_id]);

    $resena = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si ya existe, actualizamos
    if ($resena) {
        $sql = "UPDATE reseñas 
                SET puntuacion = ?, comentario = ?, fecha = NOW()
                WHERE usuario_id = ? AND producto_id = ?";

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            $puntuacion,
            $comentario,
            $usuario_id,
            $producto_id
        ]);
    }

    // Si no existe, insertamos
    $sql = "INSERT INTO reseñas 
            (usuario_id, producto_id, puntuacion, comentario)
            VALUES (?, ?, ?, ?)";

    $stmt = $this->conexion->prepare($sql);
    return $stmt->execute([
        $usuario_id,
        $producto_id,
        $puntuacion,
        $comentario
    ]);
}
// Obtener productos comprados por un usuario para historial de descargas
public function obtenerProductosCompradosUsuario($usuario_id)
{
    $sql = "SELECT 
                p.id,
                p.titulo,
                p.imagen,
                dp.cantidad,
                dp.precio_unitario,
                pe.fecha_pedido,
                pe.id AS pedido_id
            FROM pedidos pe
            INNER JOIN detalle_pedido dp ON dp.pedido_id = pe.id
            INNER JOIN productos p ON p.id = dp.producto_id
            WHERE pe.usuario_id = ?
            AND pe.estado = 'pagado'
            ORDER BY pe.fecha_pedido DESC";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$usuario_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Obtener reseñas de un producto para admin
public function obtenerResenasAdminPorProducto($producto_id)
{
    $sql = "SELECT 
                r.id,
                r.producto_id,
                r.usuario_id,
                r.comentario,
                r.puntuacion,
                r.fecha,
                r.estado,
                u.nombre,
                u.apellidos,
                u.email,
                u.puede_resenar
            FROM reseñas r
            INNER JOIN usuarios u ON u.id = r.usuario_id
            WHERE r.producto_id = ?
            ORDER BY r.fecha DESC";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$producto_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Denunciar reseña y bloquear al usuario para futuras reseñas
public function denunciarResenaYBloquearUsuario($resena_id, $usuario_id)
{
    $this->conexion->beginTransaction();

    $sql = "UPDATE reseñas 
            SET estado = 'denunciada'
            WHERE id = ?";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$resena_id]);

    $sql = "UPDATE usuarios 
            SET puede_resenar = 0
            WHERE id = ?";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$usuario_id]);

    $this->conexion->commit();

    return true;
}
// Comprobar si el usuario puede publicar reseñas
public function usuarioPuedeResenar($usuario_id)
{
    $sql = "SELECT puede_resenar 
            FROM usuarios 
            WHERE id = ?";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$usuario_id]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    return $usuario && (int)$usuario['puede_resenar'] === 1;
}
// Cambiar estado de una reseña
public function cambiarEstadoResena($resena_id, $estado)
{
    $estadosPermitidos = ['visible', 'oculta', 'denunciada'];

    if (!in_array($estado, $estadosPermitidos)) {
        return false;
    }

    $sql = "UPDATE reseñas 
            SET estado = ?
            WHERE id = ?";

    $stmt = $this->conexion->prepare($sql);
    return $stmt->execute([$estado, $resena_id]);
}
}

