<?php

namespace Mygento\AccessControlBundle\Core\Domain\Service;

use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SecurityVoter
{
    private AccessControlCheckerInterface $accessControlChecker;

    private UserProviderInterface $userProvider;

    private ?CustomAccessControlCheckerInterface $customAccessControlChecker;

    public function __construct(
        AccessControlCheckerInterface $accessControlChecker,
        UserProviderInterface $userProvider,
        ?CustomAccessControlCheckerInterface $resourceProvider = null
    ) {
        $this->accessControlChecker = $accessControlChecker;
        $this->userProvider = $userProvider;
        $this->customAccessControlChecker = $resourceProvider;
    }

    /**
     * Checks if user has access to specified resource.
     *
     * @throws AccessDeniedException if authorized user doesn't have access to requested resource
     */
    public function isGranted(Id $resourceId, Id $userId = null): bool
    {
        if (null === $userId) {
            $userId = $this->getUserIdentity();
        }

        if (!$this->accessControlChecker->isResourceAvailableForUser($userId, $resourceId)) {
            throw new AccessDeniedException();
        }

        return true;
    }

    /**
     * Checks if user has access to resource, specified by abstract criteria.
     *
     * @param mixed $criteria
     */
    public function isGrantedByCriteria($criteria, Id $userId = null): bool
    {
        if (null === $this->customAccessControlChecker) {
            throw new \LogicException('Please, implement '.CustomAccessControlCheckerInterface::class.'.');
        }

        if (null === $userId) {
            $userId = $this->getUserIdentity();
        }

        if (!$this->customAccessControlChecker->isResourceAvailableForUserByCriteria($userId, $criteria)) {
            throw new AccessDeniedException();
        }

        return true;
    }

    private function getUserIdentity(): Id
    {
        $user = $this->userProvider->getUser();

        if (null === $user->getId()) {
            throw new \DomainException('Can not check access for user with empty ID! Please provide the user ID beforehand.');
        }

        return $user->getId();
    }
}
