<?php

namespace App\Tests\Handler;

use App\Command\SetTableUsers;
use App\Entity\Table;
use App\Entity\User;
use App\Event\UserEvent;
use App\Events;
use App\Handler\SetTableUsersHandler;
use App\Repository\TableRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SetTableUsersHandlerTest extends TestCase
{
    public function test_throws_if_table_does_not_exist()
    {
        $this->expectException(\InvalidArgumentException::class);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $tableRepository = $this->getMockBuilder(TableRepositoryInterface::class)->getMock();
        $tableRepository
            ->expects($this->once())
            ->method('get')
            ->with('b9df4794-268b-499c-9fea-b4e4f5bcb2ef')
            ->willReturn(null);

        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();

        $handler = new SetTableUsersHandler($tableRepository, $entityManager, $userRepository, $eventDispatcher);

        $handler(new SetTableUsers('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', ['email@example.com']));
    }

    public function test_throws_if_user_does_not_exist()
    {
        $this->expectException(\InvalidArgumentException::class);

        $table = new Table('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', 'test', 'test.csv');

        $tableRepository = $this->getMockBuilder(TableRepositoryInterface::class)->getMock();
        $tableRepository
            ->method('get')
            ->willReturn($table);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $userRepository
            ->expects($this->once())
            ->method('get')
            ->willReturn(null);

        $handler = new SetTableUsersHandler($tableRepository, $entityManager, $userRepository, $eventDispatcher);

        $handler(new SetTableUsers('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', ['email@example.com']));
    }

    public function test_persists()
    {
        $table = new Table('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', 'test', 'test.csv');
        $user = new User('email@example.com');

        $tableRepository = $this->getMockBuilder(TableRepositoryInterface::class)->getMock();
        $tableRepository
            ->method('get')
            ->willReturn($table);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($table);

        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $userRepository
            ->expects($this->once())
            ->method('get')
            ->willReturn($user);

        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $handler = new SetTableUsersHandler($tableRepository, $entityManager, $userRepository, $eventDispatcher);

        $handler(new SetTableUsers('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', ['email@example.com']));

        $this->assertSame([$user], $table->getUsers());
    }

    public function test_clears_previous()
    {
        $table = new Table('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', 'test', 'test.csv');
        $table->addUser(new User('email@example.com'));
        $user = new User('new@example.com');

        $tableRepository = $this->getMockBuilder(TableRepositoryInterface::class)->getMock();
        $tableRepository
            ->method('get')
            ->willReturn($table);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($table);

        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $userRepository
            ->expects($this->once())
            ->method('get')
            ->with('new@example.com')
            ->willReturn($user);

        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $handler = new SetTableUsersHandler($tableRepository, $entityManager, $userRepository, $eventDispatcher);

        $handler(new SetTableUsers('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', ['new@example.com']));

        $this->assertSame([$user], $table->getUsers());
    }

    public function test_dispatches_event()
    {
        $table = new Table('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', 'test', 'test.csv');
        $user = new User('email@example.com');

        $tableRepository = $this->getMockBuilder(TableRepositoryInterface::class)->getMock();
        $tableRepository
            ->method('get')
            ->willReturn($table);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($table);

        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $userRepository
            ->expects($this->once())
            ->method('get')
            ->willReturn($user);

        $usersGrantedAccess = [];
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(
            Events::USER_ACCESS_TO_TABLE_GRANTED,
            function(UserEvent $event) use (&$usersGrantedAccess) {
                $usersGrantedAccess[] = $event->user();
            }
        );

        $handler = new SetTableUsersHandler($tableRepository, $entityManager, $userRepository, $eventDispatcher);

        $handler(new SetTableUsers('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', ['email@example.com']));

        $this->assertSame([$user], $usersGrantedAccess);
    }

    public function test_not_dispatche_event_if_user_already_had_access()
    {
        $table = new Table('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', 'test', 'test.csv');
        $table->addUser(new User('old@example.com'));
        $user = new User('email@example.com');

        $tableRepository = $this->getMockBuilder(TableRepositoryInterface::class)->getMock();
        $tableRepository
            ->method('get')
            ->willReturn($table);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($table);

        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $userRepository
            ->expects($this->once())
            ->method('get')
            ->willReturn($user);

        $usersGrantedAccess = [];
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(
            Events::USER_ACCESS_TO_TABLE_GRANTED,
            function(UserEvent $event) use (&$usersGrantedAccess) {
                $usersGrantedAccess[] = $event->user();
            }
        );

        $handler = new SetTableUsersHandler($tableRepository, $entityManager, $userRepository, $eventDispatcher);

        $handler(new SetTableUsers('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', ['email@example.com']));

        $this->assertSame([$user], $usersGrantedAccess);
    }
}
