<?php

namespace App\Service;

use App\Exception\UnknownLoadBalancingAlgorithm;

class LoadBalancer
{
    public const ROUND_ROBIN = 1;
    public const LOAD_BASED = 2;
    /**
     * @var Host[]
     */
    private array $hosts;
    private int $algorithm;

    /**
     * @param Host[] $hosts
     */
    public function __construct(array $hosts, int $algorithm)
    {
        $this->hosts = $hosts;
        $this->algorithm = $algorithm;
    }

    public function handleRequest(Request $request): void
    {
        if ($this->algorithm === self::ROUND_ROBIN) {
            $this->handleRequestRoundRobin($request);
        } elseif ($this->algorithm === self::LOAD_BASED) {
            $this->handleRequestLoadBased($request);
        } else {
            throw new UnknownLoadBalancingAlgorithm('Unknown algorithm variant');
        }
    }

    public function handleRequestRoundRobin($request) {
        // Pass the requests sequentially in rotation
    }

    public function handleRequestLoadBased($request) {
        // Find the first host with load under 0.75 or the one with the lowest load
    }
}
