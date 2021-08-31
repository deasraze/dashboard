<?php

declare(strict_types=1);

namespace App\Service\Uploader;

use League\Flysystem\FilesystemOperator;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private FilesystemOperator $storage;
    private string $baseUrl;

    public function __construct(FilesystemOperator $storage, string $baseUrl)
    {
        $this->storage = $storage;
        $this->baseUrl = $baseUrl;
    }

    public function upload(UploadedFile $file): File
    {
        $path = \date('Y/m/d');
        $name = Uuid::uuid4()->toString().'.'.$file->getClientOriginalExtension();

        $stream = \fopen($file->getRealPath(), 'r+b');

        try {
            $this->storage->writeStream($path.'/'.$name, $stream);
        } finally {
            \fclose($stream);
        }

        return new File($path, $name, $file->getSize());
    }

    public function generateUrl(string $path): string
    {
        return $this->baseUrl.'/'.$path;
    }
}
