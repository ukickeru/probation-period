<?php

namespace App\Domain;

class GumballMachine
{
    private int $gumballs = 0;

    public function getGumballs(): int
    {
        return $this->gumballs;
    }

    public function setGumballs(int $gumballs): void
    {
        $this->gumballs = $gumballs;
    }

    public function turnWheel(): void
    {
        $this->setGumballs($this->gumballs - 1);
    }
}