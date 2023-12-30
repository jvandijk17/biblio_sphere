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

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups("loan")]
    private ?\DateTimeInterface $estimated_return_date = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotNull(message: "Loan Status cannot be null.")]
    #[Assert\NotBlank(message: "Loan Status cannot be blank.")]
    #[Assert\Choice(choices: ['pending', 'accepted', 'returned'], message: 'Invalid loan status.')]
    #[Groups("loan")]
    private ?string $status = 'pending';

    #[ORM\ManyToOne(inversedBy: 'loans')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "User cannot be null, make sure that the provided user exists in the database.")]
    #[Assert\NotBlank(message: "User cannot be blank, make sure that the provided user exists in the database.")]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'loans')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Book cannot be null, make sure that the provided book exists in the database.")]
    #[Assert\NotBlank(message: "Book cannot be blank, make sure that the provided book exists in the database.")]
    #[BookNotRented(groups: ["Create"])]
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

        /** @var \DateTime $loan_date */
        $this->estimated_return_date = (clone $loan_date)->modify('+30 days');

        return $this;
    }

    #[Groups("loan")]
    public function getEstimatedReturnDate(): ?\DateTimeInterface
    {
        return $this->estimated_return_date;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        if (!in_array($status, ['pending', 'accepted', 'returned'])) {
            throw new \InvalidArgumentException('Invalid loan status.');
        }
        $this->status = $status;

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

    #[Groups("loan_secret")]
    public function getBookId(): ?int
    {
        return $this->book->getId();
    }

    #[Groups("loan_secret")]
    public function getUserId(): ?int
    {
        return $this->user->getId();
    }

    public function getBookIfNotReturned(): ?Book
    {
        return $this->return_date === null ? $this->book : null;
    }
}
