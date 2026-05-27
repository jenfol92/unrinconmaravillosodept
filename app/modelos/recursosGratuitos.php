<?php

/**
 * Modelo RecursoGratuito
 * ---------------------------------------------------------
 * Este modelo se encarga de gestionar los recursos gratuitos
 * de la aplicación.
 *
 * Funcionalidades principales:
 *
 * - Obtener categorías de recursos gratuitos.
 * - Crear nuevas categorías gratuitas desde administración.
 * - Obtener recursos gratuitos para la parte pública.
 * - Filtrar recursos gratuitos por categoría y búsqueda.
 * - Obtener recursos gratuitos para la home.
 * - Registrar clicks y descargas.
 * - Guardar métricas históricas con fecha.
 * - Obtener recursos gratuitos para el panel administrador.
 * - Crear, actualizar y eliminar recursos gratuitos.
 *
 * Tablas principales utilizadas:
 *
 * - categorias_gratuitas
 * - recursos_gratuitos
 * - recursos_gratuitos_metricas
 */

require_once __DIR__ . '/../../config/conexion.php';

class RecursoGratuito
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
     * Al crear una instancia de RecursoGratuito, se establece
     * automáticamente la conexión con la base de datos.
     */
    public function __construct()
    {
        $this->conexion = conectarBD();
    }

   
    // CATEGORÍAS GRATUITAS


    /**
     * Obtiene las categorías gratuitas activas.
     * ---------------------------------------------------------
     * Devuelve solo las categorías cuyo campo activa sea 1.
     *
     * Se utiliza para:
     * - Filtros públicos de recursos gratuitos.
     * - Formularios del panel administrador.
     * - Clasificar recursos gratuitos.
     *
     * @return array Listado de categorías gratuitas activas.
     */
    public function obtenerCategoriasGratuitas()
    {
        $sql = "SELECT *
                FROM categorias_gratuitas
                WHERE activa = 1
                ORDER BY nombre ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea una nueva categoría gratuita.
     * ---------------------------------------------------------
     * Inserta una categoría en la tabla categorias_gratuitas
     * dejándola activa por defecto.
     *
     * @param string $nombre Nombre de la categoría.
     *
     * @return int ID de la categoría creada.
     */
    public function crearCategoriaGratuita($nombre)
    {
        $sql = "INSERT INTO categorias_gratuitas (nombre, activa)
                VALUES (?, 1)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            $nombre
        ]);

        return (int)$this->conexion->lastInsertId();
    }


    // RECURSOS GRATUITOS - PARTE PÚBLICA


    /**
     * Obtiene recursos gratuitos activos para la parte pública.
     * ---------------------------------------------------------
     * Permite filtrar por:
     *
     * - Categorías.
     * - Texto de búsqueda.
     *
     * Devuelve recursos activos junto con su categoría, formato,
     * imagen, enlace de Drive y métricas acumuladas.
     *
     * @param array $categorias IDs de categorías seleccionadas.
     * @param string $busqueda Texto introducido en el buscador.
     *
     * @return array Listado de recursos gratuitos.
     */
    public function obtenerRecursosGratuitos($categorias = [], $busqueda = '')
    {
        $sql = "SELECT 
                    r.id,
                    r.titulo,
                    r.imagen,
                    r.url_drive,
                    r.formato,
                    COALESCE(r.clicks, 0) AS clicks,
                    COALESCE(r.descargas, 0) AS descargas,
                    r.fecha_creacion,
                    r.categoria_id,
                    c.nombre AS categoria_nombre
                FROM recursos_gratuitos r
                LEFT JOIN categorias_gratuitas c 
                    ON c.id = r.categoria_id
                WHERE r.estado = 'activo'";

        $params = [];

        /*
            Filtro por categorías.

            Si se seleccionan varias categorías, se construyen placeholders
            dinámicos para mantener la consulta protegida.
        */
        if (!empty($categorias)) {
            $placeholders = implode(',', array_fill(0, count($categorias), '?'));
            $sql .= " AND r.categoria_id IN ($placeholders)";
            $params = array_merge($params, $categorias);
        }

        /*
            Filtro de búsqueda por título.
        */
        if (!empty($busqueda)) {
            $sql .= " AND r.titulo LIKE ?";
            $params[] = '%' . $busqueda . '%';
        }

        /*
            Los recursos se ordenan mostrando primero los más recientes.
        */
        $sql .= " ORDER BY r.fecha_creacion DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un recurso gratuito público por ID.
     * ---------------------------------------------------------
     * Solo devuelve el recurso si está activo.
     *
     * Se utiliza para comprobar que un recurso existe antes de
     * permitir acciones públicas como abrirlo o descargarlo.
     *
     * @param int $id ID del recurso gratuito.
     *
     * @return array|false Datos del recurso o false si no existe.
     */
    public function obtenerRecursoPorId($id)
    {
        $sql = "SELECT *
                FROM recursos_gratuitos
                WHERE id = ?
                AND estado = 'activo'";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Registra una métrica individual de un recurso gratuito.
     * ---------------------------------------------------------
     * Inserta una fila en recursos_gratuitos_metricas indicando:
     *
     * - recurso_id
     * - tipo de métrica
     * - fecha y hora
     *
     * Tipos usados:
     * - click
     * - descarga
     *
     * Esta función es privada porque solo se utiliza internamente
     * desde incrementarClicks() e incrementarDescargas().
     *
     * @param int $recursoId ID del recurso gratuito.
     * @param string $tipo Tipo de métrica: click o descarga.
     *
     * @return bool True si se registra correctamente.
     */
    private function registrarMetrica($recursoId, $tipo)
    {
        $sql = "INSERT INTO recursos_gratuitos_metricas
                (recurso_id, tipo, fecha)
                VALUES (?, ?, NOW())";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $recursoId,
            $tipo
        ]);
    }

    /**
     * Incrementa los clicks de un recurso gratuito.
     * ---------------------------------------------------------
     * Esta función realiza dos acciones dentro de una transacción:
     *
     * 1. Suma +1 al contador total de clicks del recurso.
     * 2. Inserta una métrica histórica con fecha y hora.
     *
     * De esta forma se conserva:
     *
     * - El total acumulado de clicks.
     * - El histórico de clicks para filtrar por periodo.
     *
     * @param int $id ID del recurso gratuito.
     *
     * @return bool True si se completa correctamente.
     *
     * @throws Exception Si falla la transacción.
     */
    public function incrementarClicks($id)
    {
        $this->conexion->beginTransaction();

        try {
            /*
                Actualizamos el contador total de clicks.
            */
            $sql = "UPDATE recursos_gratuitos
                    SET clicks = COALESCE(clicks, 0) + 1
                    WHERE id = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                $id
            ]);

            /*
                Registramos la métrica individual con fecha y hora.
            */
            $this->registrarMetrica($id, 'click');

            $this->conexion->commit();

            return true;
        } catch (Exception $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }

    /*
     * Incrementa las descargas de un recurso gratuito.
     * ---------------------------------------------------------
     * Esta función realiza dos acciones dentro de una transacción:
     *
     * 1. Suma +1 al contador total de descargas.
     * 2. Inserta una métrica histórica con fecha y hora.
     *
     * Esto permite mostrar totales acumulados y estadísticas por periodo.
     *
     * @param int $id ID del recurso gratuito.
     *
     * @return bool True si se completa correctamente.
     *
     * @throws Exception Si falla la transacción.
     
    public function incrementarDescargas($id)
    {
        $this->conexion->beginTransaction();

        try {
           
                Actualizamos el contador total de descargas.
            
            $sql = "UPDATE recursos_gratuitos
                    SET descargas = COALESCE(descargas, 0) + 1
                    WHERE id = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                $id
            ]);

           
                Registramos la descarga con fecha y hora.
            
            $this->registrarMetrica($id, 'descarga');

            $this->conexion->commit();

            return true;

        } catch (Exception $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    } */

    /**
     * Obtiene recursos gratuitos destacados para la home.
     * ---------------------------------------------------------
     * Devuelve recursos gratuitos activos o sin estado definido,
     * ordenados por número de clicks y por ID descendente.
     *
     * Se utiliza para mostrar una pequeña selección de recursos
     * gratuitos en la página principal.
     *
     * @param int $limite Número máximo de recursos a devolver.
     *
     * @return array Listado de recursos gratuitos para la home.
     */
    public function obtenerRecursosGratuitosHome($limite = 3)
    {
        $sql = "SELECT 
                    rg.id,
                    rg.titulo,
                    rg.imagen,
                    rg.url_drive,
                    rg.categoria_id,
                    rg.formato,
                    COALESCE(rg.clicks, 0) AS clicks,
                    COALESCE(rg.descargas, 0) AS descargas,
                    cg.nombre AS categoria_nombre
                FROM recursos_gratuitos rg
                LEFT JOIN categorias_gratuitas cg 
                    ON cg.id = rg.categoria_id
                WHERE rg.estado = 'activo' OR rg.estado IS NULL
                ORDER BY COALESCE(rg.clicks, 0) DESC, rg.id DESC
                LIMIT ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, (int)$limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    // RECURSOS GRATUITOS - PANEL ADMINISTRADOR


    /**
     * Obtiene recursos gratuitos para el panel administrador.
     * ---------------------------------------------------------
     * Esta función tiene dos comportamientos:
     *
     * 1. Si recibe fechaInicio y fechaFin:
     *    - Calcula clicks y descargas solo dentro de ese periodo
     *      usando la tabla recursos_gratuitos_metricas.
     *
     * 2. Si no recibe fechas:
     *    - Devuelve los totales acumulados guardados directamente
     *      en recursos_gratuitos.clicks y recursos_gratuitos.descargas.
     *
     * Se utiliza para mostrar estadísticas en el panel admin.
     *
     * @param string|null $fechaInicio Fecha inicial en formato Y-m-d.
     * @param string|null $fechaFin Fecha final en formato Y-m-d.
     *
     * @return array Listado de recursos gratuitos con métricas.
     */
    public function obtenerRecursosGratuitosAdmin($fechaInicio = null, $fechaFin = null)
    {
        /*
            Si se recibe un rango de fechas, se calculan métricas
            únicamente dentro de ese periodo.
        */
        if ($fechaInicio && $fechaFin) {
            $sql = "SELECT 
                        rg.id,
                        rg.titulo,
                        rg.imagen,
                        rg.url_drive,
                        rg.formato,
                        rg.estado,
                        rg.categoria_id,
                        rg.fecha_creacion,
                        cg.nombre AS categoria_nombre,

                        COALESCE(SUM(CASE WHEN m.tipo = 'click' THEN 1 ELSE 0 END), 0) AS clicks,

                        COALESCE(SUM(CASE WHEN m.tipo = 'descarga' THEN 1 ELSE 0 END), 0) AS descargas

                    FROM recursos_gratuitos rg

                    LEFT JOIN categorias_gratuitas cg 
                        ON cg.id = rg.categoria_id

                    LEFT JOIN recursos_gratuitos_metricas m
                        ON m.recurso_id = rg.id
                        AND m.fecha BETWEEN ? AND ?

                    GROUP BY rg.id
                    ORDER BY rg.id DESC";

            $stmt = $this->conexion->prepare($sql);

            /*
                Convertimos las fechas en rango completo de día.

                Ejemplo:
                2026-05-01 -> 2026-05-01 00:00:00
                2026-05-31 -> 2026-05-31 23:59:59
            */
            $stmt->execute([
                $fechaInicio . ' 00:00:00',
                $fechaFin . ' 23:59:59'
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        /*
            Si no se recibe periodo, se devuelven los totales acumulados.
        */
        $sql = "SELECT 
                    rg.id,
                    rg.titulo,
                    rg.imagen,
                    rg.url_drive,
                    rg.formato,
                    rg.estado,
                    rg.categoria_id,
                    rg.fecha_creacion,
                    COALESCE(rg.clicks, 0) AS clicks,
                    COALESCE(rg.descargas, 0) AS descargas,
                    cg.nombre AS categoria_nombre
                FROM recursos_gratuitos rg
                LEFT JOIN categorias_gratuitas cg 
                    ON cg.id = rg.categoria_id
                ORDER BY rg.id DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un recurso gratuito por ID para administración.
     * ---------------------------------------------------------
     * A diferencia del método público, aquí no se exige que el recurso
     * esté activo, ya que el administrador puede editar recursos inactivos.
     *
     * @param int $id ID del recurso gratuito.
     *
     * @return array|false Datos del recurso o false si no existe.
     */
    public function obtenerRecursoGratuitoAdminPorId($id)
    {
        $sql = "SELECT *
                FROM recursos_gratuitos
                WHERE id = ?
                LIMIT 1";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea o actualiza un recurso gratuito desde administración.
     * ---------------------------------------------------------
     * Si recibe un ID mayor que 0:
     * - Actualiza un recurso existente.
     *
     * Si no recibe ID:
     * - Crea un recurso nuevo.
     *
     * Normaliza nombres de campos por si llegan desde distintos formularios:
     * - categoria_id o categoria_gratuita_id.
     * - url_drive o drive_url.
     *
     * @param array $datos Datos del recurso gratuito.
     *
     * @return bool|int True si actualiza, ID si crea un nuevo recurso.
     */
    public function guardarRecursoGratuitoAdmin($datos)
    {
        /*
            Normalización de datos recibidos.
        */
        $id = !empty($datos['id']) ? (int)$datos['id'] : 0;

        $titulo = trim($datos['titulo'] ?? '');
        $imagen = $datos['imagen'] ?? 'default.png';

        $categoriaId = (int)(
            $datos['categoria_id']
            ?? $datos['categoria_gratuita_id']
            ?? 0
        );

        $urlDrive = trim(
            $datos['url_drive']
                ?? $datos['drive_url']
                ?? ''
        );

        $formato = $datos['formato'] ?? 'PDF';
        $estado = $datos['estado'] ?? 'activo';

        /*
            Si hay ID, actualizamos un recurso existente.
        */
        if ($id > 0) {
            $sql = "UPDATE recursos_gratuitos
        SET titulo = ?,
            imagen = ?,
            categoria_id = ?,
            url_drive = ?,
            google_drive_file_id = ?,
            formato = ?,
            estado = ?
        WHERE id = ?";

            $stmt = $this->conexion->prepare($sql);

            $stmt->execute([
                $datos['titulo'],
                $datos['imagen'],
                $datos['categoria_id'],
                $datos['url_drive'],
                $datos['google_drive_file_id'],
                $datos['formato'],
                $datos['estado'],
                $datos['id']
            ]);
        }

        /*
            Si no hay ID, creamos un recurso nuevo.

            Los contadores de clicks y descargas comienzan en 0.
        */
        $sql = "INSERT INTO recursos_gratuitos
        (titulo, imagen, categoria_id, url_drive, google_drive_file_id, formato, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            $datos['titulo'],
            $datos['imagen'],
            $datos['categoria_id'],
            $datos['url_drive'],
            $datos['google_drive_file_id'],
            $datos['formato'],
            $datos['estado']
        ]);

        return (int)$this->conexion->lastInsertId();
    }

    /**
     * Elimina un recurso gratuito desde administración.
     * ---------------------------------------------------------
     * Borra físicamente el registro de la tabla recursos_gratuitos.
     *
     * La eliminación de imagen local o archivo externo, si existe,
     * se gestiona desde el controlador antes de llamar a este método.
     *
     * @param int $id ID del recurso gratuito.
     *
     * @return bool True si se elimina correctamente.
     */
    public function eliminarRecursoGratuitoAdmin($id)
    {
        $sql = "DELETE FROM recursos_gratuitos
                WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $id
        ]);
    }
}
