<?php

namespace App\Handler;

use App\Command\CreateUser;
use App\Command\DeleteUser;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class DeleteUserHandler implements MessageHandlerInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        UserRepositoryInterface $userRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function __invoke(DeleteUser $command): void
    {
        if (null === $user = $this->userRepository->get($command->email())) {
            throw new \InvalidArgumentException(\sprintf('User %s does not exist', $command->email()));
        }

        $this->entityManager->remove($user);
    }
}
