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

    public function saveLoan(?Loan $loan, array $data, $isAdmin): Loan
    {
        $validationGroups = [];

        if (!$loan) {
            $loan = new Loan();
            $loan->setLoanDate(new \DateTime());
            $this->entityManager->persist($loan);
            $validationGroups[] = 'Create';
            if (!$isAdmin) {
                $loan->setStatus('pending');
            }
        }

        if (isset($data["user"])) {
            $loan->setUser($this->entityManager->getRepository(User::class)->find($data["user"]));
        }
        if (isset($data["book"])) {
            $loan->setBook($this->entityManager->getRepository(Book::class)->find($data["book"]));
        }
        if (isset($data["status"])) {
            $loan->setStatus($data["status"]);
        }
        if (isset($data["return_date"]) && $loan->getReturnDate() === null) {
            $loan->setReturnDate(new \DateTime($data["return_date"]));
        } else if (isset($data["return_date"])) {
            throw new \InvalidArgumentException('Loan already returned.');
        }

        $errors = $this->validator->validate($loan, null, $validationGroups);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException('Invalid loan data: ' . (string) $errors);
        }

        $this->entityManager->flush();

        return $loan;
    }
}
