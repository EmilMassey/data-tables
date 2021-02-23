<?php

namespace App\Uploader;

use Symfony\Component\HttpFoundation\File\File;

interface FileUploaderInterface
{
    /**
     * @return string Uploaded file path
     */
    public function upload(File $file): string;
}
