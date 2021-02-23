<?php

namespace App\Handler;

use App\Command\CreateUser;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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

    public function __construct(
        UserRepositoryInterface $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function __invoke(CreateUser $command): void
    {
        if (null !== $this->userRepository->get($command->email())) {
            throw new \RuntimeException(\sprintf('User %s already exists', $command->email()));
        }

        $user = new User($command->email(), $command->admin());
        $user->setPassword($this->passwordEncoder->encodePassword($user, $command->plainPassword()));

        $this->entityManager->persist($user);
    }
}
