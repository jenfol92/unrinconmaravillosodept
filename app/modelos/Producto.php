<?php

/**
 * Modelo Producto
 * ---------------------------------------------------------
 * Este modelo centraliza las operaciones relacionadas con los productos
 * y recursos de la tienda.
 *
 * Funcionalidades principales:
 *
 * - Obtener categorías y niveles.
 * - Crear y actualizar productos desde el panel admin.
 * - Obtener recursos destacados para la home.
 * - Filtrar productos en la tienda pública.
 * - Obtener productos por ID.
 * - Obtener productos relacionados.
 * - Gestionar reseñas.
 * - Gestionar favoritos.
 * - Obtener productos para el panel admin con métricas.
 * - Registrar clics de productos.
 * - Asociar archivos de Cloudflare R2 a productos.
 * - Eliminar o desactivar productos.
 * - Generar datos para exportación PDF.
 * - Crear derechos de descarga tras un pago.
 * - Validar descargas mediante token seguro.
 */

require_once __DIR__ . '/../../config/conexion.php';

class Producto
{
    /**
     * Conexión PDO con la base de datos.
     *
     * @var PDO
     */
    private $conexion;

    /**
     * Constructor del modelo.
     * ---------------------------------------------------------
     * Al crear una instancia de Producto, se establece la conexión
     * con la base de datos mediante la función conectarBD().
     */
    public function __construct()
    {
        $this->conexion = conectarBD();
    }

