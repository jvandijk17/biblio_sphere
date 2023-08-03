<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Library;
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

    public function saveCategory(?Category $category, array $data): Category
    {
        if(!$category) {
            $category = new Category();
            $this->entityManager->persist($category);
        }

        if(isset($data["name"])) {
            $category->setName($data["name"]);
        }
        $errors = $this->validator->validate($category);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException('Invalid category data ' . (string) $errors);
        }

        $this->entityManager->flush();
        return $category;
    }
}