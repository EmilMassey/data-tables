<?php

namespace App\Handler;

use App\Command\ChangeUserPassword;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChangeUserPasswordHandler implements MessageHandlerInterface
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

    public function __invoke(ChangeUserPassword $command): void
    {
        if (null === $user = $this->userRepository->get($command->email())) {
            throw new \InvalidArgumentException(\sprintf('User %s does not exist', $command->email()));
        }

        $password = $this->passwordEncoder->encodePassword($user, $command->plainPassword());

        $user->setPassword($password);

        $this->entityManager->persist($user);
    }
}
