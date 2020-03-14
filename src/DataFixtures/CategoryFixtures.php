<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

/**
 * Class CategoryFixtures
 *
 * @package App\DataFixtures
 */
class CategoryFixtures extends Fixture
{
    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * CategoryFixtures constructor.
     */
    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    /**
     * @param \Doctrine\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->create(10) as $category) {
            $manager->persist($category);
        }

        $manager->flush();
    }

    /**
     * @param int $number
     *
     * @return \Generator
     */
    private function create(int $number)
    {
        for ($i = 0; $i < $number; $i++) {
            $category = new Category();
            $category->setName($this->faker->words(random_int(1, 2), true));
            $category->setPostCount(random_int(10, 100));

            yield $category;
        }
    }
}
