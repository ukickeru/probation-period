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
 * @ORM\Table(name="access_control_project")
 */
class Project
{
    /**
     * @ORM\Embedded(class=Id::class, columnPrefix=false)
     */
    private ?Id $id;

    /**
     * @ORM\Embedded(class=Name::class)
     */
    protected Name $name;

    /**
     * @ORM\OneToOne(targetEntity=Group::class, cascade={"persist"})
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Group $group;

    public function __construct(
        Name $name,
        Group $group,
        iterable $resources = []
    ) {
        $this->name = $name;
        $this->group = $group;

        foreach ($resources as $resource) {
            $this->group->addResource($resource);
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
        return $this->group->getResources();
    }

    public function addResource(Resource $resource): self
    {
        $this->group->addResource($resource);

        return $this;
    }

    public function removeResource(Resource $resource): self
    {
        $this->group->removeResource($resource);

        return $this;
    }
}
