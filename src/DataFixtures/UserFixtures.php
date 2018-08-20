<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
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
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->create(10) as $user) {
             $manager->persist($user);
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
            $user = new User();
            $user->setUsername(sprintf('user_%d', $i + 1));
            $user->setPassword('password');

            yield $user;
        }
    }
}
