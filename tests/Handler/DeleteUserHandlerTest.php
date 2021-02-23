<?php

namespace App\Tests\Handler;

use App\Command\DeleteUser;
use App\Entity\User;
use App\Handler\DeleteUserHandler;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class DeleteUserHandlerTest extends TestCase
{
    public function test_throws_if_user_does_not_exist()
    {
        $this->expectException(\InvalidArgumentException::class);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();

        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $repository
            ->expects($this->once())
            ->method('get')
            ->with('email@example.com')
            ->willReturn(null);

        $handler = new DeleteUserHandler($repository, $entityManager);

        $handler(new DeleteUser('email@example.com'));
    }

    public function test_removes()
    {
        $user = new User('email@empressia.pl');

        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $repository
            ->method('get')
            ->willReturn($user);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($user);

        $handler = new DeleteUserHandler($repository, $entityManager);

        $handler(new DeleteUser('email@example.com'));
    }
}
