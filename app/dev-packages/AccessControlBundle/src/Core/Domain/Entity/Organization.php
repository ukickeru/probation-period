<?php

namespace Mygento\AccessControlBundle\Core\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Mygento\AccessControlBundle\Core\Repository\OrganizationRepository;

/**
 * @ORM\Entity(repositoryClass=OrganizationRepository::class)
 */
class Organization
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Embedded(class=Name::class, columnPrefix="")
     */
    protected Name $name;

    /**
     * @ORM\OneToOne(targetEntity=Group::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected Group $group;

    /**
     * @ORM\OneToMany(targetEntity=Resource::class, mappedBy="organization")
     */
    protected $resources;

    public function __construct(
        Name $name,
        Group $group,
        iterable $resources = null
    ) {
        $this->name = $name;
        $this->group = $group;

        $this->resources = new ArrayCollection();
        if (null !== $resources) {
            foreach ($resources as $resource) {
                $this->addResource($resource);
            }
        }
    }

    public function getId()
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
