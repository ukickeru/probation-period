<?php

namespace Mygento\AccessControlBundle\Tests\Unit\Core\Domain\ValueObject;

use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;
use PHPUnit\Framework\TestCase;

class IdTest extends TestCase
{
    public function testBehavior()
    {
        $id = new Id(1);
        $this->assertInstanceOf(Id::class, $id);

        // To hide deprecation notice
        // $id = new Id(1.1);
        // $this->assertInstanceOf(Id::class, $id);

        $id = new Id(true);
        $this->assertInstanceOf(Id::class, $id);

        $id = new Id('1');
        $this->assertInstanceOf(Id::class, $id);

        try {
            new Id(0);
        } catch (\DomainException $exception) {
            $this->assertInstanceOf(\DomainException::class, $exception);
        }

        try {
            new Id(-1);
        } catch (\DomainException $exception) {
            $this->assertInstanceOf(\DomainException::class, $exception);
        }

        try {
            new Id('abc');
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(\TypeError::class, $exception);
        }

        try {
            new Id(null);
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(\TypeError::class, $exception);
        }
    }
}
