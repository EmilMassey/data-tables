<?php

namespace App\Password;

use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;

final class RandomPasswordGenerator implements PasswordGeneratorInterface
{
    public function generate(int $length = PasswordGeneratorInterface::DEFAULT_LENGTH): string
    {
        $generator = new ComputerPasswordGenerator();

        $generator
            ->setUppercase()
            ->setLowercase()
            ->setNumbers()
            ->setSymbols(false)
            ->setLength($length);

        return $generator->generatePassword();
    }
}
