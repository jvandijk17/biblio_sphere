<?php

namespace App\Service;

use App\Entity\Library;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LibraryService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function createLibrary(array $data): Library
    {
        $library = new Library();
        $library->setName($data['name']);
        $library->setAddress($data['address']);
        $library->setCity($data['city']);
        $library->setProvince($data['province']);
        $library->setPostalCode($data['postal_code']);

        $errors = $this->validator->validate($library);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException('Invalid library data: ' . (string) $errors);
        }

        $this->entityManager->persist($library);
        $this->entityManager->flush();

        return $library;
    }

    public function updateLibrary(Library $library, array $data): Library
    {
        if (isset($data['name'])) {
            $library->setName($data['name']);
        }
        if (isset($data['address'])) {
            $library->setAddress($data['address']);
        }
        if (isset($data['city'])) {
            $library->setCity($data['city']);
        }
        if (isset($data['province'])) {
            $library->setProvince($data['province']);
        }
        if (isset($data['postal_code'])) {
            $library->setPostalCode($data['postal_code']);
        }    

        $errors = $this->validator->validate($library);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException((string) $errors);
        }

        $this->entityManager->flush();

        return $library;
    }
}
