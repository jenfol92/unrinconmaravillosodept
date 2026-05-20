<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/r2.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class R2Service
{
    private $s3;

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

    public function subirArchivoProducto($producto_id, $archivo, $tituloProducto)
    {
        if (empty($archivo['name'])) {
            return null;
        }

        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al recibir el archivo. Código: ' . $archivo['error']);
        }

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        $extensionesPermitidas = ['pdf', 'zip'];

        if (!in_array($extension, $extensionesPermitidas, true)) {
            throw new Exception('Solo se permiten archivos PDF o ZIP.');
        }

        $maxBytes = 50 * 1024 * 1024; // 50 MB

        if ($archivo['size'] > $maxBytes) {
            throw new Exception('El archivo supera el tamaño máximo permitido de 50 MB.');
        }

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

        $slug = $this->crearSlug($tituloProducto);
        $nombreArchivo = $slug . '-' . time() . '.' . $extension;

        $key = 'recursos/productos/' . (int)$producto_id . '/' . $nombreArchivo;

        $contentType = $extension === 'pdf'
            ? 'application/pdf'
            : 'application/zip';

        try {
            $this->s3->putObject([
                'Bucket' => R2_BUCKET,
                'Key' => $key,
                'SourceFile' => $archivo['tmp_name'],
                'ContentType' => $contentType,
            ]);

            return $key;

        } catch (AwsException $e) {
            throw new Exception(
                'Error subiendo el archivo a Cloudflare R2: ' .
                ($e->getAwsErrorMessage() ?: $e->getMessage())
            );
        }
    }

    public function eliminarArchivo($key)
    {
        if (empty($key)) {
            return false;
        }

        try {
            $this->s3->deleteObject([
                'Bucket' => R2_BUCKET,
                'Key' => $key,
            ]);

            return true;

        } catch (AwsException $e) {
            return false;
        }
    }

    private function crearSlug($texto)
    {
        $texto = strtolower($texto);
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT', $texto);
        $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
        $texto = trim($texto, '-');

        return $texto ?: 'recurso';
    }
   public function generarUrlDescargaTemporal($key, $minutos = 10)
{
    /*
        Genera una URL temporal firmada para descargar un archivo privado
        almacenado en Cloudflare R2.

        $key debe ser la ruta interna guardada en productos.archivo_s3_key.

        Ejemplo:
        recursos/productos/65/tarjetas-mayor-menor-e-igual-2o-1779111817.pdf
    */

    if (empty($key)) {
        throw new Exception('No se ha indicado ningún archivo para descargar.');
    }

    $nombreArchivo = basename($key);

    $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

    $contentType = match ($extension) {
        'pdf' => 'application/pdf',
        'zip' => 'application/zip',
        default => 'application/octet-stream',
    };

    try {
        $cmd = $this->s3->getCommand('GetObject', [
            'Bucket' => R2_BUCKET,
            'Key' => $key,

            /*
                Fuerza la descarga del archivo en vez de abrirlo en el navegador.
            */
            'ResponseContentDisposition' => 'attachment; filename="' . $nombreArchivo . '"',
            'ResponseContentType' => $contentType,
        ]);

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