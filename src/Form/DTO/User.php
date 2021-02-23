<?php

namespace App\Form\DTO;

use App\Validator\UniqueUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueUser()
 */
final class User
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @var bool
     */
    public $admin = false;
}
