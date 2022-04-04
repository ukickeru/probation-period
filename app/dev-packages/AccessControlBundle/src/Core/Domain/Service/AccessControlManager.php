<?php

namespace Mygento\AccessControlBundle\Core\Domain\Service;

use Mygento\AccessControlBundle\ACL\Domain\ACESynchronizer;

class AccessControlManager
{
    private SecurityVoter $securityVoter;

    private ACESynchronizer $ACESynchronizer;

    public function __construct(
        SecurityVoter $securityVoter,
        ACESynchronizer $ACESynchronizer
    ) {
        $this->securityVoter = $securityVoter;
        $this->ACESynchronizer = $ACESynchronizer;
    }

    public function isGranted($resourceId, $userId = null): bool
    {
        return $this->securityVoter->isGranted($resourceId, $userId);
    }

    // createUser($groups)
    // editUser($groups)
    // removeUser($id)

    // createGroup($users, $resources)
    // editGroup($id, $users, $resources)
    // removeGroup($id)

    // createResource(Organization $organization, Project $project, $groups)
    // editResource($id, Organization $organization, Project $project, $groups)
    // removeResource($id)

    // createProject
    // editProject
    // removeProject

    // createOrganization
    // editOrganization
    // removeOrganization

    public function synchronizeACEGlobally(): void
    {
        $this->ACESynchronizer->synchronizeACEGlobally();
    }

    public function synchronizeACEForUser($userId): void
    {
        $this->ACESynchronizer->synchronizeACEForUser($userId);
    }

    public function synchronizeACEForGroup($groupId): void
    {
        $this->ACESynchronizer->synchronizeACEForGroup($groupId);
    }

    public function synchronizeACEForResource($resourceId): void
    {
        $this->ACESynchronizer->synchronizeACEForGroup($resourceId);
    }
}
