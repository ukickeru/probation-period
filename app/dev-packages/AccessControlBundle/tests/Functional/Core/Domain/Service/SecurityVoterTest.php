<?php

namespace Mygento\AccessControlBundle\Tests\Functional\Core\Domain\Service;

use Doctrine\ORM\EntityManagerInterface;
use Mygento\AccessControlBundle\ACL\Domain\Service\ACESynchronizer;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;
use Mygento\AccessControlBundle\Core\Domain\Exception\AccessDeniedException;
use Mygento\AccessControlBundle\Core\Domain\Service\SecurityVoter;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SecurityVoterTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    private ?SecurityVoter $securityVoter;

    private ?ACESynchronizer $ACESynchronizer;

    public function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->securityVoter = $kernel->getContainer()
            ->get('mygento.access_control.security_voter');

        $this->ACESynchronizer = $kernel->getContainer()
            ->get('mygento.access_control.ace_synchronizer');
    }

    public function testAccessControl()
    {
        $name = new Name('Example');

        $user = new User($name);
        $this->entityManager->persist($user);

        $group1 = new Group($name, [$user]);
        $this->entityManager->persist($group1);
        $group2 = new Group($name, [$user]);
        $this->entityManager->persist($group2);

        $resource = new Resource([$group1, $group2]);
        $this->entityManager->persist($resource);

        $this->entityManager->flush();

        $this->ACESynchronizer->synchronizeACEForUser($user->getId());

        $this->assertTrue($this->securityVoter->isGranted($resource->getId(), $user->getId()));

        $this->entityManager->remove($group1);
        $this->entityManager->flush();

        $this->ACESynchronizer->synchronizeACEForUser($user->getId());

        $this->assertTrue($this->securityVoter->isGranted($resource->getId(), $user->getId()));

        $this->entityManager->remove($group2);
        $this->entityManager->flush();

        $this->ACESynchronizer->synchronizeACEForUser($user->getId());

        $this->expectException(AccessDeniedException::class);
        $this->securityVoter->isGranted($resource->getId(), $user->getId());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
        $this->securityVoter = null;
        $this->ACESynchronizer = null;
    }
}
