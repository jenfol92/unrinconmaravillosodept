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
 //CREAR UN PRODUCTO NUEVO
public function crearProductoAdmin($datos)
{
    $sql = "INSERT INTO productos
            (titulo, precio, descripcion, contenido, categoria_id, nivel_id, estado, imagen)
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $this->conexion->prepare($sql);

    $stmt->execute([
        $datos['titulo'],
        $datos['precio'],
        $datos['descripcion'],
        $datos['contenido'],
        $datos['categoria_id'],
        $datos['nivel_id'],
        $datos['estado'],
        $datos['imagen'] ?? 'default.png'
    ]);

    return (int)$this->conexion->lastInsertId();
}

//ACTUALIZAR UN PRODUCTO
public function actualizarProductoAdmin($producto_id, $datos)
{
    $sql = "UPDATE productos
            SET titulo = ?,
                precio = ?,
                descripcion = ?,
                contenido = ?,
                categoria_id = ?,
                nivel_id = ?,
                estado = ?,
                imagen = ?
            WHERE id = ?";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([
        $datos['titulo'],
        $datos['precio'],
        $datos['descripcion'],
        $datos['contenido'],
        $datos['categoria_id'],
        $datos['nivel_id'],
        $datos['estado'],
        $datos['imagen'] ?? 'default.png',
        $producto_id
    ]);
}
    /*Función para obtener los siguientes datos de hasta 3 recursos según número de descargas de mayor a menor ordenador por total de descargas y donde el producto esté activo.
El parámetro límite será 3 por defecto.
Datos de obtención: id, titulo, descripcion, precio, imagen, categoria(nombre), nivel (nivel).
*/
    public function obtenerRecursosDestacados($limite = 3)
    {
        $sql = "SELECT 
                p.id,
                p.titulo,
                p.imagen,
                p.precio,
                COALESCE(p.clicks, 0) AS clicks,
                c.nombre AS categoria_nombre,
                c.nombre AS categoria
            FROM productos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE p.estado = 'activo'
            AND (p.es_gratuito IS NULL OR p.es_gratuito = 0)
            ORDER BY COALESCE(p.clicks, 0) DESC, p.id DESC
            LIMIT ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, (int)$limite, PDO::PARAM_INT);
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
// Obtiene productos para el panel de administración con filtros
public function obtenerProductosAdmin(
    $categorias = [],
    $niveles = [],
    $busqueda = '',
    $estado = '',
    $limite = 10,
    $offset = 0,
    $fechaInicio = null,
    $fechaFin = null
) {
    /*
        Esta función devuelve los productos para el panel admin.

        Calcula:
        - total_compras: número de filas en descargas.
        - total_descargas: suma de numero_descargas.
        - total_resenas: número de reseñas.
        - total_clicks: clics históricos o clics por periodo.

        Si hay $fechaInicio y $fechaFin:
            compras/descargas se filtran por descargas.fecha_compra.
            clics se filtran por productos_clicks_metricas.fecha.

        Si NO hay fechas:
            compras/descargas son totales desde el inicio.
            clics salen de productos.clicks.
    */

    $limite = (int)$limite;
    $offset = (int)$offset;

    $params = [];

    /*
        SUBCONSULTA DE COMPRAS Y DESCARGAS

        total_compras:
            COUNT(*) sobre la tabla descargas.

        total_descargas:
            SUM(numero_descargas).
    */
    if ($fechaInicio && $fechaFin) {
        $subqueryDescargas = "
            SELECT
                producto_id,
                COUNT(*) AS total_compras,
                COALESCE(SUM(numero_descargas), 0) AS total_descargas
            FROM descargas
            WHERE fecha_compra BETWEEN ? AND ?
            GROUP BY producto_id
        ";

        $params[] = $fechaInicio . ' 00:00:00';
        $params[] = $fechaFin . ' 23:59:59';
    } else {
        $subqueryDescargas = "
            SELECT
                producto_id,
                COUNT(*) AS total_compras,
                COALESCE(SUM(numero_descargas), 0) AS total_descargas
            FROM descargas
            GROUP BY producto_id
        ";
    }

    /*
        SUBCONSULTA DE CLICS

        Si hay fechas, contamos filas de productos_clicks_metricas.
        Si no hay fechas, usamos productos.clicks.
    */
    if ($fechaInicio && $fechaFin) {
        $subqueryClicks = "
            SELECT
                producto_id,
                COUNT(*) AS total_clicks
            FROM productos_clicks_metricas
            WHERE fecha BETWEEN ? AND ?
            GROUP BY producto_id
        ";

        $params[] = $fechaInicio . ' 00:00:00';
        $params[] = $fechaFin . ' 23:59:59';

        $selectClicks = "COALESCE(pc.total_clicks, 0) AS total_clicks";
        $joinClicks = "LEFT JOIN ($subqueryClicks) pc ON pc.producto_id = p.id";
    } else {
        $selectClicks = "COALESCE(p.clicks, 0) AS total_clicks";
        $joinClicks = "";
    }

    /*
        CONSULTA PRINCIPAL

        OJO:
        Uso subconsultas para evitar que las compras/descargas se dupliquen
        si un producto tiene varias reseñas.
    */
    $sql = "SELECT
                p.*,
                n.nombre AS nivel_nombre,
                c.nombre AS categoria_nombre,

                COALESCE(d.total_compras, 0) AS total_compras,
                COALESCE(d.total_descargas, 0) AS total_descargas,

                COALESCE(r.total_resenas, 0) AS total_resenas,

                $selectClicks

            FROM productos p

            LEFT JOIN niveles n
                ON p.nivel_id = n.id

            LEFT JOIN categorias c
                ON p.categoria_id = c.id

            LEFT JOIN ($subqueryDescargas) d
                ON d.producto_id = p.id

            LEFT JOIN (
                SELECT
                    producto_id,
                    COUNT(*) AS total_resenas
                FROM `reseñas`
                GROUP BY producto_id
            ) r
                ON r.producto_id = p.id

            $joinClicks

            WHERE 1 = 1";

    if (!empty($estado)) {
        $sql .= " AND p.estado = ?";
        $params[] = $estado;
    }

    if (!empty($categorias)) {
        $placeholders = implode(',', array_fill(0, count($categorias), '?'));
        $sql .= " AND p.categoria_id IN ($placeholders)";

        foreach ($categorias as $catId) {
            $params[] = (int)$catId;
        }
    }

    if (!empty($niveles)) {
        $placeholders = implode(',', array_fill(0, count($niveles), '?'));
        $sql .= " AND p.nivel_id IN ($placeholders)";

        foreach ($niveles as $nivelId) {
            $params[] = (int)$nivelId;
        }
    }

    if (!empty($busqueda)) {
        $sql .= " AND p.titulo LIKE ?";
        $params[] = '%' . $busqueda . '%';
    }

    $sql .= " ORDER BY p.id DESC LIMIT ? OFFSET ?";

    $stmt = $this->conexion->prepare($sql);

    $pos = 1;

    foreach ($params as $param) {
        $stmt->bindValue($pos, $param);
        $pos++;
    }

    $stmt->bindValue($pos, $limite, PDO::PARAM_INT);
    $pos++;

    $stmt->bindValue($pos, $offset, PDO::PARAM_INT);

    $stmt->execute();

    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /*
        Paginación:
        cuenta productos, no métricas.
    */
    $sqlTotal = "SELECT COUNT(*)
                 FROM productos p
                 WHERE 1 = 1";

    $paramsTotal = [];

    if (!empty($estado)) {
        $sqlTotal .= " AND p.estado = ?";
        $paramsTotal[] = $estado;
    }

    if (!empty($categorias)) {
        $placeholders = implode(',', array_fill(0, count($categorias), '?'));
        $sqlTotal .= " AND p.categoria_id IN ($placeholders)";

        foreach ($categorias as $catId) {
            $paramsTotal[] = (int)$catId;
        }
    }

    if (!empty($niveles)) {
        $placeholders = implode(',', array_fill(0, count($niveles), '?'));
        $sqlTotal .= " AND p.nivel_id IN ($placeholders)";

        foreach ($niveles as $nivelId) {
            $paramsTotal[] = (int)$nivelId;
        }
    }

    if (!empty($busqueda)) {
        $sqlTotal .= " AND p.titulo LIKE ?";
        $paramsTotal[] = '%' . $busqueda . '%';
    }

    $stmtTotal = $this->conexion->prepare($sqlTotal);
    $stmtTotal->execute($paramsTotal);

    $total = (int)$stmtTotal->fetchColumn();

    return [
        'productos' => $productos,
        'total_paginas' => (int)ceil($total / $limite)
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
public function incrementarClicks($id)
{
    /*
        Esta función registra un clic de producto.

        Hace dos cosas:
        1. Suma +1 en productos.clicks.
           Esto mantiene el total histórico que ya tenías.

        2. Inserta una fila en productos_clicks_metricas.
           Esto permite filtrar clics por fecha.
    */

    $usuarioId = $_SESSION['usuario_id'] ?? null;

    $this->conexion->beginTransaction();

    try {
        // 1. Contador total histórico
        $sql = "UPDATE productos
                SET clicks = COALESCE(clicks, 0) + 1
                WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            $id
        ]);

        // 2. Registro con fecha/hora
        $sql = "INSERT INTO productos_clicks_metricas
                (producto_id, usuario_id, fecha)
                VALUES (?, ?, NOW())";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            $id,
            $usuarioId
        ]);

        $this->conexion->commit();

        return true;

    } catch (Exception $e) {
        $this->conexion->rollBack();
        throw $e;
    }
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
    //Actualizar producto. subir pdf a Cloudflare R2
    public function actualizarArchivoR2($producto_id, $archivo_s3_key)
{
    $sql = "UPDATE productos 
            SET archivo_s3_key = ?
            WHERE id = ?";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([
        $archivo_s3_key,
        $producto_id
    ]);
}
//ELIMINAR UN ARCHIVO DE CLOUDFLARE Y DEJAR EL PRODUCTO COMO INACTIVO PARA NO ROMPER HISTORIAL DE VENTAS Y DESCARGAS.
public function desactivarProductoAdmin($producto_id)
{
    $sql = "UPDATE productos 
            SET estado = 'inactivo',
                archivo_s3_key = NULL
            WHERE id = ?";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([
        $producto_id
    ]);
}

