
<?php
/*Cargamos los controladores y generamos una instancia del controlador para acceder a sus funciones.
a su vez cargamos los modulos de conexion con la base de datos que ya están incluidos en controladores/AuthController.php*/
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ .  '/../app/controladores/AuthController.php';

$auth = new AuthController();
$auth->login();