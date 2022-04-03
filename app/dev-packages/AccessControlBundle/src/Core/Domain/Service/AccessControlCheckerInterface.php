<?php

namespace Mygento\AccessControlBundle\Core\Domain\Service;

interface AccessControlCheckerInterface
{
    public function isResourceAvailableForUser($userId, $resourceId);
}
