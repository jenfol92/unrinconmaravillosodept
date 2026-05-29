<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <!--
        ESTILOS INTERNOS DEL REPORTE PDF
        ---------------------------------------------------------
        Esta vista está pensada para generar un reporte, normalmente
        en PDF mediante una librería como Dompdf.

        Por eso se usan estilos CSS sencillos e internos:
        - tipografía compatible con PDF
        - tablas simples
        - colores básicos
        - clases de estado
    -->
    <style>
        /*
            Estilo general del documento.
            -----------------------------------------------------
            DejaVu Sans suele usarse en PDF porque soporta bien
            caracteres especiales, tildes y símbolos.
        */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        /*
            Título principal del reporte.
        */
        h1 {
            text-align: center;
            margin-bottom: 5px;
        }

        /*
            Subtítulo bajo el título principal.
            Se usa para mostrar la marca y la fecha de generación.
        */
        .subtitulo {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }

        /*
            Caja donde se muestran los filtros aplicados
            al generar el reporte.
        */
        .filtros {
            background: #f2f2f2;
            padding: 8px;
            margin-bottom: 15px;
        }

        /*
            Tabla principal del reporte.
            Ocupa todo el ancho disponible.
        */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        /*
            Cabecera de la tabla.
        */
        th {
            background: #56B3AD;
            color: white;
            padding: 7px;
            text-align: left;
        }

        /*
            Celdas de la tabla.
        */
        td {
            border-bottom: 1px solid #ddd;
            padding: 6px;
        }

        /*
            Clase auxiliar para alinear importes y cantidades
            a la derecha.
        */
        .text-end {
            text-align: right;
        }

        /*
            Estado activo del producto.
        */
        .activo {
            color: #0f5132;
            font-weight: bold;
        }

        /*
            Estado inactivo del producto.
        */
        .inactivo {
            color: #842029;
            font-weight: bold;
        }

        /*
            Pie del reporte.
        */
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #777;
            font-size: 9px;
        }
    </style>
</head>

<body>

    <!--
        TÍTULO DEL REPORTE
        ---------------------------------------------------------
        Encabezado principal del documento PDF.
    -->
    <h1>Reporte de recursos</h1>


    <!--
        SUBTÍTULO
        ---------------------------------------------------------
        Muestra la marca de la web y la fecha de generación.

        Variable esperada:
        $fecha
        - Fecha formateada desde el controlador o servicio que genera el PDF.
    -->
    <div class="subtitulo">
        Un Rincón Maravilloso de PT · Generado el <?= htmlspecialchars($fecha) ?>
    </div>


    <!--
        FILTROS APLICADOS
        ---------------------------------------------------------
        Muestra los criterios usados para generar el reporte.

        Variables esperadas:
        $busqueda
        - Texto de búsqueda aplicado.

        $categoria
        - Categoría filtrada.

        $estado
        - Estado filtrado: activo, inactivo o todos.

        Si no hay filtro, se muestra un texto por defecto.
    -->
    <div class="filtros">
        <strong>Filtros aplicados:</strong><br>

        Búsqueda: <?= htmlspecialchars($busqueda ?: 'Sin filtro') ?><br>

        Categoría: <?= htmlspecialchars($categoria ?: 'Todas') ?><br>

        Estado: <?= htmlspecialchars($estado ?: 'Todos') ?>
    </div>


    <!--
        TABLA PRINCIPAL DEL REPORTE
        ---------------------------------------------------------
        Lista los productos/recursos encontrados según los filtros.
    -->
    <table>

        <!--
            CABECERA DE LA TABLA
        -->
        <thead>
            <tr>
                <th>ID</th>
                <th>Recurso</th>
                <th>Categoría</th>
                <th>Nivel</th>
                <th>Precio</th>
                <th>Ventas</th>
                <th>Importe</th>
                <th>Estado</th>
            </tr>
        </thead>


        <!--
            CUERPO DE LA TABLA
        -->
        <tbody>

            <!--
                Si no hay productos, mostramos una fila informativa.
            -->
            <?php if (empty($productos)): ?>

                <tr>
                    <td colspan="8" style="text-align:center; padding:20px;">
                        No hay productos para los filtros seleccionados.
                    </td>
                </tr>

            <?php else: ?>

                <!--
                    Recorremos los productos encontrados.

                    Variable esperada:
                    $productos
                    - Array de productos con datos comerciales y estadísticos.
                -->
                <?php foreach ($productos as $p): ?>

                    <tr>

                        <!--
                            ID del producto.
                            Se convierte a entero por seguridad.
                        -->
                        <td>
                            <?= (int)$p['id'] ?>
                        </td>


                        <!--
                            Título del recurso.
                            Se escapa con htmlspecialchars para evitar HTML no deseado.
                        -->
                        <td>
                            <?= htmlspecialchars($p['titulo'] ?? '') ?>
                        </td>


                        <!--
                            Nombre de la categoría.
                        -->
                        <td>
                            <?= htmlspecialchars($p['categoria_nombre'] ?? '') ?>
                        </td>


                        <!--
                            Nombre del nivel educativo.
                        -->
                        <td>
                            <?= htmlspecialchars($p['nivel_nombre'] ?? '') ?>
                        </td>


                        <!--
                            Precio del producto.
                            Se formatea con:
                            - 2 decimales
                            - coma decimal
                            - punto de miles
                        -->
                        <td class="text-end">
                            <?= number_format((float)($p['precio'] ?? 0), 2, ',', '.') ?> €
                        </td>


                        <!--
                            Unidades vendidas.
                            Se convierte a entero.
                        -->
                        <td class="text-end">
                            <?= (int)($p['unidades_vendidas'] ?? 0) ?>
                        </td>


                        <!--
                            Importe total vendido.
                            Normalmente será precio x unidades vendidas,
                            calculado previamente desde la consulta/modelo.
                        -->
                        <td class="text-end">
                            <?= number_format((float)($p['importe_vendido'] ?? 0), 2, ',', '.') ?> €
                        </td>


                        <!--
                            Estado del producto.
                            Se muestra visualmente como:
                            - Activo
                            - Inactivo
                        -->
                        <td>
                            <?php if (($p['estado'] ?? '') === 'activo'): ?>

                                <span class="activo">
                                    Activo
                                </span>

                            <?php else: ?>

                                <span class="inactivo">
                                    Inactivo
                                </span>

                            <?php endif; ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

        </tbody>

    </table>


    <!--
        PIE DEL REPORTE
        ---------------------------------------------------------
        Texto final del documento.
    -->
    <div class="footer">
        Reporte generado automáticamente desde el panel de administración.
    </div>

</body>

</html>