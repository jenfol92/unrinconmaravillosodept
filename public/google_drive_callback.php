<?php

require_once __DIR__ . '/../app/controladores/GoogleDriveOAuthController.php';

$controller = new GoogleDriveOAuthController();
$controller->callback();