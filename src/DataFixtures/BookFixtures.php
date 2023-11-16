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
    private $libraries;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->libraries = $this->getLibraryReferences();

        $bookList = $this->createBooks();

        $this->addBookReferences($bookList);
    }

    private function createBooks(): array
    {
        $bookList = [];
        for ($i = 0; $i < 20; $i++) {
            $bookData = $this->generateBookAttributes();
            $book = $this->bookService->saveBook(null, $bookData);
            $bookList[] = $book;
        }
        return $bookList;
    }

    private function generateBookAttributes(): array
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
            'library' => $this->libraries[array_rand($this->libraries)]->getId(),
        ];
    }

    private function getLibraryReferences(): array
    {
        $libraries = [];
        for ($i = 0; $i < 5; $i++) {
            $libraries[] = $this->getReference('library-' . $i);
        }
        return $libraries;
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
