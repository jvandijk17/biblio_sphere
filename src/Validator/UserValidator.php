<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\User $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
