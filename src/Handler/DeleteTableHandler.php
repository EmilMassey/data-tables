<?php

namespace App\Handler;

use App\Command\DeleteTable;
use App\Repository\TableRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DeleteTableHandler implements MessageHandlerInterface
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

    public function __invoke(DeleteTable $command): void
    {
        if (null === $table = $this->repository->get($command->id())) {
            throw new \InvalidArgumentException(\sprintf('Table %s does not exist', $command->id()));
        }

        $this->entityManager->remove($table);

        @unlink($table->getFilePath());
    }
}
