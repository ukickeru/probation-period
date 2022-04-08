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
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Embedded(class=Name::class)
     */
    protected Name $name;

    /**
     * @ORM\OneToOne(targetEntity=Group::class, cascade={"persist"})
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Group $group;

    /**
     * @ORM\OneToMany(targetEntity=Resource::class, mappedBy="project", cascade={"persist"})
     */
    protected $resources;

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
        return null === $this->id ? null : new Id($this->id);
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
    public function getUsers()
    {
        return $this->group->getUsers();
    }

    public function addUser(User $user): self
    {
        if (!$this->getUsers()->contains($user)) {
            $this->group->addUser($user);
            $user->addGroup($this->group);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->getUsers()->contains($user)) {
            $this->group->removeUser($user);
            $user->removeGroup($this->group);
        }

        return $this;
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
        if (!$this->resources->contains($resource)) {
            $this->resources->add($resource);
            $resource->setProject($this);
            $this->group->addResource($resource);
        }

        return $this;
    }

    public function removeResource(Resource $resource): self
    {
        if ($this->resources->contains($resource)) {
            $this->resources->removeElement($resource);
            $resource->setProject(null);
            $this->group->removeResource($resource);
        }

        return $this;
    }
}
