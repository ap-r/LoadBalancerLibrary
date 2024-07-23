<?php

namespace App\Service;

interface LoadBalancerInterface
{
    public function handleRequest(Request $request): void;
}
