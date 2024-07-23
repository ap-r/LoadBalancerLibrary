<?php

namespace App\Service;

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
    }
}
