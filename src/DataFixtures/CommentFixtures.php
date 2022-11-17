<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\Comment;
use Faker\Generator as FakerGenerator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    private FakerGenerator $faker;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $posts = $manager->getRepository(Post::class)->findAll();

        foreach ($posts as $post) {
            foreach ($this->create($post) as $comment) {
                $post->addComment($comment);
                $manager->persist($comment);
            }

            $manager->flush();
        }
    }

    private function create(Post $post): \Generator|Comment
    {
        $number = random_int(1, 5);

        for ($i = 0; $i < $number; $i++) {
            $comment = (new Comment())
                ->setUsername($this->faker->userName)
                ->setEmail($this->faker->email)
                ->setContent($this->faker->sentences(random_int(1, 5), true))
                ->setCreatedAt($this->faker->dateTimeBetween($post->getCreatedAt()))
                ;

            $comment->setUpdatedAt($comment->getCreatedAt());

            yield $comment;
        }
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
            PostFixtures::class,
        ];
    }
}
