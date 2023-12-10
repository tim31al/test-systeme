<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\PaymentSystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CalculatePriceAction extends AbstractController
{
    public function __construct(private readonly PaymentSystem $service) {}

    /**
     * @throws \App\Exception\LoggedException
     */
    #[Route(path: '/calculate-price', name: 'calculate_price', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->request->all();
        $dto  = $this->service->process($data, PaymentSystem::METHOD_PRICE);

        return $this->json($dto, $dto->getCode(), [], ['groups' => 'api']);
    }
}