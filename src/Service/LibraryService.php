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

    public function saveLibrary(?Library $library, array $data): Library
    {
        if(!$library) {
            $library = new Library();
            $this->entityManager->persist($library);
        }
        
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
            throw new \InvalidArgumentException('Invalid library data: ' . (string) $errors);
        }

        $this->entityManager->flush();
        return $library;
    }
}
