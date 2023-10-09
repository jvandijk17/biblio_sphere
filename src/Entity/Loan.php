<?php

namespace App\Entity;

use App\Repository\LoanRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\BookNotRented;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LoanRepository::class)]
class Loan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("loan")]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "Loan Date cannot be null.")]
    #[Assert\NotBlank(message: "Loan Date cannot be blank.")]
    #[Groups("loan")]
    private ?\DateTimeInterface $loan_date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups("loan")]
    private ?\DateTimeInterface $return_date = null;

    #[ORM\ManyToOne(inversedBy: 'loans')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "User cannot be null, make sure that the provided user exists in the database.")]
    #[Assert\NotBlank(message: "User cannot be blank, make sure that the provided user exists in the database.")]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'loans')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Book cannot be null, make sure that the provided book exists in the database.")]
    #[Assert\NotBlank(message: "Book cannot be blank, make sure that the provided book exists in the database.")]
    #[BookNotRented]
    private ?Book $book = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    #[Groups("loan")]
    public function getBookId(): ?int
    {
        return $this->book->getId();
    }

    #[Groups("loan")]
    public function getUserId(): ?int
    {
        return $this->user->getId();
    }
}
