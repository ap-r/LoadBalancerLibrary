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

        // Capture the initial state
        $states = [];
        $algorithm = $loadBalancer->getAlgorithmName();
        $states[] = $this->formatHostStates($loadBalancer->getHosts());

        // Handle the first request and capture state
        $states = $this->sendOneRequestAndCaptureState($loadBalancer, $request, $states);

        // Handle the second request and capture state
        $states = $this->sendOneRequestAndCaptureState($loadBalancer, $request, $states);

        // Handle the third request and capture state
        $states = $this->sendOneRequestAndCaptureState($loadBalancer, $request, $states);

        // Handle the fourth request and capture state
        $states = $this->sendOneRequestAndCaptureState($loadBalancer, $request, $states);

        return $this->render('load_balancer_info.html.twig', [
            'algorithm' => $algorithm,
            'states' => $states,
        ]);
    }

    #[Route('/load-balancer-load-based', name: 'load_based_example')]
    public function loadBasedAlgorithmExample(): Response
    {
        $firstHost = new Host(BigDecimal::of(0.5));
        $secondHost = new Host(BigDecimal::of(0.7));
        $thirdHost = new Host(BigDecimal::of(0.8));

        $loadBalancer = new LoadBalancer([$firstHost, $secondHost, $thirdHost], LoadBalancer::LOAD_BASED);

        $request = new Request(BigDecimal::of(0.1));

        // Capture the initial state
        $states = [];
        $algorithm = $loadBalancer->getAlgorithmName();
        $states[] = $this->formatHostStates($loadBalancer->getHosts());

        // Handle the first request and capture state
        $states = $this->sendOneRequestAndCaptureState($loadBalancer, $request, $states);

        // Handle the second request and capture state
        $states = $this->sendOneRequestAndCaptureState($loadBalancer, $request, $states);

        // Handle the third request and capture state
        $states = $this->sendOneRequestAndCaptureState($loadBalancer, $request, $states);

        // Handle the fourth request and capture state
        $states = $this->sendOneRequestAndCaptureState($loadBalancer, $request, $states);

        // Handle the fifth request and capture state
        $states = $this->sendOneRequestAndCaptureState($loadBalancer, $request, $states);

        // Handle the sixth request and capture state
        $states = $this->sendOneRequestAndCaptureState($loadBalancer, $request, $states);

        return $this->render('load_balancer_info.html.twig', [
            'algorithm' => $algorithm,
            'states' => $states,
        ]);
    }

    /**
     * Helper method to format host states for display.
     */
    private function formatHostStates(array $hosts): array
    {
        return array_map(function (Host $host) {
            return [
                'load' => $host->getLoad(),
            ];
        }, $hosts);
    }

    /**
     * @param LoadBalancer $loadBalancer
     * @param Request $request
     * @param array $states
     * @return array
     */
    public function sendOneRequestAndCaptureState(LoadBalancer $loadBalancer, Request $request, array $states): array
    {
        $loadBalancer->handleRequest($request);
        $states[] = $this->formatHostStates($loadBalancer->getHosts());
        return $states;
    }
}
