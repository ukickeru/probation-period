<?php

namespace Mygento\AccessControlBundle\Tests\Unit\Core\Domain\ValueObject;

use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function testBehavior()
    {
        $name = new Name('Example');

        $this->assertInstanceOf(Name::class, $name);

        try {
            new Name('');
        } catch (\DomainException $exception) {
            $this->assertInstanceOf(\DomainException::class, $exception);
        }
    }
}
