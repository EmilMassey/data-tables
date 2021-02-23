<?php

namespace App\Repository;

use App\Entity\TableInterface;

interface TableRepositoryInterface
{
    public function get(string $id): ?TableInterface;

    /** @return TableInterface[] */
    public function getAll(): array;
}
