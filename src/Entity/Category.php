<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("category_secret")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Name cannot be null.")]
    #[Assert\NotBlank(message: "Name cannot be blank.")]
    #[Groups("category")]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: BookCategory::class, cascade: ['remove'])]
    private Collection $bookCategories;

    public function __construct()
    {
        $this->bookCategories = new ArrayCollection();
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
            $bookCategory->setCategory($this);
        }

        return $this;
    }

    public function removeBookCategory(BookCategory $bookCategory): static
    {
        if ($this->bookCategories->removeElement($bookCategory)) {
            // set the owning side to null (unless already changed)
            if ($bookCategory->getCategory() === $this) {
                $bookCategory->setCategory(null);
            }
        }

        return $this;
    }


    #[Groups("category")]
    public function getBookCategoryIds(): array
    {
        return $this->bookCategories->map(function ($bookCategory) {
            return $bookCategory->getId();
        })->toArray();
    }
}