    /**
     * Obtiene todas las categorías de productos.
     * ---------------------------------------------------------
     * Se utiliza para:
     * - Filtros de tienda.
     * - Formularios de creación/edición de productos.
     * - Panel de administración.
     *
     * @return array Listado de categorías.
     */
    public function obtenercategorias()
    {
        $sql = "SELECT * FROM categorias";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Obtiene todos los niveles educativos.
     * ---------------------------------------------------------
     * Se utiliza para filtrar productos y asignar un nivel a cada recurso.
     *
     * @return array Listado de niveles.
     */
    public function obtenerNiveles()
    {
        $sql = "SELECT * FROM niveles";

        return $this->conexion->query($sql)->fetchAll();
    }

    /**
     * Crea un nuevo producto desde el panel administrador.
     * ---------------------------------------------------------
     * Inserta un producto nuevo en la tabla productos.
     *
     * Campos esperados en $datos:
     * - titulo
     * - precio
     * - descripcion
     * - contenido
     * - categoria_id
     * - nivel_id
     * - estado
     * - imagen
     *
     * @param array $datos Datos del producto.
     * @return int ID del producto creado.
     */
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

    /**
     * Actualiza un producto existente desde el panel administrador.
     * ---------------------------------------------------------
     * Modifica los datos principales del producto.
     *
     * @param int $producto_id ID del producto que se va a actualizar.
     * @param array $datos Nuevos datos del producto.
     *
     * @return bool True si la actualización se ejecuta correctamente.
     */
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

    /**
     * Obtiene recursos destacados para la página de inicio.
     * ---------------------------------------------------------
     * Devuelve productos activos, no gratuitos, ordenados por número
     * de clics y posteriormente por ID descendente.
     *
     * Se utiliza para mostrar recursos destacados en la home.
     *
     * @param int $limite Número máximo de productos a devolver.
     * @return array Listado de productos destacados.
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

    /**
     * Obtiene productos filtrados para la tienda pública.
     * ---------------------------------------------------------
     * Permite filtrar productos por:
     * - Categorías.
     * - Niveles.
     * - Búsqueda por título.
     *
     * También aplica paginación mediante límite y offset.
     *
     * Devuelve:
     * - productos encontrados.
     * - total de páginas.
     *
     * @param array|null $categorias Categorías seleccionadas.
     * @param array|null $niveles Niveles seleccionados.
     * @param string $busqueda Texto de búsqueda.
     * @param int $limite Número de productos por página.
     * @param int $offset Desplazamiento para paginación.
     *
     * @return array Productos y total de páginas.
     */
    public function RecursosFiltrados($categorias, $niveles, $busqueda, $limite, $offset)
    {
        /*
            Consulta base.

            Se cruzan productos con niveles y categorías para poder mostrar
            el nombre del nivel y el nombre de la categoría en la tienda.
        */
        $sql = "SELECT 
                    p.*,
                    n.nombre AS nivel_nombre,
                    c.nombre AS categoria_nombre
                FROM productos p
                INNER JOIN niveles n ON p.nivel_id = n.id
                INNER JOIN categorias c ON p.categoria_id = c.id
                WHERE estado = 'activo'";

        $params = [];

        /*
            Filtro por categorías.

            Se generan placeholders dinámicos para evitar inyección SQL.
        */
        if (!empty($categorias)) {
            $placeholders = implode(',', array_fill(0, count($categorias), '?'));
            $sql .= " AND p.categoria_id IN ($placeholders)";
            $params = array_merge($params, $categorias);
        }

        /*
            Filtro por niveles.
        */
        if (!empty($niveles)) {
            $placeholders = implode(',', array_fill(0, count($niveles), '?'));
            $sql .= " AND p.nivel_id IN ($placeholders)";
            $params = array_merge($params, $niveles);
        }

        /*
            Filtro por búsqueda textual en el título del producto.
        */
        if (!empty($busqueda)) {
            $sql .= " AND p.titulo LIKE ?";
            $params[] = "%$busqueda%";
        }

        /*
            Paginación.

            En este proyecto se muestran normalmente 9 productos por página.
        */
        $sql .= " LIMIT $limite OFFSET $offset";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /*
            Consulta para calcular el número total de productos encontrados
            con los mismos filtros.
        */
        $sql_total = "SELECT COUNT(*) 
                      FROM productos p 
                      WHERE p.estado = 'activo'";

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

        $stmt_total = $this->conexion->prepare($sql_total);
        $stmt_total->execute($params_total);

        $total = $stmt_total->fetchColumn();

        /*
            Calculamos el total de páginas.
        */
        $total_paginas = ceil($total / $limite);

        return [
            "productos" => $productos,
            "total_paginas" => $total_paginas
        ];
    }

    /**
     * Obtiene uno o varios productos por ID.
     * ---------------------------------------------------------
     * Esta función se adapta a dos usos:
     *
     * 1. Si recibe un único ID:
     *    - Devuelve un solo producto.
     *
     * 2. Si recibe un array de IDs:
     *    - Devuelve todos los productos encontrados.
     *
     * Se utiliza en:
     * - Detalle de producto.
     * - Carrito.
     * - Procesos de pago.
     *
     * @param int|array $ids ID único o array de IDs.
     * @return array|false Producto único, listado de productos o false.
     */
    public function obtenerProductosID($ids)
    {
        /*
            Normalizamos la entrada.

            Si se recibe un único ID, lo convertimos en array para poder
            construir una consulta IN.
        */
        $esArray = is_array($ids);
        $listaIds = $esArray ? $ids : [$ids];

        if (empty($listaIds)) {
            return [];
        }

        /*
            Creamos placeholders dinámicos según la cantidad de IDs.
        */
        $placeholders = implode(',', array_fill(0, count($listaIds), '?'));

        $sql = "SELECT 
                    p.*,
                    c.nombre AS categoria_nombre,
                    n.nombre AS nivel_nombre
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                LEFT JOIN niveles n ON p.nivel_id = n.id 
                WHERE p.id IN ($placeholders)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($listaIds);

        /*
            Si la entrada original era array, devolvemos todos.
            Si era un único ID, devolvemos una sola fila.
        */
        if ($esArray) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene productos relacionados.
     * ---------------------------------------------------------
     * Busca productos activos de la misma categoría que el producto actual,
     * excluyendo el producto que se está visualizando.
     *
     * @param int $categoria_id Categoría del producto actual.
     * @param int $producto_actual_id ID del producto actual.
     * @param int $limite Número máximo de relacionados.
     *
     * @return array Productos relacionados.
     */
    public function obtenerProductosRelacionados($categoria_id, $producto_actual_id, $limite = 4)
    {
        $sql = "SELECT 
                    p.*,
                    c.nombre AS categoria_nombre
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
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

    /**
     * Obtiene reseñas públicas de un producto.
     * ---------------------------------------------------------
     * Devuelve las reseñas asociadas a un producto junto con el nombre
     * del usuario que las escribió.
     *
     * @param int $producto_id ID del producto.
     * @return array Listado de reseñas.
     */
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

    /**
     * Añade o elimina un producto favorito.
     * ---------------------------------------------------------
     * Si el favorito ya existe, lo elimina.
     * Si no existe, lo crea.
     *
     * Esta función permite implementar un botón tipo "toggle".
     *
     * @param int $usuario_id ID del usuario.
     * @param int $producto_id ID del producto.
     *
     * @return string 'guardado' o 'eliminado'.
     */
    public function toggleFavorito($usuario_id, $producto_id)
    {
        /*
            Comprobamos si ya existe el favorito.
        */
        $sql = "SELECT id 
                FROM favoritos 
                WHERE usuario_id = ? 
                AND producto_id = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id, $producto_id]);

        $favorito = $stmt->fetch(PDO::FETCH_ASSOC);

        /*
            Si ya existe, lo eliminamos.
        */
        if ($favorito) {
            $sql = "DELETE FROM favoritos 
                    WHERE usuario_id = ? 
                    AND producto_id = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$usuario_id, $producto_id]);

            return 'eliminado';
        }

