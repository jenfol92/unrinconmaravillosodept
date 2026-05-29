<?php

/**
 * UsuarioController
 * ---------------------------------------------------------
 * Controlador encargado de gestionar las acciones del usuario cliente.
 *
 * Actualmente contiene la función perfil(), que carga toda la información
 * necesaria para mostrar el panel personal del usuario.
 *
 * Funcionalidades principales del perfil:
 *
 * - Comprobar que el usuario ha iniciado sesión.
 * - Comprobar que el usuario tiene rol de cliente.
 * - Cargar los datos personales del usuario.
 * - Cargar sus productos favoritos.
 * - Cargar sus recursos adquiridos.
 * - Cargar sus tickets de soporte.
 * - Mostrar la vista perfil_view.php.
 */
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/Soporte.php';

class UsuarioController
{
    /**
     * Muestra el perfil del usuario cliente.
     * ---------------------------------------------------------
     * Esta función se ejecuta cuando un usuario accede a su panel personal.
     *
     * Flujo de funcionamiento:
     *
     * 1. Comprueba que existe una sesión activa.
     * 2. Comprueba que el usuario tiene rol 3, correspondiente a cliente.
     * 3. Carga los datos personales del usuario desde la base de datos.
     * 4. Carga los productos favoritos del usuario.
     * 5. Carga los productos o recursos adquiridos por el usuario.
     * 6. Carga los tickets de soporte creados por el usuario.
     * 7. Muestra la vista perfil_view.php.
     *
     * @return void
     */
    public function perfil()
    {
        /*
            Comprobamos que el usuario esté logueado.

            Si no existe $_SESSION['usuario_id'], significa que no hay
            una sesión activa. En ese caso, redirigimos al login.
        */
        if (!isset($_SESSION['usuario_id'])) {
            header("Location:" . PUBLIC_URL . " login.php");
            exit();
        }

        /*
            Comprobamos que el usuario tenga rol 3.

            En este proyecto, el rol 3 corresponde al usuario cliente.
            Si intenta acceder otro tipo de usuario, se redirige al inicio.
        */
        if ($_SESSION['rol'] != 3) {
            header("Location:" . PUBLIC_URL . "index.php");
            exit();
        }

        /*
            Guardamos el ID del usuario en una variable para reutilizarlo
            en las consultas siguientes.
        */
        $usuarioId = $_SESSION['usuario_id'];

        /*
            Cargamos los datos personales del usuario.

            El modelo Usuario obtiene información como:
            - Nombre
            - Apellidos
            - Email
            - Localidad
            - Código postal
            - Fecha de registro
        */
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->obtenerPorId($usuarioId);

        /*
            Cargamos los productos favoritos del usuario.

            El modelo Producto consulta los recursos que el usuario ha
            marcado como favoritos.
        */
        $productoModel = new Producto();
        $favoritos = $productoModel->obtenerProductosFavoritos($usuarioId);
        
        $usuarioModel->sincronizarDescargasUsuario($usuarioId);

        /*
            Cargamos los recursos adquiridos por el usuario.

            Estos recursos se mostrarán en el perfil para que el usuario
            pueda consultar sus compras y acceder a sus descargas.
        */
        $productosComprados = $usuarioModel->obtenerRecursosAdquiridosUsuario($usuarioId);

        /*
            Cargamos los tickets de soporte del usuario.

            El modelo Soporte devuelve las consultas que el usuario ha
            enviado desde su panel personal.
        */
        $soporteModel = new Soporte();
        $ticketsSoporte = $soporteModel->obtenerTicketsUsuario($usuarioId);

        /*
            Cargamos la vista del perfil.

            Las variables creadas anteriormente quedan disponibles dentro
            de perfil_view.php:
            - $usuario
            - $favoritos
            - $productosComprados
            - $ticketsSoporte
        */
        require_once __DIR__ . '/../vistas/perfil_view.php';
    }
}
