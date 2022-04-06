<?php

namespace Mygento\AccessControlBundle\Tests\Functional\Core\Domain\Service;

use Doctrine\ORM\EntityManagerInterface;
use Mygento\AccessControlBundle\ACL\Domain\Entity\ACE;
use Mygento\AccessControlBundle\ACL\Repository\ACERepository;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\Organization;
use Mygento\AccessControlBundle\Core\Domain\Entity\Project;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;
use Mygento\AccessControlBundle\Core\Domain\Service\AccessControlManager;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Mygento\AccessControlBundle\Core\Repository\GroupRepository;
use Mygento\AccessControlBundle\Core\Repository\OrganizationRepository;
use Mygento\AccessControlBundle\Core\Repository\ProjectRepository;
use Mygento\AccessControlBundle\Core\Repository\ResourceRepository;
use Mygento\AccessControlBundle\Core\Repository\UserRepository;
use Mygento\AccessControlBundle\Tests\Functional\Core\Repository\SchemaRecreationTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AccessControlManagerTest extends KernelTestCase
{
    use SchemaRecreationTrait;

    private ?EntityManagerInterface $entityManager;

    private AccessControlManager $accessControlManager;

    private UserRepository $userRepository;

    private GroupRepository $groupRepository;

    private ResourceRepository $resourceRepository;

    private OrganizationRepository $organizationRepository;

    private ProjectRepository $projectRepository;

    private ACERepository $ACERepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->accessControlManager = $kernel->getContainer()
            ->get('mygento.access_control.manager');

        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->groupRepository = $this->entityManager->getRepository(Group::class);
        $this->resourceRepository = $this->entityManager->getRepository(Resource::class);
        $this->organizationRepository = $this->entityManager->getRepository(Organization::class);
        $this->projectRepository = $this->entityManager->getRepository(Project::class);
        $this->ACERepository = $this->entityManager->getRepository(ACE::class);

        $this->recreateSchema($this->entityManager);
    }

    public function testCreateUser()
    {
        $name = new Name('Example');

        $groups = [
            new Group($name),
        ];

        $user = $this->accessControlManager->createUser($name, $groups);

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->getId());
        $this->assertCount(1, $user->getGroups());
    }

    public function testEditUser()
    {
        $name = new Name('Example');

        $groups = [
            new Group($name),
            new Group($name),
        ];

        $user = $this->accessControlManager->createUser($name, $groups);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($name, $user->getName());
        $this->assertCount(2, $user->getGroups());

        $newName = new Name('Example 2');
        $newGroup = new Group($newName);

        $user = $this->accessControlManager->editUser($user->getId(), $newName, [$newGroup]);

        $this->assertEquals($newName, $user->getName());
        $this->assertCount(1, $user->getGroups());
        $this->assertEquals($newName, $user->getGroups()->first()->getName());
    }

    public function testRemoveUser()
    {
        $name = new Name('Example');

        $groups = [
            $group = new Group($name),
        ];

        $user = $this->accessControlManager->createUser($name, $groups);

        $this->assertInstanceOf(User::class, $user);
        $this->assertCount(1, $user->getGroups());

        $userId = $user->getId();
        $groupId = $group->getId();
        $user = null;
        $group = null;
        $this->entityManager->clear();

        $this->accessControlManager->removeUser($userId);

        try {
            $this->userRepository->findById($userId);
        } catch (\DomainException $exception) {
            $this->assertEquals(
                User::class.' with ID "'.$userId.'" was not found!',
                $exception->getMessage()
            );
        }

        $group = $this->groupRepository->findById($groupId);
        $this->assertInstanceOf(Group::class, $group);
    }

    public function testCreateGroup()
    {
        $name = new Name('Example');

        $users = [
            new User($name),
        ];

        $resources = [
            new Resource(),
            new Resource(),
        ];

        $group = $this->accessControlManager->createGroup($name, $users, $resources);

        $this->assertInstanceOf(Group::class, $group);
        $this->assertNotNull($group->getId());
        $this->assertCount(1, $group->getUsers());
        $this->assertCount(2, $group->getResources());
    }

    public function testEditGroup()
    {
        $name = new Name('Example');

        $users = [
            $user = new User($name),
        ];

        $resources = [
            new Resource(),
            new Resource(),
        ];

        $group = $this->accessControlManager->createGroup($name, $users, $resources);

        $this->assertInstanceOf(Group::class, $group);
        $this->assertCount(1, $group->getUsers());
        $this->assertCount(2, $group->getResources());

        $newName = new Name('Example 2');
        $newUser = new User($newName);
        $newResource = new Resource();

        $group = $this->accessControlManager->editGroup($group->getId(), $newName, [$newUser], [$newResource]);

        $this->assertEquals($newName, $group->getName());
        $this->assertCount(1, $group->getUsers());
        $this->assertEquals($newName, $group->getUsers()->first()->getName());
        $this->assertCount(1, $group->getResources());
    }

    public function testRemoveGroup()
    {
        $name = new Name('Example');

        $users = [
            new User($name),
        ];

        $resources = [
            new Resource(),
            new Resource(),
        ];

        $group = $this->accessControlManager->createGroup($name, $users, $resources);

        $this->assertInstanceOf(Group::class, $group);
        $this->assertCount(1, $group->getUsers());
        $this->assertCount(2, $group->getResources());

        $groupId = $group->getId();
        $userId = $group->getUsers()->first()->getId();
        $resourceId = $group->getResources()->first()->getId();
        $group = null;
        $this->entityManager->clear();

        $this->accessControlManager->removeGroup($groupId);

        try {
            $this->groupRepository->findById($groupId);
        } catch (\DomainException $exception) {
            $this->assertEquals(
                Group::class.' with ID "'.$userId.'" was not found!',
                $exception->getMessage()
            );
        }

        $user = $this->userRepository->findById($userId);
        $this->assertInstanceOf(User::class, $user);

        $resource = $this->resourceRepository->findById($resourceId);
        $this->assertInstanceOf(Resource::class, $resource);
    }

    public function testCreateResource()
    {
        $resource = $this->accessControlManager->createResource();

        $this->assertInstanceOf(Resource::class, $resource);
        $this->assertNotNull($resource->getId());

        $name = new Name('Example');

        $groups = [
            $group = new Group($name),
        ];
        $this->groupRepository->save($group);

        $organization = new Organization($name, $group);
        $this->organizationRepository->save($organization);

        $project = new Project($name, $group);
        $this->projectRepository->save($project);

        // Check resource creation with already existing related entities
        $resource = $this->accessControlManager->createResource($groups, $organization, $project);

        $this->assertInstanceOf(Resource::class, $resource);
        $this->assertNotNull($resource->getId());
        $this->assertCount(1, $resource->getGroups());
        $this->assertEquals($name, $resource->getOrganization()->getName());
        $this->assertEquals($name, $resource->getProject()->getName());
    }

    public function testEditResource()
    {
        $name = new Name('Example');

        $groups = [
            $group = new Group($name),
        ];
        $this->groupRepository->save($group);

        $organization = new Organization($name, $group);
        $this->organizationRepository->save($organization);

        $project = new Project($name, $group);
        $this->projectRepository->save($project);

        $resource = $this->accessControlManager->createResource($groups, $organization, $project);

        $this->assertInstanceOf(Resource::class, $resource);
        $this->assertNotNull($resource->getId());
        $this->assertCount(1, $resource->getGroups());
        $this->assertEquals($name, $resource->getOrganization()->getName());
        $this->assertEquals($name, $resource->getProject()->getName());

        $newName = new Name('Example 2');
        $newGroups = [
            $group1 = new Group($newName),
            $group2 = new Group($newName),
        ];

        $this->groupRepository->save($group1);
        $this->groupRepository->save($group2);

        $resource = $this->accessControlManager->editResource($resource->getId(), $newGroups);

        $this->assertInstanceOf(Resource::class, $resource);
        $this->assertCount(2, $resource->getGroups());
        $this->assertEquals($newName, $resource->getGroups()->first()->getName());
    }

    public function testRemoveResource()
    {
        $resource = $this->accessControlManager->createResource();

        $this->assertInstanceOf(Resource::class, $resource);
        $this->assertNotNull($resource->getId());

        $resourceId = $resource->getId();
        $resource = null;
        $this->entityManager->clear();

        $this->accessControlManager->removeResource($resourceId);

        try {
            $this->resourceRepository->findById($resourceId);
        } catch (\DomainException $exception) {
            $this->assertEquals(
                Resource::class.' with ID "'.$resourceId.'" was not found!',
                $exception->getMessage()
            );
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // to avoid memory leaks
        $this->dropSchema($this->entityManager);
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
