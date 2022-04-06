<?php

namespace Mygento\AccessControlBundle\Tests\Functional\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\Organization;
use Mygento\AccessControlBundle\Core\Domain\Entity\Project;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Mygento\AccessControlBundle\Core\Repository\GroupRepository;
use Mygento\AccessControlBundle\Core\Repository\ResourceRepository;
use Mygento\AccessControlBundle\Core\Repository\UserRepository;

class UserRepositoryTest extends BaseRepositoryTestCase
{
    /** @var UserRepository */
    protected ?ServiceEntityRepository $repository;

    protected ?GroupRepository $groupRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->groupRepository = $this->entityManager->getRepository(Group::class);
    }

    public function testCreationAndUpdating()
    {
        $name = new Name('Example');

        $user = new User($name);

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
        $name = new Name('Example');

        $user = new User($name);

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

    public function testGetAllUserIds()
    {
        $name = new Name('Example');
        for ($i = 0; $i < 5; ++$i) {
            $user = new User($name);
            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();

        $this->assertCount(5, $this->repository->getAllUsersId());
    }

    public function testIsResourceAvailableForUser()
    {
        $name = new Name('Example');

        $group = new Group($name);
        $this->entityManager->persist($group);

        $organization = new Organization($name, $group);
        $this->entityManager->persist($organization);

        $project = new Project($name, $group);
        $this->entityManager->persist($project);

        $resources = [
            $resource1 = new Resource([], $organization, $project),
            $resource2 = new Resource([], $organization, $project),
            $resource3 = new Resource([], $organization, $project),
        ];

        foreach ($resources as $resource) {
            $this->entityManager->persist($resource);
        }

        $groups = [
            $group1 = new Group($name),
            $group2 = new Group($name),
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

        $user = new User($name, $groups);
        $this->entityManager->persist($user);

        $this->entityManager->flush();

        $this->assertTrue($this->repository->isResourceAvailableForUser($user->getId(), $resource1->getId()));

        /** @var ResourceRepository $resourceRepository */
        $resourceRepository = $this->entityManager->getRepository(Resource::class);
        $resourceRepository->remove($resource1);

        $this->assertFalse($this->repository->isResourceAvailableForUser($user->getId(), $resource1->getId()));

        // Also check if we use not existing $resourceId
        $this->assertFalse($this->repository->isResourceAvailableForUser($user->getId(), $resource1->getId()));
    }

    public function testGetResourcesIdsAvailableForUser()
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
            $group1 = new Group($name),
            $group2 = new Group($name),
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

        $user = new User($name, $groups);
        $this->entityManager->persist($user);

        $this->entityManager->flush();

        $persistedResourcesIds = array_map(
            function ($resource) {
                return $resource->getId()->value();
            },
            $resources
        );

        $availableResourcesIds = $this->repository->getResourcesIdAvailableForUser($user->getId());

        $this->assertEquals($persistedResourcesIds, $availableResourcesIds);
    }

    public function testGetACL()
    {
        $name = new Name('Example');

        $user1 = new User($name);
        $user2 = new User($name);
        $user3 = new User($name);

        $resource1 = new Resource();
        $resource2 = new Resource();
        $resource3 = new Resource();

        $group1 = new Group($name, [$user1, $user2], [$resource1, $resource2]);
        $group2 = new Group($name, [$user2, $user3], [$resource2]);
        $group3 = new Group($name, [$user3], [$resource3]);

        $this->groupRepository->save($group1);
        $this->groupRepository->save($group2);
        $this->groupRepository->save($group3);

        $expectedACL = [
            [$user1->getId()->value(), $resource1->getId()->value()],
            [$user1->getId()->value(), $resource2->getId()->value()],
            [$user2->getId()->value(), $resource1->getId()->value()],
            [$user2->getId()->value(), $resource2->getId()->value()],
            [$user3->getId()->value(), $resource2->getId()->value()],
            [$user3->getId()->value(), $resource3->getId()->value()],
        ];

        sort($expectedACL);

        $foundACL = $this->repository->getACL();

        $this->assertEquals($expectedACL, $foundACL);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->groupRepository = null;
    }

    protected function getEntityFQCN(): string
    {
        return User::class;
    }
}
