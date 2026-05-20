<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../app/modelos/Producto.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id']) || !in_array((int)$_SESSION['rol'], [1, 2], true)) {
    echo json_encode([
        'ok' => false,
        'error' => 'No autorizado'
    ]);
    exit;
}

try {
    $model = new Producto();

    // ==========================
    // PAGINACIÓN
    // ==========================
    $pagina = max(1, (int)($_GET['pagina'] ?? 1));
    $limite = 10;
    $offset = ($pagina - 1) * $limite;

    // ==========================
    // FILTROS BÁSICOS
    // ==========================
    $busqueda = trim($_GET['busqueda'] ?? '');
    $estado = trim($_GET['estado'] ?? '');
    $categoria = $_GET['categoria'] ?? '';

    $categorias = $categoria !== '' ? [(int)$categoria] : [];
    $niveles = [];

    // ==========================
    // FILTRO DE PERIODO
    // ==========================
    $periodoDatos = $_GET['periodo_datos'] ?? 'todos';
    $datosDesde = $_GET['datos_desde'] ?? '';
    $datosHasta = $_GET['datos_hasta'] ?? '';

    $fechaInicio = null;
    $fechaFin = null;

    $hoy = new DateTime();

    switch ($periodoDatos) {
        case 'ultimos_7':
            $fechaInicio= (clone $hoy)->modify('-6 days')->format('Y-m-d');
            $fechaFin = $hoy->format('Y-m-d');
            break;

        case 'ultimos_30':
            $fechaInicio = (clone $hoy)->modify('-29 days')->format('Y-m-d');
            $fechaFin = $hoy->format('Y-m-d');
            break;

        case 'mes_anterior':
            $fechaInicio = (new DateTime('first day of last month'))->format('Y-m-d');
            $fechaFin= (new DateTime('last day of last month'))->format('Y-m-d');
            break;

        case 'personalizado':
            if ($datosDesde !== '' && $datosHasta !== '') {
                $fechaInicio = $datosDesde;
                $fechaFin = $datosHasta;
            }
            break;

        case 'todos':
        default:
            $fechaInicio = null;
            $fechaFin = null;
            break;
    }

    // ==========================
    // CONSULTA AL MODELO
    // ==========================
    $resultado = $model->obtenerProductosAdmin(
        $categorias,
        $niveles,
        $busqueda,
        $estado,
        $limite,
        $offset,
        $fechaInicio,
        $fechaFin
    );

    echo json_encode([
        'ok' => true,
        'productos' => $resultado['productos'],
        'total_paginas' => $resultado['total_paginas'],
        'pagina_actual' => $pagina,
        'periodo_datos' => [
            'periodo' => $periodoDatos,
            'inicio' => $fechaInicio,
            'fin' => $fechaFin
        ]
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'ok' => false,
        'error' => 'Error cargando productos: ' . $e->getMessage()
    ]);
    exit;
}