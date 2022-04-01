<?php

namespace Mygento\AccessControlBundle\AccessControl\Core\Domain\Entity;

class Organization
{
    private $id;

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
