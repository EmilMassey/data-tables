<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var ObjectRepository
     */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(User::class);
    }

    public function get(string $email): ?UserInterface
    {
        return $this->repository->find($email);
    }

    public function getAll(): array
    {
        return $this->repository->findBy([], ['admin' => 'DESC', 'email' => 'ASC']);
    }

    public function getAllNonAdmins(): array
    {
        return $this->repository->findBy(["admin" => false], ['email' => 'ASC']);
    }
}
