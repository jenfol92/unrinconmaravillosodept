<?php

// Contraseña que quieres usar
$password = '3vecessi';

// Generamos hash compatible con password_verify()
$hash = password_hash($password, PASSWORD_DEFAULT);

// Mostramos el hash
echo $hash;
