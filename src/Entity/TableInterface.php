<?php

namespace App\Entity;

interface TableInterface
{
    public function getId(): string;

    public function getName(): string;

    public function setName(string $name): void;

    public function getFilePath(): string;

    public function clearUsers(): void;

    public function addUser(UserInterface $user): void;

    /** @return UserInterface[] */
    public function getUsers(): array;
}
