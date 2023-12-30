<?php

namespace App\Entity;

use App\Repository\LibraryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LibraryRepository::class)]
class Library
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["library_secret", "preview_library"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["library", "preview_library"])]
    #[Assert\NotNull(message: "Name cannot be null.")]
    #[Assert\NotBlank(message: "Name cannot be blank.")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Address cannot be null.")]
    #[Assert\NotBlank(message: "Address cannot be blank.")]
    #[Groups("library")]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "City cannot be null.")]
    #[Assert\NotBlank(message: "City cannot be blank.")]
    #[Groups(["library", "preview_library"])]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Province cannot be null.")]
    #[Assert\NotBlank(message: "Province cannot be blank.")]
    #[Groups("library")]
    private ?string $province = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotNull(message: "Postal code cannot be null.")]
    #[Assert\NotBlank(message: "Postal code cannot be blank.")]
    #[Groups("library")]
    private ?string $postal_code = null;

    #[ORM\OneToMany(mappedBy: 'library', targetEntity: User::class)]    
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'library', targetEntity: Book::class)]
    private Collection $books;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(string $province): static
    {
        $this->province = $province;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(?string $postal_code): static
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setLibrary($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            if ($user->getLibrary() === $this) {
                $user->setLibrary(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setLibrary($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            if ($book->getLibrary() === $this) {
                $book->setLibrary(null);
            }
        }

        return $this;
    }

    #[Groups("library")]
    public function getUserIds(): array
    {
        return $this->users->map(function ($user) {
            return $user->getId();
        })->toArray();
    }

    #[Groups("library")]
    public function getBookIds(): array
    {
        return $this->books->map(function ($book) {
            return $book->getId();
        })->toArray();
    }

}
