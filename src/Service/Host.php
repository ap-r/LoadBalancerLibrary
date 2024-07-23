<?php

namespace App\Service;

use Brick\Math\BigDecimal;

class Host
{
    private BigDecimal $load;

    public function __construct(BigDecimal $load)
    {
        $this->load = $load;
    }

    public function getLoad(): BigDecimal
    {
        return $this->load;
    }

    public function handleRequest(Request $request): void
    {
        // Simulate handling the request by increasing the load
        $this->load = $this->load->plus(BigDecimal::of($request->getLoadIncrease()));
    }
}
