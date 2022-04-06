<?php

namespace Mygento\AccessControlBundle\ACL\Domain\Service;

use Mygento\AccessControlBundle\ACL\Repository\ACERepository;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;
use Mygento\AccessControlBundle\Core\Repository\UserRepository;

class ACESynchronizer
{
    private ACERepository $ACERepository;

    private UserRepository $userRepository;

    public function __construct(
        ACERepository $ACERepository,
        UserRepository $userRepository
    ) {
        $this->ACERepository = $ACERepository;
        $this->userRepository = $userRepository;
    }

    public function synchronizeACEGlobally(): void
    {
        $ACL = $this->userRepository->getACL();

        $ACEs = new ACEsCollection($ACL);

        $this->ACERepository->updateACLGlobally($ACEs);
    }

    public function synchronizeACEForUser(Id $userId): void
    {
        $resourcesIdAlreadyAvailableForUser = $this->userRepository->getResourcesIdAvailableForUser($userId);

        $resourcesIdAlreadyAvailableForUserByACE = $this->ACERepository->getResourcesIdAvailableForUser($userId);

        $resourcesIdToCreateACE = array_diff($resourcesIdAlreadyAvailableForUser, $resourcesIdAlreadyAvailableForUserByACE);

        $resourcesIdToRemoveACE = array_diff($resourcesIdAlreadyAvailableForUserByACE, $resourcesIdAlreadyAvailableForUser);

        $ACEsToCreate = new ACEsCollection();
        foreach ($resourcesIdToCreateACE as $resourceId) {
            $ACEsToCreate->addACE([$userId, $resourceId]);
        }

        $ACEsToRemove = new ACEsCollection();
        foreach ($resourcesIdToRemoveACE as $resourceId) {
            $ACEsToRemove->addACE([$userId, $resourceId]);
        }

        if (count($ACEsToCreate) > 0 && count($ACEsToRemove) > 0) {
            $this->ACERepository->updateACEs($ACEsToCreate, $ACEsToRemove);

            return;
        }

        if (count($ACEsToCreate) > 0) {
            $this->ACERepository->insertACEs($ACEsToCreate);

            return;
        }

        if (count($ACEsToRemove) > 0) {
            $this->ACERepository->removeACEs($ACEsToRemove);
        }
    }
}
