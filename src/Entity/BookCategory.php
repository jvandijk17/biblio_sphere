<?php

namespace App\Entity;

use App\Repository\BookCategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookCategoryRepository::class)]
class BookCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("bookCategory")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookCategories')]
    #[Assert\NotNull(message: "Book cannot be null.")]
    #[Assert\NotNull(message: "Book cannot be null.")]
    private ?Book $book = null;

    #[ORM\ManyToOne(inversedBy: 'bookCategories')]
    #[Assert\NotNull(message: "Category cannot be null.")]
    #[Assert\NotBlank(message: "Category cannot be blank.")]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    #[Groups("bookCategory")]
    public function getCategoryId(): ?int
    {
        return $this->category->getId();
    }

    #[Groups("bookCategory")]
    public function getBookId(): ?int
    {
        return $this->getBook()->getId();
    }
}
