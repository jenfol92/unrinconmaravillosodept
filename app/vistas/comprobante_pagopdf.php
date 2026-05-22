<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <!--
        Vista: comprobante_pago_pdf_view.php
        ---------------------------------------------------------
        Esta vista genera el HTML que posteriormente se convierte
        en PDF para entregar al usuario un comprobante de pago.

        Normalmente esta vista se carga desde un controlador o endpoint
        que utiliza una librería como Dompdf.

        Variables recibidas:

        - $pedido:
          Array con los datos generales del pedido y del pago.

          Campos usados:
          - pedido_id
          - fecha_pedido
          - fecha_pago
          - metodo_pago
          - referencia_pago
          - total

        - $lineas:
          Array con los productos incluidos en el pedido.

          Campos usados:
          - titulo
          - cantidad
          - precio_unitario

        Funcionalidad principal:

        - Mostrar datos generales del pedido.
        - Mostrar datos del pago realizado.
        - Mostrar tabla con productos comprados.
        - Calcular el total de cada línea.
        - Mostrar el total final pagado.
        - Añadir nota legal/informativa sobre el comprobante.

        Seguridad:

        - Se utiliza htmlspecialchars() para imprimir datos dinámicos
          de forma segura en el HTML.
        - Los importes se convierten a float antes de mostrarse.
        - Las cantidades se convierten a int antes de calcular totales.
    -->

    <style>
        /*
            Estilos internos para el PDF.
            ---------------------------------------------------------
            En documentos PDF generados con Dompdf suele ser recomendable
            usar CSS sencillo e incrustado en la propia vista.
        */

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #333;
            font-size: 12px;
        }

        /*
            Cabecera del comprobante.
            Incluye título del documento y nombre de la web/proyecto.
        */
        .header {
            border-bottom: 2px solid #56B3AD;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        /*
            Título principal del comprobante.
        */
        h1 {
            color: #56B3AD;
            margin: 0;
        }

        /*
            Tabla de productos incluidos en el pedido.
        */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        /*
            Celdas de la tabla.
            Se usa borde inferior para facilitar la lectura en PDF.
        */
        th,
        td {
            border-bottom: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        /*
            Total final pagado.
            Se destaca visualmente al final del documento.
        */
        .total {
            margin-top: 20px;
            text-align: right;
            font-size: 16px;
            font-weight: bold;
        }

        /*
            Texto pequeño para notas legales o aclaraciones.
        */
        .small {
            color: #777;
            font-size: 11px;
        }
    </style>
</head>

<body>

    <!--
         Muestra el título del documento y la identificación
         general de la tienda.
    -->
    <div class="header">
        <h1>Comprobante de pago</h1>
        <p>UN RINCÓN DE PT</p>
    </div>

    <!-- 
         DATOS GENERALES DEL PEDIDO
         Información principal del pedido y del pago.
         Los datos proceden del array $pedido.
   -->
    <p>
        <strong>Pedido nº:</strong>
        <?= htmlspecialchars($pedido['pedido_id']) ?>
    </p>

    <p>
        <strong>Fecha del pedido:</strong>
        <?= htmlspecialchars($pedido['fecha_pedido']) ?>
    </p>

    <p>
        <strong>Fecha de pago:</strong>
        <?= htmlspecialchars($pedido['fecha_pago'] ?? '') ?>
    </p>

    <p>
        <strong>Método de pago:</strong>
        <?= htmlspecialchars($pedido['metodo_pago']) ?>
    </p>

    <p>
        <strong>Estado:</strong>
        Pagado
    </p>

    <p>
        <strong>Referencia:</strong>
        <?= htmlspecialchars($pedido['referencia_pago'] ?? '') ?>
    </p>

    <!-- 
         TABLA DE PRODUCTOS DEL PEDIDO
         Recorre el array $lineas y muestra cada producto comprado:
         - título
         - cantidad
         - precio unitario
         - total de línea
     -->
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio unitario</th>
                <th>Total línea</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($lineas as $linea): ?>

                <?php
                /*
                    Normalizamos los valores antes de imprimirlos
                    y antes de calcular el total de línea.
                */
                $titulo = $linea['titulo'] ?? 'Producto';
                $cantidad = (int)($linea['cantidad'] ?? 0);
                $precioUnitario = (float)($linea['precio_unitario'] ?? 0);

                /*
                    Calculamos el total de la línea:
                    cantidad x precio unitario.
                */
                $totalLinea = $precioUnitario * $cantidad;
                ?>

                <tr>
                    <td>
                        <?= htmlspecialchars($titulo) ?>
                    </td>

                    <td>
                        <?= $cantidad ?>
                    </td>

                    <td>
                        <?= number_format($precioUnitario, 2) ?> €
                    </td>

                    <td>
                        <?= number_format($totalLinea, 2) ?> €
                    </td>
                </tr>

            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- 
         TOTAL FINAL DEL PEDIDO
         Muestra el importe total pagado.
         El total procede de $pedido['total'].
  -->
    <div class="total">
        Total pagado:
        <?= number_format((float)$pedido['total'], 2) ?> €
    </div>

    <!-- 
         NOTA INFORMATIVA
         Aclara que el documento acredita el pago, pero no sustituye
         necesariamente a una factura formal.
 -->
    <p class="small">
        Este documento acredita el pago realizado a través de Stripe.
        No sustituye a una factura formal salvo que se emita conforme a la normativa fiscal aplicable.
    </p>

</body>

</html>