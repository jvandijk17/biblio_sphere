<?php

namespace App\Service;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function createCategory(array $data): Category
    {
        $category = new Category();
        $category->setName($data["name"]);

        $errors = $this->validator->validate($category);
        if (count($errors) > 0) {
            return new \InvalidArgumentException('Invalid category data ' . (string) $errors);
        }

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

    public function updateCategory(Category $category, array $data): Category
    {
        if(isset($data["name"])) {
            $category->setName($data["name"]);
        }

        $errors = $this->validator->validate($category);
        if (count($errors) > 0) {
            return new \InvalidArgumentException((string) $errors);
        }

        $this->entityManager->flush();

        return $category;
    }
}