<?php

namespace Mygento\AccessControlBundle\Tests\Unit\Core\Domain\Entity;

use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testBehavior()
    {
        $name = new Name('Example');

        $user = new User($name);

        $this->assertInstanceOf(User::class, $user);

        $groups = [
            $group = new Group($name),
            new Group($name),
        ];

        $user = new User($name, $groups);

        $this->assertInstanceOf(User::class, $user);
        $this->assertCount(2, $user->getGroups());

        $user->removeGroup($group);
        $this->assertCount(1, $user->getGroups());
    }
}
