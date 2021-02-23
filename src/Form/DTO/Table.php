<?php

namespace App\Form\DTO;

use App\Entity\TableInterface;
use App\Entity\UserInterface;
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

    /**
     * @var TableUsers
     *
     * @Assert\Valid()
     */
    public $users;

    public static function createFromEntity(TableInterface $table): self
    {
        $self = new self;
        $self->name = $table->getName();
        $self->users = new TableUsers();

        $self->users->users = \array_map(function (UserInterface $user) {
            return $user->getEmail();
        }, $table->getUsers());

        return $self;
    }
}
