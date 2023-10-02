<?php

namespace App\DataFixtures;

use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * @group fixtures
 */
class UserFixtures extends Fixture implements DependentFixtureInterface
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
            'roles' => ['ROLE_USER', 'ROLE_ADMIN'],
            'library' => $libraryReference->getId(),
        ];

        $this->userService->saveUser(null, $userData);
    }

    public function getDependencies()
    {
        return [
            LibraryFixtures::class,
        ];
    }
}
