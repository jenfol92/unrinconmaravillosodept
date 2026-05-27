<?php

/**
 * GoogleDriveService
 * ---------------------------------------------------------
 * Servicio encargado de subir archivos a Google Drive usando
 * una cuenta central autorizada previamente mediante OAuth.
 *
 * Requisitos:
 * - vendor/autoload.php
 * - config/oauth.php
 * - GOOGLE_CLIENT_ID
 * - GOOGLE_CLIENT_SECRET
 * - GOOGLE_DRIVE_ADMIN_REFRESH_TOKEN
 * - Opcional: GOOGLE_DRIVE_FOLDER_ID
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/oauth.php';

class GoogleDriveService
{
    /**
     * Cliente Google.
     *
     * @var Google\Client
     */
    private $client;

    /**
     * Servicio Drive.
     *
     * @var Google\Service\Drive
     */
    private $drive;

    /**
     * Constructor.
     * ---------------------------------------------------------
     * Configura el cliente con el refresh_token de la cuenta
     * central de Drive.
     */
    public function __construct()
    {
        $this->client = new Google\Client();

        $this->client->setClientId(GOOGLE_CLIENT_ID);
        $this->client->setClientSecret(GOOGLE_CLIENT_SECRET);

        $this->client->addScope(Google\Service\Drive::DRIVE_FILE);
        $this->client->setAccessType('offline');

        $this->client->fetchAccessTokenWithRefreshToken(
            GOOGLE_DRIVE_ADMIN_REFRESH_TOKEN
        );

        $this->drive = new Google\Service\Drive($this->client);
    }

    /**
     * Sube un archivo a Google Drive.
     * ---------------------------------------------------------
     * Recibe la ruta temporal del archivo subido mediante formulario
     * y lo envía a Google Drive.
     *
     * @param string $rutaTemporal Ruta temporal del archivo en PHP.
     * @param string $nombreArchivo Nombre final del archivo.
     * @param string|null $mimeType Tipo MIME del archivo.
     *
     * @return array Datos del archivo subido.
     */
    public function subirArchivo($rutaTemporal, $nombreArchivo, $mimeType = null)
    {
        if (!is_file($rutaTemporal)) {
            throw new Exception('El archivo temporal no existe.');
        }

        $metadata = [
            'name' => $nombreArchivo
        ];

        /**
         * Si tienes una carpeta concreta de Drive, se sube ahí.
         */
        if (defined('GOOGLE_DRIVE_FOLDER_ID') && GOOGLE_DRIVE_FOLDER_ID !== '') {
            $metadata['parents'] = [GOOGLE_DRIVE_FOLDER_ID];
        }

        $fileMetadata = new Google\Service\Drive\DriveFile($metadata);

        $contenido = file_get_contents($rutaTemporal);

        $archivo = $this->drive->files->create($fileMetadata, [
            'data' => $contenido,
            'mimeType' => $mimeType ?: 'application/octet-stream',
            'uploadType' => 'multipart',
            'fields' => 'id,name,webViewLink,webContentLink'
        ]);

        /**
         * Opcional:
         * Hacer el archivo accesible para cualquiera con enlace.
         * Si no haces esto, solo podrá verlo la cuenta propietaria.
         */
        $this->hacerPublicoConEnlace($archivo->id);

        /**
         * Volvemos a pedir el archivo con enlaces actualizados.
         */
        $archivo = $this->drive->files->get($archivo->id, [
            'fields' => 'id,name,webViewLink,webContentLink'
        ]);

        return [
            'id' => $archivo->id,
            'name' => $archivo->name,
            'webViewLink' => $archivo->webViewLink,
            'webContentLink' => $archivo->webContentLink
        ];
    }
    /**
 * Elimina un archivo de Google Drive.
 * ---------------------------------------------------------
 * Recibe el ID interno del archivo de Google Drive.
 *
 * Importante:
 * No se debe pasar la URL completa de Drive.
 * Se debe pasar el file_id guardado en base de datos.
 *
 * Ejemplo válido:
 * 1cW9gHQamj1PGz8XsFcihm_ZDFwc6gDMq
 *
 * @param string|null $fileId ID del archivo en Google Drive.
 *
 * @return bool
 */
public function eliminarArchivo($fileId)
{
    if (empty($fileId)) {
        return false;
    }

    $this->drive->files->delete($fileId);

    return true;
}

    /**
     * Hace que un archivo sea accesible para cualquiera con el enlace.
     * ---------------------------------------------------------
     * Esto es útil para recursos gratuitos o enlaces de vista.
     *
     * @param string $fileId ID del archivo en Google Drive.
     *
     * @return void
     */
    private function hacerPublicoConEnlace($fileId)
    {
        $permission = new Google\Service\Drive\Permission([
            'type' => 'anyone',
            'role' => 'reader'
        ]);

        $this->drive->permissions->create($fileId, $permission);
    }
}