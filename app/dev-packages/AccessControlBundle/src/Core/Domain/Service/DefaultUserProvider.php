<?php

namespace Mygento\AccessControlBundle\Core\Domain\Service;

use Mygento\AccessControlBundle\Core\Domain\Entity\UserInterface;
use Symfony\Component\Security\Core\Security;

class DefaultUserProvider implements UserProviderInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getUser(): UserInterface
    {
        $user = $this->security->getUser();

        if (!$user instanceof UserInterface) {
            throw new \DomainException('Please implement '.UserInterface::class.' by your User entity, or make a service that implements '.UserProviderInterface::class.' and use it in DI container!');
        }

        return $user;
    }
}
