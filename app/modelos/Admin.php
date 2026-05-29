<?php

/**
 * Modelo Admin
 * ---------------------------------------------------------
 * Este modelo se encarga de obtener datos generales para el
 * panel de administración.
 *
 * Funcionalidades principales:
 *
 * - Obtener estadísticas superiores del dashboard.
 * - Calcular ventas pagadas en un periodo concreto.
 * - Obtener productos más vendidos.
 * - Obtener últimos productos registrados.
 * - Obtener tickets pendientes de soporte.
 *
 * Este modelo trabaja directamente con la base de datos mediante PDO.
 */

// Incluimos el archivo de conexión a la base de datos.
require_once __DIR__ . '/../../config/conexion.php';

class Admin
{
    /**
     * Propiedad que almacena la conexión PDO con la base de datos.
     *
     * @var PDO
     */
    private $conexion;

    /**
     * Constructor del modelo.
     * ---------------------------------------------------------
     * Al crear una instancia de Admin, se establece automáticamente
     * la conexión con la base de datos usando la función conectarBD().
     */
    public function __construct()
    {
        $this->conexion = conectarBD();
    }
    /**
     * Obtiene las estadísticas principales del dashboard.
     * ---------------------------------------------------------
     * Esta función devuelve los datos que se muestran en las tarjetas
     * superiores del panel de administración.
     *
     * Datos que obtiene:
     *
     * - Total de productos activos.
     * - Usuarios clientes registrados dentro del periodo seleccionado.
     * - Descargas realizadas dentro del periodo seleccionado.
     * - Total de ventas pagadas dentro del periodo seleccionado.
     *
     * Si no se recibe rango de fechas, devuelve los totales históricos.
     *
     * @param string|null $fechaInicio Fecha inicial en formato Y-m-d.
     * @param string|null $fechaFin Fecha final en formato Y-m-d.
     *
     * @return array Devuelve ventas, usuarios, descargas y productos activos.
     */
    public function obtenerEstadisticas($fechaInicio = null, $fechaFin = null)
    {
        /*
            Total de productos activos.

            Este dato se mantiene como total general, porque representa
            los recursos actualmente publicados y no depende del periodo
            de ventas seleccionado.
        */
        $productosActivos = $this->conexion
            ->query("SELECT COUNT(*) FROM productos WHERE estado = 'activo'")
            ->fetchColumn();

        /*
            Fechas completas para consultas con rango.

            Se añaden horas para incluir todo el día inicial y todo el día final.
        */
        $fechaInicioCompleta = $fechaInicio ? $fechaInicio . ' 00:00:00' : null;
        $fechaFinCompleta = $fechaFin ? $fechaFin . ' 23:59:59' : null;

        /*
            Usuarios clientes registrados.

            Si hay rango de fechas, cuenta solo los usuarios registrados
            dentro de ese periodo usando usuarios.fecha_registro.
        */
        $sqlUsuarios = "SELECT COUNT(*) 
                        FROM usuarios 
                        WHERE rol_id = 3";

        $paramsUsuarios = [];

        if ($fechaInicio && $fechaFin) {
            $sqlUsuarios .= " AND fecha_registro BETWEEN ? AND ?";
            $paramsUsuarios[] = $fechaInicioCompleta;
            $paramsUsuarios[] = $fechaFinCompleta;
        }

        $stmtUsuarios = $this->conexion->prepare($sqlUsuarios);
        $stmtUsuarios->execute($paramsUsuarios);
        $usuarios = $stmtUsuarios->fetchColumn();

        /*
            Descargas realizadas.

            Si hay rango de fechas, suma solo las descargas cuya fecha_compra
            está dentro del periodo seleccionado.
        */
        $sqlDescargas = "SELECT COALESCE(SUM(numero_descargas), 0) 
                         FROM descargas
                         WHERE 1 = 1";

        $paramsDescargas = [];

        if ($fechaInicio && $fechaFin) {
            $sqlDescargas .= " AND fecha_compra BETWEEN ? AND ?";
            $paramsDescargas[] = $fechaInicioCompleta;
            $paramsDescargas[] = $fechaFinCompleta;
        }

        $stmtDescargas = $this->conexion->prepare($sqlDescargas);
        $stmtDescargas->execute($paramsDescargas);
        $descargas = $stmtDescargas->fetchColumn();

        /*
            Ventas pagadas.

            Solo se tienen en cuenta pagos con estado 'pagado'.
            Si hay rango de fechas, se filtra por pagos.fecha_pago.
        */
        $sqlVentas = "SELECT COALESCE(SUM(monto), 0) 
                      FROM pagos 
                      WHERE estado = 'pagado'";

        $paramsVentas = [];

        if ($fechaInicio && $fechaFin) {
            $sqlVentas .= " AND fecha_pago BETWEEN ? AND ?";
            $paramsVentas[] = $fechaInicioCompleta;
            $paramsVentas[] = $fechaFinCompleta;
        }

        $stmtVentas = $this->conexion->prepare($sqlVentas);
        $stmtVentas->execute($paramsVentas);
        $ventas = $stmtVentas->fetchColumn();

        /*
            Devolvemos todas las estadísticas en un array asociativo
            para que el controlador pueda pasarlas a la vista.
        */
        return [
            'ventas' => $ventas,
            'usuarios' => $usuarios,
            'descargas' => $descargas,
            'productos_activos' => $productosActivos
        ];
    }

