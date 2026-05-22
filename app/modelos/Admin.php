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
     * Esta función devuelve los datos que se muestran normalmente
     * en las tarjetas superiores del panel de administración.
     *
     * Datos que obtiene:
     *
     * - Total de productos activos.
     * - Total de usuarios clientes.
     * - Total de descargas registradas.
     * - Total de ventas pagadas.
     *
     * Las ventas pueden filtrarse por rango de fechas.
     *
     * @param string|null $fechaInicio Fecha inicial en formato Y-m-d.
     * @param string|null $fechaFin Fecha final en formato Y-m-d.
     *
     * @return array Devuelve un array con ventas, usuarios, descargas y productos activos.
     */
    public function obtenerEstadisticas($fechaInicio = null, $fechaFin = null)
    {
        /*
            Total de productos activos.

            Se cuentan únicamente los productos cuyo estado es 'activo',
            ya que son los recursos visibles o disponibles en la tienda.
        */
        $productosActivos = $this->conexion
            ->query("SELECT COUNT(*) FROM productos WHERE estado = 'activo'")
            ->fetchColumn();

        /*
            Total de usuarios clientes.

            En este proyecto, el rol_id = 3 corresponde a usuarios clientes.
            No se cuentan administradores ni otros roles internos.
        */
        $usuarios = $this->conexion
            ->query("SELECT COUNT(*) FROM usuarios WHERE rol_id = 3")
            ->fetchColumn();

        /*
            Total de descargas.

            Se suma el campo numero_descargas de la tabla descargas.
            COALESCE evita que el resultado sea NULL si no hay registros.
        */
        $descargas = $this->conexion
            ->query("SELECT COALESCE(SUM(numero_descargas), 0) FROM descargas")
            ->fetchColumn();

        /*
            Consulta base para calcular ventas pagadas.

            Solo se tienen en cuenta los pagos cuyo estado sea 'pagado'.
        */
        $sqlVentas = "SELECT COALESCE(SUM(monto), 0) 
                      FROM pagos 
                      WHERE estado = 'pagado'";

        /*
            Array de parámetros para consulta preparada.

            Se rellena solo si se recibe rango de fechas.
        */
        $params = [];

        /*
            Si se reciben fechaInicio y fechaFin, se filtran las ventas
            entre el inicio del primer día y el final del último día.
        */
        if ($fechaInicio && $fechaFin) {
            $sqlVentas .= " AND fecha_pago BETWEEN ? AND ?";
            $params[] = $fechaInicio . ' 00:00:00';
            $params[] = $fechaFin . ' 23:59:59';
        }

        /*
            Preparamos y ejecutamos la consulta de ventas.

            Se usa prepare() porque la consulta puede recibir parámetros.
        */
        $stmt = $this->conexion->prepare($sqlVentas);
        $stmt->execute($params);
        $ventas = $stmt->fetchColumn();

        /*
            Devolvemos todas las estadísticas en un array asociativo
            para que el controlador pueda pasarlas fácilmente a la vista.
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