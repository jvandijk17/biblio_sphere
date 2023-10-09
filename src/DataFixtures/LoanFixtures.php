<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Loan;
use App\Entity\User;
use App\Entity\Book;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

/**
 * @group fixtures
 */
class LoanFixtures extends Fixture implements DependentFixtureInterface
{
    private const MAX_SUCCESS = 20;
    private const MAX_ATTEMPTS = 200;

    private \Faker\Generator $faker;
    private array $users = [];

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->users = $manager->getRepository(User::class)->findAll();
        $books = $manager->getRepository(Book::class)->findAll();
        shuffle($books);
        $loanRepo = $manager->getRepository(Loan::class);
        $successes = 0;
        $attempts = 0;

        foreach ($books as $book) {
            if ($successes >= self::MAX_SUCCESS) {
                break;
            }

            $user = $this->getRandomUser();

            if (!$this->loanExists($loanRepo, $book, $user)) {
                try {
                    $loan = new Loan();
                    $loan->setLoanDate($this->faker->dateTimeBetween('-2 years', 'now'));
                    $loan->setReturnDate($this->faker->optional(0.7)->dateTimeBetween('-1 years', 'now'));
                    $loan->setUser($user);
                    $loan->setBook($book);

                    $manager->persist($loan);
                    $successes++;
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }
            $attempts++;
            if ($attempts >= self::MAX_ATTEMPTS) {
                throw new \Exception("After " . self::MAX_ATTEMPTS . " attempts, " . self::MAX_SUCCESS . " loans could not be loaded.");
            }
        }

        $manager->flush();
    }

    private function getRandomUser(): User
    {
        return $this->users[array_rand($this->users)];
    }

    private function loanExists($loanRepo, $book, $user): bool
    {
        $existingLoan = $loanRepo->findOneBy([
            'book' => $book,
            'user' => $user
        ]);

        return $existingLoan !== null;
    }

    public function getDependencies()
    {
        return [
            BookFixtures::class,
            UserFixtures::class
        ];
    }
}
