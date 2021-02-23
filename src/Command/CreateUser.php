<?php

namespace App\Command;

use Webmozart\Assert\Assert;

final class CreateUser
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $plainPassword;

    /**
     * @var bool
     */
    private $admin;

    public function __construct(string $email, string $plainPassword, bool $admin = false)
    {
        Assert::email($email);
        Assert::minLength($plainPassword, 5);

        $this->email = $email;
        $this->plainPassword = $plainPassword;
        $this->admin = $admin;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function plainPassword(): string
    {
        return $this->plainPassword;
    }

    public function admin(): bool
    {
        return $this->admin;
    }
}
