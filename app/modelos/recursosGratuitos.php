<?php

require_once __DIR__ . '/../../config/conexion.php';

class RecursoGratuito
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = conectarBD();
    }

    // ============================
    // CATEGORÍAS GRATUITAS
    // ============================

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

    // ============================
    // RECURSOS GRATUITOS - PÚBLICO
    // ============================

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

        if (!empty($categorias)) {
            $placeholders = implode(',', array_fill(0, count($categorias), '?'));
            $sql .= " AND r.categoria_id IN ($placeholders)";
            $params = array_merge($params, $categorias);
        }

        if (!empty($busqueda)) {
            $sql .= " AND r.titulo LIKE ?";
            $params[] = '%' . $busqueda . '%';
        }

        $sql .= " ORDER BY r.fecha_creacion DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

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
// Guarda una métrica individual con fecha.
// Esto permite saber cuándo ocurrió cada click o descarga.
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


// Incrementa el contador total de clicks
// y además guarda una fila histórica en recursos_gratuitos_metricas.
public function incrementarClicks($id)
{
    $this->conexion->beginTransaction();

    try {
        // 1. Actualizamos contador total.
        $sql = "UPDATE recursos_gratuitos
                SET clicks = COALESCE(clicks, 0) + 1
                WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            $id
        ]);

        // 2. Registramos la acción con fecha y hora.
        $this->registrarMetrica($id, 'click');

        $this->conexion->commit();

        return true;

    } catch (Exception $e) {
        $this->conexion->rollBack();
        throw $e;
    }
}


// Incrementa el contador total de descargas
// y además guarda una fila histórica en recursos_gratuitos_metricas.
public function incrementarDescargas($id)
{
    $this->conexion->beginTransaction();

    try {
        // 1. Actualizamos contador total.
        $sql = "UPDATE recursos_gratuitos
                SET descargas = COALESCE(descargas, 0) + 1
                WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            $id
        ]);

        // 2. Registramos la descarga con fecha y hora.
        $this->registrarMetrica($id, 'descarga');

        $this->conexion->commit();

        return true;

    } catch (Exception $e) {
        $this->conexion->rollBack();
        throw $e;
    }
}
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

   
    // RECURSOS GRATUITOS - ADMIN


public function obtenerRecursosGratuitosAdmin($fechaInicio = null, $fechaFin = null)
{
    /*
        Si recibimos fechaInicio y fechaFin, mostramos clicks y descargas
        solo dentro de ese periodo.

        Si NO recibimos fechas, mostramos los totales generales guardados
        en recursos_gratuitos.clicks y recursos_gratuitos.descargas.
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

                    -- Contamos clicks solo dentro del periodo
                    COALESCE(SUM(CASE WHEN m.tipo = 'click' THEN 1 ELSE 0 END), 0) AS clicks,

                    -- Contamos descargas solo dentro del periodo
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
            Aquí convertimos la fecha en rango completo de día.

            Ejemplo:
            $fechaInicio = 2026-05-01
            $fechaFin = 2026-05-31

            Se consulta:
            2026-05-01 00:00:00
            hasta
            2026-05-31 23:59:59
        */
        $stmt->execute([
            $fechaInicio . ' 00:00:00',
            $fechaFin . ' 23:59:59'
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
        Sin periodo: mostramos totales acumulados desde el inicio.
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

    public function guardarRecursoGratuitoAdmin($datos)
    {
        /*
            Normalizamos nombres por si desde el controlador te llegan
            como categoria_gratuita_id / drive_url.
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

        if ($id > 0) {
            $sql = "UPDATE recursos_gratuitos
                    SET titulo = ?,
                        imagen = ?,
                        categoria_id = ?,
                        url_drive = ?,
                        formato = ?,
                        estado = ?
                    WHERE id = ?";

            $stmt = $this->conexion->prepare($sql);

            return $stmt->execute([
                $titulo,
                $imagen,
                $categoriaId,
                $urlDrive,
                $formato,
                $estado,
                $id
            ]);
        }

        $sql = "INSERT INTO recursos_gratuitos
                (titulo, imagen, categoria_id, url_drive, formato, estado, clicks, descargas, fecha_creacion)
                VALUES (?, ?, ?, ?, ?, ?, 0, 0, NOW())";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            $titulo,
            $imagen,
            $categoriaId,
            $urlDrive,
            $formato,
            $estado
        ]);

        return (int)$this->conexion->lastInsertId();
    }

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