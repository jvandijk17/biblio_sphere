<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY)]
class BookNotRented extends Constraint
{
    public $message = 'The book {{ bookId }} is already rented out and not returned.';
}
