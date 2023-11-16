<?php

namespace App\DataFixtures;

use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Validator\Constraints\Length;

/**
 * @group fixtures
 */
class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private UserService $userService;
    private Generator $faker;
    private $libraries;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->libraries = $this->getLibraryReferences();

        // Create JWT user
        $jwtUserData = $this->createJwtUserData();
        $this->userService->saveUser(null, $jwtUserData);

        // Create 19 random users
        for ($i = 0; $i < 19; $i++) {
            $randomUserData = $this->createRandomUserData();
            $this->userService->saveUser(null, $randomUserData);
        }
    }

    private function createJwtUserData(): array
    {
        return $this->generateUserData([
            'email' => $_ENV['JWT_TEST_MAIL'],
            'password' => $_ENV['JWT_TEST_PASS'],
            'roles' => ['ROLE_USER', 'ROLE_ADMIN'],
            'blocked' => false
        ]);
    }

    private function createRandomUserData(): array
    {
        return $this->generateUserData([
            'email' => Factory::create()->unique()->safeEmail,
            'password' => $_ENV['JWT_TEST_PASS'],
            'roles' => ['ROLE_USER'],
            'blocked' => 0
        ]);
    }

    private function generateUserData(array $overrides = []): array
    {
        $this->faker = Factory::create();

        $data = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'province' => $this->faker->state,
            'postal_code' => $this->faker->postcode,
            'birth_date' => $this->faker->dateTimeBetween('-40 years', '-18 years')->format('Y-m-d'),
            'reputation' => $this->faker->numberBetween(0, 100),
            'library' => $this->libraries[array_rand($this->libraries)]->getId()
        ];

        return array_merge($data, $overrides);
    }

    private function getLibraryReferences(): array
    {
        $libraries = [];
        for ($i = 0; $i < 5; $i++) {
            $libraries[] = $this->getReference('library-' . $i);
        }
        return $libraries;
    }

    public function getDependencies()
    {
        return [
            LibraryFixtures::class,
        ];
    }
}