    /**
     * Obtiene los productos más vendidos.
     * ---------------------------------------------------------
     * Esta función devuelve un listado de productos ordenados por:
     *
     * 1. Unidades vendidas.
     * 2. Importe vendido.
     *
     * Solo se tienen en cuenta pedidos y pagos con estado 'pagado'.
     *
     * Puede filtrar por fechas si se reciben fechaInicio y fechaFin.
     *
     * @param string|null $fechaInicio Fecha inicial en formato Y-m-d.
     * @param string|null $fechaFin Fecha final en formato Y-m-d.
     * @param int $limite Número máximo de productos a devolver.
     *
     * @return array Listado de productos más vendidos.
     */
    public function obtenerProductosMasVendidos($fechaInicio = null, $fechaFin = null, $limite = 5)
    {
        /*
            Consulta principal.

            Se cruzan las siguientes tablas:
            - productos: datos del recurso.
            - detalle_pedido: productos incluidos en pedidos.
            - pedidos: estado general del pedido.
            - pagos: estado y fecha del pago.
            - categorias: nombre de la categoría.

            Se usan COALESCE y SUM para calcular:
            - unidades_vendidas
            - importe_vendido
        */
        $sql = "SELECT 
                    p.id,
                    p.titulo,
                    p.precio,
                    p.imagen,
                    p.estado,
                    c.nombre AS categoria_nombre,
                    COALESCE(SUM(dp.cantidad), 0) AS unidades_vendidas,
                    COALESCE(SUM(dp.cantidad * dp.precio_unitario), 0) AS importe_vendido
                FROM productos p
                INNER JOIN detalle_pedido dp ON dp.producto_id = p.id
                INNER JOIN pedidos pe ON pe.id = dp.pedido_id
                INNER JOIN pagos pa ON pa.pedido_id = pe.id
                LEFT JOIN categorias c ON c.id = p.categoria_id
                WHERE pe.estado = 'pagado'
                AND pa.estado = 'pagado'";

        $params = [];

        /*
            Si existe rango de fechas, filtramos por fecha_pago.
        */
        if ($fechaInicio && $fechaFin) {
            $sql .= " AND pa.fecha_pago BETWEEN ? AND ?";
            $params[] = $fechaInicio . ' 00:00:00';
            $params[] = $fechaFin . ' 23:59:59';
        }

        /*
            Agrupamos por producto y ordenamos por ventas.

            LIMIT se recibe como parámetro entero para controlar
            cuántos productos se muestran en el dashboard.
        */
        $sql .= " GROUP BY p.id
                  ORDER BY unidades_vendidas DESC, importe_vendido DESC
                  LIMIT ?";

        $stmt = $this->conexion->prepare($sql);

        /*
            bindValue se usa para enlazar manualmente los parámetros.

            Primero enlazamos las fechas, si existen.
            Después enlazamos el límite como entero.
        */
        $pos = 1;

        foreach ($params as $param) {
            $stmt->bindValue($pos, $param);
            $pos++;
        }

        $stmt->bindValue($pos, $limite, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los últimos productos registrados.
     * ---------------------------------------------------------
     * Esta función se puede utilizar para mostrar una tabla resumen
     * con los últimos productos creados en el panel de administración.
     *
     * También calcula el total de descargas de cada producto.
     *
     * @param int $limite Número máximo de productos a devolver.
     *
     * @return array Listado de últimos productos.
     */
    public function obtenerUltimosProductos($limite = 5)
    {
        /*
            Consulta de últimos productos.

            Se usa LEFT JOIN para que aparezcan productos aunque no tengan
            categoría o descargas registradas.
        */
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

        /*
            Preparamos la consulta y enlazamos el límite como entero.
        */
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los últimos tickets pendientes de soporte.
     * ---------------------------------------------------------
     * Esta función devuelve los tickets que están en estado:
     *
     * - abierto
     * - respondido
     *
     * Se limita a los últimos 5 tickets para mostrar un resumen
     * en el dashboard.
     *
     * @return array Listado de tickets pendientes o respondidos.
     */
    public function obtenerTicketsPendientes()
    {
        /*
            Consulta de tickets pendientes.

            Se une soporte_tickets con usuarios para mostrar también
            el nombre del usuario que creó el ticket.
        */
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
