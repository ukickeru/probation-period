<?php

namespace Mygento\AccessControlBundle\AccessControl\Core\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Resource
{
    private $id;

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
            $group->addResource($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->removeResource($this);
        }

        return $this;
    }
}
