<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("book_secret")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Title cannot be null.")]
    #[Assert\NotBlank(message: "Title cannot be blank.")]
    #[Groups("book")]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Author cannot be null.")]
    #[Assert\NotBlank(message: "Author cannot be blank.")]
    #[Groups("book")]
    private ?string $author = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Publisher cannot be null.")]
    #[Assert\NotBlank(message: "Publisher cannot be blank.")]
    #[Groups("book")]
    private ?string $publisher = null;

    #[ORM\Column(length: 13)]
    #[Assert\NotNull(message: "ISBN cannot be null.")]
    #[Assert\NotBlank(message: "ISBN cannot be blank.")]
    #[Groups("book")]
    private ?string $isbn = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "Publication year cannot be null.")]
    #[Assert\NotBlank(message: "Publication year cannot be blank.")]
    #[Groups("book")]
    private ?\DateTimeInterface $publication_year = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Page count cannot be null.")]
    #[Assert\NotBlank(message: "Page count cannot be blank.")]
    #[Groups("book")]
    private ?int $page_count = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Library $library = null;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Loan::class, cascade: ['remove'])]
    private Collection $loans;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: BookCategory::class, cascade: ['remove'])]
    private Collection $bookCategories;

    public function __construct()
    {
        $this->loans = new ArrayCollection();
        $this->bookCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): static
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getPublicationYear(): ?\DateTimeInterface
    {
        return $this->publication_year;
    }

    public function setPublicationYear(?\DateTimeInterface $publication_year): static
    {
        $this->publication_year = $publication_year;

        return $this;
    }

    public function getPageCount(): ?int
    {
        return $this->page_count;
    }

    public function setPageCount(?int $page_count): static
    {
        $this->page_count = $page_count;

        return $this;
    }

    public function getLibrary(): ?Library
    {
        return $this->library;
    }

    public function setLibrary(?Library $library): static
    {
        $this->library = $library;

        return $this;
    }

    /**
     * Get the library name associated with this book.
     *
     * @return string|null
     */
    #[Groups("book")]
    public function getLibraryName(): ?string
    {
        return $this->library ? $this->library->getName() : null;
    }

    #[Groups("book")]
    public function getLibraryId(): ?int
    {
        return $this->library->getId();
    }

    /**
     * @return Collection<int, Loan>
     */
    public function getLoans(): Collection
    {
        return $this->loans;
    }

    public function addLoan(Loan $loan): static
    {
        if (!$this->loans->contains($loan)) {
            $this->loans->add($loan);
            $loan->setBook($this);
        }

        return $this;
    }

    public function removeLoan(Loan $loan): static
    {
        if ($this->loans->removeElement($loan)) {
            // set the owning side to null (unless already changed)
            if ($loan->getBook() === $this) {
                $loan->setBook(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BookCategory>
     */
    public function getBookCategories(): Collection
    {
        return $this->bookCategories;
    }

    public function addBookCategory(BookCategory $bookCategory): static
    {
        if (!$this->bookCategories->contains($bookCategory)) {
            $this->bookCategories->add($bookCategory);
            $bookCategory->setBook($this);
        }

        return $this;
    }

    public function removeBookCategory(BookCategory $bookCategory): static
    {
        if ($this->bookCategories->removeElement($bookCategory)) {
            // set the owning side to null (unless already changed)
            if ($bookCategory->getBook() === $this) {
                $bookCategory->setBook(null);
            }
        }

        return $this;
    }

    #[Groups("book")]
    public function getBookCategoryIds(): array
    {
        return $this->bookCategories->map(function ($bookCategory) {
            return $bookCategory->getId();
        })->toArray();
    }

    #[Groups("book")]
    public function getBookCategoryNames(): array
    {
        return $this->bookCategories->map(function ($bookCategory) {
            return $bookCategory->getCategory()->getName();
        })->toArray();
    }

    #[Groups("book")]
    public function getActiveLoanId(): ?int
    {
        $activeLoan = $this->loans->filter(function (Loan $loan) {
            return $loan->getBookIfNotReturned() !== null;
        })->first();

        return $activeLoan ? $activeLoan->getId() : null;
    }


    public function getLoanIds(): array
    {
        return $this->loans->map(function ($loan) {
            return $loan->getId();
        })->toArray();
    }
}
