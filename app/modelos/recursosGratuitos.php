<?php

require_once __DIR__ . '/../../config/conexion.php';

class RecursoGratuito
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = conectarBD();
    }

    public function obtenerCategoriasGratuitas()
    {
        $sql = "SELECT *
                FROM categorias_gratuitas
                WHERE activa = 1
                ORDER BY  nombre ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerRecursosGratuitos($categorias = [], $busqueda = '')
    {
        $sql = "SELECT 
                    r.id,
                    r.titulo,
                    r.imagen,
                    r.url_drive,
                    r.formato,
                    r.clicks,
                    r.fecha_creacion,
                    c.nombre AS categoria_nombre
                FROM recursos_gratuitos r
                LEFT JOIN categorias_gratuitas c ON c.id = r.categoria_id
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
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function incrementarClicks($id)
    {
        $sql = "UPDATE recursos_gratuitos
                SET clicks = clicks + 1
                WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$id]);
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
}