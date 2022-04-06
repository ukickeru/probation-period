<?php

namespace Mygento\AccessControlBundle\Tests\Functional\ACL\Domain;

use Doctrine\ORM\EntityManagerInterface;
use Mygento\AccessControlBundle\ACL\Domain\Entity\ACE;
use Mygento\AccessControlBundle\ACL\Domain\Service\ACESynchronizer;
use Mygento\AccessControlBundle\ACL\Repository\ACERepository;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Mygento\AccessControlBundle\Core\Repository\UserRepository;
use Mygento\AccessControlBundle\Tests\Functional\Core\Repository\SchemaRecreationTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ACESynchronizerTest extends KernelTestCase
{
    use SchemaRecreationTrait;

    private ?ACESynchronizer $ACESynchronizer;

    private ?EntityManagerInterface $entityManager;

    private ?ACERepository $ACERepository;

    private ?UserRepository $userRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->ACESynchronizer = $kernel->getContainer()
            ->get('mygento.access_control.ace_synchronizer');

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->ACERepository = $this->entityManager->getRepository(ACE::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);

        $this->recreateSchema($this->entityManager);
    }

    public function testACESynchronizationForUser()
    {
        $name = new Name('Example');

        $group = new Group($name);
        $this->entityManager->persist($group);

        $resources = [
            $resource1 = new Resource(),
            $resource2 = new Resource(),
            $resource3 = new Resource(),
        ];

        foreach ($resources as $resource) {
            $this->entityManager->persist($resource);
        }

        $groups = [
            $group1 = new Group($name, [], [$resource1, $resource2]),
            $group2 = new Group($name, [], [$resource2, $resource3]),
        ];

        foreach ($groups as $group) {
            $this->entityManager->persist($group);
        }

        $user = new User($name, $groups);
        $this->entityManager->persist($user);

        $this->entityManager->flush();

        $expectedResourcesIdAvailableForUser = [
            $resource1->getId(),
            $resource2->getId(),
            $resource3->getId(),
        ];

        // Checks if there are 3 resources ids, available for user from User-Group-Resource model
        $this->assertEquals(
            $expectedResourcesIdAvailableForUser,
            $this->userRepository->getResourcesIdAvailableForUser($user->getId())
        );

        // Checks if there are no resources ids, available for user from ACL model
        $this->assertEmpty($this->ACERepository->getResourcesIdAvailableForUser($user->getId()));

        // Synchronize ACL with User-Group-Resource model
        $this->ACESynchronizer->synchronizeACEForUser($user->getId());

        // Checks if ACL model was synchronized with User-Group-Resource
        $this->assertEquals(
            $expectedResourcesIdAvailableForUser,
            $this->ACERepository->getResourcesIdAvailableForUser($user->getId())
        );

        $resource4 = new Resource([$group2]);
        $this->entityManager->persist($resource4);
        $this->entityManager->flush();

        // Checks if there are still 3 resources ids, available for user from ACL model
        $this->assertEquals(
            $expectedResourcesIdAvailableForUser,
            $this->ACERepository->getResourcesIdAvailableForUser($user->getId())
        );

        $expectedResourcesIdAvailableForUser[] = $resource4->getId();

        // Checks if now there are 4 resources ids, available for user from ACL model
        $this->assertEquals(
            $expectedResourcesIdAvailableForUser,
            $this->userRepository->getResourcesIdAvailableForUser($user->getId())
        );

        // Synchronize ACL with User-Group-Resource model
        $this->ACESynchronizer->synchronizeACEForUser($user->getId());

        // Checks if ACL model was synchronized with User-Group-Resource
        $this->assertEquals(
            $expectedResourcesIdAvailableForUser,
            $this->ACERepository->getResourcesIdAvailableForUser($user->getId())
        );

        // Now we exclude user from group 1 and wants to check if synchronizer will delete ACE with resource 1
        $user->removeGroup($group1);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->assertCount(3, $this->userRepository->getResourcesIdAvailableForUser($user->getId()));

        // ACL is not synchronized now
        $this->assertCount(4, $this->ACERepository->getResourcesIdAvailableForUser($user->getId()));

        $this->ACESynchronizer->synchronizeACEForUser($user->getId());

        $this->assertCount(3, $this->ACERepository->getResourcesIdAvailableForUser($user->getId()));

        // Now we exclude user from group 2 and wants to check if synchronizer will clear ACL for user 1
        $user->removeGroup($group2);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->assertCount(0, $this->userRepository->getResourcesIdAvailableForUser($user->getId()));

        // ACL is not synchronized now
        $this->assertCount(3, $this->ACERepository->getResourcesIdAvailableForUser($user->getId()));

        $this->ACESynchronizer->synchronizeACEForUser($user->getId());

        $this->assertCount(0, $this->ACERepository->getResourcesIdAvailableForUser($user->getId()));
    }

    public function testSynchronizeACEGlobally()
    {
        $name = new Name('Example');

        $group = new Group($name);
        $this->entityManager->persist($group);

        $resources = [
            $resource1 = new Resource(),
            $resource2 = new Resource(),
            $resource3 = new Resource(),
            $resource4 = new Resource(),
        ];

        foreach ($resources as $resource) {
            $this->entityManager->persist($resource);
        }

        $groups = [
            $group1 = new Group($name, [], [$resource1]),
            $group2 = new Group($name, [], [$resource2]),
            $group3 = new Group($name, [], [$resource2, $resource3]),
            $group4 = new Group($name, [], [$resource4]),
        ];

        foreach ($groups as $group) {
            $this->entityManager->persist($group);
        }

        $users = [
            $user1 = new User($name, [$group1, $group2]),
            $user2 = new User($name, [$group2, $group3]),
            $user3 = new User($name, [$group4]),
        ];

        foreach ($users as $user) {
            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        // User 1 has access to resources 1 & 2
        $this->assertEquals([$resource1->getId(), $resource2->getId()], $this->userRepository->getResourcesIdAvailableForUser($user1->getId()));

        // User 2 has access to resources 2 & 3
        $this->assertEquals([$resource2->getId(), $resource3->getId()], $this->userRepository->getResourcesIdAvailableForUser($user2->getId()));

        // User 3 has access to resource 4
        $this->assertEquals([$resource4->getId()], $this->userRepository->getResourcesIdAvailableForUser($user3->getId()));

        // None of these users have access to the resources in the ACL model
        $this->assertEmpty($this->ACERepository->getResourcesIdAvailableForUser($user1->getId()));
        $this->assertEmpty($this->ACERepository->getResourcesIdAvailableForUser($user2->getId()));
        $this->assertEmpty($this->ACERepository->getResourcesIdAvailableForUser($user3->getId()));

        $this->ACESynchronizer->synchronizeACEGlobally();

        // Now they have access
        $this->assertEquals([$resource1->getId(), $resource2->getId()], $this->ACERepository->getResourcesIdAvailableForUser($user1->getId()));
        $this->assertEquals([$resource2->getId(), $resource3->getId()], $this->ACERepository->getResourcesIdAvailableForUser($user2->getId()));
        $this->assertEquals([$resource4->getId()], $this->ACERepository->getResourcesIdAvailableForUser($user3->getId()));

        // Now lets remove access to resource 2 from group 2
        $group2->removeResource($resource2);
        $this->entityManager->persist($group2);
        $this->entityManager->flush();

        // ... and also synchronize ACL
        $this->ACESynchronizer->synchronizeACEGlobally();

        // User 1 have no more access to this resource
        $this->assertEquals([$resource1->getId()], $this->userRepository->getResourcesIdAvailableForUser($user1->getId()));
        $this->assertEquals([$resource1->getId()], $this->ACERepository->getResourcesIdAvailableForUser($user1->getId()));

        // But user 2 still may access it, through group 3
        $this->assertEquals([$resource2->getId(), $resource3->getId()], $this->userRepository->getResourcesIdAvailableForUser($user2->getId()));
        $this->assertEquals([$resource2->getId(), $resource3->getId()], $this->ACERepository->getResourcesIdAvailableForUser($user2->getId()));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // to avoid memory leaks
        $this->dropSchema($this->entityManager);
        $this->entityManager->close();
        $this->entityManager = null;
        $this->ACERepository = null;
        $this->userRepository = null;
        $this->ACESynchronizer = null;
    }
}
