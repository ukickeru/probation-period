<?php

namespace Mygento\AccessControlBundle\Core\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Mygento\AccessControlBundle\Core\Repository\UserRepository;

/**
 * @ORM\MappedSuperclass(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id_value", type="integer")
     */
    private $id;

    /**
     * @ORM\Embedded(class=Name::class)
     */
    protected Name $name;

    /**
     * @ORM\ManyToMany(targetEntity=Group::class, inversedBy="users", cascade={"persist"})
     * @ORM\JoinTable(name="group_user",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id_value")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id_value")},
     * )
     */
    private $groups;

    public function __construct(
        Name $name,
        iterable $groups = []
    ) {
        $this->name = $name;

        $this->groups = new ArrayCollection();
        foreach ($groups as $group) {
            $this->addGroup($group);
        }
    }

    public function getId(): ?Id
    {
        return new Id($this->id);
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
    public function getGroups()
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
            $group->addUser($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->removeUser($this);
        }

        return $this;
    }
}
