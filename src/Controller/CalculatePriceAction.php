<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\SuccessDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CalculatePriceAction extends AbstractController
{
    #[Route(path: '/calculate-price', name: 'calculate_price', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->request->all();
        $dto  = new SuccessDTO('Success');

        return $this->json($dto, $dto->getCode());
    }
}