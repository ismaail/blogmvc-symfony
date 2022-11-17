<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Faker;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Category;
use Faker\Generator as FakerGenerator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    private FakerGenerator $faker;

    /**
     * @var array<\App\Entity\User>
     */
    private array $authors;

    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

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

    private function create(Category $category): \Generator|Post
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

    private function randomAuthor(): User
    {
        $random = random_int(0, \count($this->authors) - 1);

        return $this->authors[$random];
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array<class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
