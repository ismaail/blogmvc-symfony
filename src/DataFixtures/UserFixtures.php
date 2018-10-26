<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * CategoryFixtures constructor.
     *
     * @param \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
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
            $user->setPassword($this->passwordEncoder->encodePassword($user, 'password'));
            $user->setRoles([User::ROLE_ADMIN]);

            yield $user;
        }
    }
}
