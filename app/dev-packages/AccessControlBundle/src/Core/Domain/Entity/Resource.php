<?php

namespace Mygento\AccessControlBundle\Core\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;
use Mygento\AccessControlBundle\Core\Repository\ResourceRepository;

/**
 * @ORM\Entity(repositoryClass=ResourceRepository::class)
 */
class Resource
{
    /**
     * @ORM\Id()
     * @ORM\Embedded(class=Id::class)
     */
    private ?id $id;

    /**
     * @ORM\ManyToMany(targetEntity=Group::class, mappedBy="resources")
     */
    private $groups;

    /**
     * @ORM\ManyToOne(targetEntity=Organization::class, inversedBy="resources")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id_value", nullable=true, onDelete="SET NULL")
     */
    private ?Organization $organization;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="resources")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id_value", nullable=true, onDelete="SET NULL")
     */
    private ?Project $project;

    public function __construct(
        iterable $groups = [],
        ?Organization $organization = null,
        ?Project $project = null
    ) {
        $this->groups = new ArrayCollection();
        foreach ($groups as $group) {
            $this->addGroup($group);
        }

        $this->organization = $organization;
        $this->project = $project;
    }

    public function getId(): ?Id
    {
        return $this->id;
    }

    /**
     * @return Collection
     */
    public function getGroups()
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

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }
}
