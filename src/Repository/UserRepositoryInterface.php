<?php

namespace App\Repository;

use App\Entity\UserInterface;

interface UserRepositoryInterface
{
    public function get(string $email): ?UserInterface;

    /** @return UserInterface[] */
    public function getAll(): array;
}
