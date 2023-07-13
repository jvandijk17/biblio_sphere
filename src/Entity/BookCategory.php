<?php

namespace App\Entity;

use App\Repository\BookCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookCategoryRepository::class)]
class BookCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $book_id = null;

    #[ORM\Column]
    private ?int $category_id = null;

    #[ORM\ManyToOne(inversedBy: 'bookCategories')]
    private ?Book $book_id_fk = null;

    #[ORM\ManyToOne(inversedBy: 'bookCategories')]
    private ?Category $category_id_fk = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }

    public function setCategoryId(int $category_id): static
    {
        $this->category_id = $category_id;

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

    public function getCategoryIdFk(): ?Category
    {
        return $this->category_id_fk;
    }

    public function setCategoryIdFk(?Category $category_id_fk): static
    {
        $this->category_id_fk = $category_id_fk;

        return $this;
    }
}
