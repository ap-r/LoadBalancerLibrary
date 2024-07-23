<?php

namespace App\Controller;

use App\Service\Host;
use App\Service\LoadBalancer;
use App\Service\Request;
use Brick\Math\BigDecimal;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/', name: 'index_page')]
    public function indexPage(): Response
    {
        return $this->render('index.html.twig');
    }
    #[Route('/load-balancer-round-robin', name: 'round_robin_example')]
    public function roundRobinAlgorithmExample(): Response
    {
        $firstHost = new Host(BigDecimal::of(0.2));
        $secondHost = new Host(BigDecimal::of(0.4));
        $thirdHost = new Host(BigDecimal::of(0.5));

        $loadBalancer = new LoadBalancer([$firstHost, $secondHost, $thirdHost], LoadBalancer::ROUND_ROBIN);

        $request = new Request(BigDecimal::of(0.1));

        $firstState = $loadBalancer->__toString();
        $loadBalancer->handleRequest($request);

        $secondState = $loadBalancer->__toString();
        $loadBalancer->handleRequest($request);

        $thirdState = $loadBalancer->__toString();
        $loadBalancer->handleRequest($request);

        return new Response("First state: \n". $firstState. "\nSecond state: \n". $secondState . "\nThird state: \n". $thirdState);
    }

    #[Route('/load-balancer-load-based', name: 'load_based_example')]
    public function loadBasedAlgorithmExample(): Response
    {
        $firstHost = new Host(BigDecimal::of(0.3));
        $secondHost = new Host(BigDecimal::of(0.7));
        $thirdHost = new Host(BigDecimal::of(0.5));

        $loadBalancer = new LoadBalancer([$firstHost, $secondHost, $thirdHost], LoadBalancer::LOAD_BASED);

        $request = new Request(BigDecimal::of(0.1));

        $firstState = $loadBalancer->__toString();
        $loadBalancer->handleRequest($request);

        $secondState = $loadBalancer->__toString();
        $loadBalancer->handleRequest($request);

        $thirdState = $loadBalancer->__toString();
        $loadBalancer->handleRequest($request);

        return new Response("First state: \n". $firstState. "\nSecond state: \n". $secondState . "\nThird state: \n". $thirdState);
    }
}
