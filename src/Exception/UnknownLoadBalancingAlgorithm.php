<?php

namespace App\Exception;

class UnknownLoadBalancingAlgorithm extends \InvalidArgumentException
{
    public function __construct(string $message = 'Unknown load balancing algorithm', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
