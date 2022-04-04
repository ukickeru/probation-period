<?php

namespace Mygento\AccessControlBundle\Tests\Functional\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\Organization;
use Mygento\AccessControlBundle\Core\Domain\Entity\Project;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;
use Mygento\AccessControlBundle\Core\Repository\ResourceRepository;
use Mygento\AccessControlBundle\Core\Repository\UserRepository;

class UserRepositoryTest extends BaseRepositoryTestCase
{
    /** @var UserRepository */
    protected ?ServiceEntityRepository $repository;

    public function testCreationAndUpdating()
    {
        $user = new User();

        $this->repository->save($user);

        $userId = $user->getId();

        $this->entityManager->clear();
        unset($user);

        $user = $this->repository->findById($userId);

        $this->assertInstanceOf(User::class, $user);

        $this->repository->update($user);

        $this->entityManager->clear();
        unset($user);

        $user = $this->repository->findById($userId);

        $this->assertInstanceOf(User::class, $user);
    }

    public function testRemoving()
    {
        $user = new User();

        $this->repository->save($user);

        $userId = $user->getId();

        $this->entityManager->clear();
        unset($user);

        $user = $this->repository->findById($userId);

        $this->assertInstanceOf(User::class, $user);

        $this->repository->remove($userId);

        $this->entityManager->clear();
        unset($user);

        try {
            $this->repository->findById($userId);
        } catch (\DomainException $exception) {
            $this->assertEquals(User::class.' with ID "'.$userId.'" was not found!', $exception->getMessage());
        }
    }

    public function testGetAvailableUserResources()
    {
        $group = new Group(null, []);
        $this->entityManager->persist($group);

        $organization = new Organization($group);
        $this->entityManager->persist($organization);

        $project = new Project($group);
        $this->entityManager->persist($project);

        $resources = [
            $resource1 = new Resource(null, [], $organization, $project),
            $resource2 = new Resource(null, [], $organization, $project),
            $resource3 = new Resource(null, [], $organization, $project),
        ];

        foreach ($resources as $resource) {
            $this->entityManager->persist($resource);
        }

        $groups = [
            $group1 = new Group(),
            $group2 = new Group(),
        ];

        foreach ($groups as $group) {
            $this->entityManager->persist($group);
        }

        $group1
            ->addResource($resource1)
            ->addResource($resource2);

        $group2
            ->addResource($resource2)
            ->addResource($resource3);

        $user = new User(null, $groups);
        $this->entityManager->persist($user);

        $this->entityManager->flush();

        $persistedResourcesIds = array_map(
            function ($resource) {
                return $resource->getId();
            },
            $resources
        );

        $availableResourcesIds = $this->repository->getAllUserResources($user->getId());

        $this->assertEquals($persistedResourcesIds, $availableResourcesIds);
    }

    public function testIsResourceAvailableForUser()
    {
        $group = new Group(null, []);
        $this->entityManager->persist($group);

        $organization = new Organization($group);
        $this->entityManager->persist($organization);

        $project = new Project($group);
        $this->entityManager->persist($project);

        $resources = [
            $resource1 = new Resource(null, [], $organization, $project),
            $resource2 = new Resource(null, [], $organization, $project),
            $resource3 = new Resource(null, [], $organization, $project),
        ];

        foreach ($resources as $resource) {
            $this->entityManager->persist($resource);
        }

        $groups = [
            $group1 = new Group(),
            $group2 = new Group(),
        ];

        foreach ($groups as $group) {
            $this->entityManager->persist($group);
        }

        $group1
            ->addResource($resource1)
            ->addResource($resource2);

        $group2
            ->addResource($resource2)
            ->addResource($resource3);

        $user = new User(null, $groups);
        $this->entityManager->persist($user);

        $this->entityManager->flush();

        $this->assertTrue($this->repository->isResourceAvailableForUser($user->getId(), $resource1->getId()));

        /** @var ResourceRepository $resourceRepository */
        $resourceRepository = $this->entityManager->getRepository(Resource::class);
        $resourceRepository->remove($resource1);

        $this->assertFalse($this->repository->isResourceAvailableForUser($user->getId(), $resource1->getId()));

        // Also check if we use not existing $resourceId
        $this->assertFalse($this->repository->isResourceAvailableForUser($user->getId(), 'resourceId'));
    }

    protected function getEntityFQCN(): string
    {
        return User::class;
    }
}
