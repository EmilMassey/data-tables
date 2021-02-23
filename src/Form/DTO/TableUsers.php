<?php

namespace App\Form\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class TableUsers
{
    /**
     * @var bool
     */
    public $allUsers = true;

    /**
     * @var string[]
     *
     * @Assert\All(constraints={@Assert\Email()})
     */
    public $users;
}
