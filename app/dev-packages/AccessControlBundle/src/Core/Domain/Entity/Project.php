<?php

namespace Mygento\AccessControlBundle\Core\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Mygento\AccessControlBundle\Core\Repository\ProjectRepository;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\Embedded(class=Id::class)
     */
    private ?Id $id;

    /**
     * @ORM\Embedded(class=Name::class)
     */
    private Name $name;

    /**
     * @ORM\OneToOne(targetEntity=Group::class, cascade={"persist"})
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id_value", nullable=false, onDelete="CASCADE")
     */
    private Group $group;

    /**
     * @ORM\OneToMany(targetEntity=Resource::class, mappedBy="project", cascade={"persist"})
     */
    private $resources;

    public function __construct(
        Name $name,
        Group $group,
        iterable $resources = []
    ) {
        $this->name = $name;
        $this->group = $group;

        $this->resources = new ArrayCollection();
        foreach ($resources as $resource) {
            $this->addResource($resource);
        }
    }

    public function getId(): ?Id
    {
        return $this->id;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function setName(Name $name): self
    {
        $this->name = $name;

        return $this;
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
            $resource->setProject($this);
        }

        return $this;
    }

    public function removeResource(Resource $resource): self
    {
        if ($this->resources->contains($resource)) {
            $this->resources->removeElement($resource);
            $resource->setProject(null);
        }

        return $this;
    }
}
