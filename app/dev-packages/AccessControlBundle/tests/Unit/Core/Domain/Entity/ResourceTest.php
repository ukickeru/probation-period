<?php

namespace Mygento\AccessControlBundle\Tests\Unit\Core\Domain\Entity;

use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use PHPUnit\Framework\TestCase;

class ResourceTest extends TestCase
{
    public function testBehavior()
    {
        $resource = new Resource();

        $this->assertInstanceOf(Resource::class, $resource);

        $groups = [
            $group = new Group(),
            new Group(),
        ];

        $resource = new Resource(null, $groups);

        $this->assertInstanceOf(Resource::class, $resource);
        $this->assertCount(2, $resource->getGroups());

        $resource->removeGroup($group);
        $this->assertCount(1, $resource->getGroups());
    }
}
