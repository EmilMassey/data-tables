<?php


namespace App\Repository;


use App\Entity\Table;
use App\Entity\TableInterface;
use App\Entity\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class TableRepository implements TableRepositoryInterface
{
    /**
     * @var ObjectRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Table::class);
        $this->entityManager = $entityManager;
    }

    public function get(string $id): ?TableInterface
    {
        return $this->repository->find($id);
    }

    public function getAll(): array
    {
        return $this->repository->findBy([], ['name' => 'ASC']);
    }

    public function getAllByUser(UserInterface $user): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('table')
            ->from(Table::class, 'table')
            ->where(':user MEMBER OF table.users')
            ->orderBy('table.name')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
