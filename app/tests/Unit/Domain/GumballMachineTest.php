<?php

namespace App\Tests\Unit\Domain;

use App\Domain\GumballMachine;
use PHPUnit\Framework\TestCase;

class GumballMachineTest extends TestCase
{
    public function testGumballMachine()
    {
        $gumballMachine = new GumballMachine();

        $this->assertEquals(0, $gumballMachine->getGumballs());

        $gumballMachine->setGumballs(100);

        $gumballMachine->turnWheel();

        $this->assertEquals(99, $gumballMachine->getGumballs());
    }
}