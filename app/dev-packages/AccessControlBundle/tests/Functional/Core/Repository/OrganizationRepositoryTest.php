<?php

namespace Mygento\AccessControlBundle\Tests\Functional\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\Organization;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Mygento\AccessControlBundle\Core\Repository\GroupRepository;
use Mygento\AccessControlBundle\Core\Repository\OrganizationRepository;
use Mygento\AccessControlBundle\Core\Repository\ResourceRepository;

class OrganizationRepositoryTest extends BaseRepositoryTestCase
{
    /** @var OrganizationRepository */
    protected ?ServiceEntityRepository $repository;

    protected GroupRepository $groupRepository;

    protected ResourceRepository $resourceRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->groupRepository = $this->entityManager->getRepository(Group::class);

        $this->resourceRepository = $this->entityManager->getRepository(Resource::class);
    }

    public function testCascadeOperations()
    {
        $name = new Name('Example');

        $group = new Group($name);

        $resources = [
            $resource = new Resource(),
        ];

        $organization = new Organization($name, $group, $resources);

        // Check that group and resources related to organization, also have been persisted (by cascade)
        $this->repository->save($organization);

        // Check that organization was successfully persisted
        $this->assertEquals($name, $organization->getName());
        $this->assertEquals($group->getName(), $organization->getGroup()->getName());
        $this->assertCount(1, $organization->getResources());

        // Save entities id and clear entity manager
        $organizationId = $organization->getId();
        $groupId = $group->getId();
        $resourceId = $resource->getId();
        $this->entityManager->clear();

        // Remove organization
        $this->repository->remove($organizationId);

        // Check that organization was successfully removed
        try {
            $this->repository->findById($organizationId);
        } catch (\DomainException $exception) {
            $this->assertEquals(
                Organization::class.' with ID "'.$organizationId.'" was not found!',
                $exception->getMessage()
            );
        }

        // Check that group, related to organization also was removed
        try {
            $this->groupRepository->findById($groupId);
        } catch (\DomainException $exception) {
            $this->assertEquals(
                Group::class.' with ID "'.$groupId.'" was not found!',
                $exception->getMessage()
            );
        }

        // Check that resource still in database
        $resource = $this->resourceRepository->findById($resourceId);

        $this->assertEquals(null, $resource->getOrganization());
    }

    protected function getEntityFQCN(): string
    {
        return Organization::class;
    }
}
