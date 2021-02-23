<?php

namespace App\Tests\Validator;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use App\Validator\UniqueUser;
use App\Validator\UniqueUserValidator;
use Monolog\Test\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UniqueUserValidatorTest extends TestCase
{
    /**
     * @var ExecutionContext
     */
    private $context;

    public function setUp(): void
    {
        $this->context = $this->createContext($this->getMockBuilder(ValidatorInterface::class)->getMock());
        $this->context->setConstraint(new UniqueUser());
    }

    public function test_accept_null_value()
    {
        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();

        $validator = new UniqueUserValidator($repository, new PropertyAccessor());
        $validator->validate(null, new UniqueUser());

        $this->assertSame(0, $this->context->getViolations()->count());
    }

    public function test_accept_empty_value()
    {
        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();

        $validator = new UniqueUserValidator($repository, new PropertyAccessor());
        $validator->validate('', new UniqueUser());

        $this->assertSame(0, $this->context->getViolations()->count());
    }

    public function test_throw_if_not_string_nor_object()
    {
        $this->expectException(UnexpectedValueException::class);

        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();

        $validator = new UniqueUserValidator($repository, new PropertyAccessor());
        $validator->validate(5, new UniqueUser());
    }

    public function test_throw_if_string_but_not_email()
    {
        $this->expectException(UnexpectedValueException::class);

        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();

        $validator = new UniqueUserValidator($repository, new PropertyAccessor());
        $validator->validate('dummy', new UniqueUser());
    }

    public function test_throw_if_email_property_not_accessible()
    {
        $this->expectException(\Exception::class);

        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();

        $validator = new UniqueUserValidator($repository, new PropertyAccessor());
        $validator->validate(new \stdClass(), new UniqueUser());
    }

    public function test_violate_on_email_string()
    {
        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $repository
            ->expects($this->once())
            ->method('get')
            ->with('email@example.com')
            ->willReturn(new User('email@example.com'));

        $validator = new UniqueUserValidator($repository, new PropertyAccessor());
        $validator->initialize($this->context);
        $validator->validate('email@example.com', new UniqueUser());

        $this->assertSame(1, $this->context->getViolations()->count());
        $this->assertSame(
            (new UniqueUser())->message,
            $this->context->getViolations()->get(0)->getMessageTemplate()
        );
    }

    public function test_no_violate_on_email_string()
    {
        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $repository
            ->expects($this->once())
            ->method('get')
            ->with('email@example.com')
            ->willReturn(null);

        $validator = new UniqueUserValidator($repository, new PropertyAccessor());
        $validator->initialize($this->context);
        $validator->validate('email@example.com', new UniqueUser());

        $this->assertSame(0, $this->context->getViolations()->count());
    }

    public function test_violate_on_public_property()
    {
        $object = new \stdClass();
        $object->email = 'email@example.com';

        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $repository
            ->expects($this->once())
            ->method('get')
            ->with('email@example.com')
            ->willReturn(new User('email@example.com'));

        $validator = new UniqueUserValidator($repository, new PropertyAccessor());
        $validator->initialize($this->context);
        $validator->validate($object, new UniqueUser());

        $this->assertSame(1, $this->context->getViolations()->count());
        $this->assertSame(
            (new UniqueUser())->message,
            $this->context->getViolations()->get(0)->getMessageTemplate()
        );
    }

    public function test_throw_when_private_property()
    {
        $object = new class {
            private $email = 'email@example.com';
        };

        $this->expectException(\Exception::class);

        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();

        $validator = new UniqueUserValidator($repository, new PropertyAccessor());
        $validator->initialize($this->context);
        $validator->validate($object, new UniqueUser());

        $this->assertSame(1, $this->context->getViolations()->count());
        $this->assertSame(
            (new UniqueUser())->message,
            $this->context->getViolations()->get(0)->getMessageTemplate()
        );
    }

    public function test_violate_on_getter()
    {
        $object = new class {
            public function getEmail(): string
            {
                return 'email@example.com';
            }
        };

        $repository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $repository
            ->expects($this->once())
            ->method('get')
            ->with('email@example.com')
            ->willReturn(new User('email@example.com'));

        $validator = new UniqueUserValidator($repository, new PropertyAccessor());
        $validator->initialize($this->context);
        $validator->validate($object, new UniqueUser());

        $this->assertSame(1, $this->context->getViolations()->count());
        $this->assertSame(
            (new UniqueUser())->message,
            $this->context->getViolations()->get(0)->getMessageTemplate()
        );
    }

    private function createContext(ValidatorInterface $validator, $root = 'root'): ExecutionContext
    {
        $translator = $this->getMockBuilder(TranslatorInterface::class)->getMock();
        $translator
            ->method('trans')
            ->willReturnArgument(0);

        return new ExecutionContext(
            $validator,
            $root,
            $translator
        );
    }
}
