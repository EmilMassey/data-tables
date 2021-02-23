<?php

namespace App\Handler;

use App\Command\ChangeTableName;
use App\Repository\TableRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ChangeTableNameHandler implements MessageHandlerInterface
{
    /**
     * @var TableRepositoryInterface
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(TableRepositoryInterface $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function __invoke(ChangeTableName $command): void
    {
        if (null === $table = $this->repository->get($command->id())) {
            throw new \InvalidArgumentException(\sprintf('Table %s does not exist', $command->id()));
        }

        $table->setName($command->name());
        $this->entityManager->persist($table);
    }
}
