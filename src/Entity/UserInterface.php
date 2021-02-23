<?php

namespace App\Entity;

interface UserInterface extends \Symfony\Component\Security\Core\User\UserInterface
{
    public function getEmail(): string;
    public function setPassword(string $password): void;
    public function getPassword(): ?string;
    public function isAdmin(): bool;
}
