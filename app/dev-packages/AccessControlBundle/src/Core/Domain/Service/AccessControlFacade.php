<?php

namespace Mygento\AccessControlBundle\Core\Domain\Service;

use Mygento\AccessControlBundle\ACL\Domain\Service\ACLSynchronizer;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\Organization;
use Mygento\AccessControlBundle\Core\Domain\Entity\Project;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;
use Mygento\AccessControlBundle\Core\Repository\GroupRepository;
use Mygento\AccessControlBundle\Core\Repository\OrganizationRepository;
use Mygento\AccessControlBundle\Core\Repository\ProjectRepository;
use Mygento\AccessControlBundle\Core\Repository\ResourceRepository;
use Mygento\AccessControlBundle\Core\Repository\UserRepository;

class AccessControlFacade
{
    private SecurityVoter $securityVoter;

    private ACLSynchronizer $ACLSynchronizer;

    private UserRepository $userRepository;

    private GroupRepository $groupRepository;

    private ResourceRepository $resourceRepository;

    private OrganizationRepository $organizationRepository;

    private ProjectRepository $projectRepository;

    public function __construct(
        SecurityVoter $securityVoter,
        ACLSynchronizer $ACESynchronizer,
        UserRepository $userRepository,
        GroupRepository $groupRepository,
        ResourceRepository $resourceRepository,
        OrganizationRepository $organizationRepository,
        ProjectRepository $projectRepository
    ) {
        $this->securityVoter = $securityVoter;
        $this->ACLSynchronizer = $ACESynchronizer;
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

    public function synchronizeACL(): void
    {
        $this->ACLSynchronizer->synchronize();
    }

    public function createUser(User $user): void
    {
        $this->userRepository->save($user);
    }

    public function editUser(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * @param User|Id $user
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeUser($user): void
    {
        $this->userRepository->remove($user);
    }

    public function createGroup(Group $group): void
    {
        $this->groupRepository->save($group);
    }

    public function editGroup(Group $group): void
    {
        $this->groupRepository->save($group);
    }

    /**
     * @param Group|Id $group
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeGroup($group): void
    {
        $this->groupRepository->remove($group);
    }

    public function createResource(Resource $resource): void
    {
        $this->resourceRepository->save($resource);
    }

    public function editResource(Resource $resource): void
    {
        $this->resourceRepository->save($resource);
    }

    /**
     * @param \Mygento\AccessControlBundle\Core\Domain\Entity\Resource|Id $resource
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeResource($resource): void
    {
        $this->resourceRepository->remove($resource);
    }

    public function createProject(Project $project): void
    {
        $this->projectRepository->save($project);
    }

    public function editProject(Project $project): void
    {
        $this->projectRepository->save($project);
    }

    /**
     * @param Project|Id $project
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeProject($project): void
    {
        $this->projectRepository->remove($project);
    }

    public function createOrganization(Organization $organization): void
    {
        $this->organizationRepository->save($organization);
    }

    public function editOrganization(Organization $organization): void
    {
        $this->organizationRepository->save($organization);
    }

    /**
     * @param Organization|Id $organization
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeOrganization($organization): void
    {
        $this->organizationRepository->remove($organization);
    }
}
