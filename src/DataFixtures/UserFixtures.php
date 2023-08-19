<?php

namespace App\DataFixtures;

use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * @Order(2)
 * @DependsOn(LibraryFixtures::class)
 */
class UserFixtures extends Fixture
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $libraryReference = $this->getReference('default-library');
        $userData = [
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'email' => $_ENV['JWT_TEST_MAIL'],
            'password' => $_ENV['JWT_TEST_PASS'],
            'address' => $faker->address,
            'city' => $faker->city,
            'province' => $faker->state,
            'postal_code' => $faker->postcode,
            'birth_date' => $faker->dateTimeBetween('-40 years', '-18 years')->format('Y-m-d'),
            'reputation' => $faker->numberBetween(0, 100),
            'blocked' => $faker->boolean,
            'library' => $libraryReference->getId(),
        ];

        $this->userService->saveUser(null, $userData);
    }
}