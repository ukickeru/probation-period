<?php

namespace Mygento\AccessControlBundle\Tests\Unit\ACL\Domain\Service;

use Mygento\AccessControlBundle\ACL\Domain\Service\ACEsCollection;
use PHPUnit\Framework\TestCase;

class ACEsCollectionTest extends TestCase
{
    public function testBehavior()
    {
        $ACEs = [
            [1, 1],
            [1, 2],
            [1, 3],
            [2, 1],
            [2, 2],
        ];

        $ACEsCollection = new ACEsCollection($ACEs);

        $this->assertInstanceOf(ACEsCollection::class, $ACEsCollection);

        $expectedString = '(1,1),(1,2),(1,3),(2,1),(2,2)';

        $this->assertEquals($expectedString, (string) $ACEsCollection);

        $ACEsCollection->addACE([2, 3]);

        $this->assertCount(6, $ACEsCollection);

        try {
            $ACEsCollection = new ACEsCollection($ACEs);
            $ACEsCollection->addACE([1, 1]);
        } catch (\DomainException $exception) {
            $this->assertInstanceOf(\DomainException::class, $exception);
        }

        try {
            new ACEsCollection([1]);
        } catch (\DomainException $exception) {
            $this->assertInstanceOf(\DomainException::class, $exception);
        }

        try {
            new ACEsCollection([null]);
        } catch (\DomainException $exception) {
            $this->assertInstanceOf(\DomainException::class, $exception);
        }

        try {
            new ACEsCollection([[1, 1], [1, 1]]);
        } catch (\DomainException $exception) {
            $this->assertInstanceOf(\DomainException::class, $exception);
        }
    }
}
