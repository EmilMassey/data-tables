<?php

namespace App\Uploader;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Webmozart\Assert\Assert;

class TableUploader implements FileUploaderInterface
{
    /**
     * @var string
     */
    private $uploadDirectory;

    /**
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * @var string
     */
    private $filename;

    public function __construct(string $uploadDirectory, SluggerInterface $slugger)
    {
        Assert::directory($uploadDirectory);
        Assert::writable($uploadDirectory);
        $this->uploadDirectory = $uploadDirectory;
        $this->slugger = $slugger;
    }

    public function upload(File $file): string
    {
        if (null === $this->filename) {
            if ($file instanceof UploadedFile) {
                $this->filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            } else {
                $this->filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            }
        }

        $path = $this->slugger->slug($this->filename).'-'.uniqid().'.csv';

        $file->move($this->uploadDirectory, $path);

        return $this->uploadDirectory . '/' . $path;
    }

    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }
}
