<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\Organization;
use Mygento\AccessControlBundle\Core\Domain\Entity\Project;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $resource1 = new Resource();
        $resource2 = new Resource();

        $organizationName = new Name('Organization');
        $organizationGroup = new Group($organizationName);
        $organization = new Organization($organizationName, $organizationGroup, [$resource1]);
        $manager->persist($organization);

        $projectName = new Name('Project');
        $projectGroup = new Group($projectName);
        $project = new Project($projectName, $projectGroup, [$resource2]);
        $manager->persist($project);

        $test1User = new User(
            'test1@email.com',
            [
                'ROLE_USER',
            ],
            new Name('Test 1'),
            [
                $organizationGroup,
            ],
            'password',
            $this->passwordHasher
        );
        $manager->persist($test1User);

        $test2User = new User(
            'test2@email.com',
            [
                'ROLE_USER',
            ],
            new Name('Test 2'),
            [
                $organizationGroup,
                $projectGroup,
            ],
            'password',
            $this->passwordHasher
        );
        $manager->persist($test2User);

        $manager->flush();
    }
}
