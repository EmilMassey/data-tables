<?php

namespace App\Command;

use Webmozart\Assert\Assert;

final class ChangeUserPassword
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $plainPassword;

    public function __construct(string $email, string $plainPassword)
    {
        Assert::email($email);
        Assert::minLength($plainPassword, 5);

        $this->email = $email;
        $this->plainPassword = $plainPassword;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function plainPassword(): string
    {
        return $this->plainPassword;
    }
}
