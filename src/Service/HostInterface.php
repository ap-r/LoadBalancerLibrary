<?php

namespace App\Service;

interface HostInterface
{
    public function getLoad(): float;

    public function handleRequest(Request $request): void;
}
