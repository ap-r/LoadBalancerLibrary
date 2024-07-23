<?php

namespace App\Tests\Unit\Service;

use App\Service\Host;
use App\Service\LoadBalancer;
use App\Service\Request;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\NumberFormatException;
use PHPUnit\Framework\TestCase;

class LoadBalancerTest extends TestCase
{
    /**
     * @throws \Throwable
     * @throws NumberFormatException
     * @throws DivisionByZeroException
     */
    public function testItPassesRequestAccordingToRoundRobin(): void
    {
        $hosts = [
            new Host(BigDecimal::of(0.2)),
            new Host(BigDecimal::of(0.6)),
            new Host(BigDecimal::of(0.1)),
        ];
        $request = new Request(BigDecimal::of(0.1));
        $loadBalancer = new LoadBalancer($hosts, LoadBalancer::ROUND_ROBIN);

        $expectedLoads = [
            // Request 1: Sent to Host 1
            1 => [
                BigDecimal::of('0.3'), // Load for Host 1
                BigDecimal::of('0.6'), // Load for Host 2
                BigDecimal::of('0.1'),  // Load for Host 3
            ],
            // Request 2: Sent to Host 2
            2 => [
                BigDecimal::of('0.3'), // Load for Host 1
                BigDecimal::of('0.7'), // Load for Host 2
                BigDecimal::of('0.1'),  // Load for Host 3
            ],
            // Request 3: Sent to Host 3
            3 => [
                BigDecimal::of('0.3'), // Load for Host 1
                BigDecimal::of('0.7'), // Load for Host 2
                BigDecimal::of('0.2'),  // Load for Host 3
            ],
            // Request 4: Sent to Host 1
            4 => [
                BigDecimal::of('0.4'), // Load for Host 1
                BigDecimal::of('0.7'), // Load for Host 2
                BigDecimal::of('0.2'),  // Load for Host 3
            ],
            // Request 5: Sent to Host 2
            5 => [
                BigDecimal::of('0.4'), // Load for Host 1
                BigDecimal::of('0.8'), // Load for Host 2
                BigDecimal::of('0.2'),  // Load for Host 3
            ],
            // Request 6: Sent to Host 3
            6 => [
                BigDecimal::of('0.4'), // Load for Host 1
                BigDecimal::of('0.8'), // Load for Host 2
                BigDecimal::of('0.3'),  // Load for Host 3
            ],
            // Request 7: Sent to Host 1
            7 => [
                BigDecimal::of('0.5'), // Load for Host 1
                BigDecimal::of('0.8'), // Load for Host 2
                BigDecimal::of('0.3'),  // Load for Host 3
            ],
        ];

        foreach ($expectedLoads as $requestCount => $expectedLoad) {
            $loadBalancer->handleRequest($request);
            $this->assertEquals($expectedLoad[0], $hosts[0]->getLoad(), "Load for host 0 after request $requestCount");
            $this->assertEquals($expectedLoad[1], $hosts[1]->getLoad(), "Load for host 1 after request $requestCount");
            $this->assertEquals($expectedLoad[2], $hosts[2]->getLoad(), "Load for host 2 after request $requestCount");
        }
    }

    /**
     * @throws NumberFormatException
     * @throws DivisionByZeroException
     * @throws \Throwable
     */
    public function testItPassesRequestToTheFirstHostAndThenToTheHostWithTheLowestLoad(): void
    {
        $hosts = [
            new Host(BigDecimal::of('0.5')),
            new Host(BigDecimal::of('0.8')),
            new Host(BigDecimal::of('0.71')),
        ];
        $request = new Request(BigDecimal::of('0.1'));
        $loadBalancer = new LoadBalancer($hosts, LoadBalancer::LOAD_BASED);

        $expectedLoads = [
            // After 1st request: Sent to Host 1 (load < 0.75)
            1 => [
                BigDecimal::of('0.6'), // Load for Host 1
                BigDecimal::of('0.8'), // Load for Host 2
                BigDecimal::of('0.71'), // Load for Host 3
            ],
            // After 2nd request: Sent to Host 1 again (load < 0.75)
            2 => [
                BigDecimal::of('0.7'), // Load for Host 1
                BigDecimal::of('0.8'), // Load for Host 2
                BigDecimal::of('0.71'), // Load for Host 3
            ],
            // After 3rd request: Sent to Host 1 again (load < 0.75)
            3 => [
                BigDecimal::of('0.8'), // Load for Host 1
                BigDecimal::of('0.8'), // Load for Host 2
                BigDecimal::of('0.71'), // Load for Host 3
            ],
            // After 4th request: Sent to Host 3 (load < 0.75)
            4 => [
                BigDecimal::of('0.8'), // Load for Host 1
                BigDecimal::of('0.8'), // Load for Host 2
                BigDecimal::of('0.81'), // Load for Host 3
            ],
            // After 5th request: Sent to Host 1 with the lowest load (all loads >= 0.75)
            5 => [
                BigDecimal::of('0.9'), // Load for Host 1
                BigDecimal::of('0.8'), // Load for Host 2
                BigDecimal::of('0.81'), // Load for Host 3
            ],
            // After 6th request: Sent to Host 2 with the lowest load (all loads >= 0.75)
            6 => [
                BigDecimal::of('0.9'), // Load for Host 1
                BigDecimal::of('0.9'), // Load for Host 2
                BigDecimal::of('0.81'), // Load for Host 3
            ],
            // After 7th request: Sent to Host 2 with the lowest load (all loads >= 0.75)
            7 => [
                BigDecimal::of('0.9'), // Load for Host 1
                BigDecimal::of('0.9'), // Load for Host 2
                BigDecimal::of('0.91'), // Load for Host 3
            ],
        ];

        foreach ($expectedLoads as $requestNumber => $expectedLoad) {
            $loadBalancer->handleRequest($request);

            foreach ($hosts as $index => $host) {
                $this->assertEquals(
                    $expectedLoad[$index],
                    $host->getLoad(),
                    "Load mismatch after request #{$requestNumber} for host #{$index}"
                );
            }
        }
    }

