<?php

namespace App\Command;

use Webmozart\Assert\Assert;

final class CreateTable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $csvFilePath;

    public function __construct(string $id, string $name, string $csvFilePath)
    {
        Assert::uuid($id);
        Assert::minLength($name, 1);
        Assert::file($csvFilePath);

        $this->id = $id;
        $this->name = $name;
        $this->csvFilePath = $csvFilePath;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function csvFilePath(): string
    {
        return $this->csvFilePath;
    }
}
