<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Service\BookService;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

/**
 * @group fixtures
 */
class BookFixtures extends Fixture implements DependentFixtureInterface
{
    private BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $libraryReference = $this->getReference('default-library');

        for ($i = 0; $i < 20; $i++) {

            $titleWords = $faker->words(5);
            $title = implode(' ', $titleWords);

            $bookData = [
                'title' => $title,
                'author' => $faker->name,
                'publisher' => $faker->company,
                'isbn' => $faker->isbn13,
                'publication_year' => $faker->dateTimeBetween('-100 years', 'now')->format('Y-m-d'),
                'page_count' => $faker->numberBetween(50, 1000),
                'library' => $libraryReference->getId(),
            ];

            $this->bookService->saveBook(null, $bookData);
        }
    }

    public function getDependencies()
    {
        return [
            LibraryFixtures::class,
        ];
    }
}
