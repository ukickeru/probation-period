<?php

namespace Mygento\AccessControlBundle\ACL\Domain;

use Mygento\AccessControlBundle\ACL\Repository\ACERepository;

class ACESynchronizer
{
    private ACERepository $ACERepository;

    public function __construct(
        ACERepository $ACERepository
    ) {
        $this->ACERepository = $ACERepository;
    }

    public function synchronizeACEGlobally(): void
    {
        // TODO: implement method
    }

    public function synchronizeACEForUser($userId): void
    {
        // TODO: implement method
    }

    public function synchronizeACEForGroup($groupId): void
    {
        // TODO: implement method
    }

    public function synchronizeACEForResource($resourceId): void
    {
        // TODO: implement method
    }
}
