<?php

namespace App\Handler;

use App\Command\CreateTable;
use App\Entity\Table;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateTableHandler implements MessageHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(CreateTable $command): void
    {
        $table = new Table($command->id(), $command->name(), $command->csvFilePath());

        $this->entityManager->persist($table);
    }
}
