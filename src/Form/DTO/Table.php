<?php

namespace App\Form\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

final class Table
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @var UploadedFile
     *
     * @Assert\File()
     */
    public $file;
}
