<?php

namespace Mygento\AccessControlBundle\Core\Domain\Service;

class AccessControlManager
{
    private SecurityVoter $securityVoter;

    public function __construct(
        SecurityVoter $securityVoter
    ) {
        $this->securityVoter =  $securityVoter;
    }

    public function isGranted($resourceId, $userId = null): bool
    {
        return $this->securityVoter->isGranted($resourceId, $userId);
    }

    public function createUser()
    {
    }
}
