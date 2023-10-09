<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class BookNotRented extends Constraint
{
    public $message = 'The book {{ bookId }} is already rented out and not returned.';
}
