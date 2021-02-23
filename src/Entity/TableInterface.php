<?php

namespace App\Entity;

interface TableInterface
{
    public function getId(): string;
    public function getName(): string;
    public function getFilePath(): string;
}
