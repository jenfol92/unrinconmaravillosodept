<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        h1 {
            text-align: center;
            margin-bottom: 5px;
        }

        .subtitulo {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }

        .filtros {
            background: #f2f2f2;
            padding: 8px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #56B3AD;
            color: white;
            padding: 7px;
            text-align: left;
        }

        td {
            border-bottom: 1px solid #ddd;
            padding: 6px;
        }

        .text-end {
            text-align: right;
        }

        .activo {
            color: #0f5132;
            font-weight: bold;
        }

        .inactivo {
            color: #842029;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            color: #777;
            font-size: 9px;
        }
    </style>
</head>

<body>

<h1>Reporte de recursos</h1>

<div class="subtitulo">
    Un Rincón Maravilloso de PT · Generado el <?= htmlspecialchars($fecha) ?>
</div>

<div class="filtros">
    <strong>Filtros aplicados:</strong><br>
    Búsqueda: <?= htmlspecialchars($busqueda ?: 'Sin filtro') ?><br>
    Categoría: <?= htmlspecialchars($categoria ?: 'Todas') ?><br>
    Estado: <?= htmlspecialchars($estado ?: 'Todos') ?>
</div>

<table>
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

    <tbody>
        <?php if (empty($productos)): ?>
            <tr>
                <td colspan="8" style="text-align:center; padding:20px;">
                    No hay productos para los filtros seleccionados.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($productos as $p): ?>
                <tr>
                    <td><?= (int)$p['id'] ?></td>

                    <td><?= htmlspecialchars($p['titulo'] ?? '') ?></td>

                    <td><?= htmlspecialchars($p['categoria_nombre'] ?? '') ?></td>

                    <td><?= htmlspecialchars($p['nivel_nombre'] ?? '') ?></td>

                    <td class="text-end">
                        <?= number_format((float)($p['precio'] ?? 0), 2, ',', '.') ?> €
                    </td>

                    <td class="text-end">
                        <?= (int)($p['unidades_vendidas'] ?? 0) ?>
                    </td>

                    <td class="text-end">
                        <?= number_format((float)($p['importe_vendido'] ?? 0), 2, ',', '.') ?> €
                    </td>

                    <td>
                        <?php if (($p['estado'] ?? '') === 'activo'): ?>
                            <span class="activo">Activo</span>
                        <?php else: ?>
                            <span class="inactivo">Inactivo</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div class="footer">
    Reporte generado automáticamente desde el panel de administración.
</div>

</body>
</html>