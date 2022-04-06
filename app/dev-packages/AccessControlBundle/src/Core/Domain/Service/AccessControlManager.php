<?php

namespace Mygento\AccessControlBundle\Core\Domain\Service;

use Mygento\AccessControlBundle\ACL\Domain\Service\ACESynchronizer;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\Organization;
use Mygento\AccessControlBundle\Core\Domain\Entity\Project;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Mygento\AccessControlBundle\Core\Repository\GroupRepository;
use Mygento\AccessControlBundle\Core\Repository\OrganizationRepository;
use Mygento\AccessControlBundle\Core\Repository\ProjectRepository;
use Mygento\AccessControlBundle\Core\Repository\ResourceRepository;
use Mygento\AccessControlBundle\Core\Repository\UserRepository;

class AccessControlManager
{
    private SecurityVoter $securityVoter;

    private ACESynchronizer $ACESynchronizer;

    private UserRepository $userRepository;

    private GroupRepository $groupRepository;

    private ResourceRepository $resourceRepository;

    private OrganizationRepository $organizationRepository;

    private ProjectRepository $projectRepository;

    public function __construct(
        SecurityVoter $securityVoter,
        ACESynchronizer $ACESynchronizer,
        UserRepository $userRepository,
        GroupRepository $groupRepository,
        ResourceRepository $resourceRepository,
        OrganizationRepository $organizationRepository,
        ProjectRepository $projectRepository
    ) {
        $this->securityVoter = $securityVoter;
        $this->ACESynchronizer = $ACESynchronizer;
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
        $this->resourceRepository = $resourceRepository;
        $this->organizationRepository = $organizationRepository;
        $this->projectRepository = $projectRepository;
    }

    public function isGranted(Id $resourceId, Id $userId = null): bool
    {
        return $this->securityVoter->isGranted($resourceId, $userId);
    }

    // todo: составить метод для проверки ACL по классификационным признакам (id организации, проекта и т.д. и т.п.)
    // public function isGrantedByClassificationSigns(ClassificationSignInterface ...$classificationSigns)

    public function createUser(Name $name, array $groups = []): User
    {
        $user = new User($name, $groups);

        $this->userRepository->save($user);

        $this->ACESynchronizer->synchronizeACEForUser($user->getId());

        return $user;
    }

    public function editUser(Id $id, Name $name, array $groups = []): User
    {
        $user = $this->userRepository->findById($id);

        $user->setName($name);

        $userGroups = $user->getGroups();

        foreach ($groups as $group) {
            $user->addGroup($group);
        }

        foreach ($userGroups as $group) {
            if (!in_array($group, $groups)) {
                $user->removeGroup($group);
            }
        }

        $this->userRepository->save($user);

        $this->ACESynchronizer->synchronizeACEForUser($user->getId());

        return $user;
    }

    public function removeUser(Id $id): void
    {
        $this->userRepository->remove($id);
    }

    public function createGroup(Name $name, array $users = [], array $groups = []): Group
    {
        $group = new Group($name, $users, $groups);

        $this->groupRepository->save($group);

        $this->ACESynchronizer->synchronizeACEGlobally();

        return $group;
    }

    public function editGroup(Id $id, Name $name, array $users = [], array $resources = []): Group
    {
        $group = $this->groupRepository->findById($id);

        $group->setName($name);

        $groupUsers = $group->getUsers();

        foreach ($users as $user) {
            $group->addUser($user);
        }

        foreach ($groupUsers as $user) {
            if (!in_array($user, $users)) {
                $group->removeUser($user);
            }
        }

        $groupResources = $group->getResources();

        foreach ($resources as $resource) {
            $group->addResource($resource);
        }

        foreach ($groupResources as $resource) {
            if (!in_array($resource, $resources)) {
                $group->removeResource($resource);
            }
        }

        $this->groupRepository->save($group);

        $this->ACESynchronizer->synchronizeACEGlobally();

        return $group;
    }

    public function removeGroup(Id $id): void
    {
        $this->groupRepository->remove($id);
    }

    public function createResource(array $groups = [], ?Organization $organization = null, ?Project $project = null): Resource
    {
        $resource = new Resource($groups, $organization, $project);

        $this->resourceRepository->save($resource);

        $this->ACESynchronizer->synchronizeACEGlobally();

        return $resource;
    }

    public function editResource(Id $id, array $groups = []): Resource
    {
        $resource = $this->resourceRepository->findById($id);

        $resourceGroups = $resource->getGroups();

        foreach ($groups as $group) {
            $resource->addGroup($group);
        }

        foreach ($resourceGroups as $group) {
            if (!in_array($group, $groups)) {
                $resource->removeGroup($group);
            }
        }

        $this->resourceRepository->save($resource);

        $this->ACESynchronizer->synchronizeACEGlobally();

        return $resource;
    }

    public function removeResource(Id $id): void
    {
        $this->resourceRepository->remove($id);
    }

    /**
     * @todo Здесь и далее в подобные методы можно добавить, например, добавление пользователей в создаваемую группу и т.п.
     */
    public function createProject(Name $name): Project
    {
        $group = new Group($name);
        $project = new Project($name, $group);
        $this->projectRepository->save($project);

        return $project;
    }

    public function editProject(Id $id, Name $name): Project
    {
        $project = $this->projectRepository->findById($id);

        $project->setName($name);
        $this->projectRepository->save($project);

        return $project;
    }

    public function removeProject(Id $id): void
    {
        $this->projectRepository->remove($id);
    }

    public function createOrganization(Name $name): Organization
    {
        $group = new Group($name);
        $organization = new Organization($name, $group);
        $this->organizationRepository->save($organization);

        return $organization;
    }

    public function editOrganization(Id $id, Name $name): Organization
    {
        $organization = $this->organizationRepository->findById($id);

        $organization->setName($name);
        $this->organizationRepository->save($organization);

        return $organization;
    }

    public function removeOrganization(Id $id): void
    {
        $this->organizationRepository->remove($id);
    }

    public function synchronizeACEGlobally(): void
    {
        $this->ACESynchronizer->synchronizeACEGlobally();
    }

    public function synchronizeACEForUser(Id $userId): void
    {
        $this->ACESynchronizer->synchronizeACEForUser($userId);
    }
}
