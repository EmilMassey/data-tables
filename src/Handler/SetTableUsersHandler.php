<?php

namespace App\Handler;

use App\Command\SetTableUsers;
use App\Repository\TableRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SetTableUsersHandler implements MessageHandlerInterface
{
    /**
     * @var TableRepositoryInterface
     */
    private $tableRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(
        TableRepositoryInterface $tableRepository,
        EntityManagerInterface $entityManager,
        UserRepositoryInterface $userRepository
    ) {
        $this->tableRepository = $tableRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function __invoke(SetTableUsers $command): void
    {
        if (null === $table = $this->tableRepository->get($command->id())) {
            throw new \InvalidArgumentException(\sprintf('Table %s does not exist', $command->id()));
        }

        $table->clearUsers();

        foreach ($command->users() as $user) {
            if (null === $user = $this->userRepository->get($user)) {
                throw new \InvalidArgumentException(\sprintf('User %s does not exist', $user));
            }

            $table->addUser($user);
        }

        $this->entityManager->persist($table);
    }
}
