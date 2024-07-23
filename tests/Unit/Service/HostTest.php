<?php

namespace App\Tests\Unit\Service;

use App\Exception\LoadExceededException;
use App\Service\Host;
use App\Service\Request;
use Brick\Math\BigDecimal;
use PHPUnit\Framework\TestCase;

class HostTest extends TestCase
{
    public function testItIncreasesLoad(): void
    {
        $host = new Host(BigDecimal::of('0.5'));
        $request = new Request(BigDecimal::of('0.1'));
        $host->handleRequest($request);

        $this->assertEquals(BigDecimal::of('0.6'), $host->getLoad());
    }

    public function testItThrowsExceptionForOverloadedHost(): void
    {
        $host = new Host(BigDecimal::of('0.7'));
        $firstRequest = new Request(BigDecimal::of('0.1'));
        $host->handleRequest($firstRequest);

        $this->assertEquals(BigDecimal::of('0.8'), $host->getLoad());

        $this->expectException(LoadExceededException::class);

        $secondRequest = new Request(BigDecimal::of('0.3'));
        $host->handleRequest($secondRequest);
    }
}
