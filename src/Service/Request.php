<?php

namespace App\Service;

class Request
{
    private float $loadIncrease;

    public function __construct(float $loadIncrease = 0.1)
    {
        $this->loadIncrease = $loadIncrease;
    }

    public function getLoadIncrease(): float
    {
        return $this->loadIncrease;
    }
}
