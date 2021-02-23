<?php

namespace App\Command;

use Webmozart\Assert\Assert;

final class DeleteUser
{
    /**
     * @var string
     */
    private $email;

    public function __construct(string $email)
    {
        Assert::email($email);
        $this->email = $email;
    }

    public function email(): string
    {
        return $this->email;
    }
}
