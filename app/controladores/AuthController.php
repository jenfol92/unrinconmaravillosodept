<?php

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../modelos/Usuario.php';

class AuthController
{

    /*Funcion que recibe el email y contraseña del usuario , comprueba que está registrado 
a través de la conexion del modelos/Usuario.php e inicia la sesion redirigiendo al index.php. 
o muertra errir.*/
    public function login()
    {
        // Si entra por GET, solo muestra el formulario
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once __DIR__ . '/../vistas/login_view.php';
            return;
        }

        // Recogemos datos del formulario
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validación básica
     if ($email === '' || $password === '') {
    $errorLogin = "Debes introducir email y contraseña.";
    require_once __DIR__ . '/../vistas/login_view.php';
    return;
}

        // Buscar usuario en BD
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->obtenerPorEmail($email);

        // Comprobar contraseña
        if ($usuario && password_verify($password, $usuario['password_hash'])) {

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre_usuario'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol_id'];
            $_SESSION['usuario_email'] = $usuario['email'];
            header("Location: index.php");
            exit();
      } else {
    $errorLogin = "Email o contraseña incorrectos.";
    require_once __DIR__ . '/../vistas/login_view.php';
    return;
}
    }
    /* Funcion de registro de usuario (cliente), donde recibe los datos del formulario de registro_view.php 
crea una instancia de Usuario y llama a la funcion que crea el usuario en modelos/Usuario.php
conectado a la base de datos, enviandole los datos recogidos en el formulario de registro_view.php.
Esto redirigirá al login para inicio de sesión y comprobar que se ha registrado correctamente*/
    public function registro()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        require_once __DIR__ . '/../vistas/registro_view.php';
        return;
    }

    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($nombre === '' || $apellidos === '' || $email === '' || $password === '') {
            echo "Todos los campos son obligatorios";
            return;
        }

    if ($password !== $password_confirm) {
        echo "Las contraseñas no coinciden";
        return;
    }

    $usuarioModel = new Usuario();
        $existe = $usuarioModel->obtenerPorEmail($email);
          if ($existe) {
            echo "Este email ya está registrado";
            return;
        }
 $creado = $usuarioModel->crear($nombre, $email, $password, $apellidos);
             if (!$creado) {
            echo "No se pudo registrar el usuario";
            return;
        }
  
    header("Location: index.php");
    exit();
}
}
