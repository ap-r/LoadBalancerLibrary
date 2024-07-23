<?php

namespace App\Service;

use App\Exception\UnknownLoadBalancingAlgorithm;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\NumberFormatException;

class LoadBalancer
{
    public const ROUND_ROBIN = 1;
    public const LOAD_BASED = 2;

    private BigDecimal $threshold;
    /**
     * @var Host[]
     */
    private array $hosts;
    private int $algorithm;

    private int $currentHostIndex = 0;

    /**
     * @param Host[] $hosts
     *
     * @throws NumberFormatException|\Throwable
     */
    public function __construct(array $hosts, int $algorithm)
    {
        $this->threshold = BigDecimal::of('0.75');
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
        $selectedHost = null;
        $lowestLoadHost = $this->hosts[0];

        foreach ($this->hosts as $host) {
            if ($host->getLoad()->isLessThan($this->threshold)) {
                $selectedHost = $host;
                break;
            }
            if ($host->getLoad()->isLessThan($lowestLoadHost->getLoad())) {
                $lowestLoadHost = $host;
            }
        }

        if (null === $selectedHost) {
            $selectedHost = $lowestLoadHost;
        }

        $selectedHost->handleRequest($request);
    }
}
