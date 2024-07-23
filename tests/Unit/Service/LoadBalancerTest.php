<?php

namespace App\Tests\Unit\Service;

use App\Service\Host;
use App\Service\LoadBalancer;
use App\Service\Request;
use Brick\Math\BigDecimal;
use PHPUnit\Framework\TestCase;

class LoadBalancerTest extends TestCase
{
    public function testItPassesRequestAccordingToRoundRobin(): void
    {
        $hosts = [
            new Host(BigDecimal::of(0.2)),
            new Host(BigDecimal::of(0.6)),
            new Host(BigDecimal::of(0.1)),
        ];
        $request = new Request(BigDecimal::of(0.1));
        $loadBalancer = new LoadBalancer($hosts, LoadBalancer::ROUND_ROBIN);

        // First request should go to the first host
        $loadBalancer->handleRequest($request);
        $this->assertEquals(BigDecimal::of(0.3), $hosts[0]->getLoad());
        $this->assertEquals(BigDecimal::of(0.6), $hosts[1]->getLoad());
        $this->assertEquals(BigDecimal::of(0.1), $hosts[2]->getLoad());

        // Second request should go to the second host
        $loadBalancer->handleRequest($request);
        $this->assertEquals(BigDecimal::of(0.3), $hosts[0]->getLoad());
        $this->assertEquals(BigDecimal::of(0.7), $hosts[1]->getLoad());
        $this->assertEquals(BigDecimal::of(0.1), $hosts[2]->getLoad());

        // Third request should go to the third host
        $loadBalancer->handleRequest($request);
        $this->assertEquals(BigDecimal::of(0.3), $hosts[0]->getLoad());
        $this->assertEquals(BigDecimal::of(0.7), $hosts[1]->getLoad());
        $this->assertEquals(BigDecimal::of(0.2), $hosts[2]->getLoad());
    }

    public function testItPassesRequestToTheFirstHostAndThenToTheHostWithTheLowestLoad(): void
    {
        $hosts = [
            new Host(BigDecimal::of('0.5')),
            new Host(BigDecimal::of('0.8')),
            new Host(BigDecimal::of('0.71')),
        ];
        $request = new Request(BigDecimal::of('0.1'));
        $loadBalancer = new LoadBalancer($hosts, LoadBalancer::LOAD_BASED);

        // First request should go to the first host (load < 0.75)
        $loadBalancer->handleRequest($request);
        $this->assertEquals(BigDecimal::of('0.6'), $hosts[0]->getLoad());
        $this->assertEquals(BigDecimal::of('0.8'), $hosts[1]->getLoad());
        $this->assertEquals(BigDecimal::of('0.71'), $hosts[2]->getLoad());

        // Second request should go to the second host (load < 0.75)
        $loadBalancer->handleRequest($request);
        $this->assertEquals(BigDecimal::of('0.7'), $hosts[0]->getLoad());
        $this->assertEquals(BigDecimal::of('0.8'), $hosts[1]->getLoad());
        $this->assertEquals(BigDecimal::of('0.71'), $hosts[2]->getLoad());

        // Third request should go to the first host again (still load < 0.75)
        $loadBalancer->handleRequest($request);
        $this->assertEquals(BigDecimal::of('0.8'), $hosts[0]->getLoad());
        $this->assertEquals(BigDecimal::of('0.8'), $hosts[1]->getLoad());
        $this->assertEquals(BigDecimal::of('0.71'), $hosts[2]->getLoad());

        // Fourth request should go to the third host (load < 0.75)
        $loadBalancer->handleRequest($request);
        $this->assertEquals(BigDecimal::of('0.8'), $hosts[0]->getLoad());
        $this->assertEquals(BigDecimal::of('0.8'), $hosts[1]->getLoad());
        $this->assertEquals(BigDecimal::of('0.81'), $hosts[2]->getLoad());

        // Fifth request should go to the first host with the lowest load since all are >= 0.75
        $loadBalancer->handleRequest($request);
        $this->assertEquals(BigDecimal::of('0.9'), $hosts[0]->getLoad());
        $this->assertEquals(BigDecimal::of('0.8'), $hosts[1]->getLoad());
        $this->assertEquals(BigDecimal::of('0.81'), $hosts[2]->getLoad());
    }

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

        // First request should go to the fourth host with the lowest load since all are >= 0.75
        $loadBalancer->handleRequest($request);
        $this->assertEquals(BigDecimal::of('0.9'), $hosts[0]->getLoad());
        $this->assertEquals(BigDecimal::of('0.95'), $hosts[1]->getLoad());
        $this->assertEquals(BigDecimal::of('0.85'), $hosts[2]->getLoad());
        $this->assertEquals(BigDecimal::of('0.81'), $hosts[3]->getLoad());

        // Second request should go to the fourth host with the lowest load since all are >= 0.75
        $loadBalancer->handleRequest($request);
        $this->assertEquals(BigDecimal::of('0.9'), $hosts[0]->getLoad());
        $this->assertEquals(BigDecimal::of('0.95'), $hosts[1]->getLoad());
        $this->assertEquals(BigDecimal::of('0.85'), $hosts[2]->getLoad());
        $this->assertEquals(BigDecimal::of('0.91'), $hosts[3]->getLoad());

        // Third request should go to the third host with the lowest load since all are >= 0.75
        $loadBalancer->handleRequest($request);
        $this->assertEquals(BigDecimal::of('0.9'), $hosts[0]->getLoad());
        $this->assertEquals(BigDecimal::of('0.95'), $hosts[1]->getLoad());
        $this->assertEquals(BigDecimal::of('0.95'), $hosts[2]->getLoad());
        $this->assertEquals(BigDecimal::of('0.91'), $hosts[3]->getLoad());

        // Fourth request should go to the third host with the lowest load since all are >= 0.75
        $loadBalancer->handleRequest($request);
        $this->assertEquals(BigDecimal::of('1.0'), $hosts[0]->getLoad());
        $this->assertEquals(BigDecimal::of('0.95'), $hosts[1]->getLoad());
        $this->assertEquals(BigDecimal::of('0.95'), $hosts[2]->getLoad());
        $this->assertEquals(BigDecimal::of('0.91'), $hosts[3]->getLoad());
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
