<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class PostFixtures
 *
 * @package App\DataFixtures
 */
class PostFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var \App\Entity\User[]
     */
    private $authors;

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
    public function load(ObjectManager $manager): void
    {
        $categories = $manager->getRepository(Category::class)->findAll();
        $this->authors = $manager->getRepository(User::class)->findAll();

        foreach ($categories as $category) {
            foreach ($this->create($category) as $post) {
                $manager->persist($post);
            }
        }

        $manager->flush();
    }

    /**
     * @param \App\Entity\Category $category
     *
     * @return \Generator
     */
    private function create(Category $category): \Generator
    {
        for ($i = 0; $i < $category->getPostCount(); $i++) {
            yield (new Post())
                ->setCategory($category)
                ->setAuthor($this->randomAuthor())
                ->setTitle($this->faker->words(random_int(4, 10), true))
                ->setContent($this->faker->sentences(random_int(5, 14), true))
                ->setCreatedAt($this->faker->dateTimeBetween('-24 months'))
                ;
        }
    }

    /**
     * @return \App\Entity\User
     */
    private function randomAuthor(): User
    {
        $random = random_int(0, \count($this->authors) - 1);

        return $this->authors[$random];
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
