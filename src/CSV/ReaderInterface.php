<?php

namespace App\CSV;

interface ReaderInterface
{
    public function read(string $filepath): ContentInterface;
}
