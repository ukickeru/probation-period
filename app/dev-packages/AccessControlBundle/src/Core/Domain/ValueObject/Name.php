<?php

namespace Mygento\AccessControlBundle\Core\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class Name
{
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $value;

    public function __construct(
        string $name
    ) {
        if (0 === strlen($name)) {
            throw new \DomainException('Name can not be empty!');
        }

        $this->value = $name;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
