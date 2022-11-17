<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use Faker\Generator as FakerGenerator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private FakerGenerator $faker;

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
        $this->faker = Faker\Factory::create();
    }

    public function load(ObjectManager $manager): void
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

    private function createAdmin(): User
    {
        $admin = new User();
        $admin->setUsername('jhon-doe');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $admin->setRoles([User::ROLE_ADMIN]);

        return $admin;
    }

    private function createMembers(): \Generator|User
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
