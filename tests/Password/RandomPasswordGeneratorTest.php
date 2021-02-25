<?php

namespace App\Tests\Password;

use App\Password\RandomPasswordGenerator;
use PHPUnit\Framework\TestCase;

class RandomPasswordGeneratorTest extends TestCase
{
    public function test_it_generates_correct_length()
    {
        $generator = new RandomPasswordGenerator();
        $length = 10;

        for ($i = 0; $i < 100; ++$i) {
            $password = $generator->generate($length);
            $this->assertSame($length, \strlen($password));
        }
    }
}
