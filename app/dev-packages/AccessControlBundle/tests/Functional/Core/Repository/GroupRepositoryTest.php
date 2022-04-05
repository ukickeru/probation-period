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

class GroupRepositoryTest extends BaseRepositoryTestCase
{
    /** @var GroupRepository */
    protected ?ServiceEntityRepository $repository;

    public function testGetAllGroupUserIds()
    {
        $name = new Name('Example');

        $users = [
            $user1 = new User($name),
            $user2 = new User($name),
            $user3 = new User($name),
        ];

        foreach ($users as $user) {
            $this->entityManager->persist($user);
        }

        $group = new Group($name, $users);
        $this->entityManager->persist($group);

        $this->entityManager->flush();

        $persistedUserIds = array_map(
            function ($user) {
                return $user->getId();
            },
            $users
        );

        $availableUserIds = $this->repository->getAllGroupUsersId($group->getId());

        $this->assertEquals($persistedUserIds, $availableUserIds);

        /** @var UserRepository $resourceRepository */
        $resourceRepository = $this->entityManager->getRepository(User::class);
        $resourceRepository->remove($user1);

        $this->assertCount(2, $this->repository->getAllGroupUsersId($group->getId()));
    }

    public function testGetAllGroupResourceIds()
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

        $availableResourceIds = $this->repository->getAllGroupResourcesId($group->getId());

        $this->assertEquals($persistedResourceIds, $availableResourceIds);

        /** @var ResourceRepository $resourceRepository */
        $resourceRepository = $this->entityManager->getRepository(Resource::class);
        $resourceRepository->remove($resource1);

        $this->assertCount(2, $this->repository->getAllGroupResourcesId($group->getId()));
    }

    protected function getEntityFQCN(): string
    {
        return Group::class;
    }
}
