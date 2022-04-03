<?php

namespace Mygento\AccessControlBundle\ACL\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mygento\AccessControlBundle\ACL\Repository\ACERepository;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;

/**
 * @ORM\Entity(repositoryClass=ACERepository::class)
 * @ORM\Table(
 *     name="acl",
 *     indexes={
 *         @ORM\Index(name="search_index", columns={"user_id", "group_id"})
 *     }
 * )
 */
class ACE
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\Column(name="user_id")
     */
    private $userId;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity=Group::class)
     * @ORM\Column(name="group_id")
     */
    private $groupId;

    public function __construct(
        $userId,
        $groupId
    ) {
        $this->userId = $userId;
        $this->groupId = $groupId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }
}
