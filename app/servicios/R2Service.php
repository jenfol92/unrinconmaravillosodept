<?php

/**
 * R2Service
 * ---------------------------------------------------------
 * Servicio encargado de gestionar archivos privados en Cloudflare R2.
 *
 * Cloudflare R2 es compatible con la API S3, por eso se utiliza
 * Aws\S3\S3Client para conectar con el bucket.
 *
 * Funcionalidades principales:
 *
 * - Conectar con Cloudflare R2.
 * - Subir archivos PDF o ZIP asociados a productos.
 * - Validar extensión, tamaño y tipo MIME del archivo.
 * - Eliminar archivos del bucket.
 * - Crear nombres de archivo seguros mediante slug.
 * - Generar URLs temporales firmadas para descarga privada.
 *
 * Este servicio se utiliza principalmente desde el panel administrador
 * y desde el sistema de descargas de recursos comprados.
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/r2.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class R2Service
{
    /**
     * Cliente S3 utilizado para comunicarse con Cloudflare R2.
     *
     * Aunque se use Aws\S3\S3Client, realmente se está conectando
     * con Cloudflare R2 porque R2 es compatible con la API S3.
     *
     * @var S3Client
     */
    private $s3;

    /**
     * Constructor del servicio.
     * ---------------------------------------------------------
     * Configura el cliente S3 con los datos definidos en config/r2.php.
     *
     * Constantes necesarias:
     *
     * - R2_ENDPOINT
     * - R2_ACCESS_KEY_ID
     * - R2_SECRET_ACCESS_KEY
     * - R2_BUCKET
     *
     * El archivo config/r2.php no debe subirse a GitHub si contiene
     * claves reales.
     */
    public function __construct()
    {
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region' => 'auto',
            'endpoint' => R2_ENDPOINT,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => R2_ACCESS_KEY_ID,
                'secret' => R2_SECRET_ACCESS_KEY,
            ],
        ]);
    }

    /**
     * Sube un archivo de producto a Cloudflare R2.
     * ---------------------------------------------------------
     * Esta función se utiliza para subir el archivo descargable
     * asociado a un producto de pago.
     *
     * Validaciones aplicadas:
     *
     * - Comprueba que exista archivo.
     * - Comprueba que no haya error en la subida.
     * - Permite solo extensiones PDF y ZIP.
     * - Limita el tamaño máximo a 50 MB.
     * - Comprueba el tipo MIME del archivo.
     *
     * Si todo es correcto:
     *
     * - Genera un nombre seguro para el archivo.
     * - Construye una key interna dentro del bucket.
     * - Sube el archivo a Cloudflare R2.
     * - Devuelve la key para guardarla en la base de datos.
     *
     * @param int $producto_id ID del producto al que pertenece el archivo.
     * @param array $archivo Archivo recibido desde $_FILES.
     * @param string $tituloProducto Título del producto usado para generar el nombre.
     *
     * @return string|null Key del archivo en R2 o null si no hay archivo.
     *
     * @throws Exception Si el archivo no es válido o falla la subida.
     */
    public function subirArchivoProducto($producto_id, $archivo, $tituloProducto)
    {
        /*
            Si no se ha seleccionado ningún archivo, devolvemos null.

            Esto permite que el controlador decida si continúa o no.
        */
        if (empty($archivo['name'])) {
            return null;
        }

        /*
            Comprobamos si PHP ha recibido correctamente el archivo.
        */
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al recibir el archivo. Código: ' . $archivo['error']);
        }

        /*
            Obtenemos la extensión original del archivo.
        */
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        /*
            Solo se permiten archivos PDF o ZIP porque la tienda vende
            recursos digitales descargables.
        */
        $extensionesPermitidas = ['pdf', 'zip'];

        if (!in_array($extension, $extensionesPermitidas, true)) {
            throw new Exception('Solo se permiten archivos PDF o ZIP.');
        }

        /*
            Tamaño máximo permitido: 50 MB.
        */
        $maxBytes = 50 * 1024 * 1024;

        if ($archivo['size'] > $maxBytes) {
            throw new Exception('El archivo supera el tamaño máximo permitido de 50 MB.');
        }

        /*
            Comprobamos el MIME real del archivo temporal.

            Esto añade una segunda validación además de la extensión.
        */
        $mime = mime_content_type($archivo['tmp_name']);

        $mimesPermitidos = [
            'application/pdf',
            'application/zip',
            'application/x-zip-compressed',
            'multipart/x-zip',
            'application/octet-stream'
        ];

        if (!in_array($mime, $mimesPermitidos, true)) {
            throw new Exception('El archivo no parece ser un PDF o ZIP válido.');
        }

        /*
            Creamos un nombre seguro a partir del título del producto.

            Ejemplo:
            "Tarjetas Mayor Menor e Igual" -> "tarjetas-mayor-menor-e-igual"
        */
        $slug = $this->crearSlug($tituloProducto);

        /*
            Añadimos time() para evitar nombres duplicados.
        */
        $nombreArchivo = $slug . '-' . time() . '.' . $extension;

        /*
            Key interna dentro del bucket.

            Esta ruta es la que se guarda después en productos.archivo_s3_key.
        */
        $key = 'recursos/productos/' . (int)$producto_id . '/' . $nombreArchivo;

        /*
            Definimos el Content-Type según la extensión.
        */
        $contentType = $extension === 'pdf'
            ? 'application/pdf'
            : 'application/zip';

        try {
            /*
                Subimos el archivo a Cloudflare R2.

                SourceFile apunta al archivo temporal recibido por PHP.
            */
            $this->s3->putObject([
                'Bucket' => R2_BUCKET,
                'Key' => $key,
                'SourceFile' => $archivo['tmp_name'],
                'ContentType' => $contentType,
            ]);

            /*
                Devolvemos la key para guardarla en la base de datos.
            */
            return $key;

        } catch (AwsException $e) {
            /*
                Si AWS SDK devuelve un error, lo convertimos en Exception
                normal para que el controlador pueda gestionarlo.
            */
            throw new Exception(
                'Error subiendo el archivo a Cloudflare R2: ' .
                ($e->getAwsErrorMessage() ?: $e->getMessage())
            );
        }
    }

    /**
     * Elimina un archivo de Cloudflare R2.
     * ---------------------------------------------------------
     * Se utiliza cuando:
     *
     * - Se sustituye el archivo de un producto.
     * - Se elimina un producto.
     * - Se desactiva un recurso con archivo asociado.
     *
     * @param string $key Ruta interna del archivo en R2.
     *
     * @return bool True si se elimina correctamente, false si falla.
     */
    public function eliminarArchivo($key)
    {
        /*
            Si no hay key, no hay nada que eliminar.
        */
        if (empty($key)) {
            return false;
        }

        try {
            /*
                Eliminamos el objeto del bucket.
            */
            $this->s3->deleteObject([
                'Bucket' => R2_BUCKET,
                'Key' => $key,
            ]);

            return true;

        } catch (AwsException $e) {
            /*
                Si falla el borrado, devolvemos false.

                No lanzamos excepción porque en algunos casos puede no interesar
                romper todo el flujo por un fallo de eliminación.
            */
            return false;
        }
    }

    /**
     * Crea un slug seguro a partir de un texto.
     * ---------------------------------------------------------
     * Convierte el título de un producto en una cadena válida
     * para usar como parte del nombre de archivo.
     *
     * Ejemplo:
     *
     * "Tarjetas de Lectura Fácil 2º" pasa a:
     * "tarjetas-de-lectura-facil-2"
     *
     * Pasos:
     *
     * - Convierte a minúsculas.
     * - Elimina tildes y caracteres especiales.
     * - Sustituye bloques no alfanuméricos por guiones.
     * - Elimina guiones sobrantes al inicio y final.
     *
     * @param string $texto Texto original.
     *
     * @return string Slug generado.
     */
    private function crearSlug($texto)
    {
        $texto = strtolower($texto);
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT', $texto);
        $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
        $texto = trim($texto, '-');

        return $texto ?: 'recurso';
    }

    /**
     * Genera una URL temporal firmada para descargar un archivo privado.
     * ---------------------------------------------------------
     * Esta función permite descargar un archivo almacenado en Cloudflare R2
     * sin hacerlo público permanentemente.
     *
     * Funcionamiento:
     *
     * - Recibe la key interna del archivo.
     * - Crea un comando GetObject.
     * - Genera una URL firmada con caducidad.
     * - Devuelve la URL para redirigir al usuario o usarla como enlace.
     *
     * Ventajas:
     *
     * - El bucket puede mantenerse privado.
     * - El enlace caduca tras unos minutos.
     * - El usuario solo puede descargar si previamente ha pasado
     *   las comprobaciones de compra, token y límites.
     *
     * @param string $key Ruta interna del archivo en R2.
     * @param int $minutos Tiempo de validez de la URL temporal.
     *
     * @return string URL temporal firmada.
     *
     * @throws Exception Si no hay key o si falla la generación de la URL.
     */
    public function generarUrlDescargaTemporal($key, $minutos = 10)
    {
        /*
            Validamos que exista una key.

            La key debe coincidir con el valor guardado en
            productos.archivo_s3_key.
        */
        if (empty($key)) {
            throw new Exception('No se ha indicado ningún archivo para descargar.');
        }

        /*
            Obtenemos el nombre final del archivo desde la key.
        */
        $nombreArchivo = basename($key);

        /*
            Detectamos la extensión para indicar el Content-Type correcto.
        */
        $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

        $contentType = match ($extension) {
            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
            default => 'application/octet-stream',
        };

        try {
            /*
                Creamos el comando GetObject.

                ResponseContentDisposition fuerza la descarga del archivo
                en lugar de abrirlo en el navegador.
            */
            $cmd = $this->s3->getCommand('GetObject', [
                'Bucket' => R2_BUCKET,
                'Key' => $key,

                /*
                    Fuerza descarga:
                    attachment; filename="archivo.pdf"
                */
                'ResponseContentDisposition' => 'attachment; filename="' . $nombreArchivo . '"',

                /*
                    Tipo de contenido devuelto al navegador.
                */
                'ResponseContentType' => $contentType,
            ]);

            /*
                Creamos la URL temporal firmada.

                Por defecto caduca en 10 minutos.
            */
            $request = $this->s3->createPresignedRequest(
                $cmd,
                '+' . (int)$minutos . ' minutes'
            );

            return (string)$request->getUri();

        } catch (AwsException $e) {
            throw new Exception(
                'Error generando URL temporal de descarga: ' .
                ($e->getAwsErrorMessage() ?: $e->getMessage())
            );
        }
    }
}