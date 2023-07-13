<?php

namespace App\Entity;

use App\Repository\LoanRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoanRepository::class)]
class Loan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column]
    private ?int $book_id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $loan_date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $return_date = null;

    #[ORM\ManyToOne(inversedBy: 'loans')]
    private ?User $user_id_fk = null;

    #[ORM\ManyToOne(inversedBy: 'loans')]
    private ?Book $book_id_fk = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getBookId(): ?int
    {
        return $this->book_id;
    }

    public function setBookId(int $book_id): static
    {
        $this->book_id = $book_id;

        return $this;
    }

    public function getLoanDate(): ?\DateTimeInterface
    {
        return $this->loan_date;
    }

    public function setLoanDate(\DateTimeInterface $loan_date): static
    {
        $this->loan_date = $loan_date;

        return $this;
    }

    public function getReturnDate(): ?\DateTimeInterface
    {
        return $this->return_date;
    }

    public function setReturnDate(?\DateTimeInterface $return_date): static
    {
        $this->return_date = $return_date;

        return $this;
    }

    public function getUserIdFk(): ?User
    {
        return $this->user_id_fk;
    }

    public function setUserIdFk(?User $user_id_fk): static
    {
        $this->user_id_fk = $user_id_fk;

        return $this;
    }

    public function getBookIdFk(): ?Book
    {
        return $this->book_id_fk;
    }

    public function setBookIdFk(?Book $book_id_fk): static
    {
        $this->book_id_fk = $book_id_fk;

        return $this;
    }
}
