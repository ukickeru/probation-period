<?php

namespace Mygento\AccessControlBundle\Tests\Functional\ACL\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Mygento\AccessControlBundle\ACL\Domain\Entity\ACE;
use Mygento\AccessControlBundle\ACL\Domain\Service\ACEsCollection;
use Mygento\AccessControlBundle\ACL\Repository\ACERepository;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Mygento\AccessControlBundle\Tests\Functional\Core\Repository\BaseRepositoryTestCase;

class ACERepositoryTest extends BaseRepositoryTestCase
{
    /** @var ACERepository */
    protected ?ServiceEntityRepository $repository;

    public function testIsResourceAvailableForUser()
    {
        $name = new Name('Example');

        $user = new User($name);
        $this->entityManager->persist($user);

        $resource = new Resource();
        $this->entityManager->persist($resource);

        $ACE = new ACE($user, $resource);
        $this->entityManager->persist($ACE);

        $this->entityManager->flush();

        $this->assertTrue($this->repository->isResourceAvailableForUser($user->getId(), $resource->getId()));
    }

    public function testGetResourcesIdAvailableForUser()
    {
        $name = new Name('Example');

        $user = new User($name);
        $this->entityManager->persist($user);

        $resource1 = new Resource();
        $this->entityManager->persist($resource1);
        $resource2 = new Resource();
        $this->entityManager->persist($resource2);
        $resource3 = new Resource();
        $this->entityManager->persist($resource3);

        $ACE1 = new ACE($user, $resource1);
        $this->entityManager->persist($ACE1);
        $ACE2 = new ACE($user, $resource2);
        $this->entityManager->persist($ACE2);
        $ACE3 = new ACE($user, $resource3);
        $this->entityManager->persist($ACE3);

        $this->entityManager->flush();

        $availableResourcesId = $this->repository->getResourcesIdAvailableForUser($user->getId());

        $this->assertEquals(
            [
                $resource1->getId()->value(),
                $resource2->getId()->value(),
                $resource3->getId()->value(),
            ],
            $availableResourcesId
        );
    }

    public function testCheckSynchronizeGlobally()
    {
        // Check that nothing will happen if we pass an empty collection
        $ACEs = new ACEsCollection();
        $this->repository->updateACLGlobally($ACEs);
        $this->assertTrue(true);
    }

    public function testCascadeOperations()
    {
        $name = new Name('Example');

        $user = new User($name);
        $this->entityManager->persist($user);

        $resource = new Resource();
        $this->entityManager->persist($resource);

        $this->entityManager->flush();

        // Create and save new ACE
        $ACE = new ACE($user, $resource);
        $this->repository->save($ACE);

        // Save entities ids
        $userId = clone $user->getId();
        $resourceId = clone $resource->getId();

        // Check that user was successfully removed
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $this->entityManager->clear();

        // Check that ACE related to user, also have been persisted (by cascade)
        try {
            $this->repository->findById($userId, $resourceId);
        } catch (\DomainException $exception) {
            $this->assertEquals(
                ACE::class.' with user ID "'.$userId.'" and resource ID "'.$resourceId.'" was not found!',
                $exception->getMessage()
            );
        }

        // Check that resource still in database
        $resource = $this->entityManager->find(Resource::class, $resourceId);
        $this->assertInstanceOf(Resource::class, $resource);
    }

    protected function getEntityFQCN(): string
    {
        return ACE::class;
    }
}
