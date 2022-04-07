<?php

namespace Mygento\AccessControlBundle\Core\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Mygento\AccessControlBundle\Core\Repository\GroupRepository;

/**
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @ORM\Table(name="access_control_group")
 */
class Group
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
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="groups", cascade={"persist"})
     * @ORM\JoinTable(name="access_control_group_user",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity=Resource::class, inversedBy="groups", cascade={"persist"})
     * @ORM\JoinTable(name="access_control_group_resource",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="resource_id", referencedColumnName="id")}
     * )
     */
    private $resources;

    public function __construct(
        Name $name,
        iterable $users = [],
        iterable $resources = []
    ) {
        $this->name = $name;

        $this->users = new ArrayCollection();
        foreach ($users as $user) {
            $this->addUser($user);
        }

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

    /**
     * @return Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addGroup($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeGroup($this);
        }

        return $this;
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
            $resource->addGroup($this);
        }

        return $this;
    }

    public function removeResource(Resource $resource): self
    {
        if ($this->resources->contains($resource)) {
            $this->resources->removeElement($resource);
            $resource->removeGroup($this);
        }

        return $this;
    }
}
