<?php

namespace App\Event;

use App\Entity\TableInterface;
use App\Entity\UserInterface;

final class UserHasBeenGrantedAccessToTableEvent extends UserEvent
{
    /**
     * @var TableInterface
     */
    private $table;

    public function __construct(UserInterface $user, TableInterface $table)
    {
        parent::__construct($user);
        $this->table = $table;
    }

    public function table(): TableInterface
    {
        return $this->table;
    }
}
