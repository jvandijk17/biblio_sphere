<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Library;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function createUser(array $data): User
    {
        $user = new User();
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setEmail($data['email']);
        $user->setPasswordHash($data['password_hash']);
        $user->setAddress($data['address']);
        $user->setCity($data['city']);
        $user->setProvince($data['province']);
        $user->setPostalCode($data['postal_code']);
        $user->setRegistrationDate(new \DateTime());
        $user->setBirthDate(new \DateTime($data['birth_date']));        
        $user->setReputation($data['reputation']);
        $user->setBlocked($data['blocked']);
        $user->setLibrary($this->entityManager->getRepository(Library::class)->find($data['library']));

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException('Invalid user data: ' . (string) $errors);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function updateUser(User $user, array $data): User
    {
        if (isset($data['first_name'])) {
            $user->setFirstName($data['first_name']);
        }
        if (isset($data['last_name'])) {
            $user->setLastName($data['last_name']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['password_hash'])) {
            $user->setPasswordHash($data['password_hash']);
        }
        if (isset($data['address'])) {
            $user->setAddress($data['address']);
        }
        if (isset($data['city'])) {
            $user->setCity($data['city']);
        }
        if (isset($data['province'])) {
            $user->setProvince($data['province']);
        }
        if (isset($data['postal_code'])) {
            $user->setPostalCode($data['postal_code']);
        }
        if (isset($data['registration_date'])) {
            $user->setRegistrationDate(new \DateTime($data['registration_date']));
        }
        if (isset($data['birth_date'])) {
            $user->setBirthDate(new \DateTime($data['birth_date']));
        }    
        if (isset($data['reputation'])) {
            $user->setReputation($data['reputation']);
        }
        if (isset($data['blocked'])) {
            $user->setBlocked($data['blocked']);
        }
        if(isset($data['library'])) {                    
            $user->setLibrary($this->entityManager->getRepository(Library::class)->find($data['library']));
        }

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException((string) $errors);
        }

        $this->entityManager->flush();

        return $user;
    }
}
