<?php

namespace Mygento\AccessControlBundle\ACL\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mygento\AccessControlBundle\ACL\Repository\ACERepository;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;

/**
 * @ORM\Entity(repositoryClass=ACERepository::class)
 */
class ACE
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id_value", onDelete="CASCADE")
     */
    private ?User $user;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity=Resource::class)
     * @ORM\JoinColumn(name="resource_id", referencedColumnName="id_value", onDelete="CASCADE")
     */
    private ?Resource $resource;

    public function __construct(
        User $user,
        Resource $resource
    ) {
        $this->user = $user;
        $this->resource = $resource;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getResource(): ?Resource
    {
        return $this->resource;
    }
}
