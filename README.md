# LoadBalancer Service

## Overview

The `LoadBalancer` class is designed to distribute requests across a set of `Host` instances using different load balancing algorithms. This service helps in managing the load efficiently by either distributing requests in a round-robin fashion or based on the current load of each host.

## Algorithms

The `LoadBalancer` supports two load balancing algorithms:

1. **Round Robin**
2. **Load-Based**

### 1. Round Robin

The round-robin algorithm distributes requests sequentially to each host in the list. Once it reaches the end of the list, it starts again from the beginning. This ensures that each host receives an equal share of requests over time.

### 2. Load-Based

The load-based algorithm selects a host based on its current load:
- If a host's load is below a threshold (0.75), it will be selected to handle the request.
- If all hosts have a load above the threshold, the host with the lowest load will be chosen.

## Example of usage
### 1. Round Robin
```php
$hosts = [
    new Host(BigDecimal::of(0.2)),
    new Host(BigDecimal::of(0.6)),
    new Host(BigDecimal::of(0.1)),
];
$request = new Request(BigDecimal::of(0.1));
$loadBalancer = new LoadBalancer($hosts, LoadBalancer::ROUND_ROBIN);

$loadBalancer->handleRequest($request);
$loadBalancer->handleRequest($request);
```

### 2. Load-Based
```php
$hosts = [
    new Host(BigDecimal::of(0.3)),
    new Host(BigDecimal::of(0.6)),
    new Host(BigDecimal::of(0.7)),
];
$request = new Request(BigDecimal::of(0.2));
$loadBalancer = new LoadBalancer($hosts, LoadBalancer::LOAD_BASED);

$loadBalancer->handleRequest($request);
$loadBalancer->handleRequest($request);

```