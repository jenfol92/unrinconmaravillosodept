<?php

/**
 * Controlador FavoritoController
 * ---------------------------------------------------------
 * Controlador encargado de gestionar acciones AJAX relacionadas
 * con favoritos de productos.
 *
 * Este controlador NO carga vistas.
 * Sus métodos devuelven respuestas JSON porque son llamados
 * desde JavaScript mediante fetch/AJAX.
 *
 * Responsabilidades:
 * - Comprobar que el usuario esté logueado.
 * - Validar el producto recibido.
 * - Llamar al modelo Producto.
 * - Devolver respuesta JSON al frontend.
 *
 * Modelo usado:
 * - Producto:
 *   Contiene el método toggleFavorito(), que añade o elimina
 *   un producto de favoritos según el estado actual.
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../modelos/Producto.php';

class FavoritoController
{
    /**
     * Alterna el estado de favorito de un producto.
     * ---------------------------------------------------------
     * Si el producto no está en favoritos, lo añade.
     * Si ya está en favoritos, lo elimina.
     *
     * Archivo público que llama a este método:
     * - public/ajax_toggle_favorito.php
     *   o el nombre real que ya esté usando tu JavaScript.
     *
     * Entrada esperada por POST:
     * - producto_id: ID del producto que se quiere añadir o quitar
     *   de favoritos.
     *
     * Respuesta correcta:
     * {
     *   "ok": true,
     *   "estado": "guardado"
     * }
     *
     * o:
     *
     * {
     *   "ok": true,
     *   "estado": "eliminado"
     * }
     *
     * @return void
     */
    public function toggleFavoritoAjax()
    {
        /**
         * Indicamos que la respuesta será JSON.
         */
        header('Content-Type: application/json; charset=utf-8');

        /**
         * Comprobamos que el usuario esté logueado.
         * -----------------------------------------------------
         * Esta acción no es de administrador, pero sí requiere
         * sesión, porque los favoritos se asocian a un usuario.
         */
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode([
                'ok' => false,
                'error' => 'No autorizado'
            ]);
            exit;
        }

        /**
         * Recogemos y normalizamos el ID del producto.
         * -----------------------------------------------------
         * Convertimos a entero para evitar pasar valores no válidos
         * al modelo.
         */
        $producto_id = isset($_POST['producto_id'])
            ? (int) $_POST['producto_id']
            : 0;

        /**
         * Validamos que el producto sea válido.
         */
        if ($producto_id <= 0) {
            echo json_encode([
                'ok' => false,
                'error' => 'Producto no válido'
            ]);
            exit;
        }

        try {
            /**
             * Instanciamos el modelo Producto.
             */
            $model = new Producto();

            /**
             * Alternamos favorito.
             * -------------------------------------------------
             * El método toggleFavorito() debe encargarse de:
             * - comprobar si ya existe favorito;
             * - añadirlo si no existe;
             * - eliminarlo si ya existe;
             * - devolver el estado resultante.
             *
             * Normalmente devuelve:
             * - guardado
             * - eliminado
             */
            $resultado = $model->toggleFavorito(
                $_SESSION['usuario_id'],
                $producto_id
            );

            /**
             * Respuesta correcta.
             */
            echo json_encode([
                'ok' => true,
                'estado' => $resultado
            ]);
            exit;

        } catch (Exception $e) {
            /**
             * Respuesta en caso de error.
             * -------------------------------------------------
             * En producción conviene no mostrar directamente
             * $e->getMessage() para no exponer detalles internos.
             */
            echo json_encode([
                'ok' => false,
                'error' => 'Error actualizando favoritos.'
            ]);
            exit;
        }
    }
}