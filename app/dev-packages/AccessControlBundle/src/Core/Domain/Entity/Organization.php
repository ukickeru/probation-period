<?php

namespace Mygento\AccessControlBundle\Core\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity=Resource::class, mappedBy="organization")
     */
    private $resources;

    public function __construct(
        Group $group,
        $id = null,
        iterable $resources = []
    ) {
        $this->group = $group;
        $this->id = $id;

        $this->resources = new ArrayCollection();
        foreach ($resources as $resource) {
            $this->addResource($resource);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    /**
     * @return Collection
     */
    public function getResources()
    {
        return $this->resources;
    }

    public function addResource(Resource $resource): self
    {
        if (!$this->resources->contains($resource)) {
            $this->resources->add($resource);
            $resource->setOrganization($this);
        }

        return $this;
    }

    public function removeResource(Resource $resource): self
    {
        if ($this->resources->contains($resource)) {
            $this->resources->removeElement($resource);
            $resource->setOrganization(null);
        }

        return $this;
    }
}
