<?php

namespace Mygento\AccessControlBundle\Core\Domain\Service;

use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;

interface AccessControlCheckerInterface
{
    public function isResourceAvailableForUser(Id $userId, Id $resourceId): bool;
}
