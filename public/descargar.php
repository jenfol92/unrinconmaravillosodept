<?php

/**
 * Endpoint público de descarga.
 * ---------------------------------------------------------
 * Este archivo no contiene lógica de negocio.
 * Solo carga el controlador y delega la descarga.
 */

require_once __DIR__ . '/../app/controladores/descarga_controller.php';

$controller = new DescargaController();
$controller->descargarPorToken();