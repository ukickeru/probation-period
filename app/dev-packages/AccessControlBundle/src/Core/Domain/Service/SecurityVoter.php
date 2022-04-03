<?php

namespace Mygento\AccessControlBundle\Core\Domain\Service;

use Mygento\AccessControlBundle\Core\Domain\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class SecurityVoter
{
    private AccessControlCheckerInterface $accessControlChecker;

    private Security $security;

    public function __construct(
        AccessControlCheckerInterface $accessControlChecker,
        Security $userProvider
    ) {
        $this->accessControlChecker = $accessControlChecker;
        $this->security = $userProvider;
    }

    /**
     * @param scalar $resourceId
     * @param null $userId
     * @return bool
     * @throws AccessDeniedException if authorized user doesn't have an access to requested resource
     */
    public function isGranted($resourceId, $userId = null): bool
    {
        if ($userId === null) {
            $userId = $this->security->getUser()->getUserIdentifier();
        }

        if (!$this->accessControlChecker->isResourceAvailableForUser($userId, $resourceId)) {
            throw new AccessDeniedException($userId, $resourceId);
        }

        return true;
    }
}
