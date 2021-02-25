<?php

namespace App\Event;

use App\Entity\UserInterface;

class UserEvent
{
    /**
     * @var UserInterface
     */
    private $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function user(): UserInterface
    {
        return $this->user;
    }
}
