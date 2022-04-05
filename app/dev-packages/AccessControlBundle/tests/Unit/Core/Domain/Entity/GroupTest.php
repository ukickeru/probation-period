<?php

namespace Mygento\AccessControlBundle\Tests\Unit\Core\Domain\Entity;

use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use PHPUnit\Framework\TestCase;

class GroupTest extends TestCase
{
    public function testBehavior()
    {
        $name = new Name('Example');

        $group = new Group($name);

        $this->assertInstanceOf(Group::class, $group);

        $users = [
            $user = new User($name),
            new User($name),
        ];

        $resources = [
            $resource = new Resource(),
            new Resource(),
        ];

        $group = new Group($name, $users, $resources);

        $this->assertInstanceOf(Group::class, $group);

        $this->assertCount(2, $group->getUsers());
        $group->removeUser($user);
        $this->assertCount(1, $group->getUsers());

        $this->assertCount(2, $group->getResources());
        $group->removeResource($resource);
        $this->assertCount(1, $group->getResources());
    }
}
