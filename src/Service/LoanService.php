<?php

namespace App\Service;

use App\Entity\Loan;
use App\Entity\User;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LoanService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;       
    }

    public function saveLoan(?Loan $loan, array $data): Loan
    {
        if(!$loan) {
            $loan = new Loan();
            $loan->setLoanDate(new \DateTime());
            $this->entityManager->persist($loan);
        }

        if(isset($data["user"])) {
            $loan->setUser($this->entityManager->getRepository(User::class)->find($data["user"]));
        }        
        if(isset($data["book"])) {
            $loan->setBook($this->entityManager->getRepository(Book::class)->find($data["book"]));
        }
        if(isset($data["return_date"])) {
            $loan->setReturnDate(new \DateTime($data["return_date"]));
        }        

        $errors = $this->validator->validate($loan);
        if(count($errors) > 0) {
            throw new \InvalidArgumentException('Invalid loan data: ' . (string) $errors);
        }

        $this->entityManager->flush();

        return $loan;

    }
}