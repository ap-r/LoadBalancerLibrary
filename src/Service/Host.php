<?php

namespace App\Service;

use App\Exception\LoadExceededException;
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
        $newLoad = $this->load->plus(BigDecimal::of($request->getLoadIncrease()));

        // Ensure that the load does not exceed 1
        if ($newLoad->isGreaterThan(BigDecimal::of('1.0'))) {
            throw new LoadExceededException();
        }

        $this->load = $newLoad;
    }

    public function __toString(): string
    {
        return "Host load: ". $this->load;
    }
}
