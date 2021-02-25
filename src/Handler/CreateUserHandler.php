<?php

namespace App\Handler;

use App\Command\CreateUser;
use App\Entity\User;
use App\Event\UserEvent;
use App\Events;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CreateUserHandler implements MessageHandlerInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        UserRepositoryInterface $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(CreateUser $command): void
    {
        if (null !== $this->userRepository->get($command->email())) {
            throw new \RuntimeException(\sprintf('User %s already exists', $command->email()));
        }

        $user = new User($command->email(), $command->admin());
        $user->setPassword($this->passwordEncoder->encodePassword($user, $command->plainPassword()));

        $this->entityManager->persist($user);

        $this->eventDispatcher->dispatch(new UserEvent($user), Events::USER_CREATED);
    }
}
