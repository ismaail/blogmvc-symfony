<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture
{
    /**
     * @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var \Faker\Generator
     */
    private \Faker\Generator $faker;

    /**
     * CategoryFixtures constructor.
     *
     * @param \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Faker\Factory::create();
    }

    /**
     * @param \Doctrine\Persistence\ObjectManager $manager
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
            $user->setUsername(str_replace('.', '-', $this->faker->userName));
            $user->setPassword($this->passwordEncoder->encodePassword($user, 'password'));
            $user->setRoles([User::ROLE_ADMIN]);

            yield $user;
        }
    }
}
