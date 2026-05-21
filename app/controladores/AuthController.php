<?php

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../modelos/Usuario.php';

/**
 * Clase AuthController
 * ---------------------------------------------------------
 * Controlador encargado de gestionar:
 * - Inicio de sesión de usuarios.
 * - Registro de nuevos usuarios clientes.
 *
 * En este controlador se realizan validaciones en servidor.
 * Aunque también existan validaciones en JavaScript, la validación
 * en PHP es obligatoria porque el usuario puede desactivar JavaScript
 * o enviar datos manipulados.
 */
class AuthController
{
    /**
     * Muestra el formulario de login o procesa el inicio de sesión.
     *
     * Funcionamiento:
     * - Si la petición es GET, carga la vista del formulario.
     * - Si la petición es POST, recoge email y contraseña.
     * - Valida que los campos no estén vacíos.
     * - Busca el usuario por email.
     * - Comprueba la contraseña con password_verify().
     * - Si todo es correcto, crea variables de sesión.
     */
    public function login()
    {
        // Si entra por GET, solo mostramos el formulario de login.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once __DIR__ . '/../vistas/login_view.php';
            return;
        }

        // Recogemos y limpiamos los datos enviados desde el formulario.
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validación básica en servidor.
        if ($email === '' || $password === '') {
            $errorLogin = "Debes introducir email y contraseña.";
            require_once __DIR__ . '/../vistas/login_view.php';
            return;
        }

        // Validamos que el email tenga un formato correcto.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorLogin = "Introduce un email válido.";
            require_once __DIR__ . '/../vistas/login_view.php';
            return;
        }

        // Buscamos el usuario en la base de datos.
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->obtenerPorEmail($email);

        /*
            Comprobamos:
            - Que el usuario exista.
            - Que la contraseña introducida coincida con el hash guardado.
        */
        if ($usuario && password_verify($password, $usuario['password_hash'])) {

            /*
                Creamos las variables de sesión.
                Estas variables permiten mantener al usuario identificado
                y controlar sus permisos según el rol.
            */
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre_usuario'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol_id'];
            $_SESSION['usuario_email'] = $usuario['email'];

            // Redirigimos al inicio tras iniciar sesión correctamente.
            header("Location: index.php");
            exit();

        } else {
            // Mensaje genérico para no dar pistas sobre si falla el email o la contraseña.
            $errorLogin = "Email o contraseña incorrectos.";
            require_once __DIR__ . '/../vistas/login_view.php';
            return;
        }
    }

    /**
     * Muestra el formulario de registro o procesa el alta de usuario.
     *
     * Datos obligatorios:
     * - Nombre
     * - Apellidos
     * - Email
     * - Localidad
     * - Código postal
     * - Contraseña
     * - Confirmación de contraseña
     *
     * Validaciones:
     * - Email válido.
     * - Contraseña mínima de 4 caracteres.
     * - Confirmación de contraseña coincidente.
     * - Localidad válida.
     * - Código postal español válido de 5 cifras.
     * - Email no registrado previamente.
     */
    public function registro()
    {
        // Si entra por GET, solo mostramos el formulario de registro.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once __DIR__ . '/../vistas/registro_view.php';
            return;
        }

        // Recogemos y limpiamos los datos enviados desde el formulario.
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $localidad = trim($_POST['localidad'] ?? '');
        $cp = trim($_POST['cp'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        /*
            Array de errores.
            En lugar de cortar en el primer error, guardamos todos los errores
            para poder mostrarlos juntos en la vista.
        */
        $erroresRegistro = [];

        // Validamos campos obligatorios.
        if ($nombre === '') {
            $erroresRegistro[] = "El nombre es obligatorio.";
        }

        if ($apellidos === '') {
            $erroresRegistro[] = "Los apellidos son obligatorios.";
        }

        if ($email === '') {
            $erroresRegistro[] = "El email es obligatorio.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erroresRegistro[] = "El email no tiene un formato válido.";
        }

        /*
            Validación de localidad:
            - Permite letras, tildes, ñ, espacios, puntos, guiones y barra.
            - Mínimo 2 caracteres.
            - Máximo 100 caracteres.
        */
        if ($localidad === '') {
            $erroresRegistro[] = "La localidad es obligatoria.";
        } elseif (!preg_match("/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s.'\-\/]{2,100}$/u", $localidad)) {
            $erroresRegistro[] = "La localidad no tiene un formato válido.";
        }

        /*
            Validación de código postal español:
            - Debe tener exactamente 5 cifras.
            - Las dos primeras cifras deben corresponder a provincias españolas: 01 a 52.
        */
        if ($cp === '') {
            $erroresRegistro[] = "El código postal es obligatorio.";
        } elseif (!preg_match('/^(0[1-9]|[1-4][0-9]|5[0-2])[0-9]{3}$/', $cp)) {
            $erroresRegistro[] = "El código postal debe ser español y tener 5 cifras válidas.";
        }

        // Validación de contraseña mínima.
        if ($password === '') {
            $erroresRegistro[] = "La contraseña es obligatoria.";
        } elseif (strlen($password) < 4) {
            $erroresRegistro[] = "La contraseña debe tener al menos 4 caracteres.";
        }

        // Validación de confirmación de contraseña.
        if ($password !== $password_confirm) {
            $erroresRegistro[] = "Las contraseñas no coinciden.";
        }

        /*
            Si hay errores, volvemos a cargar la vista.
            Las variables quedan disponibles para repintar el formulario.
        */
        if (!empty($erroresRegistro)) {
            require_once __DIR__ . '/../vistas/registro_view.php';
            return;
        }

        // Creamos el modelo de usuario.
        $usuarioModel = new Usuario();

        // Comprobamos que el email no exista ya en la base de datos.
        $existe = $usuarioModel->obtenerPorEmail($email);

        if ($existe) {
            $erroresRegistro[] = "Este email ya está registrado.";
            require_once __DIR__ . '/../vistas/registro_view.php';
            return;
        }

        /*
            Creamos el usuario.
            Por defecto, en el modelo se asigna rol_id = 3,
            que corresponde al usuario cliente.
        */
        $creado = $usuarioModel->crear(
            $nombre,
            $email,
            $password,
            $apellidos,
            $localidad,
            $cp
        );

        if (!$creado) {
            $erroresRegistro[] = "No se pudo registrar el usuario.";
            require_once __DIR__ . '/../vistas/registro_view.php';
            return;
        }

        /*
            Redirigimos al login.
            Se añade registro=ok para poder mostrar un mensaje si quieres:
            "Usuario creado correctamente, inicia sesión".
        */
        header("Location: login.php?registro=ok");
        exit();
    }
}