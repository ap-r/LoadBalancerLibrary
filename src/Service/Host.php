<?php

namespace App\Service;

class Host
{
    private float $load;

    public function __construct(float $load = 0.0)
    {
        $this->load = $load;
    }

    public function getLoad(): float
    {
        return $this->load;
    }

    public function handleRequest(Request $request): void
    {
        // Simulate handling the request by increasing the load
        $this->load += $request->getLoadIncrease();
    }
}
