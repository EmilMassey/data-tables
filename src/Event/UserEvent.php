<?php

namespace App\Event;

use App\Entity\UserInterface;

class UserEvent
{
    /**
     * @var UserInterface
     */
    protected $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function user(): UserInterface
    {
        return $this->user;
    }
}
