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
    private \Faker\Generator $faker;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $libraryReference = $this->getReference('default-library');
        $bookList = $this->createBooks($libraryReference);

        $this->addBookReferences($bookList);
    }

    private function createBooks($libraryReference): array
    {
        $bookList = [];
        for ($i = 0; $i < 20; $i++) {
            $bookData = $this->generateBookAttributes($libraryReference);
            $book = $this->bookService->saveBook(null, $bookData);
            $bookList[] = $book;
        }
        return $bookList;
    }

    private function generateBookAttributes($libraryReference): array
    {
        $titleWords = $this->faker->words(5);
        $title = implode(' ', $titleWords);

        return [
            'title' => $title,
            'author' => $this->faker->name,
            'publisher' => $this->faker->company,
            'isbn' => $this->faker->isbn13,
            'publication_year' => $this->faker->dateTimeBetween('-100 years', 'now')->format('Y-m-d'),
            'page_count' => $this->faker->numberBetween(50, 1000),
            'library' => $libraryReference->getId(),
        ];
    }

    private function addBookReferences(array $bookList): void
    {
        foreach ($bookList as $index => $book) {
            $this->addReference('book-' . $index, $book);
        }
    }

    public function getDependencies()
    {
        return [
            LibraryFixtures::class,
        ];
    }
}
