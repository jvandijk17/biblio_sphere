<?php

namespace App\Service;

use App\Entity\BookCategory;
use App\Entity\Category;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookCategoryService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;        
    }

    public function createBookCategory(array $data): BookCategory
    {
        $bookCategory = new BookCategory();
        $bookCategory->setCategory($this->entityManager->getRepository(Category::class)->find($data["category"]));
        $bookCategory->setBook($this->entityManager->getRepository(Book::class)->find($data["book"]));

        $errors = $this->validator->validate($bookCategory);
        if(count($errors) > 0) {
            throw new \InvalidArgumentException('Invalid book category data: ' . (string) $errors);
        }

        $this->entityManager->persist($bookCategory);
        $this->entityManager->flush();

        return $bookCategory;
    } 

    public function updateBookCategory(BookCategory $bookCategory, array $data): BookCategory
    {
        if(isset($data["category"])) {
            $bookCategory->setCategory($this->entityManager->getRepository(Category::class)->find($data["category"]));
        }
        if(isset($data["book"])) {
            $bookCategory->setBook($this->entityManager->getRepository(Book::class)->find($data["book"]));
        }

        $errors = $this->validator->validate($bookCategory);
        if(count($errors) > 0) {
            throw new \InvalidArgumentException('Invalid book category data: ' . (string) $errors);
        }

        $this->entityManager->flush();

        return $bookCategory;
    }
}