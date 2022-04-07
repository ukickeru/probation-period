<?php

namespace Mygento\AccessControlBundle\Core\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class Id
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new \DomainException('Id must be represent by positive integer value!');
        }

        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
