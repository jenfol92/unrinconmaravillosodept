<?php

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../servicios/MailService.php';

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
            $_SESSION['session_version'] = (int)($usuario['session_version'] ?? 1);

            /*
    Registramos el acceso correcto del usuario.

    Esto actualiza en la tabla usuarios:
    - ultimo_acceso
    - acceso_actual
    - ultimo_ip

    Debe hacerse aquí, justo cuando el login ha sido correcto.
*/
            $resultadoAcceso = $usuarioModel->registrarAccesoUsuario($usuario['id']);

            if (!$resultadoAcceso) {
                die("No se ha podido registrar el acceso");
            }
            /*
    Redirigimos al inicio tras iniciar sesión correctamente.
*/
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
    /**
     * Solicitud de recuperación de contraseña.
     * ---------------------------------------------------------
     * Muestra el formulario para introducir el email y, si el email existe,
     * genera un token temporal y envía un enlace por correo.
     *
     * Por seguridad, la respuesta visible será genérica.
     */
    public function recuperarPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once __DIR__ . '/../vistas/recuperar_password_view.php';
            return;
        }

        $email = trim($_POST['email'] ?? '');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorRecuperacion = "Introduce un correo electrónico válido.";
            require_once __DIR__ . '/../vistas/recuperar_password_view.php';
            return;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->obtenerPorEmail($email);

        /*
        Mensaje genérico para no revelar si el email existe o no.
    */
        $mensajeRecuperacion = "Si el correo está registrado, recibirás un enlace para restablecer tu contraseña.";

        if ($usuario) {
            try {
                /*
                Creamos token real y guardamos solo su hash.
            */
                $token = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $token);

                /*
                Invalidamos tokens anteriores del usuario.
            */
                $usuarioModel->invalidarTokensRecuperacionUsuario($usuario['id']);

                /*
                Guardamos el nuevo token con caducidad de 1 hora.
            */
                $usuarioModel->guardarTokenRecuperacion($usuario['id'], $tokenHash);

                /*
                Enlace que recibirá el usuario.
            */
                $enlace = rtrim(PUBLIC_URL, '/') . 'restablecer-password.php?token=' . urlencode($token);

                /*
                Envío real mediante SMTP.
            */
                $mailService = new MailService();
                $mailService->enviarRecuperacionPassword(
                    $usuario['email'],
                    $usuario['nombre'] ?? 'usuario',
                    $enlace
                );
            } catch (Exception $e) {
                /*
                En local mostramos el error para poder depurar.
                En producción no conviene mostrar detalles técnicos.
            */
                if (defined('APP_ENV') && APP_ENV === 'local') {
                    $errorRecuperacion = "Error enviando el email: " . $e->getMessage();
                    require_once __DIR__ . '/../vistas/recuperar_password_view.php';
                    return;
                }
            }
        }

        require_once __DIR__ . '/../vistas/recuperar_password_view.php';
    }

    /**
     * Restablecimiento de contraseña.
     * ---------------------------------------------------------
     * Valida el token recibido por URL o POST y permite crear una nueva
     * contraseña si el token es válido, no está usado y no ha caducado.
     */
    public function restablecerPassword()
    {
        $token = $_GET['token'] ?? $_POST['token'] ?? '';

        if ($token === '') {
            $errorReset = "El enlace de recuperación no es válido.";
            require_once __DIR__ . '/../vistas/restablecer_password_view.php';
            return;
        }

        $tokenHash = hash('sha256', $token);

        $usuarioModel = new Usuario();
        $tokenData = $usuarioModel->obtenerTokenRecuperacionValido($tokenHash);

        if (!$tokenData) {
            $errorReset = "El enlace ha caducado, ya ha sido utilizado o no es válido.";
            require_once __DIR__ . '/../vistas/restablecer_password_view.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once __DIR__ . '/../vistas/restablecer_password_view.php';
            return;
        }

        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if (strlen($password) < 4) {
            $errorReset = "La contraseña debe tener al menos 4 caracteres.";
            require_once __DIR__ . '/../vistas/restablecer_password_view.php';
            return;
        }

        if ($password !== $passwordConfirm) {
            $errorReset = "Las contraseñas no coinciden.";
            require_once __DIR__ . '/../vistas/restablecer_password_view.php';
            return;
        }

        try {
            $usuarioModel->actualizarPassword($tokenData['usuario_id'], $password);
            $usuarioModel->marcarTokenRecuperacionUsado($tokenData['id']);

            $mensajeReset = "Contraseña actualizada correctamente. Ya puedes iniciar sesión.";

            require_once __DIR__ . '/../vistas/restablecer_password_view.php';
            return;
        } catch (Exception $e) {
            $errorReset = "No se pudo actualizar la contraseña.";
            require_once __DIR__ . '/../vistas/restablecer_password_view.php';
            return;
        }
    }
}
