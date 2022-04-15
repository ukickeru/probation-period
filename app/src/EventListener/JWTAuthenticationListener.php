<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class JWTAuthenticationListener
{
    public function addProfilePayloadOnAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        /** @var User $user */
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $data = $event->getData();

        $data['user'] = [
            'name' => $user->getName()->value(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];

        $event->setData($data);
    }
}
