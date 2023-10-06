<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Service\BookCategoryService;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

/**
 * @group fixtures
 */
class BookCategoryFixtures extends Fixture implements DependentFixtureInterface
{
    private BookCategoryService $bookCategoryService;
    private \Faker\Generator $faker;

    public function __construct(BookCategoryService $bookCategoryService)
    {
        $this->bookCategoryService = $bookCategoryService;
        $this->faker = Factory::create();
    }


    public function load(ObjectManager $manager)
    {
        $books = $this->getBookReferences();
        $categories = $this->getCategoryReferences();

        foreach ($books as $book) {
            $randomCategory = $this->faker->randomElement($categories);
            $this->associateBookWithCategory($book, $randomCategory);
        }
    }

    private function getCategoryReferences(): array
    {
        $categories = [];
        for ($i = 0; $i < 5; $i++) {
            $categories[] = $this->getReference('category-' . $i);
        }
        return $categories;
    }

    private function getBookReferences(): array
    {
        $books = [];
        for ($i = 0; $i < 20; $i++) {
            $bookReference = $this->getReference('book-' . $i);
            if ($bookReference) {
                $books[] = $bookReference;
            }
        }
        return $books;
    }

    private function associateBookWithCategory($book, $category): void
    {
        $this->bookCategoryService->saveBookCategory(null, [
            'book' => $book,
            'category' => $category
        ]);
    }


    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
            BookFixtures::class
        ];
    }
}
