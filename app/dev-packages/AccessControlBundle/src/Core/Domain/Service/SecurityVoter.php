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
     * Checks if user has access to specified resource.
     *
     * @param scalar $resourceId
     * @param null   $userId     retrieved automatically by @Symfony\Security if not specified
     *
     * @throws AccessDeniedException if authorized user doesn't have access to requested resource
     */
    public function isGranted($resourceId, $userId = null): bool
    {
        if (null === $userId) {
            $userId = $this->security->getUser()->getUserIdentifier();
        }

        if (!$this->accessControlChecker->isResourceAvailableForUser($userId, $resourceId)) {
            throw new AccessDeniedException($userId, $resourceId);
        }

        return true;
    }
}
