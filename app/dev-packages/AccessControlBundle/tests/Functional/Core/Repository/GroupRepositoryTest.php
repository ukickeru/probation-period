<?php

namespace Mygento\AccessControlBundle\Tests\Functional\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\Organization;
use Mygento\AccessControlBundle\Core\Domain\Entity\Project;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;
use Mygento\AccessControlBundle\Core\Repository\GroupRepository;
use Mygento\AccessControlBundle\Core\Repository\ResourceRepository;
use Mygento\AccessControlBundle\Core\Repository\UserRepository;

class GroupRepositoryTest extends BaseRepositoryTestCase
{
    /** @var GroupRepository */
    protected ?ServiceEntityRepository $repository;

    public function testGetAllGroupUserIds()
    {
        $users = [
            $user1 = new User(),
            $user2 = new User(),
            $user3 = new User(),
        ];

        foreach ($users as $user) {
            $this->entityManager->persist($user);
        }

        $group = new Group(null, $users);
        $this->entityManager->persist($group);

        $this->entityManager->flush();

        $persistedUserIds = array_map(
            function ($user) {
                return $user->getId();
            },
            $users
        );

        $availableUserIds = $this->repository->getAllGroupUserIds($group->getId());

        $this->assertEquals($persistedUserIds, $availableUserIds);

        /** @var UserRepository $resourceRepository */
        $resourceRepository = $this->entityManager->getRepository(User::class);
        $resourceRepository->remove($user1);

        $this->assertCount(2, $this->repository->getAllGroupUserIds($group->getId()));
    }

    public function testGetAllGroupResourceIds()
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
            $group->addResource($resource);
            $this->entityManager->persist($resource);
        }

        $this->entityManager->flush();

        $persistedResourceIds = array_map(
            function ($resource) {
                return $resource->getId();
            },
            $resources
        );

        $availableResourceIds = $this->repository->getAllGroupResourceIds($group->getId());

        $this->assertEquals($persistedResourceIds, $availableResourceIds);

        /** @var ResourceRepository $resourceRepository */
        $resourceRepository = $this->entityManager->getRepository(Resource::class);
        $resourceRepository->remove($resource1);

        $this->assertCount(2, $this->repository->getAllGroupResourceIds($group->getId()));
    }

    protected function getEntityFQCN(): string
    {
        return Group::class;
    }
}
