<?php

namespace Mygento\AccessControlBundle\Core\Domain\Entity;

use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;

interface UserInterface
{
    public function getId(): ?Id;
}
