<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Faker;
use App\Entity\Category;
use Faker\Generator as FakerGenerator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CategoryFixtures extends Fixture
{
    private FakerGenerator $faker;

    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->create() as $category) {
            $manager->persist($category);
        }

        $manager->flush();
    }

    private function create(): \Generator|Category
    {
        for ($i = 0; $i < 10; $i++) {
            yield (new Category())
                ->setName((string)$this->faker->words(random_int(1, 2), true))
                ->setPostCount(random_int(10, 100));
        }
    }
}
