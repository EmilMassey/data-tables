<?php

namespace App\Handler;

use App\Command\SetTableUsers;
use App\Entity\UserInterface;
use App\Event\UserEvent;
use App\Events;
use App\Repository\TableRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

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
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var UserInterface[]
     */
    private $usersGrantedAccess = [];

    public function __construct(
        TableRepositoryInterface $tableRepository,
        EntityManagerInterface $entityManager,
        UserRepositoryInterface $userRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->tableRepository = $tableRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(SetTableUsers $command): void
    {
        if (null === $table = $this->tableRepository->get($command->id())) {
            throw new \InvalidArgumentException(\sprintf('Table %s does not exist', $command->id()));
        }

        $previousUsers = $table->getUsers();

        $table->clearUsers();

        foreach ($command->users() as $user) {
            if (null === $user = $this->userRepository->get($user)) {
                throw new \InvalidArgumentException(\sprintf('User %s does not exist', $user));
            }

            $table->addUser($user);

            if (!\in_array($user, $previousUsers, true)) {
                $this->usersGrantedAccess[] = $user;
            }
        }

        $this->entityManager->persist($table);

        $this->dispatchEvents();
    }

    private function dispatchEvents(): void
    {
        foreach ($this->usersGrantedAccess as $user) {
            $this->eventDispatcher->dispatch(new UserEvent($user), Events::USER_ACCESS_TO_TABLE_GRANTED);
        }
    }
}
