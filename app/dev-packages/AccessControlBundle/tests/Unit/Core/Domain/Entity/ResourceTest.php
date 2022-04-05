<?php

namespace Mygento\AccessControlBundle\Tests\Unit\Core\Domain\Entity;

use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use PHPUnit\Framework\TestCase;

class ResourceTest extends TestCase
{
    public function testBehavior()
    {
        $name = new Name('Example');

        $resource = new Resource();

        $this->assertInstanceOf(Resource::class, $resource);

        $groups = [
            $group = new Group($name),
            new Group($name),
        ];

        $resource = new Resource($groups);

        $this->assertInstanceOf(Resource::class, $resource);
        $this->assertCount(2, $resource->getGroups());

        $resource->removeGroup($group);
        $this->assertCount(1, $resource->getGroups());
    }
}
