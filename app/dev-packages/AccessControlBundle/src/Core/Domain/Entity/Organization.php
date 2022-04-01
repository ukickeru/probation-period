<?php

namespace Mygento\AccessControlBundle\Core\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Organization
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Group::class)
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    private Group $group;

    public function __construct(
        Group $group
    ) {
        $this->group = $group;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }
}
