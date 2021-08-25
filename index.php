<?php

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\FileAttributes;
use Pollen\Filesystem\StorageManager;
use Symfony\Component\Yaml\Yaml;

/** Enable debugging */
error_reporting(E_ALL);
ini_set('display_errors', 1);
/**/

require_once __DIR__ . '/vendor/autoload.php';

/** MINIO CONFIG */
$docker = Yaml::parseFile(__DIR__ . '/docker-compose.yaml');
$expose = $docker['services']['minio']['ports'][0];
$endpoint = $expose ? substr($expose, 0, -5) : null;
$user = $docker['services']['minio']['environment']['MINIO_ROOT_USER'] ?? null;
$password = $docker['services']['minio']['environment']['MINIO_ROOT_PASSWORD']  ?? null;

if (!$endpoint || !$user || !$password) {
    throw new RuntimeException('Unable to parse configuration from docker-compose file, try manually.');
}

$storage = new StorageManager();

$disk = $storage->registerS3Disk(
    'kittens-disk',
    [
        'version'     => 'latest',
        'region'      => 'minio',
        'endpoint'    => $endpoint,
        'credentials' => [
            'key'    => $user,
            'secret' => $password,
        ]
    ],
    'kittens-bucket'
);


if ($disk = $storage->disk('kittens-disk')) {
    try {
        if (!$disk->fileExists('sweet-kitten-in-bucket.jpg')) {
            $disk->write(
                'sweet-kitten-in-bucket.jpg',
                file_get_contents(__DIR__ . '/var/resources/sweet-kitten.jpg')
            );
        }
    } catch (FilesystemException|UnableToReadFile $e) {
        var_dump($e->getMessage());
    }

    try {
        $listing = $disk->listContents('/', true);

        /** @var \League\Flysystem\StorageAttributes $item */
        foreach ($listing as $item) {
            $path = $item->path();

            if ($item instanceof FileAttributes) {
                echo "<h2>This file is served with Minio !</h2>";
                echo "<img src='http://$endpoint/kittens-bucket/$path' style='max-width:100%;height:auto;max-height:100%;'/>";
            }
        }
    } catch (FilesystemException $e) {
        var_dump($e->getMessage());
    }
}