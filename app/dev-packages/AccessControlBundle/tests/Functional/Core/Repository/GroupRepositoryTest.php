<?php

namespace Mygento\AccessControlBundle\Tests\Functional\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\Organization;
use Mygento\AccessControlBundle\Core\Domain\Entity\Project;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Mygento\AccessControlBundle\Core\Repository\GroupRepository;
use Mygento\AccessControlBundle\Core\Repository\ResourceRepository;

class GroupRepositoryTest extends BaseRepositoryTestCase
{
    /** @var GroupRepository */
    protected ?ServiceEntityRepository $repository;

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
                return $resource->getId()->value();
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
