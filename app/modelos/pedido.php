<?php

require_once __DIR__ . '/../../config/conexion.php';

class Pedido
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = conectarBD();
    }

    public function crearPedidoPendiente($usuario_id, $productos)
    {
        try {
            $this->conexion->beginTransaction();

            $total = 0;

            foreach ($productos as $p) {
                $cantidad = (int)($p['cantidad'] ?? 1);
                $precio = (float)($p['precio'] ?? 0);

                if ($cantidad > 0 && $precio > 0) {
                    $total += $precio * $cantidad;
                }
            }

            if ($total <= 0) {
                throw new Exception('El total del pedido no es válido.');
            }

            $sql = "INSERT INTO pedidos 
                    (usuario_id, total, estado, metodo_pago, fecha_pedido)
                    VALUES (?, ?, 'pendiente', 'stripe', NOW())";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$usuario_id, $total]);

            $pedido_id = $this->conexion->lastInsertId();

            foreach ($productos as $p) {
                $producto_id = (int)$p['id'];
                $cantidad = (int)($p['cantidad'] ?? 1);
                $precio = (float)($p['precio'] ?? 0);

                if ($producto_id <= 0 || $cantidad <= 0 || $precio <= 0) {
                    continue;
                }

                $sql = "INSERT INTO detalle_pedido
                        (pedido_id, producto_id, cantidad, precio_unitario)
                        VALUES (?, ?, ?, ?)";

                $stmt = $this->conexion->prepare($sql);
                $stmt->execute([
                    $pedido_id,
                    $producto_id,
                    $cantidad,
                    $precio
                ]);
            }

            $sql = "INSERT INTO pagos
                    (pedido_id, monto, estado, referencia_pago, fecha_pago)
                    VALUES (?, ?, 'pendiente', NULL, NULL)";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$pedido_id, $total]);

            $this->conexion->commit();

            return $pedido_id;

        } catch (Exception $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }

    public function guardarReferenciaPago($pedido_id, $referencia_pago)
    {
        $sql = "UPDATE pagos 
                SET referencia_pago = ?
                WHERE pedido_id = ?";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $referencia_pago,
            $pedido_id
        ]);
    }

    public function marcarComoPagadoPorReferencia($referencia_pago)
    {
        try {
            $this->conexion->beginTransaction();

            $sql = "SELECT pedido_id 
                    FROM pagos 
                    WHERE referencia_pago = ?
                    LIMIT 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$referencia_pago]);

            $pago = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$pago) {
                throw new Exception('No se ha encontrado el pago en la base de datos.');
            }

            $pedido_id = (int)$pago['pedido_id'];

            $sql = "UPDATE pagos 
                    SET estado = 'pagado',
                        fecha_pago = NOW()
                    WHERE referencia_pago = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$referencia_pago]);

            $sql = "UPDATE pedidos 
                    SET estado = 'pagado'
                    WHERE id = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$pedido_id]);

            $this->conexion->commit();

            return $pedido_id;

        } catch (Exception $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }

    public function marcarComoCancelado($pedido_id)
    {
        try {
            $this->conexion->beginTransaction();

            $sql = "UPDATE pedidos 
                    SET estado = 'cancelado'
                    WHERE id = ?
                    AND estado = 'pendiente'";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$pedido_id]);

            $sql = "UPDATE pagos 
                    SET estado = 'cancelado'
                    WHERE pedido_id = ?
                    AND estado = 'pendiente'";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$pedido_id]);

            $this->conexion->commit();

            return true;

        } catch (Exception $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }
    
}