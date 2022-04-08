<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User(
            'admin@email.com',
            [
                'ROLE_USER',
            ],
            new Name('Admin'),
            [],
            'password', $this->passwordHasher
        );

        $manager->persist($user);

        $manager->flush();
    }
}
