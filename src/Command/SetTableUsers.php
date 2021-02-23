<?php

namespace App\Command;

use Webmozart\Assert\Assert;

final class SetTableUsers
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string[]
     */
    private $users;

    public function __construct(string $id, array $users)
    {
        Assert::uuid($id);
        Assert::allString($users);
        Assert::allEmail($users);

        $this->id = $id;
        $this->users = $users;
    }

    public function id(): string
    {
        return $this->id;
    }

    /** @return string[] */
    public function users(): array
    {
        return $this->users;
    }
}
