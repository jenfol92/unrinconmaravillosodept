<?php

// Conexión con la BD
require_once __DIR__ . '/../../config/conexion.php';

class Admin
{
    private $conexion;

    public function __construct()
    {
        // Guardamos la conexión
        $this->conexion = conectarBD();
    }

    // Obtiene tarjetas superiores del dashboard
    public function obtenerEstadisticas()
    {
        // Total productos activos
        $productosActivos = $this->conexion
            ->query("SELECT COUNT(*) FROM productos WHERE estado = 'activo'")
            ->fetchColumn();

        // Total usuarios clientes
        $usuarios = $this->conexion
            ->query("SELECT COUNT(*) FROM usuarios WHERE rol_id = 3")
            ->fetchColumn();

        // Total descargas
        $descargas = $this->conexion
            ->query("SELECT COALESCE(SUM(numero_descargas), 0) FROM descargas")
            ->fetchColumn();

       
       // Total de ventas pagadas
$ventas = $this->conexion
    ->query("SELECT COALESCE(SUM(monto), 0) 
            FROM pagos 
             WHERE estado = 'pagado'")
    ->fetchColumn();

        return [
            'ventas' => $ventas,
            'usuarios' => $usuarios,
            'descargas' => $descargas,
            'productos_activos' => $productosActivos
        ];
    }

    // Obtiene últimos productos para la tabla
    public function obtenerUltimosProductos($limite = 5)
    {
        $sql = "SELECT 
                    p.id,
                    p.titulo,
                    p.precio,
                    p.imagen,
                    p.estado,
                    c.nombre AS categoria_nombre,
                    COALESCE(SUM(d.numero_descargas), 0) AS total_descargas
                FROM productos p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                LEFT JOIN descargas d ON d.producto_id = p.id
                GROUP BY p.id
                ORDER BY p.id DESC
                LIMIT ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tickets pendientes de soporte
    public function obtenerTicketsPendientes()
    {
        $sql = "SELECT 
                    st.id,
                    st.asunto,
                    st.estado,
                    st.fecha,
                    u.nombre AS usuario_nombre
                FROM soporte_tickets st
                INNER JOIN usuarios u ON u.id = st.usuario_id
                WHERE st.estado IN ('abierto', 'respondido')
                ORDER BY st.fecha DESC
                LIMIT 5";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}