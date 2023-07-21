<?php

namespace App\Service;

use App\Entity\Loan;
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

    public function createLoan(array $data): Loan
    {
        $loan = new Loan;
        $loan->setLoanDate(new \DateTime());
        $loan->setUser($this->entityManager->getRepository(User::class)->find($data["user"]));
        $loan->setBook($this->entityManager->getRepository(Book::class)->find($data["book"]));

        $errors = $this->validator->validate($loan);
        if(count($errors) > 0) {
            throw new \InvalidArgumentException('Invalid loan data: ' . (string) $errors);
        }

        $this->entityManager->persist($loan);
        $this->entityManager->flush();

        return $loan;
    }

    public function updateLoan(Loan $loan, array $data): Loan
    {
        if(isset($data["user"])) {
            $loan->setUser($data["user"]);
        }
        if(isset($data["book"])) {
            $loan->setBook($data["book"]);
        }

        $errors = $this->validator->validate($loan);
        if(count($errors) > 0) {
            throw new \InvalidArgumentException((string) $errors);
        }

        $this->entityManager->flush();

        return $loan;
    }
}