        /*
            Si no existe, lo insertamos.
        */
        $sql = "INSERT INTO favoritos (usuario_id, producto_id) 
                VALUES (?, ?)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id, $producto_id]);

        return 'guardado';
    }

    /**
     * Obtiene solo los IDs de productos favoritos de un usuario.
     * ---------------------------------------------------------
     * Se utiliza normalmente para pintar corazones activos en la tienda.
     *
     * @param int $usuario_id ID del usuario.
     * @return array IDs de productos favoritos.
     */
    public function obtenerFavoritosUsuario($usuario_id)
    {
        $sql = "SELECT producto_id 
                FROM favoritos 
                WHERE usuario_id = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Obtiene los productos favoritos completos de un usuario.
     * ---------------------------------------------------------
     * Se utiliza en el perfil del usuario para mostrar sus favoritos
     * con datos completos del producto.
     *
     * @param int $usuario_id ID del usuario.
     * @return array Productos favoritos.
     */
    public function obtenerProductosFavoritos($usuario_id)
    {
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

    /**
     * Obtiene productos para el panel de administración con filtros y métricas.
     * ---------------------------------------------------------
     * Esta función es una de las más completas del modelo.
     *
     * Devuelve productos junto con:
     * - Categoría.
     * - Nivel.
     * - Total de compras.
     * - Total de descargas.
     * - Total de reseñas.
     * - Total de clics.
     *
     * Si recibe rango de fechas:
     * - compras y descargas se filtran por fecha_compra.
     * - clics se filtran por productos_clicks_metricas.fecha.
     *
     * Si no recibe fechas:
     * - compras y descargas son históricas.
     * - clics salen de productos.clicks.
     *
     * @param array $categorias Categorías filtradas.
     * @param array $niveles Niveles filtrados.
     * @param string $busqueda Texto de búsqueda.
     * @param string $estado Estado del producto.
     * @param int $limite Productos por página.
     * @param int $offset Offset de paginación.
     * @param string|null $fechaInicio Fecha inicial.
     * @param string|null $fechaFin Fecha final.
     *
     * @return array Productos y total de páginas.
     */
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
        $limite = (int)$limite;
        $offset = (int)$offset;

        $params = [];

        /*
            Subconsulta de compras y descargas.

            Se utiliza una subconsulta para evitar duplicados cuando un producto
            tiene varias reseñas o varias relaciones.
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
            Subconsulta de clics.

            Si hay fechas, se cuentan las filas de productos_clicks_metricas.
            Si no hay fechas, se utiliza el contador histórico productos.clicks.
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
            Consulta principal.

            Se unen productos con niveles, categorías y métricas calculadas.
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

        /*
            Filtro por estado.
        */
        if (!empty($estado)) {
            $sql .= " AND p.estado = ?";
            $params[] = $estado;
        }

        /*
            Filtro por categorías.
        */
        if (!empty($categorias)) {
            $placeholders = implode(',', array_fill(0, count($categorias), '?'));
            $sql .= " AND p.categoria_id IN ($placeholders)";

            foreach ($categorias as $catId) {
                $params[] = (int)$catId;
            }
        }

        /*
            Filtro por niveles.
        */
        if (!empty($niveles)) {
            $placeholders = implode(',', array_fill(0, count($niveles), '?'));
            $sql .= " AND p.nivel_id IN ($placeholders)";

            foreach ($niveles as $nivelId) {
                $params[] = (int)$nivelId;
            }
        }

        /*
            Filtro por texto de búsqueda.
        */
        if (!empty($busqueda)) {
            $sql .= " AND p.titulo LIKE ?";
            $params[] = '%' . $busqueda . '%';
        }

        $sql .= " ORDER BY p.id DESC LIMIT ? OFFSET ?";

        $stmt = $this->conexion->prepare($sql);

        /*
            Enlazamos manualmente todos los parámetros.
        */
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
            Consulta para calcular el total de productos filtrados.
            La paginación cuenta productos, no métricas.
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

    /**
     * Crea una nueva categoría.
     * ---------------------------------------------------------
     * Se utiliza desde el panel de administración para añadir
     * categorías de productos.
     *
     * @param string $nombre Nombre de la categoría.
     * @return array ID y nombre de la categoría creada.
     */
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

    /**
     * Incrementa el contador de clics de un producto.
     * ---------------------------------------------------------
     * Esta función registra una visita/clic a un producto.
     *
     * Hace dos cosas dentro de una transacción:
     *
     * 1. Suma +1 en productos.clicks.
     * 2. Inserta una fila en productos_clicks_metricas con fecha/hora.
     *
     * Esto permite tener:
     * - Total histórico de clics.
     * - Métricas filtrables por fecha.
     *
     * @param int $id ID del producto.
     * @return bool True si se registra correctamente.
     * @throws Exception Si falla la transacción.
     */
    public function incrementarClicks($id)
    {
        $usuarioId = $_SESSION['usuario_id'] ?? null;

        $this->conexion->beginTransaction();

        try {
            $sql = "UPDATE productos
                    SET clicks = COALESCE(clicks, 0) + 1
                    WHERE id = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$id]);

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

    /**
     * Crea o actualiza una reseña.
     * ---------------------------------------------------------
     * Si el usuario ya ha escrito una reseña para ese producto,
     * se actualiza.
     *
     * Si no existe, se crea una nueva.
     *
     * @param int $usuario_id ID del usuario.
     * @param int $producto_id ID del producto.
     * @param int $puntuacion Puntuación de la reseña.
     * @param string $comentario Comentario del usuario.
     *
     * @return bool True si se guarda correctamente.
     */
    public function guardarResena($usuario_id, $producto_id, $puntuacion, $comentario)
    {
        $sql = "SELECT id 
                FROM reseñas 
                WHERE usuario_id = ? 
                AND producto_id = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$usuario_id, $producto_id]);

        $resena = $stmt->fetch(PDO::FETCH_ASSOC);

        /*
            Si ya existe reseña, se actualiza.
        */
        if ($resena) {
            $sql = "UPDATE reseñas 
                    SET puntuacion = ?, comentario = ?, fecha = NOW()
                    WHERE usuario_id = ? 
                    AND producto_id = ?";

            $stmt = $this->conexion->prepare($sql);

            return $stmt->execute([
                $puntuacion,
                $comentario,
                $usuario_id,
                $producto_id
            ]);
        }

        /*
            Si no existe, se inserta.
        */
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

    /**
     * Obtiene productos comprados por un usuario.
     * ---------------------------------------------------------
     * Se utiliza para mostrar el historial de compras o descargas.
     *
     * Solo se devuelven pedidos con estado pagado.
     *
     * @param int $usuario_id ID del usuario.
     * @return array Productos comprados.
     */
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

    /**
     * Obtiene reseñas de un producto para el panel administrador.
     * ---------------------------------------------------------
     * Devuelve reseñas con datos del usuario que las escribió.
     *
     * @param int $producto_id ID del producto.
     * @return array Reseñas del producto.
     */
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

    /**
     * Denuncia una reseña y bloquea al usuario para futuras reseñas.
     * ---------------------------------------------------------
     * Cambia la reseña a estado denunciada y actualiza al usuario
     * para que no pueda volver a reseñar.
     *
     * @param int $resena_id ID de la reseña.
     * @param int $usuario_id ID del usuario.
     *
     * @return bool True si se completa.
     */
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

    /**
     * Comprueba si un usuario puede publicar reseñas.
     * ---------------------------------------------------------
     * Consulta el campo puede_resenar de la tabla usuarios.
     *
     * @param int $usuario_id ID del usuario.
     * @return bool True si puede reseñar.
     */
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

    /**
     * Cambia el estado de una reseña.
     * ---------------------------------------------------------
     * Estados permitidos:
     * - visible
     * - oculta
     * - denunciada
     *
     * @param int $resena_id ID de la reseña.
     * @param string $estado Nuevo estado.
     *
     * @return bool True si se actualiza, false si el estado no es válido.
     */
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

    /**
     * Actualiza la key del archivo en Cloudflare R2.
     * ---------------------------------------------------------
     * Guarda en productos.archivo_s3_key la referencia del archivo
     * subido a Cloudflare R2.
     *
     * @param int $producto_id ID del producto.
     * @param string $archivo_s3_key Key del archivo en R2.
     *
     * @return bool True si se actualiza.
     */
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

    /**
     * Desactiva un producto desde el panel admin.
     * ---------------------------------------------------------
     * Se utiliza cuando el producto tiene pedidos asociados.
     *
     * En vez de borrarlo físicamente, se marca como inactivo y se elimina
     * la referencia al archivo descargable para no romper el historial
     * de ventas y descargas.
     *
     * @param int $producto_id ID del producto.
     * @return bool True si se actualiza.
     */
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

    /**
     * Elimina físicamente un producto.
     * ---------------------------------------------------------
     * Solo debe usarse cuando el producto no tiene pedidos asociados.
     *
     * @param int $producto_id ID del producto.
     * @return bool True si se elimina.
     */
    public function eliminarProductoFisicoAdmin($producto_id)
    {
        $sql = "DELETE FROM productos 
                WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $producto_id
        ]);
    }

    /**
     * Comprueba si un producto tiene pedidos asociados.
     * ---------------------------------------------------------
     * Se utiliza antes de eliminar productos.
     *
     * Si tiene pedidos, no se debe borrar físicamente para no romper
     * el historial de compras.
     *
     * @param int $producto_id ID del producto.
     * @return bool True si tiene pedidos.
     */
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

    /**
     * Obtiene productos para exportar a PDF.
     * ---------------------------------------------------------
     * Devuelve productos con filtros opcionales:
     * - búsqueda
     * - categoría
     * - estado
     *
     * Incluye métricas de unidades vendidas, importe vendido y clics.
     *
     * @param string $busqueda Texto de búsqueda.
     * @param string $categoria Categoría filtrada.
     * @param string $estado Estado filtrado.
     *
     * @return array Productos para el PDF.
     */
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
     * ---------------------------------------------------------
     * Esta función NO descarga el archivo.
     *
     * Solo crea el registro en la tabla descargas para que el usuario
     * pueda verlo después en su perfil y descargarlo mediante un token seguro.
     *
     * Características:
     * - Genera un token aleatorio seguro.
     * - Inicializa numero_descargas en 0.
     * - Establece max_descargas en 5.
     * - Establece expiración en 365 días.
     *
     * @param int $usuarioId ID del usuario.
     * @param int $productoId ID del producto.
     *
     * @return string Token de descarga generado.
     * @throws Exception Si random_bytes falla.
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
     * ---------------------------------------------------------
     * Sirve para comprobar:
     *
     * - Que el usuario está logueado.
     * - Que el token existe.
     * - Que el token pertenece a ese usuario.
     * - Que el producto tiene archivo asociado en Cloudflare R2.
     *
     * @param string $token Token de descarga.
     * @param int $usuarioId ID del usuario.
     *
     * @return array|false Datos de la descarga o false si no existe.
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
     * ---------------------------------------------------------
     * Esta función se ejecuta cuando el usuario pulsa descargar
     * y ya se ha comprobado que puede acceder a ese recurso.
     *
     * @param int $descargaId ID del registro de descarga.
     *
     * @return bool True si se actualiza correctamente.
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