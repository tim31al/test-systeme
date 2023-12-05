<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\SuccessDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PurchaseAction extends AbstractController
{
    #[Route(path: '/purchase', name: 'purchase', methods: 'POST')]
    public function __invoke(Request $request): JsonResponse
    {
        $dto = new SuccessDTO('Purchase!');

        return $this->json($dto);
    }
}