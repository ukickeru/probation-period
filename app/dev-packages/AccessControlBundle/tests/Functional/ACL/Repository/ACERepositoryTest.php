<?php

namespace Mygento\AccessControlBundle\Tests\Functional\ACL\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Mygento\AccessControlBundle\ACL\Domain\Entity\ACE;
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

        $this->assertEquals([1, 2, 3], $availableResourcesId);
    }

    protected function getEntityFQCN(): string
    {
        return ACE::class;
    }
}
