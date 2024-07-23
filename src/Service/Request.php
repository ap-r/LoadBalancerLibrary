<?php

namespace App\Service;

use Brick\Math\BigDecimal;

class Request
{
    private BigDecimal $loadIncrease;

    public function __construct(BigDecimal $loadIncrease)
    {
        $this->loadIncrease = $loadIncrease;
    }

    public function getLoadIncrease(): BigDecimal
    {
        return $this->loadIncrease;
    }
}