public function eliminarProductoFisicoAdmin($producto_id)
{
    $sql = "DELETE FROM productos 
            WHERE id = ?";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([
        $producto_id
    ]);
}

public function productoTienePedidos($producto_id)
{
    $sql = "SELECT COUNT(*) 
            FROM detalle_pedido 
            WHERE producto_id = ?";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([
        $producto_id
    ]);

    return (int)$stmt->fetchColumn() > 0;
}
public function obtenerProductosAdminParaPdf($busqueda = '', $categoria = '', $estado = '')
{
    $sql = "SELECT 
                p.id,
                p.titulo,
                p.precio,
                p.estado,
                p.imagen,
                p.clicks AS total_clicks,
                c.nombre AS categoria_nombre,
                n.nombre AS nivel_nombre,
                COALESCE(SUM(dp.cantidad), 0) AS unidades_vendidas,
                COALESCE(SUM(dp.cantidad * dp.precio_unitario), 0) AS importe_vendido
            FROM productos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            LEFT JOIN niveles n ON n.id = p.nivel_id
            LEFT JOIN detalle_pedido dp ON dp.producto_id = p.id
            LEFT JOIN pedidos pe ON pe.id = dp.pedido_id AND pe.estado = 'pagado'
            WHERE 1 = 1";

    $params = [];

    if (!empty($busqueda)) {
        $sql .= " AND p.titulo LIKE ?";
        $params[] = '%' . $busqueda . '%';
    }

    if (!empty($categoria)) {
        $sql .= " AND p.categoria_id = ?";
        $params[] = $categoria;
    }

    if (!empty($estado)) {
        $sql .= " AND p.estado = ?";
        $params[] = $estado;
    }

    $sql .= " GROUP BY p.id
              ORDER BY p.id DESC";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
/**
 * Crea el derecho de descarga de un producto después de un pago correcto.
 *
 * Esta función NO descarga el archivo.
 * Solo crea el registro en la tabla descargas para que el usuario pueda verlo
 * después en su perfil y descargarlo mediante un token seguro.
 *
 * Se debe llamar cuando el pago ya esté confirmado.
 */
public function crearDescargaTrasPago($usuarioId, $productoId)
{
    $token = bin2hex(random_bytes(32));

    $sql = "INSERT INTO descargas
            (
                usuario_id,
                producto_id,
                numero_descargas,
                max_descargas,
                fecha_compra,
                fecha_expiracion,
                token_descarga
            )
            VALUES
            (?, ?, 0, 5, NOW(), DATE_ADD(NOW(), INTERVAL 365 DAY), ?)";

    $stmt = $this->conexion->prepare($sql);

    $stmt->execute([
        $usuarioId,
        $productoId,
        $token
    ]);

    return $token;
}


/**
 * Busca una descarga concreta por token y usuario.
 *
 * Sirve para comprobar que:
 * - el usuario está logueado,
 * - el token existe,
 * - el token pertenece a ese usuario,
 * - el producto tiene archivo asociado en Cloudflare R2.
 */
public function obtenerDescargaPorToken($token, $usuarioId)
{
    $sql = "SELECT 
                d.id AS descarga_id,
                d.usuario_id,
                d.producto_id,
                d.numero_descargas,
                d.max_descargas,
                d.fecha_compra,
                d.fecha_expiracion,
                d.token_descarga,
                p.titulo,
                p.archivo_s3_key
            FROM descargas d
            INNER JOIN productos p 
                ON p.id = d.producto_id
            WHERE d.token_descarga = ?
              AND d.usuario_id = ?
            LIMIT 1";

    $stmt = $this->conexion->prepare($sql);

    $stmt->execute([
        $token,
        $usuarioId
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


/**
 * Incrementa el número de descargas usadas.
 *
 * Esta función se ejecuta cuando el usuario pulsa Descargar
 * y ya hemos comprobado que puede descargar ese recurso.
 */
public function incrementarNumeroDescargas($descargaId)
{
    $sql = "UPDATE descargas
            SET numero_descargas = COALESCE(numero_descargas, 0) + 1
            WHERE id = ?";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([
        $descargaId
    ]);
}
}
