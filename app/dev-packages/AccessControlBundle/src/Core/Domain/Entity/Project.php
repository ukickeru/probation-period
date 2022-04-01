<?php

namespace Mygento\AccessControlBundle\Core\Domain\Entity;

class Project
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
