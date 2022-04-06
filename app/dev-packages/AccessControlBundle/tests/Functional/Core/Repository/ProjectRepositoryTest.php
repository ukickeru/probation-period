<?php

namespace Mygento\AccessControlBundle\Tests\Functional\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\Project;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Mygento\AccessControlBundle\Core\Repository\GroupRepository;
use Mygento\AccessControlBundle\Core\Repository\ProjectRepository;
use Mygento\AccessControlBundle\Core\Repository\ResourceRepository;

class ProjectRepositoryTest extends BaseRepositoryTestCase
{
    /** @var ProjectRepository */
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

        $project = new Project($name, $group, $resources);

        // Check that group and resources related to project, also have been persisted (by cascade)
        $this->repository->save($project);

        // Check that project was successfully persisted
        $this->assertEquals($name, $project->getName());
        $this->assertEquals($group->getName(), $project->getGroup()->getName());
        $this->assertCount(1, $project->getResources());

        // Save entities id and clear entity manager
        $projectId = $project->getId();
        $groupId = $group->getId();
        $resourceId = $resource->getId();
        $this->entityManager->clear();

        // Remove project
        $this->repository->remove($projectId);

        // Check that project was successfully removed
        try {
            $this->repository->findById($projectId);
        } catch (\DomainException $exception) {
            $this->assertEquals(
                Project::class.' with ID "'.$projectId.'" was not found!',
                $exception->getMessage()
            );
        }

        // Check that group, related to project also was removed
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
        return Project::class;
    }
}
