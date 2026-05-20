<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/r2.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

try {
    $s3 = new S3Client([
        'version' => 'latest',
        'region' => 'auto',
        'endpoint' => R2_ENDPOINT,
        'use_path_style_endpoint' => true,
        'credentials' => [
            'key' => R2_ACCESS_KEY_ID,
            'secret' => R2_SECRET_ACCESS_KEY,
        ],
    ]);

    $result = $s3->listObjectsV2([
        'Bucket' => R2_BUCKET,
        'MaxKeys' => 10,
    ]);

    echo '<h1>Conexión correcta con Cloudflare R2</h1>';

    echo '<pre>';
    print_r($result['Contents'] ?? []);
    echo '</pre>';

} catch (AwsException $e) {
    echo '<h1>Error conectando con R2</h1>';
    echo '<pre>';
    echo 'AWS Error Code: ' . htmlspecialchars($e->getAwsErrorCode() ?? '') . PHP_EOL;
    echo 'AWS Error Message: ' . htmlspecialchars($e->getAwsErrorMessage() ?: $e->getMessage()) . PHP_EOL;
    echo '</pre>';

} catch (Exception $e) {
    echo '<h1>Error general</h1>';
    echo '<pre>';
    echo htmlspecialchars($e->getMessage());
    echo '</pre>';
}