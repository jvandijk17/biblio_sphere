<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\Library;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    
    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function saveBook(?Book $book, array $data): Book
    {
        if(!$book) {
            $book = new Book();
            $this->entityManager->persist($book);
        }

        if(isset($data["title"])) {
            $book->setTitle($data["title"]);
        }
        if(isset($data["author"])) {
            $book->setAuthor($data["author"]);
        }
        if(isset($data["publisher"])) {
            $book->setPublisher($data["publisher"]);
        }
        if(isset($data["isbn"])) {
            $book->setIsbn($data["isbn"]);
        }
        if(isset($data["publication_year"])) {
            $book->setPublicationYear(new \DateTime($data['publication_year']));
        }
        if(isset($data["page_count"])) {
            $book->setPageCount($data['page_count']);
        }
        if(isset($data["library_id"])) {
            $book->setLibrary($this->entityManager->getRepository(Library::class)->find($data["library_id"]));
        }        

        $errors = $this->validator->validate($book);

        if(count($errors) > 0) {
            throw new \InvalidArgumentException('Invalid book data: ' . (string) $errors);
        }

        $this->entityManager->flush();

        return $book;
    }
}