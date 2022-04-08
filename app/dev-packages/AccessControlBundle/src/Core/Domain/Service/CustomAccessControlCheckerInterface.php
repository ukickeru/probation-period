<?php

namespace Mygento\AccessControlBundle\Core\Domain\Service;

use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;

/**
 * Application-dependent interface.
 */
interface CustomAccessControlCheckerInterface
{
    /**
     * @param mixed $criteria Criteria for resource search
     */
    public function isResourceAvailableForUserByCriteria(Id $userId, $criteria): bool;
}
