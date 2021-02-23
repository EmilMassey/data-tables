<?php

namespace App\Repository;

use App\Entity\TableInterface;
use App\Entity\UserInterface;

interface TableRepositoryInterface
{
    public function get(string $id): ?TableInterface;

    /** @return TableInterface[] */
    public function getAll(): array;

    /** @return TableInterface[] */
    public function getAllByUser(UserInterface $user): array;
}
