<?php

namespace Mygento\AccessControlBundle\AccessControl\Core\tests\Unit\Domain\Entity;

use Mygento\AccessControlBundle\AccessControl\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\AccessControl\Core\Domain\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testBehavior()
    {
        $user = new User();

        $this->assertInstanceOf(User::class, $user);

        $groups = [
            $group = new Group(),
            new Group(),
        ];

        $user = new User($groups);

        $this->assertInstanceOf(User::class, $user);
        $this->assertCount(2, $user->getGroups());

        $user->removeGroup($group);
        $this->assertCount(1, $user->getGroups());
    }
}
