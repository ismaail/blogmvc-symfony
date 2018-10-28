<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class CommentFixtures
 *
 * @package App\DataFixtures
 */
class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * CommentFixtures constructor.
     */
    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $posts = $manager->getRepository(Post::class)->findAll();

        foreach ($posts as $post) {
            foreach ($this->create() as $comment) {
                $post->addComment($comment);
                $manager->persist($comment);
            }

            $manager->flush();
        }
    }

    /**
     * @return \Generator|\App\Entity\Comment
     */
    private function create()
    {
        $number = random_int(1, 5);

        for ($i = 0; $i < $number; $i++) {
            $comment = (new Comment())
                ->setUsername($this->faker->userName)
                ->setEmail($this->faker->email)
                ->setContent($this->faker->sentences(random_int(1, 5), true))
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime())
                ;

            yield $comment;
        }
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
            PostFixtures::class,
        ];
    }
}
