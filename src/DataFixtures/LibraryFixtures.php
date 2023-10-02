<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Service\LibraryService;
use Faker\Factory;

/**
 * @group fixtures
 */
class LibraryFixtures extends Fixture
{
    private LibraryService $libraryService;

    public function __construct(LibraryService $libraryService)
    {
        $this->libraryService = $libraryService;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $libraryData = [
            'name' => $faker->company,
            'address' => $faker->streetAddress,
            'city' => $faker->city,
            'province' => $faker->state,
            'postal_code' => $faker->postcode,
        ];

        $library = $this->libraryService->saveLibrary(null, $libraryData);
        $manager->flush();

        $this->addReference('default-library', $library);
    }
}
