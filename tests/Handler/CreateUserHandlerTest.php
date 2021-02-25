<?php

namespace App\Tests\Handler;

use App\Command\CreateUser;
use App\Entity\User;
use App\Event\UserEvent;
use App\Events;
use App\Handler\CreateUserHandler;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CreateUserHandlerTest extends TestCase
{
    public function test_throws_if_user_exists()
    {
        $this->expectException(\RuntimeException::class);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $encoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->getMock();
        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $repository
            ->expects($this->once())
            ->method('get')
            ->with('email@example.com')
            ->willReturn(new User('email@example.com'));

        $handler = new CreateUserHandler($repository, $entityManager, $encoder, $eventDispatcher);

        $handler(new CreateUser('email@example.com', 'test123'));
    }

    public function test_encodes_password()
    {
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $repository
            ->method('get')
            ->willReturn(null);

        $encoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->getMock();
        $encoder
            ->expects($this->once())
            ->method('encodePassword')
            ->withAnyParameters(['test123'])
            ->willReturn('test123encoded');

        $handler = new CreateUserHandler($repository, $entityManager, $encoder, $eventDispatcher);

        $handler(new CreateUser('email@example.com', 'test123'));
    }

    public function test_persists()
    {
        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $repository
            ->method('get')
            ->willReturn(null);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $entityManager
            ->expects($this->once())
            ->method('persist');

        $encoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->getMock();
        $encoder
            ->method('encodePassword')
            ->willReturn('encoded');

        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $handler = new CreateUserHandler($repository, $entityManager, $encoder, $eventDispatcher);

        $handler(new CreateUser('email@example.com', 'test123'));
    }

    public function test_dispatches_event()
    {
        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $repository
            ->method('get')
            ->willReturn(null);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $entityManager
            ->expects($this->once())
            ->method('persist');

        $encoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->getMock();
        $encoder
            ->method('encodePassword')
            ->willReturn('encoded');

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(Events::USER_CREATED, function (UserEvent $event) {
            $this->assertSame('email@example.com', $event->user()->getEmail());
        });

        $handler = new CreateUserHandler($repository, $entityManager, $encoder, $eventDispatcher);

        $handler(new CreateUser('email@example.com', 'test123'));
    }
}
