<?php

namespace Mygento\AccessControlBundle\Core\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Group::class, mappedBy="users")
     */
    private ArrayCollection $groups;

    public function __construct(
        ?iterable $groups = []
    ) {
        $this->groups = new ArrayCollection();
        foreach ($groups as $group) {
            $this->addGroup($group);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getGroups(): ArrayCollection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
            $group->addUser($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->removeUser($this);
        }

        return $this;
    }
}
