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

    private int $currentHostIndex;

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
        if (self::ROUND_ROBIN === $this->algorithm) {
            $this->handleRequestRoundRobin($request);
        } elseif (self::LOAD_BASED === $this->algorithm) {
            $this->handleRequestLoadBased($request);
        } else {
            throw new UnknownLoadBalancingAlgorithm('Unknown algorithm variant');
        }
    }

    public function handleRequestRoundRobin(Request $request): void
    {
        // Pass the request to the current host
        $this->hosts[$this->currentHostIndex]->handleRequest($request);

        ++$this->currentHostIndex;

        // Reset the currentHostIndex after a complete rotation
        if ($this->currentHostIndex >= count($this->hosts)) {
            $this->currentHostIndex = 0;
        }
    }

    public function handleRequestLoadBased(Request $request): void
    {
        // Find the first host with load under 0.75 or the one with the lowest load
    }
}
