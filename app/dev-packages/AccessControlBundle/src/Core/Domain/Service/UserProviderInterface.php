<?php

namespace Mygento\AccessControlBundle\Core\Domain\Service;

use Mygento\AccessControlBundle\Core\Domain\Entity\UserInterface;

interface UserProviderInterface
{
    public function getUser(): UserInterface;
}
