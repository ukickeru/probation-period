<?php

namespace Mygento\AccessControlBundle\Core\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Group
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="groups")
     * @ORM\JoinTable(
     *     name="users_groups",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    private ArrayCollection $users;

    /**
     * @ORM\ManyToMany(targetEntity=Resource::class, inversedBy="groups")
     * @ORM\JoinTable(
     *     name="groups_resources",
     *     joinColumns={@ORM\JoinColumn(name="resource_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    private ArrayCollection $resources;

    public function __construct(
        ?iterable $users = [],
        ?iterable $resources = []
    ) {
        $this->users = new ArrayCollection();
        foreach ($users as $user) {
            $this->addUser($user);
        }

        $this->resources = new ArrayCollection();
        foreach ($resources as $resource) {
            $this->addResource($resource);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsers(): ArrayCollection
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

    public function getResources(): ArrayCollection
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
