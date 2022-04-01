<?php

namespace Mygento\AccessControlBundle\ACL\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;

/**
 * @ORM\Entity()
 */
class ACE
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $userId;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity=Group::class)
     */
    private $groupId;

    public function getUserId()
    {
        return $this->userId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }
}
