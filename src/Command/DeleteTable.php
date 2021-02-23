<?php

namespace App\Command;

use Webmozart\Assert\Assert;

final class DeleteTable
{
    /**
     * @var string
     */
    private $id;

    public function __construct(string $id)
    {
        Assert::uuid($id);

        $this->id = $id;
    }

    public function id(): string
    {
        return $this->id;
    }
}
