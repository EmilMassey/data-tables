<?php

namespace App\Tests\Handler;

use App\Command\ChangeUserPassword;
use App\Entity\User;
use App\Handler\ChangeUserPasswordHandler;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChangeUserPasswordHandlerTest extends TestCase
{
    public function test_throws_if_user_does_not_exist()
    {
        $this->expectException(\InvalidArgumentException::class);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $encoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->getMock();

        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $repository
            ->expects($this->once())
            ->method('get')
            ->with('email@example.com')
            ->willReturn(null);

        $handler = new ChangeUserPasswordHandler($repository, $entityManager, $encoder);

        $handler(new ChangeUserPassword('email@example.com', 'test123'));

    }

    public function test_encodes_password()
    {
        $user = new User('email@empressia.pl');

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $repository
            ->method('get')
            ->willReturn($user);

        $encoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->getMock();
        $encoder
            ->expects($this->once())
            ->method('encodePassword')
            ->withAnyParameters(['newpassword'])
            ->willReturn('encoded');

        $handler = new ChangeUserPasswordHandler($repository, $entityManager, $encoder);

        $handler(new ChangeUserPassword('email@example.com', 'newpassword'));

        $this->assertSame('encoded', $user->getPassword());
    }

    public function test_persists()
    {
        $user = new User('email@empressia.pl');

        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $repository
            ->method('get')
            ->willReturn($user);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($user);

        $encoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->getMock();
        $encoder
            ->method('encodePassword')
            ->willReturn('encoded');

        $handler = new ChangeUserPasswordHandler($repository, $entityManager, $encoder);

        $handler(new ChangeUserPassword('email@example.com', 'test123'));
    }
}
