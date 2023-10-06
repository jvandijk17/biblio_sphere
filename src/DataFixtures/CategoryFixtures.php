<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Service\CategoryService;
use Faker\Factory;

/**
 * @group fixtures
 */
class CategoryFixtures extends Fixture
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) {

            $nameWords = $faker->words(2);
            $name = implode(' ', $nameWords);

            $categoryData = [
                'name' => $name
            ];

            $category = $this->categoryService->saveCategory(null, $categoryData);
            $this->addReference('category-' . $i, $category);

            $manager->flush();
        }
    }
}
