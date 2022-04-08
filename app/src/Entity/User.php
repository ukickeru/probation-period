<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Mygento\AccessControlBundle\Core\Domain\Entity\User as AccessControlUser;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User extends AccessControlUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=false)
     */
    private string $email = '';

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string")
     */
    private string $password = '';

    public function __construct(
        string $email,
        array $roles,
        Name $name,
        array $groups = [],
        ?string $password = null,
        ?UserPasswordHasherInterface $passwordHasher = null
    ) {
        parent::__construct($name, $groups);

        $this->email = $email;

        $this->setRoles($roles);

        if (null !== $password && null !== $passwordHasher) {
            $this->setPassword($password, $passwordHasher);
        }
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(
        string $password,
        UserPasswordHasherInterface $hasher
    ): self {
        $this->password = $hasher->hashPassword($this, $password);

        return $this;
    }

    public function eraseCredentials()
    {
    }
}