    /**
     * @throws NumberFormatException
     * @throws DivisionByZeroException
     * @throws \Throwable
     */
    public function testItPassesRequestToTheHostWithTheLowestLoad(): void
    {
        $hosts = [
            new Host(BigDecimal::of('0.9')),
            new Host(BigDecimal::of('0.95')),
            new Host(BigDecimal::of('0.85')),
            new Host(BigDecimal::of('0.71')),
        ];
        $request = new Request(BigDecimal::of('0.1'));
        $loadBalancer = new LoadBalancer($hosts, LoadBalancer::LOAD_BASED);

        $expectedLoads = [
            // After 1st request: Sent to the fourth host with the lowest load (all loads >= 0.75)
            1 => [
                BigDecimal::of('0.9'), // Load for Host 1
                BigDecimal::of('0.95'), // Load for Host 2
                BigDecimal::of('0.85'), // Load for Host 3
                BigDecimal::of('0.81'),  // Load for Host 4 (lowest load)
            ],
            // After 2nd request: Sent to the fourth host with the lowest load (all loads >= 0.75)
            2 => [
                BigDecimal::of('0.9'), // Load for Host 1
                BigDecimal::of('0.95'), // Load for Host 2
                BigDecimal::of('0.85'), // Load for Host 3
                BigDecimal::of('0.91'),  // Load for Host 4 (updated after 1st request)
            ],
            // After 3rd request: Sent to the third host with the lowest load (all loads >= 0.75)
            3 => [
                BigDecimal::of('0.9'), // Load for Host 1
                BigDecimal::of('0.95'), // Load for Host 2
                BigDecimal::of('0.95'), // Load for Host 3 (updated after 2nd request)
                BigDecimal::of('0.91'),  // Load for Host 4
            ],
            // After 4th request: Sent to the third host with the lowest load (all loads >= 0.75)
            4 => [
                BigDecimal::of('1.0'), // Load for Host 1
                BigDecimal::of('0.95'), // Load for Host 2
                BigDecimal::of('0.95'), // Load for Host 3 (updated after 3rd request)
                BigDecimal::of('0.91'),  // Load for Host 4
            ],
        ];

        foreach ($expectedLoads as $requestNumber => $expectedLoad) {
            $loadBalancer->handleRequest($request);

            foreach ($hosts as $index => $host) {
                $this->assertEquals(
                    $expectedLoad[$index],
                    $host->getLoad(),
                    "Load mismatch after request #{$requestNumber} for host #{$index}"
                );
            }
        }
    }

    public function testItHandlesRequestRoundRobin(): void
    {
        $hosts = [
            new Host(BigDecimal::of('0.2')),
            new Host(BigDecimal::of('0.6')),
            new Host(BigDecimal::of('0.1')),
        ];
        $request = new Request(BigDecimal::of('0.1'));

        // Create a mock for the LoadBalancer class
        $loadBalancerMock = $this->getMockBuilder(LoadBalancer::class)
            ->setConstructorArgs([$hosts, LoadBalancer::ROUND_ROBIN])
            ->onlyMethods(['handleRequestRoundRobin', 'handleRequestLoadBased'])
            ->getMock();

        // Set up the expectation for the handleRequestRoundRobin method to be called
        $loadBalancerMock->expects($this->once())
            ->method('handleRequestRoundRobin')
            ->with($this->equalTo($request));

        // Call the handleRequest method with the ROUND_ROBIN algorithm
        $loadBalancerMock->handleRequest($request);
    }

    public function testItHandlesRequestLoadBased(): void
    {
        $hosts = [
            new Host(BigDecimal::of('0.2')),
            new Host(BigDecimal::of('0.6')),
            new Host(BigDecimal::of('0.1')),
        ];
        $request = new Request(BigDecimal::of('0.1'));

        // Create a mock for the LoadBalancer class
        $loadBalancerMock = $this->getMockBuilder(LoadBalancer::class)
            ->setConstructorArgs([$hosts, LoadBalancer::LOAD_BASED])
            ->onlyMethods(['handleRequestRoundRobin', 'handleRequestLoadBased'])
            ->getMock();

        // Set up the expectation for the handleRequestRoundRobin method to be called
        $loadBalancerMock->expects($this->once())
            ->method('handleRequestLoadBased')
            ->with($this->equalTo($request));

        // Call the handleRequest method with the LOAD_BASED algorithm
        $loadBalancerMock->handleRequest($request);
    }
}
