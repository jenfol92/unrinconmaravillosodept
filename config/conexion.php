<?php
define('HOST', "localhost");
define('DB', 'tienda_recursos');
define('USER', 'root');
define('PASS', '');

function conectarBD(){
  try{

  //utf8mb4 soporta emojis.
 $dns="mysql:host=".HOST.";dbname=".DB."; charset=utf8mb4;";
  
    $conexion= NEW PDO($dns,USER,PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
    ]);
    return $conexion;

    }catch (PDOException $e){
        die("Error de conexión: ".$e->getMessage());
    }

}


?>