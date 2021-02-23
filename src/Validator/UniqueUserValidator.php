<?php

namespace App\Validator;

use App\Repository\UserRepositoryInterface;
use Symfony\Component\Form\Exception\AccessException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * @Annotation
 */
class UniqueUserValidator extends ConstraintValidator
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(UserRepositoryInterface $repository, PropertyAccessorInterface $propertyAccessor)
    {
        $this->repository = $repository;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueUser) {
            throw new UnexpectedTypeException($constraint, UniqueUser::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (\is_string($value) && \filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $email = $value;
        } elseif (\is_object($value)) {
            $email = $this->propertyAccessor->getValue($value, 'email');
        } else {
            throw new UnexpectedValueException($value, 'string|object');
        }

        if (null !== $this->repository->get($email)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ email }}', $email)
                ->addViolation();
        }
    }
}
