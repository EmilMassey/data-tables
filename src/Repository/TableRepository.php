<?php


namespace App\Repository;


use App\Entity\Table;
use App\Entity\TableInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class TableRepository implements TableRepositoryInterface
{
    /**
     * @var ObjectRepository
     */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Table::class);
    }

    public function get(string $id): ?TableInterface
    {
        return $this->repository->find($id);
    }

    public function getAll(): array
    {
        return $this->repository->findBy([], ['name' => 'ASC']);
    }
}
