<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UserFixtures extends Fixture
{
    /**
     * @var \Faker\Generator
     */
    private \Faker\Generator $faker;

    /**
     * CategoryFixtures constructor.
     *
     * @param \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
        $this->faker = Faker\Factory::create();
    }

    /**
     * @param \Doctrine\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // Create Admin
        $admin = $this->createAdmin();
        $manager->persist($admin);

        // Create Membes
        foreach ($this->createMembers() as $member) {
             $manager->persist($member);
        }

        $manager->flush();
    }

    /**
     * @return \App\Entity\User
     */
    private function createAdmin(): User
    {
        $admin = new User();
        $admin->setUsername('jhon-doe');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $admin->setRoles([User::ROLE_ADMIN]);

        return $admin;
    }

    /**
     * @return \Generator
     */
    private function createMembers(): \Generator
    {
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername(str_replace('.', '-', $this->faker->userName));
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setRoles([User::ROLE_MEMBER]);

            yield $user;
        }
    }
}
