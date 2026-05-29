<?php

/**
 * RecursoGratuitoController
 * ---------------------------------------------------------
 * Controlador encargado de gestionar las acciones públicas
 * relacionadas con los recursos gratuitos.
 *
 * Responsabilidades:
 * - Filtrar recursos gratuitos por categoría.
 * - Filtrar recursos gratuitos por búsqueda de texto.
 * - Devolver los resultados en formato JSON para JavaScript.
 *
 * Este controlador NO pertenece al panel de administración.
 * Es parte de la zona pública de la web.
 */
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../modelos/recursosGratuitos.php';

class RecursoGratuitoController
{
    /**
     * Modelo de recursos gratuitos.
     *
     * Se usa para consultar los recursos gratuitos en la base de datos.
     */
    private $recursoGratuitoModel;

    /**
     * Constructor del controlador.
     * ---------------------------------------------------------
     * Inicializa el modelo RecursoGratuito.
     */
    public function __construct()
    {
        $this->recursoGratuitoModel = new RecursoGratuito();
    }

    /**
     * Filtra recursos gratuitos mediante AJAX.
     * ---------------------------------------------------------
     * Este método se ejecuta cuando JavaScript solicita recursos
     * gratuitos filtrados sin recargar la página.
     *
     * Recibe por GET:
     * - categorias: listado JSON de categorías seleccionadas.
     * - busqueda: texto escrito por el usuario en el buscador.
     *
     * Devuelve JSON con:
     * - ok
     * - recursos
     * - total
     *
     * @return void
     */
    public function filtrarRecursosGratuitosAjax()
    {
        /*
            Indicamos que la respuesta será JSON.

            Esto es importante porque este método será llamado
            desde JavaScript usando fetch() o AJAX.
        */
        header('Content-Type: application/json; charset=utf-8');

        /*
            Recogemos los filtros recibidos por GET.

            categorias puede venir como JSON.
            busqueda será un texto normal.
        */
        $categoriasJson = $_GET['categorias'] ?? '';
        $busqueda = trim($_GET['busqueda'] ?? '');

        /*
            Convertimos las categorías desde JSON a array PHP.

            Si no llega nada, usamos array vacío.
        */
        $categorias = $categoriasJson
            ? json_decode($categoriasJson, true)
            : [];

        /*
            Si json_decode falla o no devuelve un array,
            dejamos categorías como array vacío para evitar errores.
        */
        if (!is_array($categorias)) {
            $categorias = [];
        }

        try {
            /*
                Obtenemos los recursos gratuitos desde el modelo.

                El modelo se encarga de hacer la consulta real
                a la base de datos.
            */
            $recursos = $this->recursoGratuitoModel->obtenerRecursosGratuitos(
                $categorias,
                $busqueda
            );

            /*
                Devolvemos una respuesta correcta.
            */
            echo json_encode([
                'ok' => true,
                'recursos' => $recursos,
                'total' => count($recursos)
            ], JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Exception $e) {

            /*
                Si ocurre un error, devolvemos una respuesta controlada.

                En producción no conviene mostrar el error técnico real
                al usuario final.
            */
            echo json_encode([
                'ok' => false,
                'error' => 'No se pudieron cargar los recursos gratuitos.',
                'recursos' => [],
                'total' => 0
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    /**
     * Redirige a la URL externa de un recurso gratuito.
     * ---------------------------------------------------------
     * Se usa cuando el usuario pulsa en un recurso gratuito.
     *
     * Flujo:
     * 1. Recoge el ID por GET.
     * 2. Valida que el recurso exista.
     * 3. Incrementa el contador de clicks.
     * 4. Redirige a la URL de Drive.
     *
     * @return void
     */
    public function redirigirRecursoGratuito()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id <= 0) {
            header('Location: ' . PUBLIC_URL . 'recursos_gratuitos.php');
            exit;
        }

        $recurso = $this->recursoGratuitoModel->obtenerRecursoPorId($id);

        if (!$recurso) {
            header('Location: ' . PUBLIC_URL . 'recursos_gratuitos.php');
            exit;
        }

        $this->recursoGratuitoModel->incrementarClicks($id);

        header('Location: ' . $recurso['url_drive']);
        exit;
    }
}
