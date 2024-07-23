<?php

namespace App\Service;

use Brick\Math\BigDecimal;

interface HostInterface
{
    public function getLoad(): BigDecimal;

    public function handleRequest(Request $request): void;
}
