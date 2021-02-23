<?php

namespace App\Password;

interface PasswordGeneratorInterface
{
    const DEFAULT_LENGTH = 5;

    public function generate(int $length = self::DEFAULT_LENGTH): string;
}
