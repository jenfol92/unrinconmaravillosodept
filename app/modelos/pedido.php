<?php

/**
 * Modelo Pedido
 * ---------------------------------------------------------
 * Este modelo se encarga de gestionar los pedidos de la tienda.
 *
 * Funcionalidades principales:
 *
 * - Crear un pedido pendiente antes de enviar al usuario a Stripe.
 * - Guardar los productos asociados al pedido.
 * - Crear el registro inicial del pago en estado pendiente.
 * - Guardar la referencia de pago devuelta por Stripe.
 * - Marcar un pedido como pagado cuando Stripe confirma el pago.
 * - Marcar un pedido como cancelado si el usuario cancela el pago.
 *
 * Este modelo trabaja con tres tablas principales:
 *
 * - pedidos
 * - detalle_pedido
 * - pagos
 *
 * Se utilizan transacciones para asegurar que las operaciones importantes
 * se realizan de forma completa. Si algo falla, se revierte todo con rollback().
 */

require_once __DIR__ . '/../../config/conexion.php';

class Pedido
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
     * Al crear una instancia de Pedido, se abre la conexión con la base de datos.
     */
    public function __construct()
    {
        $this->conexion = conectarBD();
    }

    /**
     * Crea un pedido pendiente en la base de datos.
     * ---------------------------------------------------------
     * Esta función se ejecuta antes de redirigir al usuario a Stripe.
     *
     * Flujo de trabajo:
     *
     * 1. Inicia una transacción.
     * 2. Calcula el total del pedido a partir de los productos recibidos.
     * 3. Inserta el pedido en la tabla pedidos con estado "pendiente".
     * 4. Inserta cada producto en la tabla detalle_pedido.
     * 5. Crea un registro en la tabla pagos con estado "pendiente".
     * 6. Confirma la transacción con commit().
     * 7. Devuelve el ID del pedido creado.
     *
     * Si algo falla, se ejecuta rollback() y se lanza la excepción.
     *
     * @param int $usuario_id ID del usuario que realiza el pedido.
     * @param array $productos Lista de productos incluidos en el carrito.
     *
     * @return int ID del pedido creado.
     *
     * @throws Exception Si el total no es válido o falla alguna operación.
     */
    public function crearPedidoPendiente($usuario_id, $productos)
    {
        try {
            /*
                Iniciamos una transacción.

                Esto garantiza que el pedido, sus detalles y el pago pendiente
                se creen como una única operación. Si algo falla, no se guarda
                nada parcialmente.
            */
            $this->conexion->beginTransaction();

            /*
                Calculamos el total del pedido.

                Recorremos los productos recibidos desde el controlador.
                Cada producto debe incluir:
                - precio
                - cantidad
            */
            $total = 0;

            foreach ($productos as $p) {
                $cantidad = (int)($p['cantidad'] ?? 1);
                $precio = (float)($p['precio'] ?? 0);

                if ($cantidad > 0 && $precio > 0) {
                    $total += $precio * $cantidad;
                }
            }

            /*
                Si el total es 0 o negativo, el pedido no es válido.
            */
            if ($total <= 0) {
                throw new Exception('El total del pedido no es válido.');
            }

            /*
                Insertamos el pedido principal.

                Estado inicial:
                - pendiente

                Método de pago:
                - stripe
            */
            $sql = "INSERT INTO pedidos 
                    (usuario_id, total, estado, metodo_pago, fecha_pedido)
                    VALUES (?, ?, 'pendiente', 'stripe', NOW())";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$usuario_id, $total]);

            /*
                Obtenemos el ID del pedido recién creado.

                Este ID se usará para insertar el detalle del pedido
                y el registro de pago.
            */
            $pedido_id = $this->conexion->lastInsertId();

            /*
                Insertamos cada producto dentro de detalle_pedido.

                Esta tabla guarda:
                - qué pedido contiene el producto
                - qué producto es
                - cantidad comprada
                - precio unitario en el momento de la compra

                Guardar el precio unitario es importante porque el precio
                del producto podría cambiar en el futuro.
            */
            foreach ($productos as $p) {
                $producto_id = (int)$p['id'];
                $cantidad = (int)($p['cantidad'] ?? 1);
                $precio = (float)($p['precio'] ?? 0);

                /*
                    Si algún dato no es válido, se ignora ese producto.
                */
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

            /*
                Creamos el registro inicial del pago.

                Estado inicial:
                - pendiente

                referencia_pago:
                - NULL porque todavía no tenemos el ID de sesión de Stripe.

                fecha_pago:
                - NULL porque todavía no se ha completado el pago.
            */
            $sql = "INSERT INTO pagos
                    (pedido_id, monto, estado, referencia_pago, fecha_pago)
                    VALUES (?, ?, 'pendiente', NULL, NULL)";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$pedido_id, $total]);

            /*
                Confirmamos todos los cambios.
            */
            $this->conexion->commit();

            /*
                Devolvemos el ID del pedido creado para que el controlador
                pueda relacionarlo con la sesión de Stripe.
            */
            return $pedido_id;

        } catch (Exception $e) {
            /*
                Si ocurre cualquier error, revertimos la transacción.

                Así evitamos pedidos incompletos, pagos sin pedido o detalles
                huérfanos.
            */
            $this->conexion->rollBack();

            /*
                Relanzamos la excepción para que el controlador pueda gestionarla.
            */
            throw $e;
        }
    }

    /**
     * Guarda la referencia de pago devuelta por Stripe.
     * ---------------------------------------------------------
     * Después de crear la sesión de Stripe Checkout, Stripe devuelve
     * un identificador de sesión.
     *
     * Ese identificador se guarda en la tabla pagos como referencia_pago.
     *
     * Esta referencia permitirá localizar el pedido cuando Stripe redirija
     * al usuario a la página de pago exitoso.
     *
     * @param int $pedido_id ID del pedido.
     * @param string $referencia_pago ID de sesión o referencia devuelta por Stripe.
     *
     * @return bool True si la actualización se ejecuta correctamente.
     */
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

    /**
     * Marca un pedido como pagado usando la referencia de Stripe.
     * ---------------------------------------------------------
     * Esta función se ejecuta cuando Stripe devuelve al usuario a la
     * página de pago exitoso.
     *
     * Flujo de trabajo:
     *
     * 1. Inicia una transacción.
     * 2. Busca el pedido asociado a la referencia de pago.
     * 3. Cambia el estado del pago a "pagado".
     * 4. Guarda la fecha de pago con NOW().
     * 5. Cambia el estado del pedido a "pagado".
     * 6. Confirma la transacción.
     * 7. Devuelve el ID del pedido pagado.
     *
     * @param string $referencia_pago Referencia de pago guardada desde Stripe.
     *
     * @return int ID del pedido marcado como pagado.
     *
     * @throws Exception Si no se encuentra el pago o falla la operación.
     */
    public function marcarComoPagadoPorReferencia($referencia_pago)
    {
        try {
            /*
                Iniciamos transacción porque vamos a actualizar tanto pagos
                como pedidos.
            */
            $this->conexion->beginTransaction();

            /*
                Buscamos el pedido asociado a la referencia de pago.
            */
            $sql = "SELECT pedido_id 
                    FROM pagos 
                    WHERE referencia_pago = ?
                    LIMIT 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$referencia_pago]);

            $pago = $stmt->fetch(PDO::FETCH_ASSOC);

            /*
                Si no existe ningún pago con esa referencia, no podemos
                marcar nada como pagado.
            */
            if (!$pago) {
                throw new Exception('No se ha encontrado el pago en la base de datos.');
            }

            $pedido_id = (int)$pago['pedido_id'];

            /*
                Actualizamos el registro de pago.

                - estado pasa a "pagado"
                - fecha_pago se guarda con la fecha y hora actual
            */
            $sql = "UPDATE pagos 
                    SET estado = 'pagado',
                        fecha_pago = NOW()
                    WHERE referencia_pago = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$referencia_pago]);

            /*
                Actualizamos también el pedido principal.
            */
            $sql = "UPDATE pedidos 
                    SET estado = 'pagado'
                    WHERE id = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$pedido_id]);

            /*
                Confirmamos la transacción.
            */
            $this->conexion->commit();

            /*
                Devolvemos el ID del pedido para que el controlador pueda
                mostrar información de confirmación si lo necesita.
            */
            return $pedido_id;

        } catch (Exception $e) {
            /*
                Si algo falla, revertimos todos los cambios.
            */
            $this->conexion->rollBack();
            throw $e;
        }
    }

    /**
     * Marca un pedido como cancelado.
     * ---------------------------------------------------------
     * Esta función se ejecuta cuando el usuario cancela el pago
     * desde Stripe Checkout.
     *
     * Solo marca como cancelados los pedidos y pagos que sigan en estado
     * "pendiente". De esta forma se evita modificar un pedido que ya esté
     * pagado o en otro estado.
     *
     * Flujo:
     *
     * 1. Inicia una transacción.
     * 2. Cambia el estado del pedido a "cancelado" si estaba pendiente.
     * 3. Cambia el estado del pago a "cancelado" si estaba pendiente.
     * 4. Confirma la transacción.
     *
     * @param int $pedido_id ID del pedido cancelado.
     *
     * @return bool True si la operación se completa.
     *
     * @throws Exception Si falla la actualización.
     */
    public function marcarComoCancelado($pedido_id)
    {
        try {
            /*
                Iniciamos transacción porque se actualizan dos tablas:
                pedidos y pagos.
            */
            $this->conexion->beginTransaction();

            /*
                Marcamos el pedido como cancelado.

                Solo se actualiza si el pedido sigue pendiente.
            */
            $sql = "UPDATE pedidos 
                    SET estado = 'cancelado'
                    WHERE id = ?
                    AND estado = 'pendiente'";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$pedido_id]);

            /*
                Marcamos el pago asociado como cancelado.

                También solo se actualiza si sigue pendiente.
            */
            $sql = "UPDATE pagos 
                    SET estado = 'cancelado'
                    WHERE pedido_id = ?
                    AND estado = 'pendiente'";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$pedido_id]);

            /*
                Confirmamos los cambios.
            */
            $this->conexion->commit();

            return true;

        } catch (Exception $e) {
            /*
                Si ocurre cualquier error, revertimos la operación completa.
            */
            $this->conexion->rollBack();
            throw $e;
        }
    }
}